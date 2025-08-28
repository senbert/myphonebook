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

//  AJAX 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] == 'add') {
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $upload = handleUpload($_FILES['image']);
            if ($upload['success']) {
                $image_path = $upload['path'];
            } else {
                echo json_encode(['success' => false, 'message' => $upload['message']]);
                exit;
            }
        }
        
        $result = $contact->create($user_id, $_POST['first_name'], $_POST['last_name'], 
                                  $_POST['phone'], $_POST['email'], $image_path);
        echo json_encode(['success' => $result !== false, 'message' => $result === false ? 'Помилка додавання' : '']);
        exit;
    }
    
    if ($_POST['action'] == 'delete') {
        $result = $contact->delete($_POST['id'], $user_id);
        echo json_encode(['success' => $result, 'message' => $result ? '' : 'Помилка видалення']);
        exit;
    }
}

// Отримання всіх контактів
$stmt = $contact->readAll($user_id);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Телефонна книга</title>
    <link rel="stylesheet" href="assets/style.css">
    <script>
    function addContact() {
        const formData = new FormData(document.getElementById('addForm'));
        formData.append('ajax', 'true');
        formData.append('action', 'add');
        
        fetch('phonebook.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('addForm').reset();
                document.getElementById('addModal').style.display = 'none';
                location.reload();
            } else {
                alert(data.message || 'Помилка додавання');
            }
        });
    }

    function deleteContact(id) {
        if (confirm('Видалити контакт?')) {
            const formData = new FormData();
            formData.append('ajax', 'true');
            formData.append('action', 'delete');
            formData.append('id', id);
            
            fetch('phonebook.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Помилка видалення');
                }
            });
        }
    }

    function showModal() {
        document.getElementById('addModal').style.display = 'block';
    }

    function hideModal() {
        document.getElementById('addModal').style.display = 'none';
    }
    </script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Телефонна книга</h1>
            <div class="user-info">
                Вітаємо, <?php echo $_SESSION['username']; ?>!
                <a href="?logout" class="logout">Вийти</a>
            </div>
        </header>

        <button onclick="showModal()" class="btn-add">Додати контакт</button>

        <div class="contacts-grid">
            <?php foreach ($contacts as $contact): ?>
            <div class="contact-card">
                <?php if ($contact['image_path']): ?>
                <img src="<?php echo $contact['image_path']; ?>" alt="Фото" class="contact-image">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></h3>
                <p><?php echo htmlspecialchars($contact['phone']); ?></p>
                <p><?php echo htmlspecialchars($contact['email']); ?></p>
                <div class="contact-actions">
                    <a href="edit_contact.php?id=<?php echo $contact['id']; ?>" class="btn-edit">Редагувати</a>
                    <button onclick="deleteContact(<?php echo $contact['id']; ?>)" class="btn-delete">Видалити</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>



        <!-- Вікно с додаваня  -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="hideModal()">&times;</span>
                <h2>Додати контакт</h2>
                <form id="addForm" onsubmit="event.preventDefault(); addContact();">
                    <input type="text" name="first_name" placeholder="Ім'я" required>
                    <input type="text" name="last_name" placeholder="Прізвище" required>
                    <input type="tel" name="phone" placeholder="Телефон" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="file" name="image" accept="image/jpeg,image/png">
                    <button type="submit">Додати</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['logout'])) {
        $auth->logout();
    }
    ?>
</body>
</html>