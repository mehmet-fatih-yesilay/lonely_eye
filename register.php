<?php
/**
 * Register Page - User Registration
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir email adresi girin.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } elseif ($password !== $confirm_password) {
        $error = 'Şifreler eşleşmiyor.';
    } else {
        try {
            // Check if username exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Bu kullanıcı adı zaten kullanılıyor.';
            } else {
                // Check if email exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Bu email adresi zaten kayıtlı.';
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Generate avatar URL
                    $avatar_url = 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=38BDF8&color=fff&size=200';

                    // Insert user
                    $stmt = $pdo->prepare("
                        INSERT INTO users (username, email, password, avatar, role, created_at) 
                        VALUES (?, ?, ?, ?, 'user', NOW())
                    ");
                    $stmt->execute([$username, $email, $hashed_password, $avatar_url]);

                    $success = 'Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...';

                    // Redirect after 2 seconds
                    header("refresh:2;url=login.php");
                }
            }
        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            $error = 'Bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}

$page_title = "Kayıt Ol";
?>
<!DOCTYPE html>
<html lang="tr" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lonely Eye - Premium Kitap ve Dergi Sosyal Ağı">
    <title>
        <?php echo $page_title; ?> - Lonely Eye
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

    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, var(--bg-body) 0%, #1a2332 100%);
        }

        .auth-card {
            width: 100%;
            max-width: 480px;
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            padding: 3rem 2.5rem;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo i {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 1rem;
            display: inline-block;
            animation: pulse 2s ease-in-out infinite;
        }

        .auth-logo h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .auth-logo p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin: 0;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider span {
            padding: 0 1rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="auth-card fade-in">
            <!-- Logo & Branding -->
            <div class="auth-logo">
                <i class="fas fa-eye"></i>
                <h2>Lonely Eye</h2>
                <p>Kitap tutkunlarının buluşma noktası</p>
            </div>

            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Kullanıcı Adı
                    </label>
                    <input type="text" name="username" class="form-control" placeholder="Kullanıcı adınızı girin"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email Adresi
                    </label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Şifre
                    </label>
                    <input type="password" name="password" class="form-control" placeholder="En az 6 karakter" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Şifre Tekrar
                    </label>
                    <input type="password" name="confirm_password" class="form-control"
                        placeholder="Şifrenizi tekrar girin" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">
                    <i class="fas fa-user-plus"></i> Kayıt Ol
                </button>
            </form>

            <div class="divider">
                <span>veya</span>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-muted mb-0">
                    Zaten hesabınız var mı?
                    <a href="login.php" class="text-primary fw-bold">Giriş Yapın</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const htmlElement = document.documentElement;
            const savedTheme = localStorage.getItem('theme') || 'dark';
            htmlElement.setAttribute('data-theme', savedTheme);
        });
    </script>

</body>

</html>