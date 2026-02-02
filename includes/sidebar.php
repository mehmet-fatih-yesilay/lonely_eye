<?php
/**
 * Sidebar Component - Reusable Navigation
 * Premium Design with Glassmorphism
 */

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    return;
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        background: var(--bg-card);
        border-right: 1px solid var(--border-color);
        padding: 2rem 1.5rem;
        overflow-y: auto;
        z-index: 999;
        transition: all 0.3s ease;
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .sidebar-logo i {
        font-size: 2rem;
        color: var(--primary);
    }

    .sidebar-logo h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .sidebar-profile {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-glass);
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }

    .sidebar-profile img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--primary);
    }

    .sidebar-profile-info h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .sidebar-profile-info p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0 0 2rem 0;
    }

    .sidebar-menu li {
        margin-bottom: 0.5rem;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1rem;
        color: var(--text-muted);
        text-decoration: none;
        border-radius: var(--radius-md);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .sidebar-menu a:hover {
        background: rgba(56, 189, 248, 0.1);
        color: var(--primary);
    }

    .sidebar-menu a.active {
        background: rgba(56, 189, 248, 0.15);
        color: var(--primary);
        box-shadow: 0 0 20px rgba(56, 189, 248, 0.3);
        border: 1px solid rgba(56, 189, 248, 0.3);
    }

    .sidebar-menu a i {
        width: 20px;
        text-align: center;
    }

    .main-content {
        margin-left: 280px;
        padding: 2rem;
        min-height: 100vh;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            padding: 1rem;
        }
    }
</style>

<div class="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <i class="fas fa-eye"></i>
        <h3>Lonely Eye</h3>
    </div>

    <!-- User Profile -->
    <div class="sidebar-profile">
        <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar">
        <div class="sidebar-profile-info">
            <h5>
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </h5>
            <p>Okuyucu</p>
        </div>
    </div>

    <!-- Menu -->
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Ana Sayfa</span>
            </a>
        </li>
        <li>
            <a href="discover.php" class="<?php echo $current_page === 'discover.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>İnsanları Keşfet</span>
            </a>
        </li>
        <li>
            <a href="library.php" class="<?php echo $current_page === 'library.php' ? 'active' : ''; ?>">
                <i class="fas fa-book"></i>
                <span>Kitaplar</span>
            </a>
        </li>
        <li>
            <a href="messages.php" class="<?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span>Mesajlar</span>
            </a>
        </li>
        <li>
            <a href="profile.php" class="<?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user"></i>
                <span>Profilim</span>
            </a>
        </li>
    </ul>

    <!-- Logout -->
    <a href="logout.php" class="btn btn-outline-primary w-100">
        <i class="fas fa-sign-out-alt"></i> Çıkış Yap
    </a>
</div>