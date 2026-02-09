<?php
/**
 * 404 Not Found Page
 * Custom error page for missing resources
 */

$page_title = "Sayfa Bulunamadı";
// Header'ı manuel dahil edelim çünkü db.php gerektirebilir ve bu sayfa her yerden erişilebilir olmalı
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="tr" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Sayfa Bulunamadı | Lonely Eye</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/lonely_eye/assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            background: var(--bg-body);
        }

        .error-container {
            max-width: 600px;
            padding: 2rem;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: var(--primary);
            text-shadow: 0 0 30px var(--primary-glow);
            margin-bottom: 0;
            line-height: 1;
        }

        .error-icon {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }
    </style>
</head>

<body>

    <div class="error-container fade-in">
        <div class="error-icon">
            <i class="fas fa-ghost"></i>
        </div>
        <h1 class="error-code">404</h1>
        <h2 class="mb-4">Aradığınız Sayfa Bulunamadı</h2>
        <p class="text-muted mb-5">
            Üzgünüz, aradığınız sayfa silinmiş, adı değiştirilmiş veya geçici olarak kullanılamıyor olabilir.
            Belki de hiç var olmamıştır?
        </p>

        <div class="d-flex gap-3 justify-content-center">
            <a href="/lonely_eye/" class="btn btn-primary btn-lg">
                <i class="fas fa-home"></i> Ana Sayfaya Dön
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Geri Git
            </a>
        </div>
    </div>

</body>

</html>