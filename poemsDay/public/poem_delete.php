<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT id, title FROM poems WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$poem = $stmt->fetch();

if (!$poem) {
    http_response_code(404);
    die('Puisi tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare('DELETE FROM poems WHERE id = ? AND user_id = ?')
        ->execute([$poem['id'], $_SESSION['user_id']]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hapus Puisi - PoemsDay</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Hapus puisi?</h1>
    <p>Kamu akan menghapus <strong><?= htmlspecialchars($poem['title']) ?></strong>. Tindakan ini tidak bisa dibatalkan.</p>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $poem['id'] ?>">
        <button type="submit">Ya, hapus</button>
        <a href="poem_view.php?id=<?= $poem['id'] ?>">Batal</a>
    </form>
</body>
</html>