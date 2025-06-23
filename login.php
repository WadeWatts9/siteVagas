<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// Redirect if already logged in
redirectIfLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
                // In a real application, you'd store this token in the database
            }
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/jobs.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Email ou senha inválidos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Vagas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card fade-in-up">
            <div class="auth-header">
                <div class="text-center mb-3">
                    <i class="fas fa-briefcase fa-3x mb-3" style="color: var(--primary-color);"></i>
                </div>
                <h2>Bem-vindo de volta!</h2>
                <p class="text-muted mb-0">Faça login para acessar sua conta</p>
            </div>

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

            <!-- Demo Accounts Info -->
            <div class="demo-section glass p-3 rounded mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Contas de Demonstração
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="demo-card" onclick="fillAdmin()">
                            <div class="demo-icon">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div class="demo-info">
                                <strong>Administrador</strong>
                                <small class="d-block text-primary">admin@example.com</small>
                                <small class="text-muted">Senha: password</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="demo-card" onclick="fillUser()">
                            <div class="demo-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="demo-info">
                                <strong>Usuário Comum</strong>
                                <small class="d-block text-success">joao.silva@email.com</small>
                                <small class="text-muted">Senha: 123456</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Outros usuários:</strong> maria.santos@email.com, pedro.oliveira@email.com, ana.costa@email.com, carlos.ferreira@email.com (senha: 123456)
                    </small>
                </div>
            </div>
            
            <form method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required>
                    <div class="invalid-feedback">
                        Por favor, insira um email válido.
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Senha
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">
                        Por favor, insira sua senha.
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            <i class="fas fa-heart me-1"></i>Lembrar de mim
                        </label>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mb-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                    </button>
                </div>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <div class="text-center">
                <p class="mb-3">Não tem uma conta?</p>
                <a href="register.php" class="btn btn-outline-primary">
                    <i class="fas fa-user-plus me-2"></i>Criar Conta Gratuita
                </a>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-link text-muted">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Início
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-fill demo credentials
        function fillAdmin() {
            document.getElementById('email').value = 'admin@example.com';
            document.getElementById('password').value = 'password';
            document.getElementById('email').focus();
            
            // Add visual feedback
            const card = event.currentTarget;
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 150);
        }
        
        function fillUser() {
            document.getElementById('email').value = 'joao.silva@email.com';
            document.getElementById('password').value = '123456';
            document.getElementById('email').focus();
            
            // Add visual feedback
            const card = event.currentTarget;
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 150);
        }
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Add loading state to submit button
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading me-2"></span>Entrando...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    </script>
</body>
</html> 