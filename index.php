<?php
session_start();
require_once 'config/db.php';

// Загружаем товары для главной страницы (например, последние 3 новинки)
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC LIMIT 3");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lavetain — Главная</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section style="background: var(--color-surface); padding: var(--spacing-xl) 0; border-bottom: 1px solid var(--color-border); text-align: center;">
  <div class="container">
    <h1 style="color: var(--color-white); font-size: 3rem; margin-bottom: var(--spacing-sm); font-weight: 800; letter-spacing: 2px;">МАГАЗИН ОДЕЖДЫ</h1>
    <p style="color: var(--color-text-muted); font-size: var(--font-size-md); max-width: 600px; margin: 0 auto 24px; line-height: 1.6;">Эксклюзивная коллекция уличной одежды. Качество, стиль и комфорт в каждой детали.</p>
    <a href="catalog.php" class="btn btn-primary" style="display: inline-block; text-decoration: none; font-weight: 600; padding: 14px 28px;">Перейти в каталог</a>
  </div>
</section>

<main style="padding: var(--spacing-xl) 0; min-height: 50vh;">
  <div class="container">
    <h2 style="color: var(--color-white); margin-bottom: var(--spacing-lg); font-size: var(--font-size-xl); text-align: center;">Новая коллекция</h2>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--spacing-lg);">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $item): ?>
          <div style="background: var(--color-surface); border: 1px solid var(--color-border); padding: var(--spacing-md); border-radius: var(--radius-md); display: flex; flex-direction: column; justify-content: space-between; min-height: 420px;">
            
            <div>
              <div style="background: #27272a; height: 220px; border-radius: 8px; margin-bottom: var(--spacing-md); display: flex; align-items: center; justify-content: center; color: var(--color-text-muted); font-size: var(--font-size-sm); font-weight: bold; letter-spacing: 1px; overflow: hidden;">
                <?php if (!empty($item['image'])): ?>
                  <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                <?php else: ?>
                  LAVETAIN CLOTHES
                <?php endif; ?>
              </div>  
              
              <h3 style="font-size: var(--font-size-md); color: var(--color-white); margin-bottom: var(--spacing-sm); font-weight: 600;"><?= htmlspecialchars($item['title']) ?></h3>
              <p style="color: var(--color-text-muted); font-size: var(--font-size-sm); margin-bottom: var(--spacing-md); line-height: 1.4;"><?= htmlspecialchars($item['description']) ?></p>
            </div>

            <div>
              <div style="font-size: var(--font-size-lg); font-weight: 700; color: var(--color-primary); margin-bottom: var(--spacing-md);">
                <?= number_format($item['price'], 2, '.', ' ') ?> тенге.
              </div>
              <a href="buy.php?id=<?= $item['id'] ?>" class="btn btn-primary" style="width: 100%; text-align: center; box-sizing: border-box; text-decoration: none; font-weight: 600; display: block;">Купить</a>
            </div>

          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color: var(--color-text-muted); grid-column: span 3; text-align: center; font-size: var(--font-size-md);">Товары еще не добавлены в базу данных.</p>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>