<?php
session_start();
require_once 'config/db.php'; // Подключаем базу данных

// Запрос на получение всех товаров одежды из БД
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    $products = []; // Если таблицы товаров еще нет, страница не упадет
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lavetain — Магазин одежды нового поколения</title>  
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
  <section class="hero">
    <div class="container">
      <h1 class="hero__title">Будущее онлайн-шопинга</h1>
      <p class="hero__subtitle">Магазин одежды нового поколения</p>
      <a href="#catalog" class="btn btn-primary">Перейти в каталог</a>
    </div>
  </section>

  <section class="catalog" id="catalog" style="padding: var(--spacing-xl) 0;">
    <div class="container">
      <h2 style="font-size: var(--font-size-xl); margin-bottom: var(--spacing-lg); color: var(--color-white);">
        Наши Новинки
      </h2>
      
      <div class="cards-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--spacing-lg);">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $item): ?>
            <div class="product-card" style="background: var(--color-surface); border: 1px solid var(--color-border); padding: var(--spacing-lg); border-radius: var(--radius-md); display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="background: #27272a; height: 200px; border-radius: var(--radius-md); margin-bottom: var(--spacing-md); display: flex; align-items: center; justify-content: center; color: var(--color-text-muted);">
                  <?php if (!empty($item['image'])): ?>
                    <img src="img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-md);">
                  <?php else: ?>
                    Одежда
                  <?php endif; ?>
                </div>
                <h3 style="font-size: var(--font-size-lg); color: var(--color-white); margin-bottom: var(--spacing-sm);">
                  <?= htmlspecialchars($item['title']) ?>
                </h3>
                <p style="color: var(--color-text-muted); font-size: var(--font-size-sm); margin-bottom: var(--spacing-md);">
                  <?= htmlspecialchars($item['description']) ?>
                </p>
              </div>
              <div>
                <div style="font-size: var(--font-size-lg); font-weight: 700; color: var(--color-primary); margin-bottom: var(--spacing-md);">
                  <?= number_format($item['price'], 2, '.', ' ') ?> руб.
                </div>
                <a href="buy.php?id=<?= $item['id'] ?>" class="btn btn-primary" style="width: 100%; text-align: center;">
                  Купить
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color: var(--color-text-muted); grid-column: span 3;">В каталоге пока нет одежды. Добавьте товары в БД products.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>