<?php
/**
 * Profile Page - User Profile Management
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user ID from URL or use logged-in user's ID
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : $_SESSION['user_id'];
$is_own_profile = ($user_id === $_SESSION['user_id']);

$success = '';
$error = '';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: dashboard.php');
    exit;
}

// Fetch user statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as review_count FROM reviews WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

// Fetch follower count
$stmt = $pdo->prepare("SELECT COUNT(*) as follower_count FROM follows WHERE following_id = ?");
$stmt->execute([$user_id]);
$follower_stats = $stmt->fetch();

// Fetch following count
$stmt = $pdo->prepare("SELECT COUNT(*) as following_count FROM follows WHERE follower_id = ?");
$stmt->execute([$user_id]);
$following_stats = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $avatar = trim($_POST['avatar'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        // Update avatar and bio
        if (!empty($avatar) || !empty($bio)) {
            $update_avatar = !empty($avatar) ? $avatar : $user['avatar'];
            $update_bio = !empty($bio) ? $bio : $user['bio'];

            $stmt = $pdo->prepare("UPDATE users SET avatar = ?, bio = ? WHERE id = ?");
            $stmt->execute([$update_avatar, $update_bio, $user_id]);

            // Update session
            $_SESSION['avatar'] = $update_avatar;

            $success = 'Profil bilgileriniz başarıyla güncellendi!';

            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
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

    } catch (PDOException $e) {
        error_log("Profile Update Error: " . $e->getMessage());
        $error = 'Bir hata oluştu. Lütfen tekrar deneyin.';
    }
}

$page_title = "Profilim";
require_once 'includes/header.php';
?>

<style>
    /* Sidebar Styles */
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

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: rgba(56, 189, 248, 0.1);
        color: var(--primary);
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

    /* Profile Header */
    .profile-header {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 3rem;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: var(--shadow-lg);
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 4px solid var(--primary);
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 30px rgba(56, 189, 248, 0.3);
    }

    .profile-header h2 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        color: var(--text-main);
    }

    .profile-header .member-since {
        color: var(--text-muted);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .profile-header .role-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: rgba(56, 189, 248, 0.1);
        border: 1px solid var(--primary);
        border-radius: var(--radius-lg);
        color: var(--primary);
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Tabs */
    .profile-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--border-color);
    }

    .tab-button {
        padding: 1rem 2rem;
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
        position: relative;
        bottom: -2px;
    }

    .tab-button:hover {
        color: var(--primary);
    }

    .tab-button.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    /* About Section */
    .about-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        box-shadow: var(--shadow-lg);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .stat-card i {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }

    .stat-card h3 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        color: var(--text-main);
    }

    .stat-card p {
        color: var(--text-muted);
        margin: 0;
    }

    .bio-section {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
    }

    .bio-section h4 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--text-main);
    }

    .bio-section p {
        color: var(--text-muted);
        line-height: 1.8;
    }

    /* Settings Section */
    .settings-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        box-shadow: var(--shadow-lg);
    }

    .settings-group {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .settings-group h4 {
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .settings-group h4 i {
        color: var(--primary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .profile-header {
            padding: 2rem 1rem;
        }

        .profile-tabs {
            overflow-x: auto;
        }

        .tab-button {
            white-space: nowrap;
        }
    }
</style>

<!-- Sidebar -->
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
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

    <!-- Profile Header -->
    <div class="profile-header fade-in">
        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="profile-avatar">
        <h2>
            <?php echo htmlspecialchars($user['username']); ?>
        </h2>
        <p class="member-since">
            <i class="fas fa-calendar"></i>
            Üye olma tarihi:
            <?php echo date('d.m.Y', strtotime($user['created_at'])); ?>
        </p>
        <span class="role-badge">
            <i class="fas fa-<?php echo $user['role'] === 'admin' ? 'crown' : 'user'; ?>"></i>
            <?php echo $user['role'] === 'admin' ? 'Yönetici' : 'Okuyucu'; ?>
        </span>
    </div>

    <!-- Tabs -->
    <div class="profile-tabs">
        <button class="tab-button active" data-tab="about">
            <i class="fas fa-info-circle"></i> Hakkımda
        </button>
        <button class="tab-button" data-tab="settings">
            <i class="fas fa-cog"></i> Ayarlar
        </button>
    </div>

    <!-- About Tab -->
    <div class="tab-content active" id="about">
        <div class="about-section">
            <h3 class="mb-4"><i class="fas fa-chart-line"></i> İstatistikler</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-comments"></i>
                    <h3>
                        <?php echo number_format($stats['review_count']); ?>
                    </h3>
                    <p>Toplam Yorum</p>
                </div>
                <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='#followers'"
                    title="Takipçileri Görüntüle">
                    <i class="fas fa-users"></i>
                    <h3><?php echo number_format($follower_stats['follower_count']); ?></h3>
                    <p>Takipçi</p>
                </div>
                <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='#following'"
                    title="Takip Edilenleri Görüntüle">
                    <i class="fas fa-user-friends"></i>
                    <h3><?php echo number_format($following_stats['following_count']); ?></h3>
                    <p>Takip Edilen</p>
                </div>
            </div>

            <div class="bio-section">
                <h4><i class="fas fa-user-circle"></i> Biyografi</h4>
                <p>
                    <?php echo $user['bio'] ? nl2br(htmlspecialchars($user['bio'])) : 'Henüz bir biyografi eklenmemiş.'; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Settings Tab -->
    <div class="tab-content" id="settings">
        <div class="settings-section">
            <form method="POST" action="">
                <!-- Profile Settings -->
                <div class="settings-group">
                    <h4><i class="fas fa-user-edit"></i> Profil Bilgileri</h4>

                    <div class="form-group">
                        <label class="form-label">Avatar URL</label>
                        <input type="url" name="avatar" class="form-control"
                            value="<?php echo htmlspecialchars($user['avatar']); ?>"
                            placeholder="https://example.com/avatar.jpg">
                        <small class="text-muted">Profil resminizin URL adresini girin</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Biyografi</label>
                        <textarea name="bio" class="form-control" rows="4"
                            placeholder="Kendiniz hakkında birkaç şey yazın..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Password Settings -->
                <div class="settings-group">
                    <h4><i class="fas fa-lock"></i> Şifre Değiştir</h4>

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
                <button type="submit" name="update_profile" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Theme Toggle
    document.addEventListener('DOMContentLoaded', function () {
        const themeToggle = document.querySelector('.theme-toggle');
        const themeIcon = document.querySelector('.theme-icon');
        const htmlElement = document.documentElement;

        const savedTheme = localStorage.getItem('theme') || 'dark';
        htmlElement.setAttribute('data-theme', savedTheme);
        themeIcon.className = savedTheme === 'dark' ? 'fas fa-moon theme-icon' : 'fas fa-sun theme-icon';

        if (themeToggle) {
            themeToggle.addEventListener('click', function () {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                themeIcon.className = newTheme === 'dark' ? 'fas fa-moon theme-icon' : 'fas fa-sun theme-icon';
            });
        }

        // Tab Switching
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                const tabName = this.getAttribute('data-tab');

                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button and corresponding content
                this.classList.add('active');
                document.getElementById(tabName).classList.add('active');
            });
        });

        // Auto-hide alerts after 5 seconds
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