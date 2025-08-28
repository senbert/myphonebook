<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/contacts.php';
require_once 'includes/upload.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$contact = new Contact($db);

if (!$auth->isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Отримання контакту для редагування
if (!isset($_GET['id'])) {
    header("Location: phonebook.php");
    exit;
}

$contact_data = $contact->readOne($_GET['id'], $user_id);
if (!$contact_data) {
    header("Location: phonebook.php");
    exit;
}

// Обробка форми редагування
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image_path = $contact_data['image_path'];
    
    if (!empty($_FILES['image']['name'])) {
        // Видаляємо старе зображення
        if ($image_path) {
            deleteImage($image_path);
        }
        
        $upload = handleUpload($_FILES['image']);
        if ($upload['success']) {
            $image_path = $upload['path'];
        } else {
            $message = $upload['message'];
        }
    }
    
    $result = $contact->update($_GET['id'], $user_id, $_POST['first_name'], 
                              $_POST['last_name'], $_POST['phone'], $_POST['email'], $image_path);
    
    if ($result) {
        $message = "Контакт успішно оновлено!";
        header("refresh:2;url=phonebook.php");
    } else {
        $message = "Помилка оновлення контакту";
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагування контакту</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Редагування контакту</h1>
            <a href="phonebook.php" class="logout">Назад</a>
        </header>

        <?php if ($message): ?>
            <div class="<?php echo strpos($message, 'успішно') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="auth-form">
            <input type="text" name="first_name" placeholder="Ім'я" 
                   value="<?php echo htmlspecialchars($contact_data['first_name']); ?>" required>
            
            <input type="text" name="last_name" placeholder="Прізвище" 
                   value="<?php echo htmlspecialchars($contact_data['last_name']); ?>" required>
            
            <input type="tel" name="phone" placeholder="Телефон" 
                   value="<?php echo htmlspecialchars($contact_data['phone']); ?>" required>
            
            <input type="email" name="email" placeholder="Email" 
                   value="<?php echo htmlspecialchars($contact_data['email']); ?>" required>
            
            <?php if ($contact_data['image_path']): ?>
            <div>
                <img src="<?php echo $contact_data['image_path']; ?>" alt="Поточне фото" style="max-width: 200px; margin: 10px 0;">
            </div>
            <?php endif; ?>
            
            <input type="file" name="image" accept="image/jpeg,image/png">
            
            <button type="submit">Оновити контакт</button>
        </form>
    </div>
</body>
</html>