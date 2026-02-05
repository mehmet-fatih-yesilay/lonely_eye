<?php
/**
 * FIX BOOK COVERS - Multiple API Sources
 * 
 * This script tries multiple sources to find book covers:
 * 1. Open Library API (most reliable, no rate limiting)
 * 2. Google Books API (as fallback)
 * 
 * For Turkish books that can't be found, generates a styled placeholder
 */

set_time_limit(0);
ini_set('memory_limit', '512M');

require_once 'includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "═══════════════════════════════════════════════════════════════\n";
echo "   KITAP KAPAKLARI DÜZELTME\n";
echo "   Open Library + Google Books API\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

/**
 * Try Open Library API first
 */
function fetchFromOpenLibrary(string $title, string $author): ?string
{
    // Search by title and author
    $query = urlencode(trim($title));
    $url = "https://openlibrary.org/search.json?title={$query}&limit=1";

    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'LonelyEyeApp/1.0 (Contact: support@lonelyeye.com)'
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);

    if (!isset($data['docs'][0]['cover_i'])) {
        return null;
    }

    $coverId = $data['docs'][0]['cover_i'];
    // Return medium size cover (M = 180px width)
    return "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg";
}

/**
 * Try Google Books API
 */
function fetchFromGoogle(string $title, string $author): ?string
{
    $query = urlencode(trim($title) . ' ' . trim($author));
    $url = "https://www.googleapis.com/books/v1/volumes?q={$query}&maxResults=1";

    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);

    if (!isset($data['items'][0]['volumeInfo']['imageLinks']['thumbnail'])) {
        return null;
    }

    $coverUrl = $data['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
    $coverUrl = str_replace('http:', 'https:', $coverUrl);
    $coverUrl = str_replace('zoom=1', 'zoom=2', $coverUrl);

    return $coverUrl;
}

/**
 * Generate a nice looking placeholder with book initials
 */
function generatePlaceholder(string $title): string
{
    // Get first letter or first two words
    $words = explode(' ', trim($title));
    $text = '';

    foreach (array_slice($words, 0, 2) as $word) {
        $text .= mb_substr($word, 0, 1);
    }

    // Use a nice color palette
    $colors = ['2563eb', '7c3aed', 'db2777', 'dc2626', 'ea580c', '16a34a', '0891b2'];
    $color = $colors[crc32($title) % count($colors)];

    return "https://ui-avatars.com/api/?name=" . urlencode($text) . "&background={$color}&color=fff&size=192&font-size=0.4&bold=true&format=png";
}

$stats = ['updated' => 0, 'openlib' => 0, 'google' => 0, 'placeholder' => 0, 'total' => 0];

try {
    // Count books needing update (unsplash URLs)
    $stmt = $pdo->query("
        SELECT COUNT(*) as total FROM items 
        WHERE type = 'book' AND cover_image LIKE '%unsplash%'
    ");
    $stats['total'] = $stmt->fetch()['total'];

    echo "📊 Güncellenmesi gereken kitap: {$stats['total']}\n\n";

    if ($stats['total'] == 0) {
        echo "✅ Tüm kitapların kapağı düzgün!\n";
        exit;
    }

    // Process in small batches
    $batchSize = 25;
    $offset = 0;
    $startTime = microtime(true);

    while (true) {
        $stmt = $pdo->prepare("
            SELECT id, title, author FROM items 
            WHERE type = 'book' AND cover_image LIKE '%unsplash%'
            ORDER BY id LIMIT ? OFFSET ?
        ");
        $stmt->execute([$batchSize, $offset]);
        $books = $stmt->fetchAll();

        if (empty($books))
            break;

        echo "───────────────────────────────────────────────────────────────\n";
        echo "Batch " . (($offset / $batchSize) + 1) . " işleniyor...\n";
        echo "───────────────────────────────────────────────────────────────\n";

        foreach ($books as $book) {
            $coverUrl = null;
            $source = '';

            // Try Open Library first
            $coverUrl = fetchFromOpenLibrary($book['title'], $book['author']);
            if ($coverUrl) {
                $source = 'OpenLib';
                $stats['openlib']++;
            }

            // Try Google if Open Library fails
            if (!$coverUrl) {
                $coverUrl = fetchFromGoogle($book['title'], $book['author']);
                if ($coverUrl) {
                    $source = 'Google';
                    $stats['google']++;
                }
            }

            // Generate placeholder as last resort
            if (!$coverUrl) {
                $coverUrl = generatePlaceholder($book['title']);
                $source = 'Avatar';
                $stats['placeholder']++;
            }

            // Update database
            $updateStmt = $pdo->prepare("UPDATE items SET cover_image = ? WHERE id = ?");
            $updateStmt->execute([$coverUrl, $book['id']]);
            $stats['updated']++;

            $shortTitle = mb_strlen($book['title']) > 30 ? mb_substr($book['title'], 0, 30) . '...' : $book['title'];
            $icon = $source === 'Avatar' ? '📝' : '✅';
            echo "{$icon} [{$book['id']}] {$shortTitle} ({$source})\n";

            // Small delay to be nice to APIs
            usleep(100000); // 100ms

            if (ob_get_level())
                ob_flush();
            flush();
        }

        $offset += $batchSize;
        $elapsed = round(microtime(true) - $startTime, 1);
        $progress = $stats['updated'];
        echo "\n⏱️ İlerleme: {$progress}/{$stats['total']} ({$elapsed}s)\n\n";
    }

    $totalTime = round(microtime(true) - $startTime, 1);

    echo "\n═══════════════════════════════════════════════════════════════\n";
    echo "   SONUÇ RAPORU\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "📚 Toplam güncellenen: {$stats['updated']}\n";
    echo "   ├─ Open Library: {$stats['openlib']}\n";
    echo "   ├─ Google Books: {$stats['google']}\n";
    echo "   └─ Avatar/Placeholder: {$stats['placeholder']}\n";
    echo "⏱️ Toplam süre: {$totalTime} saniye\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";

    echo "✅ İşlem tamamlandı! library.php sayfasını kontrol edebilirsiniz.\n";

} catch (Exception $e) {
    echo "❌ HATA: " . $e->getMessage() . "\n";
    error_log("Cover fix error: " . $e->getMessage());
}

// Cleanup temp file
if (file_exists('temp_check.php')) {
    unlink('temp_check.php');
}
?>