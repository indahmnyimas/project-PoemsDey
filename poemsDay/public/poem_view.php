<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM poems WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$poem = $stmt->fetch();

if (!$poem) {
    http_response_code(404);
    die('Puisi tidak ditemukan.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($poem['title']) ?> - PoemsDay</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <p><a href="index.php">&larr; Kembali</a></p>

    <h1><?= htmlspecialchars($poem['title']) ?></h1>
    <div class="poem-text">

    <p><small>
        <?= htmlspecialchars($poem['status']) ?>
        <?php if ($poem['mood']): ?>· <?= htmlspecialchars($poem['mood']) ?><?php endif; ?>
        · dibuat <?= htmlspecialchars($poem['created_at']) ?>
        · diubah <?= htmlspecialchars($poem['updated_at']) ?>
    </small></p>

    <p>
        <a href="poem_edit.php?id=<?= $poem['id'] ?>">Edit</a> |
        <a href="poem_delete.php?id=<?= $poem['id'] ?>">Hapus</a>
    </p>
</body>
</html>