<?php
session_start();
require_once 'config/db.php'; // Подключаем реальную базу данных

if (isset($_SESSION['user_id'])) {
    header('Location: cabinet.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $errors[] = 'Заполните все поля';
    } else {
        // Запрос к реальной таблице вместо заглушки $fakeEmail
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Проверяем пользователя и совпадение хэшированного пароля
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: cabinet.php');
            exit;
        } else {
            $errors[] = 'Неверный email или пароль';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Вход в систему</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="auth-page" style="padding: var(--spacing-xl) 0; display: flex; justify-content: center;">
  <div class="container" style="max-width: 450px;">
    <div class="auth-card" style="background: var(--color-surface); padding: var(--spacing-xl); border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
      <h1 class="auth-card__title" style="color: var(--color-white); font-size: var(--font-size-xl); margin-bottom: var(--spacing-lg);">Вход в аккаунт</h1>

      <?php if (!empty($errors)): ?>
        <div style="background: rgba(239,68,68,0.1); border-left: 4px solid #ef4444; padding: var(--spacing-sm); margin-bottom: var(--spacing-md); color: #f87171;">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group" style="margin-bottom: var(--spacing-md);">
          <label class="form-label" style="display:block; margin-bottom:var(--spacing-sm); color:var(--color-text-muted);">Email</label>
          <input class="form-input" type="email" name="email" required placeholder="your@email.com" style="width:100%; padding:12px; background:var(--color-bg); border:1px solid var(--color-border); border-radius:var(--radius-md); color:white;">
        </div>

        <div class="form-group" style="margin-bottom: var(--spacing-lg);">
          <label class="form-label" style="display:block; margin-bottom:var(--spacing-sm); color:var(--color-text-muted);">Пароль</label>
          <input class="form-input" type="password" name="password" required placeholder="Ваш пароль" style="width:100%; padding:12px; background:var(--color-bg); border:1px solid var(--color-border); border-radius:var(--radius-md); color:white;">
        </div>

        <button class="btn btn-primary" type="submit" style="width:100%;">Войти в систему</button>
      </form>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>