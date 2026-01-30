<?php
/**
 * Contact Page
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

$page_title = "İletişim";
$success = '';
$error = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi girin.';
    } else {
        // In a real application, you would send an email or save to database
        $success = 'Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağız.';
    }
}

require_once 'includes/header.php';
?>

<style>
    .contact-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .contact-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        box-shadow: var(--shadow-lg);
    }

    .contact-card h1 {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
    }

    .contact-card h1 i {
        color: var(--primary);
        font-size: 2.5rem;
    }

    .contact-info {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .contact-info i {
        font-size: 2rem;
        color: var(--primary);
    }

    .contact-info-text h3 {
        font-size: 1.125rem;
        margin-bottom: 0.25rem;
    }

    .contact-info-text p {
        margin: 0;
        color: var(--text-muted);
    }

    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Sidebar -->
<?php if (isset($_SESSION['user_id'])): ?>
    <?php include 'includes/sidebar.php'; ?>
<?php endif; ?>

<!-- Main Content -->
<div class="main-content"
    style="<?php echo isset($_SESSION['user_id']) ? 'margin-left: 280px;' : ''; ?> padding: 2rem;">
    <div class="contact-container">
        <!-- Success/Error Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success mb-3 fade-in">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger mb-3 fade-in">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-card fade-in">
                <h1>
                    <i class="fas fa-envelope"></i>
                    Bize Ulaşın
                </h1>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Adınız</label>
                        <input type="text" name="name" class="form-control" placeholder="Adınız Soyadınız" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">E-posta</label>
                        <input type="email" name="email" class="form-control" placeholder="ornek@email.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konu</label>
                        <input type="text" name="subject" class="form-control" placeholder="Mesajınızın konusu"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mesajınız</label>
                        <textarea name="message" class="form-control" rows="6" placeholder="Mesajınızı buraya yazın..."
                            required></textarea>
                    </div>

                    <button type="submit" name="send_message" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Gönder
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="contact-card fade-in">
                <h1>
                    <i class="fas fa-info-circle"></i>
                    İletişim Bilgileri
                </h1>

                <div class="contact-info">
                    <i class="fas fa-envelope"></i>
                    <div class="contact-info-text">
                        <h3>E-posta</h3>
                        <p>info@lonelyeye.com</p>
                    </div>
                </div>

                <div class="contact-info">
                    <i class="fas fa-phone"></i>
                    <div class="contact-info-text">
                        <h3>Telefon</h3>
                        <p>+90 (555) 123 45 67</p>
                    </div>
                </div>

                <div class="contact-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="contact-info-text">
                        <h3>Adres</h3>
                        <p>İstanbul, Türkiye</p>
                    </div>
                </div>

                <div class="contact-info">
                    <i class="fas fa-clock"></i>
                    <div class="contact-info-text">
                        <h3>Çalışma Saatleri</h3>
                        <p>Pazartesi - Cuma: 09:00 - 18:00</p>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 1rem;">Sosyal Medya</h3>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>