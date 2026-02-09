<?php
require_once 'includes/header.php';

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book = null;

// Kategorileri Getir
$stmt = $pdo->query("SELECT * FROM genres ORDER BY name");
$genres = $stmt->fetchAll();

// Düzenleme Modu
if ($book_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND type = 'book'");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    
    if (!$book) {
        echo "<div class='alert alert-danger'>Kitap bulunamadı!</div>";
        exit;
    }
}

// Kaydetme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $genre_id = (int)$_POST['genre_id'];
    $publication_year = (int)$_POST['publication_year'];
    $page_count = (int)$_POST['page_count'];
    $cover_image = trim($_POST['cover_image']);
    
    try {
        if ($book_id > 0) {
            // Güncelleme
            $stmt = $pdo->prepare("
                UPDATE items SET 
                title = ?, author = ?, description = ?, genre_id = ?, 
                publication_year = ?, page_count = ?, cover_image = ? 
                WHERE id = ?
            ");
            $stmt->execute([$title, $author, $description, $genre_id, $publication_year, $page_count, $cover_image, $book_id]);
            $success = "Kitap güncellendi!";
            
            // Güncel veriyi çek
            $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
            $stmt->execute([$book_id]);
            $book = $stmt->fetch();
            
        } else {
            // Yeni Ekleme
            $stmt = $pdo->prepare("
                INSERT INTO items 
                (type, title, author, description, genre_id, publication_year, page_count, cover_image, created_at)
                VALUES 
                ('book', ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$title, $author, $description, $genre_id, $publication_year, $page_count, $cover_image]);
            $book_id = $pdo->lastInsertId();
            $success = "Yeni kitap eklendi!";
            
            // Güncel veriyi çek (düzenleme moduna geç)
            $book = [
                'id' => $book_id,
                'title' => $title,
                'author' => $author,
                'description' => $description,
                'genre_id' => $genre_id,
                'publication_year' => $publication_year,
                'page_count' => $page_count,
                'cover_image' => $cover_image
            ];
        }
    } catch (PDOException $e) {
        $error = "Veritabanı hatası: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-white"><?php echo $book ? 'Kitabı Düzenle' : 'Yeni Kitap Ekle'; ?></h1>
    <a href="books.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark border-secondary">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-white">Kitap Başlığı</label>
                        <input type="text" name="title" class="form-control" required 
                               value="<?php echo htmlspecialchars($book['title'] ?? ''); ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Yazar</label>
                            <input type="text" name="author" class="form-control" required
                                   value="<?php echo htmlspecialchars($book['author'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Kategori</label>
                            <select name="genre_id" class="form-select bg-dark text-white border-secondary">
                                <?php foreach ($genres as $genre): ?>
                                <option value="<?php echo $genre['id']; ?>" 
                                    <?php echo ($book && $book['genre_id'] == $genre['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($genre['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Yayın Yılı</label>
                            <input type="number" name="publication_year" class="form-control" 
                                   value="<?php echo htmlspecialchars($book['publication_year'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Sayfa Sayısı</label>
                            <input type="number" name="page_count" class="form-control" 
                                   value="<?php echo htmlspecialchars($book['page_count'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-white">Kapak URL</label>
                        <input type="url" name="cover_image" class="form-control" 
                               value="<?php echo htmlspecialchars($book['cover_image'] ?? ''); ?>">
                        <div class="form-text text-muted">Google Books veya Open Library görsel URL'si</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-white">Açıklama</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($book['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <?php if ($book && !empty($book['cover_image'])): ?>
        <div class="card bg-dark border-secondary">
            <div class="card-header border-secondary text-white">Kapak Önizleme</div>
            <div class="card-body text-center">
                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" class="img-fluid rounded shadow-lg" style="max-height: 400px;">
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

</main>
</body>
</html>
