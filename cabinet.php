<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$orders = [];

// Вытягиваем заказы данного пользователя вместе с названиями товаров и их ценами
try {
    $stmt = $pdo->prepare("
        SELECT orders.id as order_id, products.title, products.price, orders.status, orders.created_at 
        FROM orders 
        JOIN products ON orders.product_id = products.id 
        WHERE orders.user_id = ? 
        ORDER BY orders.id DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();
} catch (Exception $e) {
    // В случае отсутствия таблицы заказов
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Личный кабинет</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="profile-page" style="padding: var(--spacing-xl) 0;">
  <div class="container">
    <div class="profile-card" style="background: var(--color-surface); padding: var(--spacing-xl); border-radius: var(--radius-lg); border: 1px solid var(--color-border); margin-bottom: var(--spacing-xl);">
      <h1 class="profile-title" style="color: var(--color-white); font-size: var(--font-size-xl); margin-bottom: var(--spacing-sm);">Личный кабинет</h1>
      <p class="profile-text" style="color: var(--color-text-muted); margin-bottom: var(--spacing-md);">
        Добро пожаловать, <span style="color: var(--color-primary); font-weight: bold;"><?= htmlspecialchars($_SESSION['user_name']) ?></span>!
      </p>
      <a href="logout.php" class="btn btn-outline">Выйти из аккаунта</a>
    </div>

    <div class="user-orders" style="background: var(--color-surface); padding: var(--spacing-xl); border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
        <h2 style="color: var(--color-white); font-size: var(--font-size-lg); margin-bottom: var(--spacing-md);">Мои покупки (Заказы)</h2>
        
        <?php if (!empty($orders)): ?>
            <table style="width: 100%; border-collapse: collapse; color: var(--color-text);">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-border); text-align: left;">
                        <th style="padding: var(--spacing-sm);">ID Заказа</th>
                        <th style="padding: var(--spacing-sm);">Товар</th>
                        <th style="padding: var(--spacing-sm);">Цена</th>
                        <th style="padding: var(--spacing-sm);">Статус</th>
                        <th style="padding: var(--spacing-sm);">Дата</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <td style="padding: var(--spacing-md) var(--spacing-sm);">#<?= $order['order_id'] ?></td>
                            <td style="padding: var(--spacing-md) var(--spacing-sm); color: var(--color-white);"><?= htmlspecialchars($order['title']) ?></td>
                            <td style="padding: var(--spacing-md) var(--spacing-sm);"><?= number_format($order['price'], 2, '.', ' ') ?> руб.</td>
                            <td style="padding: var(--spacing-md) var(--spacing-sm);">
                                <span style="background: #27272a; padding: 4px 8px; border-radius: 4px; font-size: var(--font-size-sm);">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td style="padding: var(--spacing-md) var(--spacing-sm); color: var(--color-text-muted);"><?= $order['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: var(--color-text-muted);">Вы еще ничего не заказывали в нашем магазине одежды.</p>
        <?php endif; ?>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>