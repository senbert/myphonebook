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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $result = $auth->login($_POST['username'], $_POST['password']);
    if ($result === true) {
        header("Location: phonebook.php");
        exit;
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Телефонна книга - Вхід</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Телефонна книга</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <h2>Вхід</h2>
            <input type="text" name="username" placeholder="Логін" required 
                   pattern="[a-zA-Z0-9]{1,16}" title="Логін не відповідае вимогам">
            
            <input type="password" name="password" placeholder="Пароль" required>
            
            <button type="submit" name="login">Увійти</button>
            <p>Ще не зареєстровані? <a href="register.php">Створити акаунт</a></p>
        </form>
    </div>
</body>
</html>