<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: cabinet.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = 'Заполните все поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Неверный формат почты';
    } else {
        // Проверяем занятость почты
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Данный email уже занят другой учетной записью';
        } else {
            // Хешируем пароль по правилам безопасности из лекции
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashedPassword])) {
                $success = 'Регистрация выполнена успешно! Войдите.';
            } else {
                $errors[] = 'Ошибка при записи в БД';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Регистрация аккаунта</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="auth-page" style="padding: var(--spacing-xl) 0; display: flex; justify-content: center;">
  <div class="container" style="max-width: 450px;">
    <div class="auth-card" style="background: var(--color-surface); padding: var(--spacing-xl); border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
      <h1 class="auth-card__title" style="color: var(--color-white); font-size: var(--font-size-xl); margin-bottom: var(--spacing-lg);">Регистрация</h1>
      
      <?php if(!empty($errors)): ?>
        <div style="background: rgba(239,68,68,0.1); border-left: 4px solid #ef4444; padding: var(--spacing-sm); margin-bottom: var(--spacing-md); color: #f87171;">
            <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
        </div>
      <?php endif; ?>

      <?php if($success): ?>
        <div style="background: rgba(34,197,94,0.1); border-left: 4px solid #22c55e; padding: var(--spacing-sm); margin-bottom: var(--spacing-md); color: #4ade80;">
            <p><?= $success ?></p>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group" style="margin-bottom: var(--spacing-md);">
          <label class="form-label" style="display:block; margin-bottom:var(--spacing-sm); color:var(--color-text-muted);">Имя</label>
          <input class="form-input" type="text" name="name" required placeholder="Ваше имя" style="width:100%; padding:12px; background:var(--color-bg); border:1px solid var(--color-border); border-radius:var(--radius-md); color:white;">
        </div>
        <div class="form-group" style="margin-bottom: var(--spacing-md);">
          <label class="form-label" style="display:block; margin-bottom:var(--spacing-sm); color:var(--color-text-muted);">Email</label>
          <input class="form-input" type="email" name="email" required placeholder="your@email.com" style="width:100%; padding:12px; background:var(--color-bg); border:1px solid var(--color-border); border-radius:var(--radius-md); color:white;">
        </div>
        <div class="form-group" style="margin-bottom: var(--spacing-lg);">
          <label class="form-label" style="display:block; margin-bottom:var(--spacing-sm); color:var(--color-text-muted);">Пароль</label>
          <input class="form-input" type="password" name="password" required placeholder="Придумайте пароль" style="width:100%; padding:12px; background:var(--color-bg); border:1px solid var(--color-border); border-radius:var(--radius-md); color:white;">
        </div>
        <button class="btn btn-primary" type="submit" style="width:100%;">Создать аккаунт</button>
      </form>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>