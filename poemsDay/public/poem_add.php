<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status  = $_POST['status'] ?? 'draft';
    $mood    = trim($_POST['mood'] ?? '');

    if (!in_array($status, ['draft', 'selesai'], true)) {
        $status = 'draft';
    }

    if ($title === '' || $content === '') {
        $error = 'Judul dan isi Tulisan wajib diisi.';
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO poems (user_id, title, content, status, mood) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $content,
            $status,
            $mood !== '' ? $mood : null,
        ]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Tulisan - PoemsDay</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Buat Tulisan Baru</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <p>
            <label>Judul<br>
            <input type="text" name="title" maxlength="200" required
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"></label>
        </p>
        <p>
            <label>Isi Tulisan<br>
            <textarea name="content" rows="15" cols="50" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea></label>
        </p>
        <p>
            <label>Mood (opsional)<br>
            <input type="text" name="mood" maxlength="50" placeholder="rindu, tenang, marah..."
                   value="<?= htmlspecialchars($_POST['mood'] ?? '') ?>"></label>
        </p>
        <p>
            <label>Status<br>
            <select name="status">
                <option value="draft">Draft</option>
                <option value="selesai">Selesai</option>
            </select></label>
        </p>
        <button type="submit">Simpan dulu</button>
        <a href="index.php">Batal simpan</a>
    </form>
</body>
</html>