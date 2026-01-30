<?php
/**
 * Settings Page - User Settings Management
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $bio = trim($_POST['bio'] ?? '');

    try {
        // Update bio
        if (!empty($bio)) {
            $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
            $stmt->execute([$bio, $user_id]);
            $success = 'Biyografi başarıyla güncellendi!';
        }

        // Update password
        if (!empty($old_password) && !empty($new_password)) {
            if (!password_verify($old_password, $user['password'])) {
                $error = 'Eski şifreniz hatalı.';
            } elseif (strlen($new_password) < 6) {
                $error = 'Yeni şifre en az 6 karakter olmalıdır.';
            } elseif ($new_password !== $confirm_password) {
                $error = 'Yeni şifreler eşleşmiyor.';
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                $success = 'Şifreniz başarıyla değiştirildi!';
            }
        }

        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

    } catch (PDOException $e) {
        error_log("Settings Update Error: " . $e->getMessage());
        $error = 'Bir hata oluştu. Lütfen tekrar deneyin.';
    }
}

$page_title = "Ayarlar";
require_once 'includes/header.php';
?>

<style>
    .settings-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .settings-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }

    .settings-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
    }

    .settings-header i {
        font-size: 2.5rem;
        color: var(--primary);
    }

    .settings-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 800;
    }

    .settings-section {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .settings-section h3 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        color: var(--text-main);
    }

    .settings-section h3 i {
        color: var(--primary);
    }
</style>

<!-- Sidebar -->
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 280px; padding: 2rem;">
    <div class="settings-container">
        <!-- Back Button -->
        <a href="dashboard.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>

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

        <!-- Settings Card -->
        <div class="settings-card fade-in">
            <div class="settings-header">
                <i class="fas fa-cog"></i>
                <h1>Ayarlar</h1>
            </div>

            <form method="POST" action="">
                <!-- Profile Settings -->
                <div class="settings-section">
                    <h3><i class="fas fa-user-edit"></i> Profil Bilgileri</h3>

                    <div class="form-group">
                        <label class="form-label">Kullanıcı Adı</label>
                        <input type="text" class="form-control"
                            value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <small class="text-muted">Kullanıcı adı değiştirilemez</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>"
                            disabled>
                        <small class="text-muted">E-posta adresi değiştirilemez</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Biyografi</label>
                        <textarea name="bio" class="form-control" rows="4"
                            placeholder="Kendiniz hakkında birkaç şey yazın..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Password Settings -->
                <div class="settings-section">
                    <h3><i class="fas fa-lock"></i> Şifre Değiştir</h3>

                    <div class="form-group">
                        <label class="form-label">Eski Şifre</label>
                        <input type="password" name="old_password" class="form-control"
                            placeholder="Mevcut şifrenizi girin">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Yeni Şifre</label>
                        <input type="password" name="new_password" class="form-control" placeholder="En az 6 karakter">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Yeni Şifre Tekrar</label>
                        <input type="password" name="confirm_password" class="form-control"
                            placeholder="Yeni şifrenizi tekrar girin">
                    </div>

                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Şifre değiştirmek istemiyorsanız bu alanları boş bırakın.
                    </small>
                </div>

                <!-- Save Button -->
                <button type="submit" name="update_settings" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
            </form>
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