<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is logged in and is admin
requireAdmin();

$error = '';
$success = '';

// Handle category deletion
if (isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $success = "Categoria excluída com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao excluir categoria. Verifique se não há vagas vinculadas a ela.";
    }
}

// Handle category creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $category_name = trim($_POST['category_name']);
    $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
    
    if (empty($category_name)) {
        $error = "O nome da categoria é obrigatório.";
    } else {
        try {
            if ($category_id) {
                // Update existing category
                $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
                $stmt->execute([$category_name, $category_id]);
                $success = "Categoria atualizada com sucesso!";
            } else {
                // Create new category
                $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->execute([$category_name]);
                $success = "Categoria criada com sucesso!";
            }
        } catch (PDOException $e) {
            $error = "Erro ao salvar categoria. O nome já pode estar em uso. Erro: " . $e->getMessage();
        }
    }
}

// Get all categories
try {
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erro ao carregar categorias: " . $e->getMessage();
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - Sistema de Vagas</title>
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
                        <a class="nav-link active" href="categories.php">Categorias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php">Gerenciar Vagas</a>
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
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-plus me-2"></i>Nova Categoria</h4>
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

                        <form method="POST" id="categoryForm">
                            <input type="hidden" name="category_id" id="category_id">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">
                                    <i class="fas fa-tag me-2"></i>Nome da Categoria
                                </label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-list me-2"></i>Categorias Existentes</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($categories)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Nenhuma categoria encontrada. Crie a primeira categoria!
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Data de Criação</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td><?php echo $category['id']; ?></td>
                                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary edit-category" 
                                                            data-id="<?php echo $category['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                            title="Editar categoria">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                        <button type="submit" name="delete_category" class="btn btn-sm btn-danger" title="Excluir categoria">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
    <script>
        document.querySelectorAll('.edit-category').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                
                document.getElementById('category_id').value = id;
                document.getElementById('category_name').value = name;
                document.getElementById('category_name').focus();
                
                // Change button text to indicate editing
                const submitBtn = document.querySelector('#categoryForm button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Atualizar';
            });
        });

        // Reset form when creating new category
        document.getElementById('categoryForm').addEventListener('reset', function() {
            document.getElementById('category_id').value = '';
            const submitBtn = document.querySelector('#categoryForm button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Salvar';
        });
    </script>
</body>
</html> 