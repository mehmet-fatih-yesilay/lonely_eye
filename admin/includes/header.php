<?php
// Admin Paneli Header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../includes/db.php';

// Yetki Kontrolü
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Aktif Sayfa Belirleme
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - Lonely Eye</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        :root {
            --bg-sidebar: #1e293b;
            --text-sidebar: #e2e8f0;
            --bg-content: #0f172a;
        }

        body {
            background-color: var(--bg-content);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: 260px;
            background: var(--bg-sidebar);
            color: var(--text-sidebar);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1000;
        }

        .admin-brand {
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 800;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }

        .admin-menu {
            padding: 1rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-link {
            color: var(--text-sidebar);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(56, 189, 248, 0.1);
            color: var(--primary);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .admin-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Main Content Styles */
        .admin-content {
            flex-grow: 1;
            margin-left: 260px;
            padding: 2rem;
            width: calc(100% - 260px);
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-info h3 {
            font-size: 1.75rem;
            margin: 0;
            font-weight: 700;
        }

        .stat-info p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <nav class="admin-sidebar">
        <a href="../index.php" class="admin-brand">
            <i class="fas fa-eye text-primary"></i> Lonely Eye
        </a>

        <div class="admin-menu">
            <p class="text-muted small fw-bold px-3 mb-2">YÖNETİM</p>

            <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <a href="users.php" class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Kullanıcılar
            </a>

            <a href="books.php" class="nav-link <?php echo $current_page == 'books.php' ? 'active' : ''; ?>">
                <i class="fas fa-book"></i> Kitaplar
            </a>

            <a href="reviews.php" class="nav-link <?php echo $current_page == 'reviews.php' ? 'active' : ''; ?>">
                <i class="fas fa-comments"></i> Yorumlar
            </a>

            <p class="text-muted small fw-bold px-3 mb-2 mt-4">SİSTEM</p>

            <a href="../index.php" class="nav-link">
                <i class="fas fa-external-link-alt"></i> Siteyi Görüntüle
            </a>

            <a href="../logout.php" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a>
        </div>

        <div class="admin-footer">
            <div class="d-flex align-items-center gap-2">
                <img src="<?php echo htmlspecialchars($_SESSION['avatar'] ?? '../assets/img/default-avatar.png'); ?>"
                    alt="Admin" class="avatar avatar-sm">
                <div class="small">
                    <div class="fw-bold">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">Yönetici</div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="admin-content">