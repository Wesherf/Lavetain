<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Связываем таблицу заказов (orders) с вещами (products)
$orders_stmt = $pdo->prepare("
    SELECT o.id AS order_id, p.title AS product_title, p.image AS product_image, p.price AS product_price 
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.user_id = ?
    ORDER BY o.id DESC
");
$orders_stmt->execute([$user_id]);
$user_orders = $orders_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Личный кабинет — Lavetain</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main style="padding: 60px 0; min-height: 80vh;">
  <div class="container" style="max-width: 700px;">
    
    <h1 style="color: var(--color-white); font-size: 2.2rem; margin-bottom: 30px; font-weight: 800; text-align: center;">Личный кабинет</h1>

    <div style="background: var(--color-surface); border: 1px solid var(--color-border); padding: 24px; border-radius: var(--radius-md); margin-bottom: 24px;">
      <h2 style="color: var(--color-white); font-size: 16px; margin-bottom: 16px; font-weight: 600; border-bottom: 1px solid var(--color-border); padding-bottom: 8px;">Данные аккаунта</h2>
      <div style="display: flex; flex-direction: column; gap: 10px; color: var(--color-text-muted); font-size: 14px;">
        <div>Имя пользователя: <strong style="color: var(--color-white);"><?= htmlspecialchars($user_name) ?></strong></div>
        <div>Ваш ID на сайте: <strong style="color: var(--color-white);"><?= htmlspecialchars($user_id) ?></strong></div>
        <div>Безопасность сессии: <span style="color: #22c55e; font-weight: bold;">✓ Защищено</span></div>
      </div>
    </div>

    <div style="background: var(--color-surface); border: 1px solid var(--color-border); padding: 24px; border-radius: var(--radius-md);">
      <h2 style="color: var(--color-white); font-size: 16px; margin-bottom: 16px; font-weight: 600; border-bottom: 1px solid var(--color-border); padding-bottom: 8px;">История ваших покупок</h2>

      <?php if (!empty($user_orders)): ?>
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <?php foreach ($user_orders as $order): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; background: #121214; border: 1px solid var(--color-border); padding: 14px; border-radius: var(--radius-sm);">
              
              <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 45px; height: 45px; background: #ffffff; border-radius: 4px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                  <?php if (!empty($order['product_image'])): ?>
                    <img src="images/<?= htmlspecialchars($order['product_image']) ?>" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                  <?php else: ?>
                    <span style="font-size: 9px; color: #121214; font-weight: bold;">LVTN</span>
                  <?php endif; ?>
                </div>
                <div>
                  <div style="color: var(--color-white); font-weight: 600; font-size: 14px;"><?= htmlspecialchars($order['product_title']) ?></div>
                  <div style="color: var(--color-text-muted); font-size: 11px;">Заказ №<?= $order['order_id'] ?></div>
                </div>
              </div>

              <div style="color: var(--color-primary); font-weight: 700; font-size: 14px;">
                <?= number_format($order['product_price'], 2, '.', ' ') ?> ₸
              </div>

            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="color: var(--color-text-muted); font-size: 14px; margin: 0; text-align: center; padding: 20px 0;">Вы ещё не совершали заказов в нашем магазине.</p>
      <?php endif; ?>

    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>