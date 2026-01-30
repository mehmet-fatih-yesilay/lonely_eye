<?php
/**
 * Privacy Policy Page
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

$page_title = "Gizlilik Politikası";
require_once 'includes/header.php';
?>

<style>
    .privacy-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .privacy-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 3rem;
        box-shadow: var(--shadow-lg);
    }

    .privacy-card h1 {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
    }

    .privacy-card h1 i {
        color: var(--primary);
        font-size: 2.5rem;
    }

    .privacy-section {
        margin-bottom: 2.5rem;
    }

    .privacy-section h2 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-main);
        margin-bottom: 1rem;
    }

    .privacy-section h2 i {
        color: var(--primary);
    }

    .privacy-section p {
        line-height: 1.8;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }

    .privacy-section ul {
        color: var(--text-muted);
        line-height: 1.8;
        margin-left: 1.5rem;
    }
</style>

<!-- Sidebar -->
<?php if (isset($_SESSION['user_id'])): ?>
    <?php include 'includes/sidebar.php'; ?>
<?php endif; ?>

<!-- Main Content -->
<div class="main-content"
    style="<?php echo isset($_SESSION['user_id']) ? 'margin-left: 280px;' : ''; ?> padding: 2rem;">
    <div class="privacy-container">
        <div class="privacy-card fade-in">
            <h1>
                <i class="fas fa-shield-alt"></i>
                Gizlilik Politikası
            </h1>

            <div class="privacy-section">
                <h2><i class="fas fa-info-circle"></i> Genel Bilgiler</h2>
                <p>
                    Lonely Eye olarak, kullanıcılarımızın gizliliğine önem veriyoruz. Bu gizlilik politikası,
                    platformumuzda toplanan, kullanılan ve korunan kişisel bilgiler hakkında sizi bilgilendirmek için
                    hazırlanmıştır.
                </p>
            </div>

            <div class="privacy-section">
                <h2><i class="fas fa-database"></i> Toplanan Bilgiler</h2>
                <p>Platformumuzda aşağıdaki bilgiler toplanmaktadır:</p>
                <ul>
                    <li>Kullanıcı adı ve e-posta adresi</li>
                    <li>Profil bilgileri (biyografi, avatar)</li>
                    <li>Okuma geçmişi ve yorumlar</li>
                    <li>Sosyal etkileşimler (takip, mesajlar)</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h2><i class="fas fa-lock"></i> Bilgi Güvenliği</h2>
                <p>
                    Kişisel bilgileriniz, endüstri standardı güvenlik önlemleriyle korunmaktadır.
                    Şifreleriniz güvenli hash algoritmaları ile saklanır ve hiçbir zaman düz metin olarak depolanmaz.
                </p>
            </div>

            <div class="privacy-section">
                <h2><i class="fas fa-share-alt"></i> Bilgi Paylaşımı</h2>
                <p>
                    Kişisel bilgileriniz, yasal zorunluluklar dışında üçüncü taraflarla paylaşılmaz.
                    Platformda paylaştığınız yorumlar ve profil bilgileri diğer kullanıcılar tarafından görülebilir.
                </p>
            </div>

            <div class="privacy-section">
                <h2><i class="fas fa-cookie-bite"></i> Çerezler</h2>
                <p>
                    Platformumuz, kullanıcı deneyimini iyileştirmek için çerezler kullanmaktadır.
                    Çerezler, oturum yönetimi ve tercihlerinizi hatırlamak için kullanılır.
                </p>
            </div>

            <div class="privacy-section">
                <h2><i class="fas fa-user-shield"></i> Haklarınız</h2>
                <p>
                    Kişisel verilerinize erişme, düzeltme veya silme hakkına sahipsiniz.
                    Bu haklarınızı kullanmak için bizimle iletişime geçebilirsiniz.
                </p>
            </div>

            <div class="privacy-section">
                <h2><i class="fas fa-envelope"></i> İletişim</h2>
                <p>
                    Gizlilik politikamız hakkında sorularınız varsa, lütfen bizimle iletişime geçin.
                </p>
            </div>

            <p class="text-muted" style="margin-top: 2rem; font-size: 0.875rem;">
                <i class="fas fa-calendar"></i> Son güncelleme:
                <?php echo date('d.m.Y'); ?>
            </p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>