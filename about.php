<?php
/**
 * About Page - About Lonely Eye
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

$page_title = "Hakkımızda";
require_once 'includes/header.php';
?>

<style>
    .about-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .about-hero {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 4rem 3rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }

    .about-hero i {
        font-size: 4rem;
        color: var(--primary);
        margin-bottom: 1.5rem;
    }

    .about-hero h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .about-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }

    .about-section h2 {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        color: var(--text-main);
    }

    .about-section h2 i {
        color: var(--primary);
    }

    .about-section p {
        line-height: 1.8;
        color: var(--text-muted);
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .feature-card {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .feature-card i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }

    .feature-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }
</style>

<!-- Sidebar -->
<?php if (isset($_SESSION['user_id'])): ?>
    <?php include 'includes/sidebar.php'; ?>
<?php endif; ?>

<!-- Main Content -->
<div class="main-content"
    style="<?php echo isset($_SESSION['user_id']) ? 'margin-left: 280px;' : ''; ?> padding: 2rem;">
    <div class="about-container">
        <!-- Hero Section -->
        <div class="about-hero fade-in">
            <i class="fas fa-eye"></i>
            <h1>Lonely Eye Hakkında</h1>
            <p class="lead">Kitap ve dergi tutkunlarının buluşma noktası</p>
        </div>

        <!-- About Section -->
        <div class="about-section fade-in">
            <h2><i class="fas fa-info-circle"></i> Biz Kimiz?</h2>
            <p>
                Lonely Eye, kitap ve dergi severlerin bir araya geldiği, okuma deneyimlerini paylaştığı ve yeni keşifler
                yaptığı premium bir sosyal ağdır.
                Modern ve şık tasarımımızla, okuma tutkunuzu daha keyifli bir deneyime dönüştürüyoruz.
            </p>
            <p>
                Platformumuzda binlerce kitap ve dergi arasından favorilerinizi keşfedebilir, yorumlarınızı paylaşabilir
                ve benzer ilgi alanlarına sahip
                okuyucularla bağlantı kurabilirsiniz.
            </p>
        </div>

        <!-- Features Section -->
        <div class="about-section fade-in">
            <h2><i class="fas fa-star"></i> Özelliklerimiz</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fas fa-book"></i>
                    <h3>Geniş Kütüphane</h3>
                    <p>Binlerce kitap ve dergi</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-users"></i>
                    <h3>Sosyal Ağ</h3>
                    <p>Okuyucularla bağlantı kurun</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-comments"></i>
                    <h3>Yorumlar</h3>
                    <p>Deneyimlerinizi paylaşın</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-heart"></i>
                    <h3>Koleksiyonlar</h3>
                    <p>Favorilerinizi düzenleyin</p>
                </div>
            </div>
        </div>

        <!-- Mission Section -->
        <div class="about-section fade-in">
            <h2><i class="fas fa-bullseye"></i> Misyonumuz</h2>
            <p>
                Okuma kültürünü yaygınlaştırmak ve kitap severleri bir araya getirmek için çalışıyoruz.
                Her okuyucunun kendini ifade edebileceği, yeni kitaplar keşfedebileceği ve anlamlı bağlantılar
                kurabileceği bir platform sunmayı hedefliyoruz.
            </p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>