<?php
class Contact {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($user_id, $first_name, $last_name, $phone, $email, $image_path = null) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Невірний формат email";
        }

        $query = "INSERT INTO contacts SET user_id=:user_id, first_name=:first_name, 
                 last_name=:last_name, phone=:phone, email=:email, image_path=:image_path";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":image_path", $image_path);

        return $stmt->execute();
    }

    public function readAll($user_id) {
        $query = "SELECT * FROM contacts WHERE user_id = :user_id ORDER BY first_name, last_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id, $user_id) {
        $query = "SELECT * FROM contacts WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $user_id, $first_name, $last_name, $phone, $email, $image_path = null) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Невірний формат email";
        }

        $query = "UPDATE contacts SET first_name=:first_name, last_name=:last_name, 
                 phone=:phone, email=:email, image_path=:image_path 
                 WHERE id=:id AND user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":image_path", $image_path);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);

        return $stmt->execute();
    }

    public function delete($id, $user_id) {
        
        $contact = $this->readOne($id, $user_id);
        if ($contact && $contact['image_path']) {
            unlink($contact['image_path']);
        }

        $query = "DELETE FROM contacts WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }
}
?>