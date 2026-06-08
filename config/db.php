<?php
$host = "localhost";
$user = "root";
$password = ""; // В OpenServer по умолчанию пустой
$dbname = "lavetain_db"; // Твоя база данных

// 1. ПОДКЛЮЧЕНИЕ ДЛЯ СТАРОГО КОДА (PDO — чтобы работал index.php)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ИСПРАВЛЕНО ТУТ
} catch (PDOException $e) {
    die("Ошибка PDO: " . $e->getMessage());
}

// 2. ПОДКЛЮЧЕНИЕ ДЛЯ НОВОГО КОДА (MySQLi — чтобы работали замеры и кабинет)
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка MySQLi: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>