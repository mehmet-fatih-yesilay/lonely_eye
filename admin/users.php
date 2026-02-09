<?php
require_once 'includes/header.php';

// İşlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = (int) $_POST['user_id'];

        // Kendini silemez
        if ($user_id == $_SESSION['user_id']) {
            $error = "Kendinizi silemezsiniz!";
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $success = "Kullanıcı başarıyla silindi.";
            } catch (PDOException $e) {
                $error = "Hata: " . $e->getMessage();
            }
        }
    }

    if (isset($_POST['change_role'])) {
        $user_id = (int) $_POST['user_id'];
        $new_role = $_POST['role'];

        if ($user_id == $_SESSION['user_id']) {
            $error = "Kendi rolünüzü değiştiremezsiniz!";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->execute([$new_role, $user_id]);
                $success = "Kullanıcı rolü güncellendi.";
            } catch (PDOException $e) {
                $error = "Hata: " . $e->getMessage();
            }
        }
    }
}

// Kullanıcıları Listele
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-white">Kullanıcı Yönetimi</h1>
    <span class="badge bg-primary">
        <?php echo count($users); ?> Kullanıcı
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
                        <th>ID</th>
                        <th>Kullanıcı</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>#
                                <?php echo $user['id']; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>"
                                        class="avatar avatar-sm rounded-circle">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </div>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="change_role" value="1">
                                    <select name="role"
                                        class="form-select form-select-sm bg-dark text-white border-secondary"
                                        onchange="this.form.submit()" style="width: 100px;">
                                        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User
                                        </option>
                                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin
                                        </option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?>
                            </td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" class="d-inline"
                                        onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
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