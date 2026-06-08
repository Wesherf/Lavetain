<?php
<<<<<<< HEAD
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
=======
$host = 'localhost';
$db   = 'Lavetain_db'; // Твое новое имя базы данных
$user = 'root';      // Логин для OpenServer (обязательно маленькими буквами)
$pass = '';          // Пароль для OpenServer (по умолчанию пустой)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // В этой строчке у тебя, скорее всего, было написано $username вместо $user
     $pdo = new PDO($dsn, $user, $pass, $options); 
} catch (\PDOException $e) {
     die("Ошибка подключения к БД: " . $e->getMessage());
}
>>>>>>> 68f94e9504b026ff2515e1341e991a0ca2292f54
