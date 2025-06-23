<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is logged in and is admin
requireAdmin();

$error = '';
$success = '';

// Check if job ID is provided
if (!isset($_GET['id'])) {
    header("Location: jobs.php");
    exit;
}

$job_id = $_GET['id'];

// Get job details
$stmt = $conn->prepare("SELECT * FROM job_postings WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    header("Location: jobs.php");
    exit;
}

// Get categories for the select
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $company_name = trim($_POST['company_name']);
    $location = trim($_POST['location']);
    $salary_range = trim($_POST['salary_range']);
    $contact_email = trim($_POST['contact_email']);
    $contact_phone = trim($_POST['contact_phone']);
    $category_id = $_POST['category_id'];
    
    // Validate required fields
    if (empty($title) || empty($description) || empty($requirements) || empty($company_name) || 
        empty($location) || empty($contact_email) || empty($category_id)) {
        $error = "Todos os campos obrigatórios devem ser preenchidos.";
    } else {
        // Handle company logo upload
        $company_logo = $job['company_logo']; // Keep existing logo by default
        
        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/company_logos/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['company_logo']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $file_extension = strtolower(pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $upload_path)) {
                    // Delete old logo if exists
                    if ($company_logo && file_exists('../' . $company_logo)) {
                        unlink('../' . $company_logo);
                    }
                    $company_logo = 'uploads/company_logos/' . $new_filename;
                }
            } else {
                $error = "Apenas arquivos JPG, PNG e GIF são permitidos para o logo.";
            }
        }
        
        if (!$error) {
            try {
                $stmt = $conn->prepare("UPDATE job_postings SET title = ?, description = ?, requirements = ?, 
                                      company_name = ?, location = ?, salary_range = ?, contact_email = ?, 
                                      contact_phone = ?, company_logo = ?, category_id = ? WHERE id = ?");
                
                $stmt->execute([
                    $title, $description, $requirements, $company_name, $location,
                    $salary_range, $contact_email, $contact_phone, $company_logo,
                    $category_id, $job_id
                ]);
                
                $success = "Vaga atualizada com sucesso!";
                
                // Refresh job data
                $stmt = $conn->prepare("SELECT * FROM job_postings WHERE id = ?");
                $stmt->execute([$job_id]);
                $job = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Redirect to jobs.php after successful update
                header("Location: jobs.php?success=1");
                exit;
                
            } catch (PDOException $e) {
                $error = "Erro ao atualizar vaga. Tente novamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vaga - Sistema de Vagas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Sistema de Vagas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Vagas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categorias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="jobs.php">Gerenciar Vagas</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3><i class="fas fa-edit me-2"></i>Editar Vaga</h3>
                        <a href="jobs.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                    <div class="card-body">
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

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">
                                            <i class="fas fa-briefcase me-2"></i>Título da Vaga *
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($job['title']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">
                                            <i class="fas fa-tag me-2"></i>Categoria *
                                        </label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Selecione uma categoria</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                        <?php echo ($category['id'] == $job['category_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-2"></i>Descrição da Vaga *
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="requirements" class="form-label">
                                    <i class="fas fa-list-check me-2"></i>Requisitos *
                                </label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="4" required><?php echo htmlspecialchars($job['requirements']); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">
                                            <i class="fas fa-building me-2"></i>Nome da Empresa *
                                        </label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" 
                                               value="<?php echo htmlspecialchars($job['company_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">
                                            <i class="fas fa-map-marker-alt me-2"></i>Localização *
                                        </label>
                                        <input type="text" class="form-control" id="location" name="location" 
                                               value="<?php echo htmlspecialchars($job['location']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="salary_range" class="form-label">
                                            <i class="fas fa-money-bill-wave me-2"></i>Faixa Salarial
                                        </label>
                                        <input type="text" class="form-control" id="salary_range" name="salary_range" 
                                               value="<?php echo htmlspecialchars($job['salary_range']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_logo" class="form-label">
                                            <i class="fas fa-image me-2"></i>Logo da Empresa
                                        </label>
                                        <input type="file" class="form-control" id="company_logo" name="company_logo" accept="image/*">
                                        <?php if ($job['company_logo']): ?>
                                            <small class="text-muted">Logo atual: 
                                                <a href="../<?php echo htmlspecialchars($job['company_logo']); ?>" target="_blank">Ver logo</a>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Email para Contato *
                                        </label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                               value="<?php echo htmlspecialchars($job['contact_email']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">
                                            <i class="fas fa-phone me-2"></i>Telefone para Contato
                                        </label>
                                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                               value="<?php echo htmlspecialchars($job['contact_phone']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="jobs.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 