<?php
session_start();
require_once 'config/db.php';

// 1. Получаем данные из фильтров (GET-запрос)
$search = trim($_GET['search'] ?? '');
$price_from = trim($_GET['price_from'] ?? '');
$price_to = trim($_GET['price_to'] ?? '');
$sort = trim($_GET['sort'] ?? 'default');

// Массив для хранения условий WHERE и параметров для execute
$where = [];
$params = [];

// Фильтр по поисковому слову (ищем в названии и описании товара)
if (!empty($search)) {
    $where[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Фильтр по минимальной цене
if (!empty($price_from) && is_numeric($price_from)) {
    $where[] = "price >= ?";
    $params[] = $price_from;
}

// Фильтр по максимальной цене
if (!empty($price_to) && is_numeric($price_to)) {
    $where[] = "price <= ?";
    $params[] = $price_to;
}

// Собираем строку WHERE динамически
$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// 2. Безопасная сортировка (белый список параметров)
$allowed_sort = [
    'price_asc'  => 'price ASC',  // Сначала дешевые
    'price_desc' => 'price DESC', // Сначала дорогие
    'default'    => 'id DESC'     // Новинки
];
$sort_sql = $allowed_sort[$sort] ?? 'id DESC';

// 3. Выполняем финальный SQL-запрос к таблице продуктов
$sql = "SELECT * FROM products $where_sql ORDER BY $sort_sql";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Каталог одежды — Lavetain</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main style="padding: var(--spacing-xl) 0; min-height: 80vh;">
  <div class="container">
    <h1 style="color: var(--color-white); margin-bottom: var(--spacing-lg); font-size: var(--font-size-xl);">Каталог</h1>
        <div style="display: flex; gap: var(--spacing-lg); align-items: flex-start;">
      
      <aside style="width: 300px; background: var(--color-surface); padding: var(--spacing-lg); border-radius: var(--radius-md); border: 1px solid var(--color-border); flex-shrink: 0;">
        <form method="GET" action="catalog.php">
          
          <div style="margin-bottom: var(--spacing-md);">
            <label style="display: block; color: var(--color-text-muted); margin-bottom: var(--spacing-sm); font-size: var(--font-size-sm);">Поиск по названию</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Например: Худи..." style="width: 100%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: white; font-family: inherit;">
          </div>

          <div style="margin-bottom: var(--spacing-md);">
            <label style="display: block; color: var(--color-text-muted); margin-bottom: var(--spacing-sm); font-size: var(--font-size-sm);">Цена (тенге)</label>
            <div style="display: flex; gap: var(--spacing-sm);">
              <input type="number" name="price_from" value="<?= htmlspecialchars($price_from) ?>" placeholder="От" style="width: 50%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: white;">
              <input type="number" name="price_to" value="<?= htmlspecialchars($price_to) ?>" placeholder="До" style="width: 50%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: white;">
            </div>
          </div>

          <div style="margin-bottom: var(--spacing-lg);">
            <label style="display: block; color: var(--color-text-muted); margin-bottom: var(--spacing-sm); font-size: var(--font-size-sm);">Сортировка</label>
            <select name="sort" style="width: 100%; padding: 12px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 8px; color: white; cursor: pointer;">
              <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>По умолчанию (Новинки)</option>
              <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Сначала дешевые</option>
              <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Сначала дорогие</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; text-align: center; cursor: pointer; border: none; font-weight: 600;">Применить</button>
          <a href="catalog.php" style="display: block; text-align: center; margin-top: var(--spacing-md); color: var(--color-text-muted); font-size: var(--font-size-sm); text-decoration: none; transition: 0.2s;">Сбросить фильтры</a>
        </form>
      </aside>

<section style="flex-grow: 1;">
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
            <p style="color: var(--color-text-muted); grid-column: span 3; font-size: var(--font-size-md);">Ничего не найдено. Попробуйте изменить параметры фильтра.</p>
          <?php endif; ?>
        </div>
      </section>
                </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>