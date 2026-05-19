<?php
session_start();
require_once 'config/db.php';

// Если не авторизован — не даем купить, отправляем на форму входа
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$userId = $_SESSION['user_id'];

if ($productId) {
    try {
        // Добавляем запись в таблицу заказов orders
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$userId, $productId]);
        
        // Перенаправляем в личный кабинет, где пользователь увидит покупку
        header('Location: cabinet.php');
        exit;
    } catch (Exception $e) {
        die("Ошибка при обработке заказа: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
}