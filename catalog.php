<?php
session_start();
require_once 'config/db.php';

// Сбор параметров фильтрации
$search = trim($_GET['search'] ?? '');
$price_from = trim($_GET['price_from'] ?? '');
$price_to = trim($_GET['price_to'] ?? '');
$sort = trim($_GET['sort'] ?? 'default');

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($price_from)) {
    $where[] = "price >= ?";
    $params[] = floatval($price_from);
}

if (!empty($price_to)) {
    $where[] = "price <= ?";
    $params[] = floatval($price_to);
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$allowed_sort = [
    'default'   => 'id DESC',
    'price_asc' => 'price ASC',
    'price_desc'=> 'price DESC',
    'title_asc' => 'title ASC'
];
$sort_column = $allowed_sort[$sort] ?? 'id DESC';

$sql = "SELECT * FROM products $where_sql ORDER BY $sort_column";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Каталог — Lavetain</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main style="padding: 40px 0; min-height: 80vh;">
  <div class="container">
    <h1 style="color: var(--color-white); margin-bottom: 30px; font-size: 2rem; font-weight: 700;">Каталог</h1>

    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px; align-items: start;">
      
      <form method="GET" action="catalog.php" style="background: var(--color-surface); border: 1px solid var(--color-border); padding: 20px; border-radius: var(--radius-md); display: flex; flex-direction: column; gap: 16px;">
        
        <div>
          <label style="color: var(--color-text-muted); font-size: 13px; display: block; margin-bottom: 6px;">Поиск по названию</label>
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Например: Худи..." style="width: 100%; padding: 10px; background: #121214; border: 1px solid var(--color-border); border-radius: var(--radius-sm); color: var(--color-white); box-sizing: border-box; font-size: 14px;">
        </div>

        <div>
          <label style="color: var(--color-text-muted); font-size: 13px; display: block; margin-bottom: 6px;">Цена (тенге)</label>
          <div style="display: flex; gap: 8px;">
            <input type="number" name="price_from" value="<?= htmlspecialchars($price_from) ?>" placeholder="От" style="width: 50%; padding: 10px; background: #121214; border: 1px solid var(--color-border); border-radius: var(--radius-sm); color: var(--color-white); box-sizing: border-box; font-size: 14px;">
            <input type="number" name="price_to" value="<?= htmlspecialchars($price_to) ?>" placeholder="До" style="width: 50%; padding: 10px; background: #121214; border: 1px solid var(--color-border); border-radius: var(--radius-sm); color: var(--color-white); box-sizing: border-box; font-size: 14px;">
          </div>
        </div>

        <div>
          <label style="color: var(--color-text-muted); font-size: 13px; display: block; margin-bottom: 6px;">Сортировка</label>
          <select name="sort" style="width: 100%; padding: 10px; background: #121214; border: 1px solid var(--color-border); border-radius: var(--radius-sm); color: var(--color-white); box-sizing: border-box; font-size: 14px;">
            <option value="default" <?= $sort==='default' ? 'selected' : '' ?>>По умолчанию (Новинки)</option>
            <option value="price_asc" <?= $sort==='price_asc' ? 'selected' : '' ?>>Цена: от меньшей к большей</option>
            <option value="price_desc" <?= $sort==='price_desc' ? 'selected' : '' ?>>Цена: от большей к меньшей</option>
            <option value="title_asc" <?= $sort==='title_asc' ? 'selected' : '' ?>>По алфавиту</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px 0; font-weight: 600; margin-top: 8px;">Применить</button>
        
        <a href="catalog.php" style="text-align: center; color: var(--color-text-muted); text-decoration: none; font-size: 13px; margin-top: 4px;" onmouseover="this.style.color='var(--color-white)'" onmouseout="this.style.color='var(--color-text-muted)'">Сбросить фильтры</a>
      </form>

      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $item): ?>
            
            <div style="background: var(--color-surface); border: 1px solid var(--color-border); padding: 16px; border-radius: var(--radius-md); display: flex; flex-direction: column; justify-content: space-between; min-height: 470px;">
              
              <div>
                <div style="background: #ffffff; height: 240px; border-radius: var(--radius-sm); margin-bottom: 14px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                  <?php if (!empty($item['image'])): ?>
                    <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                  <?php else: ?>
                    <span style="color: #121214; font-weight: 800; font-size: 20px;">LAVETAIN</span>
                  <?php endif; ?>
                </div>  
                
                <h3 style="font-size: 15px; color: var(--color-white); margin-bottom: 6px; font-weight: 600;"><?= htmlspecialchars($item['title']) ?></h3>
                <p style="color: var(--color-text-muted); font-size: 13px; margin-bottom: 12px; line-height: 1.4; min-height: 36px;"><?= htmlspecialchars($item['description']) ?></p>
              </div>

              <div>
                <div style="font-size: 1.25rem; font-weight: 700; color: var(--color-primary); margin-bottom: 14px;">
                  <?= number_format($item['price'], 2, '.', ' ') ?> тенге.
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                  <a href="detail.php?id=<?= $item['id'] ?>" style="width: 100%; text-align: center; box-sizing: border-box; text-decoration: none; font-weight: 600; display: block; padding: 10px 0; background: #121214; border: 1px solid var(--color-border); color: var(--color-white); border-radius: var(--radius-md); font-size: 13px; transition: background 0.2s;" onmouseover="this.style.background='#1e1e21'" onmouseout="this.style.background='#121214'">
                    Подробнее
                  </a>
                  <a href="buy.php?id=<?= $item['id'] ?>" class="btn btn-primary" style="width: 100%; text-align: center; box-sizing: border-box; text-decoration: none; font-weight: 600; display: block; padding: 10px 0; font-size: 13px;">
                    Купить
                  </a>
                </div>
              </div>

            </div>

          <?php endforeach; ?>
        <?php else: ?>
          <p style="color: var(--color-text-muted); grid-column: span 3; text-align: center; padding: 40px 0;">Ничего не найдено.</p>
        <?php endif; ?>
      </div>

    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>