<?php
require_once 'includes/header.php';

// İşlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_book'])) {
        $book_id = (int) $_POST['book_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
            $stmt->execute([$book_id]);
            $success = "Kitap başarıyla silindi.";
        } catch (PDOException $e) {
            $error = "Hata: " . $e->getMessage();
        }
    }
}

// Sayfalama
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Toplam Kitap Sayısı
$stmt = $pdo->query("SELECT COUNT(*) FROM items WHERE type = 'book'");
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);

// Kitapları Listele
$stmt = $pdo->prepare("
    SELECT i.*, g.name as genre_name 
    FROM items i 
    LEFT JOIN genres g ON i.genre_id = g.id 
    WHERE i.type = 'book' 
    ORDER BY i.id DESC 
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-white">Kitap Yönetimi</h1>
    <a href="book_form.php" class="btn btn-primary"><i class="fas fa-plus"></i> Yeni Kitap Ekle</a>
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
                        <th>ID</th>
                        <th>Kapak</th>
                        <th>Başlık</th>
                        <th>Yazar</th>
                        <th>Kategori</th>
                        <th>Yıl</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td>#
                                <?php echo $book['id']; ?>
                            </td>
                            <td>
                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" class="rounded"
                                    style="width: 40px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <?php echo htmlspecialchars(mb_substr($book['title'], 0, 30)) . (mb_strlen($book['title']) > 30 ? '...' : ''); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($book['author']); ?>
                            </td>
                            <td><span class="badge bg-secondary">
                                    <?php echo htmlspecialchars($book['genre_name']); ?>
                                </span></td>
                            <td>
                                <?php echo htmlspecialchars($book['publication_year']); ?>
                            </td>
                            <td>
                                <a href="book_form.php?id=<?php echo $book['id']; ?>"
                                    class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                <form method="POST" class="d-inline"
                                    onsubmit="return confirm('Bu kitabı silmek istediğinize emin misiniz?');">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" name="delete_book" class="btn btn-sm btn-danger">
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

    <!-- Sayfalama -->
    <div class="card-footer border-secondary">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link bg-dark text-white border-secondary"
                            href="?page=<?php echo $page - 1; ?>">Önceki</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link bg-dark text-white border-secondary" href="?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link bg-dark text-white border-secondary"
                            href="?page=<?php echo $page + 1; ?>">Sonraki</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

</main>
</body>

</html>