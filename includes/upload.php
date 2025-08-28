<?php
function handleUpload($file) {
    global $allowed_types;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Помилка завантаження файлу'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Файл занадто великий (макс. 5MB)'];
    }

    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Дозволені тільки JPG, JPEG, PNG файли'];
    }

    // Створюємо папку uploads якщо не існує
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $target_path = UPLOAD_DIR . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'path' => $target_path];
    }

    return ['success' => false, 'message' => 'Помилка збереження файлу'];
}

function deleteImage($image_path) {
    if ($image_path && file_exists($image_path)) {
        unlink($image_path);
    }
}
?>