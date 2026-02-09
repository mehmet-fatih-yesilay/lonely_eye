<?php
/**
 * Setup Real Data - Replace Fake Data with Google Books API
 * This script cleans the database and populates it with real books
 */

require_once 'includes/db.php';

// Set execution time limit (API calls may take time)
set_time_limit(300);

echo "<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Veritabanı Kurulumu - Lonely Eye</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #0F172A;
            color: #F1F5F9;
        }
        .container {
            background: #1E293B;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        h1 {
            color: #38BDF8;
            margin-bottom: 10px;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: rgba(56, 189, 248, 0.1);
            border-left: 4px solid #38BDF8;
            border-radius: 8px;
        }
        .success {
            color: #4ADE80;
        }
        .error {
            color: #F87171;
        }
        .info {
            color: #38BDF8;
        }
        .progress {
            margin: 10px 0;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            font-size: 14px;
        }
        .book-item {
            margin: 5px 0;
            padding: 8px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 4px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Lonely Eye - Veritabanı Kurulumu</h1>
        <p>Google Books API ile gerçek kitap verisi yükleniyor...</p>
";

flush();

// ============================================
// STEP 1: CLEAN DATABASE
// ============================================
echo "<div class='step'>";
echo "<h3>📋 Adım 1: Veritabanı Temizleniyor</h3>";

try {
    // Disable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "<p class='info'>✓ Foreign key kontrolü devre dışı bırakıldı</p>";

    // Truncate tables
    $pdo->exec("TRUNCATE TABLE reviews");
    echo "<p class='success'>✓ Reviews tablosu temizlendi</p>";

    $pdo->exec("TRUNCATE TABLE user_interests");
    echo "<p class='success'>✓ User interests tablosu temizlendi</p>";

    $pdo->exec("TRUNCATE TABLE items");
    echo "<p class='success'>✓ Items tablosu temizlendi</p>";

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p class='info'>✓ Foreign key kontrolü tekrar aktif edildi</p>";

    echo "<p class='success'><strong>✅ Veritabanı başarıyla temizlendi!</strong></p>";

} catch (PDOException $e) {
    echo "<p class='error'>❌ Hata: " . htmlspecialchars($e->getMessage()) . "</p>";
    die("</div></div></body></html>");
}

echo "</div>";
flush();

// ============================================
// STEP 2: FETCH GENRES FROM DATABASE
// ============================================
echo "<div class='step'>";
echo "<h3>📚 Adım 2: Kategoriler Hazırlanıyor</h3>";

$genre_map = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM genres");
    $genres = $stmt->fetchAll();

    foreach ($genres as $genre) {
        $genre_map[strtolower($genre['name'])] = $genre['id'];
    }

    echo "<p class='success'>✓ " . count($genres) . " kategori yüklendi</p>";
} catch (PDOException $e) {
    echo "<p class='error'>❌ Hata: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div>";
flush();

// ============================================
// STEP 3: GOOGLE BOOKS API INTEGRATION
// ============================================
echo "<div class='step'>";
echo "<h3>🌐 Adım 3: Google Books API'den Veri Çekiliyor</h3>";

// Categories to fetch
$categories = [
    'Tarih' => 'history',
    'Bilim Kurgu' => 'science fiction',
    'Felsefe' => 'philosophy',
    'Psikoloji' => 'psychology',
    'Edebiyat' => 'literature classics',
    'Ekonomi' => 'economics'
];

$total_books_added = 0;
$default_genre_id = 1; // Default to first genre if not found

foreach ($categories as $turkish_name => $english_query) {
    echo "<div class='progress'>";
    echo "<strong>📖 Kategori: $turkish_name</strong><br>";

    // Find matching genre ID
    $genre_id = $default_genre_id;
    foreach ($genre_map as $genre_name => $id) {
        if (
            stripos($genre_name, strtolower($turkish_name)) !== false ||
            stripos(strtolower($turkish_name), $genre_name) !== false
        ) {
            $genre_id = $id;
            break;
        }
    }

    // Fetch from Google Books API
    $url = "https://www.googleapis.com/books/v1/volumes?q=subject:" . urlencode($english_query) .
        "&orderBy=relevance&maxResults=20&langRestrict=tr&printType=books";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200 || !$response) {
        echo "<p class='error'>⚠ API hatası (HTTP $http_code)</p>";
        echo "</div>";
        continue;
    }

    $data = json_decode($response, true);

    if (!isset($data['items']) || empty($data['items'])) {
        echo "<p class='error'>⚠ Bu kategori için kitap bulunamadı</p>";
        echo "</div>";
        continue;
    }

    $books_in_category = 0;

    foreach ($data['items'] as $item) {
        $volumeInfo = $item['volumeInfo'] ?? [];

        // Extract data
        $title = $volumeInfo['title'] ?? 'Başlıksız Kitap';
        $authors = isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Bilinmeyen Yazar';
        $description = $volumeInfo['description'] ?? 'Bu kitap hakkında henüz bir açıklama eklenmemiş.';

        // Limit description to 500 characters
        if (strlen($description) > 500) {
            $description = substr($description, 0, 497) . '...';
        }

        // Get cover image
        $cover_image = $volumeInfo['imageLinks']['thumbnail'] ??
            $volumeInfo['imageLinks']['smallThumbnail'] ??
            'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=450&fit=crop';

        // Replace http with https
        $cover_image = str_replace('http://', 'https://', $cover_image);

        $page_count = $volumeInfo['pageCount'] ?? rand(150, 400);

        // Extract publication year
        $publication_year = 2020;
        if (isset($volumeInfo['publishedDate'])) {
            $year = substr($volumeInfo['publishedDate'], 0, 4);
            if (is_numeric($year)) {
                $publication_year = (int) $year;
            }
        }

        // Insert into database
        try {
            $stmt = $pdo->prepare("
                INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, view_count, rating_score, created_at)
                VALUES ('book', ?, ?, ?, ?, ?, ?, ?, 0, ?, NOW())
            ");

            $rating = rand(35, 50) / 10; // Random rating between 3.5 and 5.0

            $stmt->execute([
                $title,
                $authors,
                $description,
                $cover_image,
                $genre_id,
                $publication_year,
                $page_count,
                $rating
            ]);

            $books_in_category++;
            $total_books_added++;

            echo "<div class='book-item'>✓ " . htmlspecialchars($title) . " - " . htmlspecialchars($authors) . "</div>";

        } catch (PDOException $e) {
            echo "<div class='book-item error'>✗ Hata: " . htmlspecialchars($title) . "</div>";
        }
    }

    echo "<p class='success'>✅ $books_in_category kitap eklendi</p>";
    echo "</div>";
    flush();

    // Small delay to avoid API rate limiting
    sleep(1);
}

echo "</div>";
flush();

// ============================================
// STEP 4: SUMMARY
// ============================================
echo "<div class='step'>";
echo "<h3>🎉 Tamamlandı!</h3>";
echo "<p class='success' style='font-size: 18px;'><strong>Veritabanı temizlendi. Google'dan $total_books_added adet gerçek kitap başarıyla eklendi.</strong></p>";
echo "<p class='info'>Artık dashboard'a gidip gerçek kitapları görebilirsiniz!</p>";
echo "<p><a href='dashboard.php' style='display: inline-block; margin-top: 20px; padding: 12px 24px; background: #38BDF8; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;'>📚 Dashboard'a Git</a></p>";
echo "</div>";

echo "</div></body></html>";
?>