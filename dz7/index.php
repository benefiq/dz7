<?php

// Определяем текущую директорию, где расположен скрипт
$directory = __DIR__;

// Получаем список файлов в текущей директории, исключая "." и ".."
$files = array_diff(scandir($directory), ['.', '..']);

// Проверяем, была ли отправлена форма для удаления файла
if (isset($_POST['delete'])) {
    // Получаем имя файла, который нужно удалить, используя basename для защиты от directory traversal
    $fileToDelete = basename($_POST['file']);
    $filePath = $directory . DIRECTORY_SEPARATOR . $fileToDelete;
    
    // Проверяем, является ли объект файлом и существует ли он, затем удаляем его
    if (is_file($filePath) && unlink($filePath)) {
        // Перезагружаем страницу после успешного удаления файла
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Выводим сообщение об ошибке, если файл не удалось удалить
        echo "Ошибка при удалении файла!";
    }
}

// Проверяем, была ли отправлена форма для переименования файла
if (isset($_POST['rename'])) {
    // Получаем старое и новое имя файла, используя basename для защиты
    $oldName = basename($_POST['file']);
    $newName = basename($_POST['new_name']);
    $oldPath = $directory . DIRECTORY_SEPARATOR . $oldName; //Старый путь к файлу
    $newPath = $directory . DIRECTORY_SEPARATOR . $newName; //Новый путь к файлу
    
    // Проверяем, существует ли файл и новое имя не пустое, затем переименовываем
    if (is_file($oldPath) && !empty($newName) && rename($oldPath, $newPath)) {
        // Перезагружаем страницу после успешного переименования
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Выводим сообщение об ошибке, если файл не удалось переименовать
        echo "Ошибка при переименовании файла!";
    }
}

// Проверяем, была ли отправлена форма загрузки файла
if (isset($_FILES['upload'])) {
    // Получаем информацию о загруженном файле
    $uploadFile = $_FILES['upload'];
    $fileName = basename($uploadFile['name']);

    // Проверяем расширение файла (разрешаем только .jpg)
    if (pathinfo($fileName, PATHINFO_EXTENSION) === 'jpg') {
        // Формируем путь для сохранения файла
        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        // Перемещаем загруженный файл в текущую директорию
        if (move_uploaded_file($uploadFile['tmp_name'], $filePath)) {
            // Перезагружаем страницу после успешной загрузки файла
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Выводим сообщение об ошибке, если загрузка не удалась
            echo "Ошибка при загрузке файла!";
        }
    } else {
        // Выводим сообщение, если загружаемый файл не является .jpg
        echo "Только .jpg файлы разрешены!";
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
        <?php foreach ($files as $file): ?> <!-- Перебираем файлы в текущей директории -->
            <?php if (is_file($directory . DIRECTORY_SEPARATOR . $file)): ?> <!-- Проверяем, является ли объект файлом -->
                <tr>
                    <td><?= htmlspecialchars($file) ?></td> <!-- Выводим имя файла с защитой от XSS -->
                    <td>
                        <!-- Форма для удаления файла -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>"> <!-- Передаем имя файла скрытым полем -->
                            <button type="submit" name="delete">Удалить</button> <!-- Кнопка удаления файла -->
                        </form>
                        <!-- Форма для переименования файла -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>"> <!-- Передаем имя файла скрытым полем -->
                            <input type="text" name="new_name" placeholder="Новое имя" required> <!-- Поле для ввода нового имени -->
                            <button type="submit" name="rename">Переименовать</button> <!-- Кнопка переименования -->
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>

    <h2>Загрузить файл</h2>
    <!-- Форма для загрузки нового файла -->
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="upload" accept=".jpg" required> <!-- Ограничиваем выбор файлов только .jpg -->
        <button type="submit">Загрузить</button> <!-- Кнопка загрузки -->
    </form>
</body>
</html>
