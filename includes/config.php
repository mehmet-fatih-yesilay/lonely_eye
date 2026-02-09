<?php
// Veritabanı bağlantı bilgilerini içeren dosya

$host = 'localhost';
$db = 'lonely_eye';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Global Ayarlar
define('SITE_URL', 'http://localhost/lonely_eye');
define('SITE_NAME', 'Lonely Eye');

// Rol Tanımları
define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');

// Sayfalama Limitleri
define('BOOKS_PER_PAGE', 42);
define('REVIEWS_PER_PAGE', 10);

// Dosya Yolları
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('AVATAR_DIR', UPLOAD_DIR . 'avatars/');

// Kategori Haritalaması (ID -> İsim)
$genre_map = [
    1 => 'Türk Edebiyatı',
    2 => 'Dünya Klasikleri',
    3 => 'Tarih',
    4 => 'Bilim',
    5 => 'Felsefe',
    6 => 'Psikoloji',
    7 => 'Şiir',
    8 => 'Din & Mitoloji',
    9 => 'Sanat & Tasarım',
    10 => 'Kişisel Gelişim',
    11 => 'Fantastik & Bilim Kurgu',
    12 => 'Polisiye & Gerilim',
    13 => 'Biyografi',
    14 => 'Gezi & Seyehat',
    15 => 'Çocuk & Gençlik'
];
?>