<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$error = '';
$success = '';

if ($auth->isLoggedIn()) {
    header("Location: phonebook.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $auth->register($_POST['username'], $_POST['email'], $_POST['password']);
    if ($result === true) {
        $success = "Реєстрація успішна! Тепер увійдіть.";
        header("refresh:2;url=index.php");
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Телефонна книга - Реєстрація</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Реєстрація</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <input type="text" name="username" placeholder="Логін" required 
                   pattern="[a-zA-Z0-9]{1,16}" title="Латинські літери та цифри, до 16 символів">
            
            <input type="email" name="email" placeholder="Email" required>
            
            <input type="password" name="password" placeholder="Пароль" required 
                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$" 
                   title="Пароль не відповідае вимогам">
            
            <button type="submit">Зареєструватися</button>
            <p>Вже є акаунт? <a href="index.php">Увійти</a></p>
        </form>
    </div>
</body>
</html>