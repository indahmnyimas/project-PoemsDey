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

$error   = '';
$title   = $poem['title'];
$content = $poem['content'];
$status  = $poem['status'];
$mood    = $poem['mood'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status  = $_POST['status'] ?? 'draft';
    $mood    = trim($_POST['mood'] ?? '');

    if (!in_array($status, ['draft', 'selesai'], true)) {
        $status = 'draft';
    }

    if ($title === '' || $content === '') {
        $error = 'Judul dan isi puisi wajib diisi.';
    } else {
        // Simpan versi lama ke riwayat revisi
        $pdo->prepare('INSERT INTO poem_versions (poem_id, title, content) VALUES (?, ?, ?)')
            ->execute([$poem['id'], $poem['title'], $poem['content']]);

        // Update puisi
        $pdo->prepare(
            'UPDATE poems SET title = ?, content = ?, status = ?, mood = ? WHERE id = ? AND user_id = ?'
        )->execute([
            $title,
            $content,
            $status,
            $mood !== '' ? $mood : null,
            $poem['id'],
            $_SESSION['user_id'],
        ]);

        header('Location: poem_view.php?id=' . $poem['id']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Puisi - PoemsDay</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Edit Puisi</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <p>
            <label>Judul<br>
            <input type="text" name="title" maxlength="200" required
                   value="<?= htmlspecialchars($title) ?>"></label>
        </p>
        <p>
            <label>Isi puisi<br>
            <textarea name="content" rows="15" cols="50" required><?= htmlspecialchars($content) ?></textarea></label>
        </p>
        <p>
            <label>Mood (opsional)<br>
            <input type="text" name="mood" maxlength="50"
                   value="<?= htmlspecialchars($mood) ?>"></label>
        </p>
        <p>
            <label>Status<br>
            <select name="status">
                <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="selesai" <?= $status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select></label>
        </p>
        <button type="submit">Simpan perubahan</button>
        <a href="poem_view.php?id=<?= $poem['id'] ?>">Batal</a>
    </form>
</body>
</html>