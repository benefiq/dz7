<?php

$directory = __DIR__;
$images = array_diff(scandir($directory), ['.', '..']);

if (isset($_POST['delete'])) {
    $fileToDelete = $_POST['file'];
    if (is_file($fileToDelete)) {
        unlink($fileToDelete);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

if (isset($_POST['rename'])) {
    $oldName = $_POST['file'];
    $newName = $_POST['new_name'];
    if (is_file($oldName) && !empty($newName)) {
        rename($oldName, $newName);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обзор папки</title>
</head>
<body>
    <h1>Содержимое папки</h1>
    <table border="1" cellpadding="10">
        <tr>
            <th>Имя файла</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($images as $file): ?>
            <?php if (is_file($file) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)): ?>
                <tr>
                    <td><?= htmlspecialchars($file) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
                            <button type="submit" name="delete">Удалить</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
                            <input type="text" name="new_name" placeholder="Новое имя" required>
                            <button type="submit" name="rename">Переименовать</button>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</body>
</html>
