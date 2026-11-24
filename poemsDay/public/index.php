<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';

$q      = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$fav    = isset($_GET['fav']);

$sql = 'SELECT id, title, content, status, mood, is_favorite, created_at
        FROM poems
        WHERE user_id = ?';
$params = [$_SESSION['user_id']];

if ($q !== '') {
    $sql .= ' AND (title LIKE ? OR content LIKE ? OR mood LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like);
}

if (in_array($status, ['draft', 'selesai'], true)) {
    $sql .= ' AND status = ?';
    $params[] = $status;
} else {
    $status = '';
}

if ($fav) {
    $sql .= ' AND is_favorite = 1';
}

$sql .= ' ORDER BY is_favorite DESC, created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$poems = $stmt->fetchAll();

$filtering = ($q !== '' || $status !== '' || $fav);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PoemsDay</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Puisi-puisi <?= htmlspecialchars($_SESSION['username']) ?></h1>
    <p>
        <a href="poem_add.php">+ Tulis puisi baru</a> |
        <a href="logout.php">Logout</a>
    </p>

    <form method="GET" class="search-form">
        <input type="text" name="q" placeholder="Cari judul, isi, atau mood..."
               value="<?= htmlspecialchars($q) ?>">
        <select name="status">
            <option value="">Semua status</option>
            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="selesai" <?= $status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>
        <label class="check">
            <input type="checkbox" name="fav" value="1" <?= $fav ? 'checked' : '' ?>> Favorit saja
        </label>
        <button type="submit">Cari</button>
        <?php if ($filtering): ?>
            <a href="index.php">Reset</a>
        <?php endif; ?>
    </form>

    <p><small><?= count($poems) ?> puisi<?= $filtering ? ' ditemukan' : '' ?></small></p>

    <?php if (count($poems) === 0): ?>
        <p><?= $filtering ? 'Tidak ada puisi yang cocok.' : 'Belum ada puisi. Yuk tulis yang pertama!' ?></p>
    <?php endif; ?>

    <?php foreach ($poems as $poem): ?>
        <div class="poem-card">
            <div class="card-head">
                <h3><a href="poem_view.php?id=<?= $poem['id'] ?>"><?= htmlspecialchars($poem['title']) ?></a></h3>
                <form method="POST" action="poem_favorite.php">
                    <input type="hidden" name="id" value="<?= $poem['id'] ?>">
                    <button type="submit" class="fav-btn" title="Favorit">
                        <?= $poem['is_favorite'] ? '★' : '☆' ?>
                    </button>
                </form>
            </div>
            <p class="poem-excerpt"><?= htmlspecialchars(mb_strimwidth($poem['content'], 0, 150, '...')) ?></p>
            <small>
                <?= htmlspecialchars($poem['status']) ?>
                <?php if ($poem['mood']): ?>
                    · <?= htmlspecialchars($poem['mood']) ?>
                <?php endif; ?>
                · <?= htmlspecialchars($poem['created_at']) ?>
            </small>
        </div>
    <?php endforeach; ?>
</body>
</html>