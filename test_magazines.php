<?php
require_once 'includes/db.php';

echo "=== DERGİ TESTİ ===\n";

// Test 1: Dergi sayısı
$stmt = $pdo->query("SELECT COUNT(*) as cnt FROM items WHERE type='magazine'");
$result = $stmt->fetch();
echo "Veritabanındaki dergi sayısı: " . $result['cnt'] . "\n\n";

// Test 2: Örnek dergiler
$stmt = $pdo->query("SELECT id, title, author, cover_image FROM items WHERE type='magazine' LIMIT 3");
echo "Örnek Dergiler:\n";
foreach ($stmt->fetchAll() as $row) {
    echo "  - " . $row['title'] . "\n";
    echo "    Kapak: " . substr($row['cover_image'], 0, 70) . "...\n";
}

// Test 3: Bozuk kapak kontrolü
$stmt = $pdo->query("SELECT COUNT(*) as cnt FROM items WHERE cover_image LIKE '%via.placeholder%'");
$result = $stmt->fetch();
echo "\nBozuk kapak (via.placeholder): " . $result['cnt'] . "\n";

$stmt = $pdo->query("SELECT COUNT(*) as cnt FROM items WHERE cover_image LIKE '%source.unsplash%'");
$result = $stmt->fetch();
echo "Bozuk kapak (unsplash): " . $result['cnt'] . "\n";

$stmt = $pdo->query("SELECT COUNT(*) as cnt FROM items WHERE cover_image LIKE '%ui-avatars%'");
$result = $stmt->fetch();
echo "ui-avatars kapak: " . $result['cnt'] . "\n";

echo "\n=== TEST TAMAMLANDI ===\n";
