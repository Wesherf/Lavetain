<?php
session_start();
require_once 'config/db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: catalog.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: catalog.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['title']) ?> — Lavetain</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main style="padding: 50px 0; min-height: 75vh;">
  <div class="container" style="max-width: 1000px;">
    
    <div style="margin-bottom: 24px;">
      <a href="catalog.php" style="color: var(--color-text-muted); text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.color='var(--color-white)'" onmouseout="this.style.color='var(--color-text-muted)'">
        ← Вернуться в каталог
      </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
      
      <div style="background: #ffffff; border: 1px solid var(--color-border); padding: 10px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; height: 450px; overflow: hidden;">
        <?php if (!empty($product['image'])): ?>
          <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        <?php else: ?>
          <span style="color: #121214; font-weight: bold;">Изображение отсутствует</span>
        <?php endif; ?>
      </div>

      <div style="display: flex; flex-direction: column; gap: 20px;">
        <h1 style="color: var(--color-white); font-size: 2.4rem; font-weight: 800; margin: 0; line-height: 1.2;">
          <?= htmlspecialchars($product['title']) ?>
        </h1>
        
        <div style="font-size: 1.8rem; font-weight: 700; color: var(--color-primary);">
          <?= number_format($product['price'], 2, '.', ' ') ?> тенге.
        </div>

        <div style="border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); padding: 20px 0;">
          <h3 style="color: var(--color-white); font-size: 16px; margin-bottom: 10px; font-weight: 600;">Описание товара:</h3>
          <p style="color: var(--color-text-muted); line-height: 1.6; font-size: 14px; margin: 0;">
            <?= htmlspecialchars($product['description']) ?>
          </p>
        </div>

        <div>
          <a href="buy.php?id=<?= $product['id'] ?>" class="btn btn-primary" style="display: inline-block; padding: 14px 40px; text-decoration: none; font-weight: 600; text-align: center; border-radius: var(--radius-md);">
            Оформить заказ
          </a>
        </div>
      </div>

    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>