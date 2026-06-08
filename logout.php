<?php
session_start();

// Полностью уничтожаем сессию
session_unset();
session_destroy();

// Перенаправляем на главную страницу
header('Location: index.php');
exit;