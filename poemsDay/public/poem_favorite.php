<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);

    // updated_at = updated_at supaya tanggal "diubah" tidak ikut berganti
    $pdo->prepare(
        'UPDATE poems SET is_favorite = 1 - is_favorite, updated_at = updated_at
         WHERE id = ? AND user_id = ?'
    )->execute([$id, $_SESSION['user_id']]);

    if (($_POST['from'] ?? '') === 'view') {
        header('Location: poem_view.php?id=' . $id);
        exit;
    }
}

header('Location: index.php');
exit;
?>