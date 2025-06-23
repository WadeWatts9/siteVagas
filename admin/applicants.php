<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Check if job_id is provided
if (!isset($_GET['job_id'])) {
    header("Location: jobs.php");
    exit;
}

$job_id = $_GET['job_id'];

// Get job details
$stmt = $conn->prepare("SELECT title, company_name FROM job_postings WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    header("Location: jobs.php");
    exit;
}

// Get applicants for this job
$stmt = $conn->prepare("SELECT u.*, ja.status, ja.created_at as application_date 
                       FROM job_applications ja 
                       JOIN users u ON ja.user_id = u.id 
                       WHERE ja.job_id = ? 
                       ORDER BY ja.created_at DESC");
$stmt->execute([$job_id]);
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscritos - Sistema de Vagas</title>
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
                        <a class="nav-link" href="../logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Inscritos para a Vaga</h2>
                <h4 class="text-muted"><?php echo htmlspecialchars($job['title']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($job['company_name']); ?></p>
            </div>
            <a href="jobs.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (empty($applicants)): ?>
                    <div class="alert alert-info">
                        Nenhum candidato inscrito para esta vaga ainda.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>LinkedIn</th>
                                    <th>Data da Inscrição</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applicants as $applicant): ?>
                                    <tr>
                                        <td>
                                            <?php if ($applicant['profile_image']): ?>
                                                <img src="../<?php echo htmlspecialchars($applicant['profile_image']); ?>" 
                                                     alt="Foto do perfil" 
                                                     class="rounded-circle"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($applicant['name']); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($applicant['email']); ?>">
                                                <?php echo htmlspecialchars($applicant['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($applicant['linkedin_url']): ?>
                                                <a href="<?php echo htmlspecialchars($applicant['linkedin_url']); ?>" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fab fa-linkedin"></i> Ver Perfil
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Não informado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($applicant['application_date'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo match($applicant['status']) {
                                                    'pending' => 'warning',
                                                    'reviewed' => 'info',
                                                    'accepted' => 'success',
                                                    'rejected' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php 
                                                    echo match($applicant['status']) {
                                                        'pending' => 'Pendente',
                                                        'reviewed' => 'Em Análise',
                                                        'accepted' => 'Aprovado',
                                                        'rejected' => 'Rejeitado',
                                                        default => 'Desconhecido'
                                                    };
                                                ?>
                                            </span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 