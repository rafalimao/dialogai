<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $site_name ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1 class="sidebar-title">
                    <i class="fas fa-robot"></i>
                    Sistema de Assistentes
                </h1>
                <p class="sidebar-subtitle">Painel Administrativo</p>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="<?= $base_url ?>/index.php?route=dashboard" class="nav-link <?= strpos($_SERVER["REQUEST_URI"], "route=dashboard") !== false ? "active" : "" ?>">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="<?= $base_url ?>/index.php?route=assistants" class="nav-link <?= strpos($_SERVER["REQUEST_URI"], "route=assistants") !== false ? "active" : "" ?>">
                        <i class="fas fa-robot nav-icon"></i>
                        Assistentes
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="<?= $base_url ?>/index.php?route=conversations" class="nav-link <?= strpos($_SERVER["REQUEST_URI"], "route=conversations") !== false ? "active" : "" ?>">
                        <i class="fas fa-comments nav-icon"></i>
                        Conversas
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="<?= $base_url ?>/index.php?route=messages" class="nav-link <?= strpos($_SERVER["REQUEST_URI"], "route=messages") !== false ? "active" : "" ?>">
                        <i class="fas fa-envelope nav-icon"></i>
                        Mensagens
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="<?= $base_url ?>/index.php?route=stats" class="nav-link <?= strpos($_SERVER["REQUEST_URI"], "route=stats") !== false ? "active" : "" ?>">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        Estatísticas
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Conteúdo Principal -->
        <main class="main-content">
            <header class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title"><?= $page_title ?? 'Painel Administrativo' ?></h1>
                        <?php if (isset($page_subtitle)): ?>
                            <p class="page-subtitle"><?= $page_subtitle ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline" id="toggleSidebar" style="display: none;">
                            <i class="fas fa-bars"></i>
                        </button>
                        
                        <div class="text-muted">
                            <i class="fas fa-clock"></i>
                            <?= date('d/m/Y H:i') ?>
                        </div>
                    </div>
                </div>
            </header>
            
            <div class="content-body">
                <!-- Mensagens de Feedback -->
                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <?php foreach ($success as $message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Conteúdo da Página -->
                <?php 
                if (isset($content_view)) {
                    $viewFile = __DIR__ . "/views/{$content_view}.php";
                    if (file_exists($viewFile)) {
                        include $viewFile;
                    } else {
                        echo "<div class='alert alert-danger'>View não encontrada: {$content_view}</div>";
                    }
                }
                ?>
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="<?= $base_url ?>/assets/js/admin.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
        
        // Mobile sidebar toggle
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.querySelector('.sidebar');
        
        function checkMobile() {
            if (window.innerWidth <= 768) {
                toggleBtn.style.display = 'block';
            } else {
                toggleBtn.style.display = 'none';
                sidebar.classList.remove('show');
            }
        }
        
        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
        
        window.addEventListener('resize', checkMobile);
        checkMobile();
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !toggleBtn.contains(e.target) && 
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>


