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

        <button class="btn btn-primary" type="submit" style="width:100%; margin-bottom: var(--spacing-md);">Войти в систему</button>
        
        <div style="text-align: center; font-size: 14px; color: var(--color-text-muted); margin-bottom: 20px;">
          Нет аккаунта? <a href="register.php" style="color: #7f56da; text-decoration: none; font-weight: bold;">Зарегистрироваться</a>
        </div>

        <div style="border-top: 1px solid var(--color-border); padding-top: 20px; display: flex; flex-direction: column; gap: 12px; align-items: center;">
            
            <a href="google-auth.php" style="display: inline-flex; align-items: center; justify-content: center; background-color: #161616; color: #F2F2F2; border: 1px solid #2A2A2A; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; width: 100%; box-sizing: border-box; transition: background 0.3s;">
                <svg style="width: 18px; height: 18px; margin-right: 10px;" viewBox="0 0 24 24">
                    <path fill="#EA4335" d="M12.24 10.285V14.4h6.887c-.275 1.565-1.88 4.604-6.887 4.604-4.33 0-7.866-3.577-7.866-8s3.536-8 7.866-8c2.46 0 4.105 1.025 5.047 1.926l3.227-3.11C18.28 1.845 15.548 1 12.24 1 5.48 1 0 6.48 0 13.2s5.48 12.2 12.24 12.2c7.055 0 11.75-4.96 11.75-11.96 0-.81-.08-1.425-.195-2.155H12.24z"/>
                </svg>
                Войти через Google
            </a>

            <script async src="https://telegram.org/js/telegram-widget.js?22" 
                    data-telegram-login="truew_auth_bot" 
                    data-size="large" 
                    data-auth-url="http://lavetain.ru/telegram-auth.php" 
                    data-request-access="write">
            </script>
            
        </div>
      </form>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>