<?php
/**
 * Search Page - Placeholder
 * Will be implemented later
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Arama";
require_once 'includes/header.php';
?>

<style>
    .search-container {
        max-width: 800px;
        margin: 4rem auto;
        padding: 0 1rem;
        text-align: center;
    }

    .search-placeholder {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 4rem 3rem;
        box-shadow: var(--shadow-lg);
    }

    .search-placeholder i {
        font-size: 5rem;
        color: var(--primary);
        margin-bottom: 2rem;
    }

    .search-placeholder h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
</style>

<!-- Sidebar -->
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 280px; padding: 2rem;">
    <div class="search-container">
        <div class="search-placeholder fade-in">
            <i class="fas fa-search"></i>
            <h1>Arama Özelliği</h1>
            <p class="lead text-muted">Bu özellik yakında eklenecek!</p>
            <a href="dashboard.php" class="btn btn-primary mt-3">
                <i class="fas fa-home"></i> Ana Sayfaya Dön
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>