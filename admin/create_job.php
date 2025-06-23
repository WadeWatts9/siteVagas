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
        $company_logo = '';
        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/company_logos/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $upload_path)) {
                $company_logo = 'uploads/company_logos/' . $new_filename;
            }
        }
        
        try {
            $stmt = $conn->prepare("INSERT INTO job_postings (title, description, requirements, company_name, 
                                  location, salary_range, contact_email, contact_phone, company_logo, 
                                  category_id, created_by) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $title, $description, $requirements, $company_name, $location,
                $salary_range, $contact_email, $contact_phone, $company_logo,
                $category_id, $_SESSION['user_id']
            ]);
            
            $success = "Vaga criada com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao criar vaga. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Vaga - Sistema de Vagas</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Criar Nova Vaga</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Título da Vaga *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoria *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição da Vaga *</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="requirements" class="form-label">Requisitos *</label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="company_name" class="form-label">Nome da Empresa *</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="company_logo" class="form-label">Logo da Empresa</label>
                                <input type="file" class="form-control" id="company_logo" name="company_logo" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Localização *</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>

                            <div class="mb-3">
                                <label for="salary_range" class="form-label">Faixa Salarial</label>
                                <input type="text" class="form-control" id="salary_range" name="salary_range">
                            </div>

                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Email para Contato *</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                            </div>

                            <div class="mb-3">
                                <label for="contact_phone" class="form-label">Telefone para Contato</label>
                                <input type="tel" class="form-control" id="contact_phone" name="contact_phone">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Criar Vaga</button>
                                <a href="jobs.php" class="btn btn-secondary">Cancelar</a>
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