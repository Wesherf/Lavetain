<?php
session_start();
// Подключаем наш универсальный файл, который лежит в папке config
require_once "config/db.php"; 

$success_msg = "";
$error_msg = "";

// Проверяем, если форма была отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Безопасно принимаем данные из формы
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message_text = trim($_POST['message'] ?? '');

    // Проверяем, что все поля заполнены
    if (!empty($name) && !empty($email) && !empty($message_text)) {
        
        // Пишем наш INSERT-запрос (используем $conn из нашего конфига)
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message_text);
        
        if ($stmt->execute()) {
            // Показываем подтверждение отправки
            $success_msg = "Ваше сообщение успешно отправлено и сохранено в БД! Администратор свяжется с вами.";
        } else {
            $error_msg = "Ошибка сохранения данных в базу.";
        }
        $stmt->close();
    } else {
        $error_msg = "Пожалуйста, заполните все поля формы.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LAVETAIN — Обратная связь</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #0a0a0a; color: #fff; }
        .navbar { background: #000; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a1a1a; }
        .navbar .logo { font-size: 22px; font-weight: bold; letter-spacing: 3px; color: #fff; text-decoration: none; }
        .navbar a { color: #fff; text-decoration: none; font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .box { max-width: 500px; margin: 60px auto; background: #111; padding: 40px; border-radius: 12px; border: 1px solid #222; }
        h2 { margin-top: 0; font-size: 24px; font-weight: 500; letter-spacing: 1px; color: #fff; margin-bottom: 25px; border-bottom: 1px solid #222; padding-bottom: 15px; }
        .field { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
        label { font-size: 13px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        input, textarea { padding: 12px; background: #161616; border: 1px solid #2c2c2c; color: #fff; border-radius: 6px; font-size: 15px; font-family: inherit; }
        input:focus, textarea:focus { border-color: #7f56da; outline: none; }
        textarea { resize: vertical; min-height: 120px; }
        .btn { background: #7f56da; color: #fff; border: none; padding: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 15px; text-transform: uppercase; letter-spacing: 1px; transition: 0.2s; }
        .btn:hover { background: #6939cc; }
        .alert-success { background: rgba(39, 174, 96, 0.1); border: 1px solid #27ae60; color: #27ae60; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .alert-error { background: rgba(192, 41, 43, 0.1); border: 1px solid #c0392b; color: #c0392b; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php" class="logo">LAVETAIN</a>
    <div><a href="cabinet.php">ЛИЧНЫЙ КАБИНЕТ →</a></div>
</div>

<div class="box">
    <h2>Связаться с нами</h2>
    
    <?php if (!empty($success_msg)): ?>
        <div class="alert-success"><?= $success_msg ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert-error"><?= $error_msg ?></div>
    <?php endif; ?>

    <form action="contact.php" method="POST">
        <div class="field">
            <label>Ваше Имя</label>
            <input type="text" name="name" placeholder="Введите ваше имя" required>
        </div>

        <div class="field">
            <label>Ваш Email</label>
            <input type="email" name="email" placeholder="name@example.com" required>
        </div>

        <div class="field">
            <label>Сообщение администратору</label>
            <textarea name="message" placeholder="Напишите ваш вопрос по поводу одежды или доставки..." required></textarea>
        </div>

        <button type="submit" class="btn">Отправить сообщение</button>
    </form>
</div>

</body>
</html>