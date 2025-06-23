<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get active job postings
$stmt = $conn->prepare("SELECT j.*, c.name as category_name 
                       FROM job_postings j 
                       LEFT JOIN categories c ON j.category_id = c.id 
                       WHERE j.is_active = 1 
                       ORDER BY j.created_at DESC");
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for the filter
$stmt = $conn->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Vagas de Emprego</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-briefcase me-2"></i>Sistema de Vagas
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Vagas
                        </a>
                    </li>
                    <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/categories.php">
                            <i class="fas fa-tags me-1"></i>Categorias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/jobs.php">
                            <i class="fas fa-cogs me-1"></i>Gerenciar Vagas
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user me-1"></i>Meu Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Sair
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Cadastrar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container-fluid">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content fade-in-up px-4">
                        <h1 class="hero-title">
                            Encontre sua <span class="text-gradient">próxima oportunidade</span>
                        </h1>
                        <p class="hero-subtitle">
                            Conectamos talentos com as melhores empresas. Descubra vagas que combinam com seu perfil e acelere sua carreira.
                        </p>
                        <div class="hero-stats">
                            <div class="stat-item">
                                <h3><?php echo count($jobs); ?></h3>
                                <p>Vagas Ativas</p>
                            </div>
                            <div class="stat-item">
                                <h3><?php echo count($categories); ?></h3>
                                <p>Categorias</p>
                            </div>
                            <div class="stat-item">
                                <h3>100+</h3>
                                <p>Empresas</p>
                            </div>
                        </div>
                        <?php if (!$isLoggedIn): ?>
                        <div class="hero-actions">
                            <a href="register.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-rocket me-2"></i>Começar Agora
                            </a>
                            <a href="#vagas" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-search me-2"></i>Ver Vagas
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image fade-in-up px-4">
                        <div class="floating-card">
                            <i class="fas fa-briefcase"></i>
                            <h4>Oportunidades Incríveis</h4>
                            <p>Vagas em empresas de tecnologia, startups e corporações</p>
                        </div>
                        <div class="floating-card delay-1">
                            <i class="fas fa-chart-line"></i>
                            <h4>Crescimento Profissional</h4>
                            <p>Desenvolva sua carreira com posições desafiadoras</p>
                        </div>
                        <div class="floating-card delay-2">
                            <i class="fas fa-users"></i>
                            <h4>Networking</h4>
                            <p>Conecte-se com profissionais e empresas do mercado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs Section -->
    <section id="vagas" class="jobs-section">
        <div class="container">
            <!-- Category Filter -->
            <div class="category-filter fade-in-up">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="section-title">
                            <i class="fas fa-briefcase me-2"></i>
                            <span id="jobsTitle">Vagas Disponíveis</span>
                        </h2>
                    </div>
                    <div class="col-md-6">
                        <div class="filter-container">
                            <label for="categoryFilter" class="form-label">
                                <i class="fas fa-filter me-2"></i>Filtrar por categoria
                            </label>
                            <select class="form-select" id="categoryFilter">
                                <option value="">Todas as categorias</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Category Pills -->
                <div class="category-pills mt-4">
                    <button class="category-btn active" data-category="">
                        <i class="fas fa-th me-2"></i>Todas
                    </button>
                    <?php foreach ($categories as $category): ?>
                        <button class="category-btn" data-category="<?php echo $category['id']; ?>">
                            <i class="fas fa-tag me-2"></i><?php echo htmlspecialchars($category['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Jobs Grid -->
            <div class="row" id="jobsList">
                <?php foreach ($jobs as $index => $job): ?>
                    <div class="col-lg-6 mb-4 fade-in-up job-item" 
                         data-category-id="<?php echo $job['category_id']; ?>" 
                         data-category-name="<?php echo htmlspecialchars($job['category_name']); ?>"
                         style="animation-delay: <?php echo ($index * 0.1); ?>s">
                        <div class="job-card">
                            <div class="card-header d-flex align-items-center">
                                <div class="company-info">
                                    <?php if ($job['company_logo']): ?>
                                        <img src="<?php echo htmlspecialchars($job['company_logo']); ?>" 
                                             class="company-logo me-3" alt="Logo da empresa">
                                    <?php else: ?>
                                        <div class="company-logo-placeholder me-3">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h5 class="job-title mb-1"><?php echo htmlspecialchars($job['title']); ?></h5>
                                        <p class="company-name mb-0"><?php echo htmlspecialchars($job['company_name']); ?></p>
                                    </div>
                                </div>
                                <div class="job-badge">
                                    <span class="badge bg-light text-dark">
                                        <?php echo htmlspecialchars($job['category_name']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="job-meta mb-3">
                                    <span class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($job['location']); ?>
                                    </span>
                                    <?php if ($job['salary_range']): ?>
                                    <span class="meta-item">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <?php echo htmlspecialchars($job['salary_range']); ?>
                                    </span>
                                    <?php endif; ?>
                                    <span class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('d/m/Y', strtotime($job['created_at'])); ?>
                                    </span>
                                </div>
                                <p class="job-description">
                                    <?php echo nl2br(htmlspecialchars(substr($job['description'], 0, 150))); ?>...
                                </p>
                                <div class="job-actions">
                                    <a href="job_details.php?id=<?php echo $job['id']; ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-eye me-2"></i>Ver Detalhes
                                    </a>
                                    <?php if ($isLoggedIn && !$isAdmin): ?>
                                    <a href="job_details.php?id=<?php echo $job['id']; ?>#apply" 
                                       class="btn btn-success">
                                        <i class="fas fa-paper-plane me-2"></i>Candidatar-se
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="text-center py-5" style="display: none;">
                <div class="empty-illustration">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h3>Nenhuma vaga encontrada</h3>
                    <p class="text-muted">Tente ajustar os filtros ou volte mais tarde para novas oportunidades.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Action Button for Admin -->
    <?php if ($isAdmin): ?>
    <a href="admin/create_job.php" class="fab" title="Criar Nova Vaga">
        <i class="fas fa-plus"></i>
    </a>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced filter functionality
        function initializeFilters() {
            const categoryFilter = document.getElementById('categoryFilter');
            const categoryBtns = document.querySelectorAll('.category-btn');
            
            // Select dropdown filter
            categoryFilter.addEventListener('change', function() {
                filterJobs(this.value);
                updateCategoryButtons(this.value);
            });
            
            // Category pills filter
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-category');
                    filterJobs(categoryId);
                    categoryFilter.value = categoryId;
                    updateCategoryButtons(categoryId);
                });
            });
        }
        
        function filterJobs(selectedCategoryId) {
            const jobCards = document.querySelectorAll('.job-item');
            const emptyState = document.getElementById('emptyState');
            let visibleCount = 0;
            
            // Add loading effect
            document.body.style.cursor = 'wait';
            
            setTimeout(() => {
                jobCards.forEach((card, index) => {
                    const cardCategoryId = card.getAttribute('data-category-id');
                    
                    if (selectedCategoryId === '' || cardCategoryId === selectedCategoryId) {
                        card.style.display = 'block';
                        card.style.animationDelay = (index * 0.05) + 's';
                        card.classList.add('fade-in-up');
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Show/hide empty state
                if (visibleCount === 0) {
                    emptyState.style.display = 'block';
                } else {
                    emptyState.style.display = 'none';
                }
                
                updateJobsTitle(visibleCount, selectedCategoryId);
                document.body.style.cursor = 'default';
            }, 100);
        }
        
        function updateCategoryButtons(selectedCategoryId) {
            const categoryBtns = document.querySelectorAll('.category-btn');
            categoryBtns.forEach(btn => {
                const btnCategory = btn.getAttribute('data-category');
                if (btnCategory === selectedCategoryId) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
        
        function updateJobsTitle(count, categoryId = '') {
            const title = document.getElementById('jobsTitle');
            const categorySelect = document.getElementById('categoryFilter');
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption.text;
            
            if (categoryId === '') {
                title.innerHTML = `<span class="count">${count}</span> Vagas Disponíveis`;
            } else {
                title.innerHTML = `<span class="count">${count}</span> Vagas em "${categoryName}"`;
            }
        }
        
        // Smooth scrolling
        function initializeSmoothScroll() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        }
        
        // Initialize everything on page load
        document.addEventListener('DOMContentLoaded', function() {
            const totalJobs = document.querySelectorAll('.job-item').length;
            updateJobsTitle(totalJobs);
            initializeFilters();
            initializeSmoothScroll();
            
            // Add intersection observer for animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.fade-in-up').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html> 