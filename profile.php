<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// Require login
requireLogin();

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: logout.php");
    exit;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $linkedin_url = trim($_POST['linkedin_url']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($name) || empty($email)) {
        $error = "Nome e email são obrigatórios.";
    } else {
        // Check if email is already used by another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->rowCount() > 0) {
            $error = "Este email já está sendo usado por outro usuário.";
        } else {
            $update_password = false;
            $hashed_password = $user['password'];
            
            // Check if password change is requested
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = "Digite sua senha atual para alterá-la.";
                } elseif (!password_verify($current_password, $user['password'])) {
                    $error = "Senha atual incorreta.";
                } elseif ($new_password !== $confirm_password) {
                    $error = "As novas senhas não coincidem.";
                } elseif (strlen($new_password) < 6) {
                    $error = "A nova senha deve ter pelo menos 6 caracteres.";
                } else {
                    $update_password = true;
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                }
            }
            
            if (!$error) {
                // Handle file upload
                $profile_image = $user['profile_image'];
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = 'uploads/profile_images/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    $file_type = $_FILES['profile_image']['type'];
                    
                    if (in_array($file_type, $allowed_types)) {
                        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                        $new_filename = uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                            // Delete old image if exists
                            if ($profile_image && file_exists($profile_image)) {
                                unlink($profile_image);
                            }
                            $profile_image = $upload_path;
                        }
                    } else {
                        $error = "Apenas arquivos JPG, PNG e GIF são permitidos.";
                    }
                }
                
                if (!$error) {
                    // Update user data
                    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, profile_image = ?, linkedin_url = ? WHERE id = ?");
                    
                    if ($stmt->execute([$name, $email, $hashed_password, $profile_image, $linkedin_url, $user_id])) {
                        $_SESSION['user_name'] = $name;
                        $success = "Perfil atualizado com sucesso!";
                        
                        // Refresh user data
                        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    } else {
                        $error = "Erro ao atualizar perfil. Tente novamente.";
                    }
                }
            }
        }
    }
}

// Get user's job applications
$stmt = $conn->prepare("SELECT ja.*, jp.title, jp.company_name, jp.created_at as job_created 
                       FROM job_applications ja 
                       JOIN job_postings jp ON ja.job_id = jp.id 
                       WHERE ja.user_id = ? 
                       ORDER BY ja.created_at DESC");
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Sistema de Vagas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sistema de Vagas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Vagas</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">Meu Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <?php if ($user['profile_image']): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                 alt="Foto do perfil" 
                                 class="profile-image mb-3">
                        <?php else: ?>
                            <div class="profile-image bg-secondary text-white d-flex align-items-center justify-content-center mb-3 mx-auto">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        
                        <?php if ($user['linkedin_url']): ?>
                            <a href="<?php echo htmlspecialchars($user['linkedin_url']); ?>" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-linkedin me-2"></i>Ver LinkedIn
                            </a>
                        <?php endif; ?>
                        
                        <hr>
                        <small class="text-muted">
                            Membro desde <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-edit me-2"></i>Editar Perfil</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Nova Foto de Perfil</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                <small class="text-muted">Deixe em branco para manter a foto atual</small>
                            </div>

                            <div class="mb-3">
                                <label for="linkedin_url" class="form-label">URL do LinkedIn</label>
                                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                       value="<?php echo htmlspecialchars($user['linkedin_url']); ?>">
                            </div>

                            <hr>
                            <h6>Alterar Senha (opcional)</h6>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Senha Atual</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nova Senha</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Job Applications -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fas fa-briefcase me-2"></i>Minhas Candidaturas</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($applications)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Você ainda não se candidatou a nenhuma vaga.
                                <a href="index.php" class="alert-link">Ver vagas disponíveis</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Vaga</th>
                                            <th>Empresa</th>
                                            <th>Data da Candidatura</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $app): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($app['title']); ?></td>
                                                <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($app['created_at'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($app['status']) {
                                                            'pending' => 'warning',
                                                            'reviewed' => 'info',
                                                            'accepted' => 'success',
                                                            'rejected' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                                                        <?php 
                                                            echo match($app['status']) {
                                                                'pending' => 'Pendente',
                                                                'reviewed' => 'Em Análise',
                                                                'accepted' => 'Aprovado',
                                                                'rejected' => 'Rejeitado',
                                                                default => 'Desconhecido'
                                                            };
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="job_details.php?id=<?php echo $app['job_id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Ver Vaga
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 