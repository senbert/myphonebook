<?php
class Auth {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $email, $password) {
     
        if (!preg_match('/^[a-zA-Z0-9]{1,16}$/', $username)) {
            return "Не вірний логін";
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Невірний формат email";
        }
        
        if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || 
            !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return "Невірний формат паролю чи невірний пароль";
        }

        
        $query = "SELECT id FROM users WHERE username = :username OR email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return "Користувач з таким логіном або email вже існує";
        }

    
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users SET username=:username, email=:email, password=:password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);

        if ($stmt->execute()) {
            return true;
        }
        return "Помилка реєстрації";
    }

    public function login($username, $password) {
        $query = "SELECT id, username, password FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                return true;
            }
        }
        return "Невірний логін або пароль";
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
?>