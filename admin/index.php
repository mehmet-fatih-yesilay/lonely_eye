<?php
require_once 'includes/header.php';

// İstatistikleri Getir
try {
    // Toplam Kullanıcı
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $stmt->fetchColumn();

    // Toplam Kitap
    $stmt = $pdo->query("SELECT COUNT(*) FROM items WHERE type = 'book'");
    $total_books = $stmt->fetchColumn();

    // Toplam Yorum
    $stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
    $total_reviews = $stmt->fetchColumn();

    // Son 5 Kullanıcı
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
    $latest_users = $stmt->fetchAll();

    // Son 5 Yorum
    $stmt = $pdo->query("
        SELECT r.*, u.username, i.title as item_title 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        JOIN items i ON r.item_id = i.id 
        ORDER BY r.created_at DESC LIMIT 5
    ");
    $latest_reviews = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Veri hatası: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-white">Dashboard</h1>
    <span class="text-muted">Hoşgeldin,
        <?php echo htmlspecialchars($_SESSION['username']); ?>
    </span>
</div>

<!-- İstatistik Kartları -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary text-white">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>
                    <?php echo number_format($total_users); ?>
                </h3>
                <p>Toplam Kullanıcı</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-success text-white">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3>
                    <?php echo number_format($total_books); ?>
                </h3>
                <p>Toplam Kitap</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning text-white">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-info">
                <h3>
                    <?php echo number_format($total_reviews); ?>
                </h3>
                <p>Toplam Yorum</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Son Kullanıcılar -->
    <div class="col-md-6">
        <div class="card bg-dark border-secondary">
            <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Son Üyeler</h5>
                <a href="users.php" class="btn btn-sm btn-outline-primary">Tümü</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kullanıcı</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latest_users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?php echo htmlspecialchars($user['avatar']); ?>"
                                                class="avatar avatar-sm rounded-circle">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        <?php echo date('d.m.Y', strtotime($user['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Yorumlar -->
    <div class="col-md-6">
        <div class="card bg-dark border-secondary">
            <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Son Yorumlar</h5>
                <a href="reviews.php" class="btn btn-sm btn-outline-primary">Tümü</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($latest_reviews as $review): ?>
                        <div class="list-group-item bg-transparent border-secondary">
                            <div class="d-flex justify-content-between">
                                <small class="text-primary fw-bold">
                                    <?php echo htmlspecialchars($review['username']); ?>
                                </small>
                                <small class="text-muted">
                                    <?php echo date('d.m', strtotime($review['created_at'])); ?>
                                </small>
                            </div>
                            <div class="text-white small mb-1">
                                <i class="fas fa-book me-1 text-muted"></i>
                                <?php echo htmlspecialchars($review['item_title']); ?>
                            </div>
                            <p class="mb-0 text-muted small text-truncate">
                                <?php echo htmlspecialchars($review['comment']); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</main>
</body>

</html>