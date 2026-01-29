<?php
/**
 * Login Page - User Authentication
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $error = 'Lütfen email ve şifrenizi girin.';
    } else {
        try {
            // Get user by email
            $stmt = $pdo->prepare("SELECT id, username, email, password, avatar, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['avatar'] = $user['avatar'];
                $_SESSION['role'] = $user['role'];

                // Redirect to dashboard
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Email veya şifre hatalı.';
            }
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            $error = 'Bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}

$page_title = "Giriş Yap";
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

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-label {
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
                <h2>Hoş Geldiniz</h2>
                <p>Okuma yolculuğunuza devam edin</p>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email Adresi
                    </label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Şifre
                    </label>
                    <input type="password" name="password" class="form-control" placeholder="Şifrenizi girin" required>
                </div>

                <div class="remember-forgot">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">
                            Beni Hatırla
                        </label>
                    </div>
                    <a href="#" class="text-primary" style="font-size: 0.875rem;">Şifremi Unuttum?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Giriş Yap
                </button>
            </form>

            <div class="divider">
                <span>veya</span>
            </div>

            <!-- Register Link -->
            <div class="text-center">
                <p class="text-muted mb-0">
                    Henüz hesabınız yok mu?
                    <a href="register.php" class="text-primary fw-bold">Kayıt Olun</a>
                </p>
            </div>

            <!-- Demo Credentials (Remove in production) -->
            <div class="mt-4 p-3"
                style="background: rgba(56, 189, 248, 0.1); border-radius: var(--radius-md); border: 1px solid rgba(56, 189, 248, 0.2);">
                <p class="text-muted small mb-2">
                    <i class="fas fa-info-circle text-primary"></i> <strong>Demo Hesap:</strong>
                </p>
                <p class="text-muted small mb-1">Email: ahmet@example.com</p>
                <p class="text-muted small mb-0">Şifre: password123</p>
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