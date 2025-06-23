<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$error = '';
$success = '';

// Check for success message from edit
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success = "Vaga atualizada com sucesso!";
}

// Handle job status toggle
if (isset($_POST['toggle_status'])) {
    $job_id = $_POST['job_id'];
    $new_status = $_POST['new_status'];
    
    try {
        $stmt = $conn->prepare("UPDATE job_postings SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_status, $job_id]);
        $success = "Status da vaga atualizado com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao atualizar status da vaga.";
    }
}

// Handle job deletion
if (isset($_POST['delete_job'])) {
    $job_id = $_POST['job_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM job_postings WHERE id = ?");
        $stmt->execute([$job_id]);
        $success = "Vaga excluída com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao excluir vaga.";
    }
}

// Get all job postings with category names
$stmt = $conn->query("SELECT j.*, c.name as category_name 
                     FROM job_postings j 
                     LEFT JOIN categories c ON j.category_id = c.id 
                     ORDER BY j.created_at DESC");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for the filter
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vagas - Sistema de Vagas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-briefcase me-2"></i>Sistema de Vagas
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-1"></i>Vagas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-tags me-1"></i>Categorias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="jobs.php">
                            <i class="fas fa-cogs me-1"></i>Gerenciar Vagas
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-container container mt-4">
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-cogs me-2"></i>Gerenciar Vagas</h2>
            <a href="create_job.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nova Vaga
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Empresa</th>
                                <th>Categoria</th>
                                <th>Status</th>
                                <th>Inscritos</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                                    <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($job['category_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $job['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $job['is_active'] ? 'Ativa' : 'Inativa'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="applicants.php?job_id=<?php echo $job['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-users me-1"></i>Ver Inscritos
                                        </a>
                                    </td>
                                    <td>
                                        <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                            <input type="hidden" name="new_status" value="<?php echo $job['is_active'] ? '0' : '1'; ?>">
                                            <button type="submit" name="toggle_status" class="btn btn-sm btn-<?php echo $job['is_active'] ? 'warning' : 'success'; ?>">
                                                <i class="fas fa-<?php echo $job['is_active'] ? 'pause' : 'play'; ?>"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta vaga?');">
                                            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                            <button type="submit" name="delete_job" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 