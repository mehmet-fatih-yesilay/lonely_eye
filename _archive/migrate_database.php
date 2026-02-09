<?php
/**
 * Database Migration Script
 * Adds comment_replies table to existing database
 */

require_once 'includes/db.php';

echo "🔄 Starting database migration...\n\n";

try {
    // Check if comment_replies table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'comment_replies'");
    $tableExists = $stmt->rowCount() > 0;

    if ($tableExists) {
        echo "✅ comment_replies table already exists\n\n";
    } else {
        echo "📝 Creating comment_replies table...\n";

        $sql = "
        CREATE TABLE comment_replies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            review_id INT NOT NULL,
            parent_reply_id INT NULL,
            user_id INT NOT NULL,
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_reply_id) REFERENCES comment_replies(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_review_id (review_id),
            INDEX idx_parent_reply_id (parent_reply_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $pdo->exec($sql);
        echo "✅ comment_replies table created successfully!\n\n";
    }

    // Check if favorites table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'favorites'");
    $favTableExists = $stmt->rowCount() > 0;

    if ($favTableExists) {
        echo "✅ favorites table already exists\n\n";
    } else {
        echo "📝 Creating favorites table...\n";

        $sql = "
        CREATE TABLE IF NOT EXISTS favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            item_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_favorite (user_id, item_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $pdo->exec($sql);
        echo "✅ favorites table created successfully!\n\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎉 MIGRATION COMPLETE!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "✅ All tables are ready\n";
    echo "✅ You can now use the reply system\n";
    echo "✅ Favorites system is active\n\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>