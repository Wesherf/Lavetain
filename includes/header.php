<?php
// Проверяем, авторизован ли пользователь, чтобы вывести его имя
$username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Гость';
$is_logged_in = isset($_SESSION['user_id']);
?>

<header style="background: var(--color-surface); border-bottom: 1px solid var(--color-border); padding: var(--spacing-md) 0;">
  <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--spacing-md);">
    
    <div style="font-size: var(--font-size-lg); font-weight: 800; letter-spacing: 2px;">
      <a href="index.php" style="color: var(--color-white); text-decoration: none;">LAVETAIN</a>
    </div>

    <nav style="display: flex; gap: var(--spacing-lg);">
      <a href="index.php" style="color: var(--color-text-muted); text-decoration: none; font-weight: 500; font-size: var(--font-size-sm); transition: color 0.2s;" onmouseover="this.style.color='var(--color-white)'" onmouseout="this.style.color='var(--color-text-muted)'">Главная</a>
      <a href="catalog.php" style="color: var(--color-white); text-decoration: none; font-weight: 600; font-size: var(--font-size-sm);">Каталог одежды</a>
    </nav>

    <div style="display: flex; align-items: center; gap: var(--spacing-md);">
      <?php if ($is_logged_in): ?>
        <span style="color: var(--color-text-muted); font-size: var(--font-size-sm);">
          Привет, <strong style="color: var(--color-white); font-weight: 600;"><?= $username ?></strong>
        </span>
        <a href="cabinet.php" class="btn" style="background: #18181b; border: 1px solid var(--color-border); color: var(--color-white); padding: 8px 16px; border-radius: var(--radius-md); text-decoration: none; font-size: var(--font-size-sm); font-weight: 500; transition: background 0.2s;" onmouseover="this.style.background='#27272a'" onmouseout="this.style.background='#18181b'">Профиль</a>
        <a href="logout.php" class="btn btn-primary" style="padding: 8px 16px; border-radius: var(--radius-md); text-decoration: none; font-size: var(--font-size-sm); font-weight: 600;">Выйти</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-primary" style="padding: 8px 16px; border-radius: var(--radius-md); text-decoration: none; font-size: var(--font-size-sm); font-weight: 600;">Войти</a>
      <?php endif; ?>
    </div>

  </div>
</header>