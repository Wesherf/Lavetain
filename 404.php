<?php
session_start();

// ВАЖНО: отправляем правильный HTTP-заголовок 404
// Без этой строки страница вернёт код 200 (OK) — неправильно
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Страница не найдена — CutTime</title>
  <link rel="stylesheet" href="/css/reset.css">
  <link rel="stylesheet" href="/css/variables.css">
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>

<main class="section">
  <div class="container">
    <div style="text-align:center;padding:60px 0;">

      <!-- Большая цифра 404 -->
      <div style="font-size:120px;font-weight:900;
                   color:var(--color-border);line-height:1;">
        404
      </div>

      <h1 style="font-size:var(--font-size-xl);font-weight:700;
                  margin:var(--spacing-lg) 0 var(--spacing-md);">
        Страница не найдена
      </h1>

      <p style="color:var(--color-text-muted);font-size:var(--font-size-lg);
                 margin-bottom:var(--spacing-xl);">
        Возможно, страница была удалена или вы перешли по неверной ссылке.
      </p>

      <!-- Кнопки навигации -->
      <div style="display:flex;gap:var(--spacing-md);
                   justify-content:center;flex-wrap:wrap;">
        <a href="/" class="btn btn-primary"
           style="max-width:200px;">
          На главную
        </a>
        <a href="catalog.php" class="btn btn-outline"
           style="max-width:200px;">
          В каталог
        </a>
      </div>

    </div>
  </div>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
