<?php
session_start();
require_once 'config/database.php';

// Check if job_id is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$job_id = $_GET['id'];
$error = '';
$success = '';

// Get job details with category name
$stmt = $conn->prepare("SELECT j.*, c.name as category_name 
                       FROM job_postings j 
                       LEFT JOIN categories c ON j.category_id = c.id 
                       WHERE j.id = ? AND j.is_active = 1");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    header("Location: index.php");
    exit;
}

// Handle job application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    // Check if user already applied
    $stmt = $conn->prepare("SELECT id FROM job_applications WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$job_id, $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        $error = "Você já se candidatou para esta vaga.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO job_applications (job_id, user_id) VALUES (?, ?)");
            $stmt->execute([$job_id, $_SESSION['user_id']]);
            $success = "Candidatura realizada com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao realizar candidatura. Tente novamente.";
        }
    }
}

// Check if user has already applied
$has_applied = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id FROM job_applications WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$job_id, $_SESSION['user_id']]);
    $has_applied = $stmt->rowCount() > 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['title']); ?> - Sistema de Vagas</title>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Meu Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Cadastrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h2>
                                <h4 class="text-muted"><?php echo htmlspecialchars($job['company_name']); ?></h4>
                            </div>
                            <?php if ($job['company_logo']): ?>
                                <img src="<?php echo htmlspecialchars($job['company_logo']); ?>" 
                                     alt="Logo da empresa" 
                                     class="img-fluid"
                                     style="max-height: 100px;">
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <span class="badge bg-primary me-2">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($job['category_name']); ?>
                            </span>
                            <span class="badge bg-secondary me-2">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?>
                            </span>
                            <?php if ($job['salary_range']): ?>
                                <span class="badge bg-info">
                                    <i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary_range']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h5>Descrição da Vaga</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>

                        <h5>Requisitos</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informações de Contato</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:<?php echo htmlspecialchars($job['contact_email']); ?>">
                                    <?php echo htmlspecialchars($job['contact_email']); ?>
                                </a>
                            </li>
                            <?php if ($job['contact_phone']): ?>
                                <li class="mb-2">
                                    <i class="fas fa-phone me-2"></i>
                                    <a href="tel:<?php echo htmlspecialchars($job['contact_phone']); ?>">
                                        <?php echo htmlspecialchars($job['contact_phone']); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($has_applied): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-check-circle"></i> Você já se candidatou para esta vaga.
                                </div>
                            <?php else: ?>
                                <form method="POST">
                                    <button type="submit" name="apply_job" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane"></i> Candidatar-se
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> Para se candidatar, você precisa estar logado.
                                <div class="mt-2">
                                    <a href="login.php" class="btn btn-primary btn-sm">Fazer Login</a>
                                    <a href="register.php" class="btn btn-outline-primary btn-sm">Cadastrar</a>
                                </div>
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