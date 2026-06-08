<?php
<<<<<<< HEAD
// 1. Инициализация сессии и базы данных
session_start();
require_once "config/db.php"; 

// 2. Определение пользователя
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}
$user_id = $_SESSION['user_id'];

// 3. Обработка отмены записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {
    $appointment_id = intval($_POST['appointment_id'] ?? 0);

    if ($appointment_id > 0) {
        $stmt = $conn->prepare("UPDATE lavetain_appointments SET status = ? WHERE id = ? AND user_id = ?");
        if ($stmt) {
            $new_status = 'cancelled';
            $stmt->bind_param("sii", $new_status, $appointment_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: cabinet.php');
    exit;
}

// 4. Получение записей для таблицы
$app_res = $conn->query("SELECT id, status, service_type, chest_cm, waist_cm, hips_cm, visit_date FROM lavetain_appointments WHERE user_id = $user_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LAVETAIN — Личный кабинет</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #0a0a0a; color: #fff; display: flex; flex-direction: column; min-height: 100vh; }
        .navbar { background: #000; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a1a1a; }
        .navbar .logo { font-size: 22px; font-weight: bold; letter-spacing: 3px; color: #fff; text-decoration: none; }
        .navbar-links { display: flex; gap: 20px; align-items: center; }
        .navbar-links a { color: #fff; text-decoration: none; font-size: 14px; text-transform: uppercase; }
        .btn-logout { background: #7f56da; padding: 8px 18px; border-radius: 6px; font-weight: bold; }
        .btn-logout:hover { background: #6939cc; }
        
        .main-content { flex: 1; max-width: 1100px; width: 100%; margin: 40px auto; padding: 0 20px; box-sizing: border-box; }
        .profile-header { text-align: center; margin-bottom: 40px; }
        .profile-header h1 { font-size: 32px; font-weight: 500; margin: 0 0 10px 0; }
        .profile-header p { color: #666; margin: 0; font-size: 14px; }
        
        /* Сетка для блоков Личные данные и Безопасность */
        .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .card { background: #111; border: 1px solid #222; padding: 30px; border-radius: 12px; }
        .card h3 { margin-top: 0; font-size: 20px; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid #222; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; gap: 8px; }
        label { font-size: 12px; color: #555; text-transform: uppercase; letter-spacing: 1px; }
        input { padding: 12px; background: #161616; border: 1px solid #2c2c2c; color: #fff; border-radius: 6px; font-size: 14px; }
        input:disabled { color: #444; }
        
        .btn-purple { background: #7f56da; color: #fff; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; transition: 0.2s; width: 100%; }
        .btn-purple:hover { background: #6939cc; }
        .btn-white { background: #fff; color: #000; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; transition: 0.2s; width: 100%; }
        .btn-white:hover { background: #e0e0e0; }
        
        /* Широкие блоки снизу */
        .wide-card { background: #111; border: 1px solid #222; padding: 30px; border-radius: 12px; margin-bottom: 30px; }
        .wide-card h3 { margin-top: 0; font-size: 20px; font-weight: 500; margin-bottom: 15px; }
        .wide-card p { color: #666; font-size: 14px; margin: 0; }
        
        /* Таблица для услуг */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; color: #ccc; font-size: 14px; }
        th { padding: 12px; border: 1px solid #222; background: #161616; text-align: left; color: #888; font-size: 11px; text-transform: uppercase; }
        td { padding: 12px; border: 1px solid #222; }
        
        .btn-link { display: inline-block; margin-top: 15px; background: #161616; border: 1px solid #222; color: #fff; text-decoration: none; padding: 10px 20px; font-size: 13px; font-weight: bold; border-radius: 6px; text-transform: uppercase; }
        .btn-link:hover { background: #222; }
        
        footer { background: #000; text-align: center; padding: 25px; border-top: 1px solid #1a1a1a; color: #444; font-size: 13px; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php" class="logo">LAVETAIN</a>
    <div class="navbar-links">
        <a href="index.php" style="color: #666;">Главная</a>
        <a href="index.php">Каталог одежды</a>
        <a href="contact.php" style="color: #7f56da; font-weight: bold; margin-left: 10px;">Обратная связь</a>
        <span style="color: #666; margin-left: 15px;">Привет, <span style="color: #fff; font-weight: bold;">dsa1</span></span>
        <a href="index.php" style="color: #aaa; margin-left: 10px;">Профиль</a>
        <a href="logout.php" class="btn-logout">Выйти</a>
    </div>
</div>

<div class="main-content">
    
    <div class="profile-header">
        <h1>Личный кабинет</h1>
        <p>Вы с нами с: 25.05.2026 в 13:15</p>
    </div>
    
    <div class="profile-grid">
        <div class="card">
            <h3>Личные данные</h3>
            <form action="#" method="POST">
                <div class="form-group">
                    <label>Ваше имя в системе</label>
                    <input type="text" value="dsa1">
                </div>
                <div class="form-group">
                    <label>Email (изменению не подлежит)</label>
                    <input type="email" value="dsa1@gmail.com" disabled>
                </div>
                <button type="button" class="btn-purple">Сохранить изменения</button>
            </form>
        </div>
        
        <div class="card">
            <h3>Безопасность</h3>
            <form action="#" method="POST">
                <div class="form-group">
                    <input type="password" placeholder="Текущий пароль">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Новый пароль (от 6 симв.)">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Повторите новый пароль">
                </div>
                <button type="button" class="btn-white">Обновить пароль</button>
            </form>
        </div>
    </div>

    <div class="wide-card" style="border-left: 4px solid #7f56da;">
        <h3 style="color: #fff; margin-bottom: 10px;">Мои записи на примерку (Услуги в БД)</h3>
        
        <?php if ($app_res && $app_res->num_rows > 0): ?>
            <table>
    <tr>
        <th>Услуга</th>
        <th>Грудь</th>
        <th>Талия</th>
        <th>Бёдра</th>
        <th>Дата визита</th>
        <th>Действие</th> </tr>
    <?php while($row = $app_res->fetch_assoc()): ?>
        <tr style="border-bottom: 1px solid #1a1a1a;">
            <td style="color:#fff;"><?= htmlspecialchars($row['service_type']) ?></td>
            <td><?= $row['chest_cm'] ?> см</td>
            <td style="color: #7f56da; font-weight: bold;"><?= $row['waist_cm'] ?> см</td>
            <td><?= $row['hips_cm'] ?> см</td>
            <td style="color: #7f56da;"><?= $row['visit_date'] ?></td>
            
            <td>
                <?php 
                // Если статус 'new', 'pending' или пустой (еще не задан в базе) — показываем кнопку
                $status = $row['status'] ?? 'pending';
                if ($status === 'pending' || $status === 'new' || $status === ''): 
                ?>
                    <form method="POST" action="cabinet.php" onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');" style="margin: 0;">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="cancel_appointment" style="background-color: #e74c3c; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; font-size: 12px;">
                            Отменить
                        </button>
                    </form>
                <?php else: ?>
                    <span style="color: #666; font-size: 13px;">Отменена</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
            <a href="booking.php" class="btn-link">Записаться на повторные замеры</a>
        <?php else: ?>
            <p style="margin-bottom: 15px;">Вы ещё не записывались на подбор размера и примерку одежды.</p>
            <a href="booking.php" class="btn-purple" style="display: inline-block; width: auto; padding: 12px 25px;">Заполнить обхват талии и замеры →</a>
        <?php endif; ?>
    </div>

    <div class="wide-card">
        <h3>История ваших покупок</h3>
        <p>Вы еще не оформляли заказы в каталоге одежды Lavetain.</p>
    </div>

</div>

<footer>
    © 2026 Lavetain · Магазин одежды нового поколения
</footer>

=======
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
>>>>>>> 68f94e9504b026ff2515e1341e991a0ca2292f54
</body>
</html>