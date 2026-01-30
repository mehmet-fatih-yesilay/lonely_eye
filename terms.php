<?php
/**
 * Terms of Service Page
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

$page_title = "Kullanım Koşulları";
require_once 'includes/header.php';
?>

<style>
    .terms-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .terms-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 3rem;
        box-shadow: var(--shadow-lg);
    }

    .terms-card h1 {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
    }

    .terms-card h1 i {
        color: var(--primary);
        font-size: 2.5rem;
    }

    .terms-section {
        margin-bottom: 2.5rem;
    }

    .terms-section h2 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-main);
        margin-bottom: 1rem;
    }

    .terms-section h2 i {
        color: var(--primary);
    }

    .terms-section p {
        line-height: 1.8;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }

    .terms-section ul {
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
    <div class="terms-container">
        <div class="terms-card fade-in">
            <h1>
                <i class="fas fa-file-contract"></i>
                Kullanım Koşulları
            </h1>

            <div class="terms-section">
                <h2><i class="fas fa-handshake"></i> Kabul ve Onay</h2>
                <p>
                    Lonely Eye platformunu kullanarak, bu kullanım koşullarını kabul etmiş sayılırsınız.
                    Koşulları kabul etmiyorsanız, lütfen platformu kullanmayınız.
                </p>
            </div>

            <div class="terms-section">
                <h2><i class="fas fa-user-check"></i> Kullanıcı Sorumlulukları</h2>
                <p>Platform kullanıcıları olarak aşağıdaki kurallara uymayı kabul edersiniz:</p>
                <ul>
                    <li>Doğru ve güncel bilgiler sağlamak</li>
                    <li>Hesap güvenliğinizi korumak</li>
                    <li>Yasalara ve topluluk kurallarına uymak</li>
                    <li>Diğer kullanıcılara saygılı davranmak</li>
                    <li>Telif haklarına saygı göstermek</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2><i class="fas fa-ban"></i> Yasak Davranışlar</h2>
                <p>Aşağıdaki davranışlar kesinlikle yasaktır:</p>
                <ul>
                    <li>Spam veya istenmeyen içerik paylaşmak</li>
                    <li>Taciz, hakaret veya tehdit içeren mesajlar göndermek</li>
                    <li>Başkalarının hesaplarını kullanmak</li>
                    <li>Platform güvenliğini tehdit etmek</li>
                    <li>Yanıltıcı veya sahte bilgiler paylaşmak</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2><i class="fas fa-copyright"></i> Fikri Mülkiyet</h2>
                <p>
                    Platformdaki tüm içerik, tasarım ve kodlar Lonely Eye'ın mülkiyetindedir.
                    Kullanıcılar tarafından oluşturulan içerik, kullanıcıların sorumluluğundadır.
                </p>
            </div>

            <div class="terms-section">
                <h2><i class="fas fa-exclamation-triangle"></i> Sorumluluk Reddi</h2>
                <p>
                    Lonely Eye, platformda paylaşılan içeriklerin doğruluğunu garanti etmez.
                    Kullanıcılar, platformu kendi sorumluluklarında kullanırlar.
                </p>
            </div>

            <div class="terms-section">
                <h2><i class="fas fa-user-times"></i> Hesap Askıya Alma</h2>
                <p>
                    Kullanım koşullarını ihlal eden hesaplar, uyarı verilmeksizin askıya alınabilir veya silinebilir.
                </p>
            </div>

            <div class="terms-section">
                <h2><i class="fas fa-edit"></i> Değişiklikler</h2>
                <p>
                    Lonely Eye, kullanım koşullarını istediği zaman değiştirme hakkını saklı tutar.
                    Değişiklikler, platformda yayınlandığı anda yürürlüğe girer.
                </p>
            </div>

            <div class="terms-section">
                <h2><i class="fas fa-envelope"></i> İletişim</h2>
                <p>
                    Kullanım koşulları hakkında sorularınız varsa, lütfen bizimle iletişime geçin.
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