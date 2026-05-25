<?php
session_start();
require_once 'config/db.php';

// Защита: если сессии нет — выкидываем на страницу входа
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = '';

// 1. Получаем актуальные данные пользователя из БД при каждой перезагрузке
$stmt = $pdo->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit;
}

// 2. ОБРАБОТКА: Изменение имени профиля (Форма 1)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        $errors[] = 'Имя пользователя не может быть пустым.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        if ($stmt->execute([$name, $userId])) {
            $_SESSION['user_name'] = $name; // Обновляем в сессии для шапки сайта
            $user['name'] = $name;           // Обновляем в локальной переменной
            $success = 'Имя успешно обновлено!';
        } else {
            $errors[] = 'Не удалось сохранить изменения.';
        }
    }
}

// 3. ОБРАБОТКА: Безопасное изменение пароля (Форма 2)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = trim($_POST['old_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $new_password_confirm = trim($_POST['new_password_confirm'] ?? '');

    if (empty($old_password) || empty($new_password) || empty($new_password_confirm)) {
        $errors[] = 'Пожалуйста, заполните все поля для смены пароля.';
    } elseif ($new_password !== $new_password_confirm) {
        $errors[] = 'Новые пароли не совпадают.';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'Новый пароль должен быть не менее 6 символов.';
    } else {
        // Запрашиваем текущий хэш пароля из БД
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $password_hash = $stmt->fetchColumn();

        // Проверяем, совпадает ли старый пароль
        if (password_verify($old_password, $password_hash)) {
            // Хэшируем новый пароль
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_hash, $userId]);
            $success = 'Пароль успешно изменен!';
        } else {
            $errors[] = 'Текущий пароль указан неверно.';
        }
    }
}

// 4. Загрузка списка совершенных покупок пользователя
$orders = [];
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
} catch (Exception $e) {}
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

<main style="padding: var(--spacing-xl) 0; min-height: 80vh;">
  <div class="container" style="max-width: 800px;">
    
    <h1 style="color: var(--color-white); margin-bottom: var(--spacing-sm); font-size: var(--font-size-xl);">Личный кабинет</h1>
    <p style="color: var(--color-text-muted); margin-bottom: var(--spacing-xl); font-size: var(--font-size-sm);">
      Вы с нами с: <strong style="color: var(--color-white);"><?= date('d.m.Y в H:i', strtotime($user['created_at'])) ?></strong>
    </p>

    <?php if (!empty($errors)): ?>
      <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; padding: var(--spacing-md); margin-bottom: var(--spacing-lg); color: #f87171; border-radius: 4px;">
        <?php foreach ($errors as $error) echo "<p style='margin: 4px 0;'>$error</p>"; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22c55e; padding: var(--spacing-md); margin-bottom: var(--spacing-lg); color: #4ade80; border-radius: 4px;">
        <p><?= $success ?></p>
      </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); margin-bottom: var(--spacing-xl);">
      
      <div style="background: var(--color-surface); padding: var(--spacing-lg); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
        <h2 style="color: var(--color-white); font-size: var(--font-size-lg); margin-bottom: var(--spacing-md); font-weight: 600;">Личные данные</h2>
        
        <form method="POST">
          <div style="margin-bottom: var(--spacing-md);">
            <label style="display: block; color: var(--color-text-muted); margin-bottom: var(--spacing-sm); font-size: var(--font-size-sm);">Ваше имя в системе</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required style="width: 100%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: white;">
          </div>

          <div style="margin-bottom: var(--spacing-lg);">
            <label style="display: block; color: var(--color-text-muted); margin-bottom: var(--spacing-sm); font-size: var(--font-size-sm);">Email (изменению не подлежит)</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="width: 100%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: var(--color-text-muted); cursor: not-allowed;">
          </div>

          <button type="submit" name="save_profile" class="btn btn-primary" style="width: 100%; border: none; font-weight: 600; cursor: pointer;">Сохранить изменения</button>
        </form>
      </div>

      <div style="background: var(--color-surface); padding: var(--spacing-lg); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
        <h2 style="color: var(--color-white); font-size: var(--font-size-lg); margin-bottom: var(--spacing-md); font-weight: 600;">Безопасность</h2>
        
        <form method="POST">
          <div style="margin-bottom: var(--spacing-sm);">
            <input type="password" name="old_password" placeholder="Текущий пароль" required style="width: 100%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: white;">
          </div>
          
          <div style="margin-bottom: var(--spacing-sm);">
            <input type="password" name="new_password" placeholder="Новый пароль (от 6 симв.)" required style="width: 100%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: white;">
          </div>

          <div style="margin-bottom: var(--spacing-lg);">
            <input type="password" name="new_password_confirm" placeholder="Повторите новый пароль" required style="width: 100%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: white;">
          </div>

          <button type="submit" name="change_password" class="btn btn-outline" style="width: 100%; font-weight: 600; cursor: pointer;">Обновить пароль</button>
        </form>
      </div>

    </div>

    <div style="background: var(--color-surface); padding: var(--spacing-lg); border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
      <h2 style="color: var(--color-white); font-size: var(--font-size-lg); margin-bottom: var(--spacing-md); font-weight: 600;">История ваших покупок</h2>
      <?php if (!empty($orders)): ?>
        <table style="width: 100%; border-collapse: collapse; color: var(--color-text);">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border); text-align: left;">
              <th style="padding: var(--spacing-sm); color: var(--color-text-muted);">ID</th>
              <th style="padding: var(--spacing-sm); color: var(--color-text-muted);">Вещь</th>
              <th style="padding: var(--spacing-sm); color: var(--color-text-muted);">Цена</th>
              <th style="padding: var(--spacing-sm); color: var(--color-text-muted);">Статус</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: var(--spacing-md) var(--spacing-sm);">#<?= $order['order_id'] ?></td>
                <td style="padding: var(--spacing-md) var(--spacing-sm); color: var(--color-white); font-weight: 500;"><?= htmlspecialchars($order['title']) ?></td>
                <td style="padding: var(--spacing-md) var(--spacing-sm); color: var(--color-primary); font-weight: 600;"><?= number_format($order['price'], 0, '.', ' ') ?> ₸</td>
                <td style="padding: var(--spacing-md) var(--spacing-sm);"><span style="background: #27272a; padding: 4px 8px; border-radius: 4px; font-size: var(--font-size-sm); color: white;"><?= htmlspecialchars($order['status']) ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p style="color: var(--color-text-muted); font-size: var(--font-size-sm);">Вы еще не оформляли заказы в каталоге одежды Lavetain.</p>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>