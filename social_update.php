<?php
/**
 * Social Update Script - Database Schema Update
 * Creates follows table for social media functionality
 */

require_once 'includes/db.php';

echo "<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Sosyal Medya Güncellemesi - Lonely Eye</title>
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
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Sosyal Medya Altyapısı Güncellemesi</h1>
        <p>Follows tablosu oluşturuluyor...</p>
";

flush();

// ============================================
// CREATE FOLLOWS TABLE
// ============================================
echo "<div class='step'>";
echo "<h3>📋 Follows Tablosu Oluşturuluyor</h3>";

try {
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'follows'");
    $table_exists = $stmt->fetch();

    if ($table_exists) {
        echo "<p class='info'>ℹ Follows tablosu zaten mevcut. Yapı kontrol ediliyor...</p>";

        // Check if table has correct structure
        $stmt = $pdo->query("DESCRIBE follows");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (in_array('id', $columns)) {
            echo "<p class='success'>✓ Tablo yapısı güncel</p>";
        } else {
            echo "<p class='info'>⚠ Tablo yeniden oluşturuluyor...</p>";
            $pdo->exec("DROP TABLE IF EXISTS follows");
            $table_exists = false;
        }
    }

    if (!$table_exists) {
        // Create follows table
        $sql = "
        CREATE TABLE IF NOT EXISTS follows (
            id INT AUTO_INCREMENT PRIMARY KEY,
            follower_id INT NOT NULL,
            following_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_follow (follower_id, following_id),
            FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_follower (follower_id),
            INDEX idx_following (following_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $pdo->exec($sql);
        echo "<p class='success'>✅ Follows tablosu başarıyla oluşturuldu!</p>";
    }

    // Add some sample follows for testing
    echo "<p class='info'>📊 Örnek takip ilişkileri ekleniyor...</p>";

    // Get all users
    $stmt = $pdo->query("SELECT id FROM users LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($users) > 1) {
        $follows_added = 0;

        // Create random follows
        for ($i = 0; $i < min(20, count($users) * 2); $i++) {
            $follower = $users[array_rand($users)];
            $following = $users[array_rand($users)];

            // Don't follow yourself
            if ($follower === $following) {
                continue;
            }

            try {
                $stmt = $pdo->prepare("INSERT IGNORE INTO follows (follower_id, following_id) VALUES (?, ?)");
                $stmt->execute([$follower, $following]);

                if ($stmt->rowCount() > 0) {
                    $follows_added++;
                }
            } catch (PDOException $e) {
                // Ignore duplicate errors
            }
        }

        echo "<p class='success'>✓ $follows_added örnek takip ilişkisi eklendi</p>";
    }

    // Show statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM follows");
    $total_follows = $stmt->fetch()['total'];

    echo "<p class='info'><strong>📈 Toplam Takip İlişkisi: $total_follows</strong></p>";

} catch (PDOException $e) {
    echo "<p class='error'>❌ Hata: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div>";

// ============================================
// SUMMARY
// ============================================
echo "<div class='step'>";
echo "<h3>🎉 Güncelleme Tamamlandı!</h3>";
echo "<p class='success' style='font-size: 18px;'><strong>Sosyal medya altyapısı başarıyla kuruldu!</strong></p>";
echo "<p class='info'>Artık kullanıcılar birbirlerini takip edebilir.</p>";
echo "<p><a href='dashboard.php' style='display: inline-block; margin-top: 20px; padding: 12px 24px; background: #38BDF8; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;'>📚 Dashboard'a Git</a></p>";
echo "</div>";

echo "</div></body></html>";
?>