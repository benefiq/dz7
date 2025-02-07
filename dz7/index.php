<?php

// Определяем текущую директорию, где расположен скрипт
$directory = __DIR__;

// Получаем список всех файлов и папок в текущей директории, исключая "." и ".."
$files = array_diff(scandir($directory), ['.', '..']);

// Проверяем, была ли отправлена форма для удаления файла
if (isset($_POST['delete'])) {
    // Получаем имя файла, который нужно удалить, защищаясь от directory traversal
    $fileToDelete = basename($_POST['file']);
    $filePath = $directory . DIRECTORY_SEPARATOR . $fileToDelete;
    
    // Проверяем, является ли объект файлом и существует ли он
    if (is_file($filePath)) {
        // Удаляем файл
        if (unlink($filePath)) {
            // Перезагружаем страницу после удаления
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Выводим сообщение об ошибке
            echo "Ошибка при удалении файла!";
        }
    }
}

// Проверяем, была ли отправлена форма для переименования файла
if (isset($_POST['rename'])) {
    // Получаем старое и новое имя файла
    $oldName = basename($_POST['file']);
    $newName = basename($_POST['new_name']);
    $oldPath = $directory . DIRECTORY_SEPARATOR . $oldName;
    $newPath = $directory . DIRECTORY_SEPARATOR . $newName;
    
    // Проверяем, существует ли файл и новое имя не пустое
    if (is_file($oldPath) && !empty($newName)) {
        // Переименовываем файл
        if (rename($oldPath, $newPath)) {
            // Перезагружаем страницу после успешного переименования
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Выводим сообщение об ошибке
            echo "Ошибка при переименовании файла!";
        }
    }
}

// Проверяем, была ли отправлена форма загрузки файла
if (isset($_FILES['upload'])) {
    // Получаем данные о загруженном файле
    $uploadFile = $_FILES['upload'];
    $fileName = basename($uploadFile['name']);
    $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
    
    // Перемещаем загруженный файл в текущую директорию
    if (move_uploaded_file($uploadFile['tmp_name'], $filePath)) {
        // Перезагружаем страницу после успешной загрузки
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Выводим сообщение об ошибке
        echo "Ошибка при загрузке файла!";
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
        <?php foreach ($files as $file): ?>
            <?php 
                // Получаем имя файла
                $file = basename($file);
                // Проверяем, является ли объект файлом
                if (is_file($directory . DIRECTORY_SEPARATOR . $file)): 
            ?>
                <tr>
                    <td><?= htmlspecialchars($file) ?></td>
                    <td>
                        <!-- Форма для удаления файла -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
                            <button type="submit" name="delete">Удалить</button>
                        </form>
                        <!-- Форма для переименования файла -->
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
    
    <h2>Загрузить новый файл</h2>
    <!-- Форма для загрузки нового файла -->
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="upload" required>
        <button type="submit">Загрузить</button>
    </form>
</body>
</html>
