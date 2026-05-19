<?php
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