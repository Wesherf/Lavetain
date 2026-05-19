<header class="header">
  <div class="container navbar">
    <a href="index.php" class="navbar__logo" style="text-decoration: none;">Lavetain</a>    
    <nav class="navbar__menu">
      <a href="index.php">Главная</a>
      <a href="index.php#catalog">Каталог</a>
    </nav>

    <div class="navbar__actions">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="navbar__user" style="color: var(--color-text); margin-right: var(--spacing-md);">
          Привет, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
        </span>
        <a href="cabinet.php" class="btn btn-outline" style="text-decoration: none; margin-right: var(--spacing-sm);">Профиль</a>
        <a href="logout.php" class="btn btn-primary" style="text-decoration: none;">Выйти</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline" style="text-decoration: none; margin-right: var(--spacing-sm);">Вход</a>
        <a href="register.php" class="btn btn-primary" style="text-decoration: none;">Регистрация</a>
      <?php endif; ?>
    </div>
  </div>
</header>