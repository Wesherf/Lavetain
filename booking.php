<?php
session_start();
require_once "config/db.php";

// Проверяем, залогинен ли пользователь (например, твой аккаунт sas)
if (!isset($_SESSION['user_id'])) {
    die("<div style='background:#111; color:#fff; height:100vh; padding:50px; font-family:sans-serif;'><h2>Ошибка доступа</h2>Чтобы записаться на примерку одежды, сначала <a href='login.php' style='color:#7f56da;'>войдите в свой аккаунт</a>.</div>");
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $service = $_POST['service_type'] ?? 'Индивидуальный подбор размера';
    $chest = intval($_POST['chest_cm'] ?? 0);
    $waist = intval($_POST['waist_cm'] ?? 0);
    $hips = intval($_POST['hips_cm'] ?? 0);
    $date = $_POST['visit_date'] ?? '';

    if ($chest > 0 && $waist > 0 && $hips > 0 && !empty($date)) {
        $stmt = $conn->prepare("INSERT INTO lavetain_appointments (user_id, service_type, chest_cm, waist_cm, hips_cm, visit_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiiis", $user_id, $service, $chest, $waist, $hips, $date);
        
        if ($stmt->execute()) {
            $msg = "<p style='color: #27ae60; font-weight: bold;'>Запись успешна! Ваши параметры одежды сохранены в БД.</p>";
        } else {
            $msg = "<p style='color: #c0392b;'>Ошибка сохранения в базу данных.</p>";
        }
    } else {
        $msg = "<p style='color: #e67e22;'>Пожалуйста, укажите корректные обхваты тела в см.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LAVETAIN — Запись на примерку одежды</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #0a0a0a; color: #fff; }
        .navbar { background: #000; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a1a1a; }
        .navbar .logo { font-size: 22px; font-weight: bold; letter-spacing: 3px; color: #fff; text-decoration: none; }
        .navbar a { color: #fff; text-decoration: none; font-size: 14px; }
        .box { max-width: 450px; margin: 60px auto; background: #111; padding: 40px; border-radius: 12px; border: 1px solid #222; }
        h2 { margin-top: 0; font-size: 24px; font-weight: 500; letter-spacing: 1px; color: #fff; margin-bottom: 25px; }
        .field { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
        label { font-size: 13px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        input, select { padding: 12px; background: #161616; border: 1px solid #2c2c2c; color: #fff; border-radius: 6px; font-size: 15px; }
        input:focus, select:focus { border-color: #7f56da; outline: none; }
        .btn { background: #7f56da; color: #fff; border: none; padding: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 15px; text-transform: uppercase; letter-spacing: 1px; transition: 0.2s; }
        .btn:hover { background: #6939cc; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php" class="logo">LAVETAIN</a>
    <div><a href="cabinet.php">ЛИЧНЫЙ КАБИНЕТ →</a></div>
</div>

<div class="box">
    <h2>Запись на примерку</h2>
    <?= $msg ?>
    <form action="booking.php" method="POST">
        <div class="field">
            <label>Тип услуги</label>
            <select name="service_type">
                <option value="Примерка новой коллекции и замеры тела">Примерка новой коллекции и замеры тела</option>
                <option value="Подбор оверсайз лука со стилистом">Подбор оверсайз лука со стилистом</option>
            </select>
        </div>

        <div class="field">
            <label>Обхват груди (см)</label>
            <input type="number" name="chest_cm" placeholder="например, 92" required>
        </div>

        <div class="field">
            <label>Обхват талии (см)</label>
            <input type="number" name="waist_cm" placeholder="например, 68" required>
        </div>

        <div class="field">
            <label>Обхват бёдер (см)</label>
            <input type="number" name="hips_cm" placeholder="например, 98" required>
        </div>

        <div class="field">
            <label>Дата и время визита</label>
            <input type="datetime-local" name="visit_date" required>
        </div>

        <button type="submit" class="btn">Записаться на замеры</button>
    </form>
</div>

</body>
</html>