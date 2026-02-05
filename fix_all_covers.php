<?php
/**
 * Fix All Broken Cover Images
 * Updates via.placeholder.com and source.unsplash.com URLs to ui-avatars
 */

require_once 'includes/db.php';

echo "<pre>\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "   BOZUK KAPAKLARI DÜZELT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$colors = ['1e40af', '7c3aed', 'be185d', 'b91c1c', 'c2410c', '15803d', '0e7490', '064e3b', '4c1d95', '831843'];

// Get all items with broken covers
$stmt = $pdo->query("
    SELECT id, title, cover_image FROM items 
    WHERE cover_image LIKE '%via.placeholder%' 
       OR cover_image LIKE '%source.unsplash%'
       OR cover_image IS NULL 
       OR cover_image = ''
");
$items = $stmt->fetchAll();

$total = count($items);
echo "📊 Düzeltilecek kapak sayısı: {$total}\n\n";

$fixed = 0;
$updateStmt = $pdo->prepare("UPDATE items SET cover_image = ? WHERE id = ?");

foreach ($items as $item) {
    // Create placeholder based on title initials
    $title = $item['title'];
    $initials = mb_substr($title, 0, 2, 'UTF-8');
    $color = $colors[array_rand($colors)];

    $newCover = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background={$color}&color=fff&size=192&bold=true";

    $updateStmt->execute([$newCover, $item['id']]);
    $fixed++;

    if ($fixed % 500 == 0) {
        echo "  ✓ {$fixed} / {$total} kapak düzeltildi...\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "   ✅ TAMAMLANDI! {$fixed} kapak düzeltildi.\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "</pre>";
