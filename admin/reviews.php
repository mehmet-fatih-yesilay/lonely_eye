<?php
require_once 'includes/header.php';

// İşlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_review'])) {
        $review_id = (int) $_POST['review_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            $success = "Yorum silindi.";
        } catch (PDOException $e) {
            $error = "Hata: " . $e->getMessage();
        }
    }
}

// Yorumları Listele
$stmt = $pdo->query("
    SELECT r.*, u.username, i.title as item_title 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    JOIN items i ON r.item_id = i.id 
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-white">Yorum Yönetimi</h1>
    <span class="badge bg-primary">
        <?php echo count($reviews); ?> Yorum
    </span>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success">
        <?php echo $success; ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="card bg-dark border-secondary">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kullanıcı</th>
                        <th>Kitap</th>
                        <th>Yorum</th>
                        <th>Puan</th>
                        <th>Tarih</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-user-circle text-muted"></i>
                                    <?php echo htmlspecialchars($review['username']); ?>
                                </div>
                            </td>
                            <td>
                                <a href="../item-detail.php?id=<?php echo $review['item_id']; ?>" target="_blank"
                                    class="text-decoration-none text-info">
                                    <i class="fas fa-external-link-alt small"></i>
                                    <?php echo htmlspecialchars(mb_substr($review['item_title'], 0, 20)) . '...'; ?>
                                </a>
                            </td>
                            <td>
                                <span title="<?php echo htmlspecialchars($review['comment']); ?>">
                                    <?php echo htmlspecialchars(mb_substr($review['comment'], 0, 50)) . (mb_strlen($review['comment']) > 50 ? '...' : ''); ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-warning">
                                    <?php for ($i = 0; $i < $review['rating']; $i++)
                                        echo '★'; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('d.m H:i', strtotime($review['created_at'])); ?>
                            </td>
                            <td>
                                <form method="POST" class="d-inline"
                                    onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?');">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" name="delete_review" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</main>
</body>

</html>