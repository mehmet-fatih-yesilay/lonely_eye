<?php
/**
 * Header Component with Glassmorphism Navbar
 * Includes Theme Toggle and User Menu
 */
?>
<!DOCTYPE html>
<html lang="tr" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lonely Eye - Premium Kitap ve Dergi Sosyal Ağı">
    <title>
        <?php echo isset($page_title) ? $page_title . ' - Lonely Eye' : 'Lonely Eye'; ?>
    </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/lonely_eye/assets/css/style.css">

    <!-- Favicon -->
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>👁️</text></svg>">
</head>

<body>

    <!-- Glassmorphism Navbar -->
    <nav class="navbar navbar-expand-lg navbar-glass">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="/lonely_eye/">
                <i class="fas fa-eye"></i>
                <span>Lonely Eye</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars" style="color: var(--text-main);"></i>
            </button>

            <!-- Navbar Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Logged In User Menu -->
                        <li class="nav-item">
                            <a class="nav-link" href="/lonely_eye/dashboard.php">
                                <i class="fas fa-home"></i> Ana Sayfa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/lonely_eye/discover.php">
                                <i class="fas fa-users"></i> İnsanları Keşfet
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/lonely_eye/library.php">
                                <i class="fas fa-book"></i> Kitaplar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/lonely_eye/messages.php">
                                <i class="fas fa-envelope"></i> Mesajlar
                            </a>
                        </li>

                        <!-- Search Form -->
                        <li class="nav-item">
                            <form action="/lonely_eye/search.php" method="GET" class="d-flex" style="margin-left: 0.5rem;">
                                <input type="search" name="q" class="form-control form-control-sm" placeholder="Ara..."
                                    style="width: 200px; background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main);">
                            </form>
                        </li>

                        <!-- Theme Toggle -->
                        <li class="nav-item">
                            <button id="theme-toggle" class="theme-toggle" title="Tema Değiştir">
                                <i class="fas fa-moon theme-icon"></i>
                            </button>
                        </li>

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown"
                                role="button" data-bs-toggle="dropdown">
                                <img src="<?php echo htmlspecialchars($_SESSION['avatar'] ?? '/lonely_eye/uploads/default.png'); ?>"
                                    alt="Avatar" class="avatar avatar-sm">
                                <span>
                                    <?php echo htmlspecialchars($_SESSION['username'] ?? 'Kullanıcı'); ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <li>
                                        <a class="dropdown-item text-danger" href="/lonely_eye/admin/">
                                            <i class="fas fa-shield-alt"></i> Yönetim Paneli
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item"
                                        href="/lonely_eye/profile.php?id=<?php echo $_SESSION['user_id']; ?>">
                                        <i class="fas fa-user"></i> Profilim
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/lonely_eye/settings.php">
                                        <i class="fas fa-cog"></i> Ayarlar
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="/lonely_eye/logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Guest Menu -->
                        <li class="nav-item">
                            <button class="theme-toggle" title="Tema Değiştir">
                                <i class="fas fa-moon theme-icon"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary btn-sm" href="/lonely_eye/login.php">
                                <i class="fas fa-sign-in-alt"></i> Giriş Yap
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm" href="/lonely_eye/register.php">
                                <i class="fas fa-user-plus"></i> Kayıt Ol
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggle = document.querySelector('.theme-toggle');
            const themeIcon = document.querySelector('.theme-icon');
            const htmlElement = document.documentElement;

            // Load saved theme from localStorage
            const savedTheme = localStorage.getItem('theme') || 'dark';
            htmlElement.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);

            // Theme toggle click handler
            if (themeToggle) {
                themeToggle.addEventListener('click', function () {
                    const currentTheme = htmlElement.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                    htmlElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateThemeIcon(newTheme);
                });
            }

            // Update icon based on theme
            function updateThemeIcon(theme) {
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.className = 'fas fa-moon theme-icon';
                    } else {
                        themeIcon.className = 'fas fa-sun theme-icon';
                    }
                }
            }
        });
    </script>