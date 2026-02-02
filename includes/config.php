<?php
/**
 * Configuration File
 * Central location for application constants and settings
 */

// ============================================
// DATABASE CONFIGURATION
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'lonely_eye');
define('DB_USER', 'root');
define('DB_PASS', '');

// ============================================
// APPLICATION SETTINGS
// ============================================
define('APP_NAME', 'Lonely Eye');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/lonely_eye');

// ============================================
// PAGINATION & LIMITS
// ============================================
define('BOOKS_PER_PAGE', 42);
define('MAX_SEARCH_RESULTS', 100);
define('GOOGLE_API_MAX_RESULTS', 40);

// ============================================
// FILE PATHS
// ============================================
define('DEFAULT_BOOK_IMAGE', 'assets/img/default_book.png');
define('DEFAULT_AVATAR', 'assets/img/default_avatar.png');
define('UPLOAD_DIR', 'uploads/');

// ============================================
// GOOGLE BOOKS API
// ============================================
define('GOOGLE_BOOKS_API_URL', 'https://www.googleapis.com/books/v1/volumes');
define('GOOGLE_API_TIMEOUT', 8); // seconds

// ============================================
// CATEGORY MAPPING (Turkish → English)
// ============================================
$GLOBALS['CATEGORY_MAP'] = [
    'Tümü' => '',
    'Roman' => 'Fiction',
    'Bilim Kurgu' => 'Science Fiction',
    'Fantastik' => 'Fantasy',
    'Tarih' => 'History',
    'Biyografi' => 'Biography',
    'Bilim' => 'Science',
    'Felsefe' => 'Philosophy',
    'Psikoloji' => 'Psychology',
    'Sanat' => 'Art',
    'Şiir' => 'Poetry',
    'Edebiyat' => 'Literature',
    'Polisiye' => 'Mystery',
    'Macera' => 'Adventure',
    'Romantik' => 'Romance',
    'Korku' => 'Horror',
    'Gezi' => 'Travel',
    'Çocuk' => 'Children',
    'Genç' => 'Young Adult',
    'Kişisel Gelişim' => 'Self Help'
];

// ============================================
// DEBUG MODE
// ============================================
define('DEBUG_MODE', true); // Set to false in production
define('SHOW_CONSOLE_LOGS', true); // JavaScript console.log visibility

// ============================================
// ERROR REPORTING
// ============================================
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
?>