<?php
session_start();
require_once "config/db.php"; // Подключаем твою базу данных ($conn)

// Секретный токен от BotFather (со скриншота)
define('BOT_TOKEN', '8927906011:AAGsTp-bWPHuh4pqYESqhunOClh7zAESZSQ');

// Если пользователь вообще не вошел в аккаунт, отправляем его на login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$auth_data = $_GET;

if (!isset($auth_data['hash'])) {
    die("Ошибка: Нет данных от Telegram.");
}

// Функция валидации цифровой подписи Telegram
function checkTelegramAuthorization($auth_data) {
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr); 
    $data_check_string = implode("\n", $data_check_arr);
    
    $secret_key = hash('sha256', BOT_TOKEN, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    
    if (strcmp($hash, $check_hash) !== 0) {
        throw new Exception('Внимание: Данные Telegram не прошли проверку.');
    }
    
    if ((time() - $auth_data['auth_date']) > 86400) {
        throw new Exception('Сессия авторизации Telegram устарела.');
    }
    
    return $auth_data;
}

try {
    $tg_user = checkTelegramAuthorization($auth_data);
    
    $current_user_id = $_SESSION['user_id'];
    $telegram_id = $tg_user['id'];
    $avatar = $tg_user['photo_url'] ?? null;
    $name = $tg_user['first_name'] . (isset($tg_user['last_name']) ? ' ' . $tg_user['last_name'] : '');

    // 1. Проверяем, не привязан ли этот Telegram уже к ДРУГОМУ аккаунту (как на Рисунке 16)
    $stmt = $conn->prepare("SELECT id FROM users WHERE telegram_id = ? AND id != ?");
    $stmt->bind_param("si", $telegram_id, $current_user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: cabinet.php?error=" . urlencode("Этот Telegram уже привязан к другому аккаунту."));
        exit;
    }
    $stmt->close();

    // 2. Привязываем: делаем UPDATE (как на Рисунке 16)
    $stmt = $conn->prepare("UPDATE users SET telegram_id = ?, avatar = COALESCE(avatar, ?) WHERE id = ?");
    $stmt->bind_param("ssi", $telegram_id, $avatar, $current_user_id);
    $stmt->execute();
    $stmt->close();

    // Обновляем данные сессии
    $_SESSION["user_avatar"] = $avatar;
    $_SESSION["auth_type"]   = "telegram";

    header("Location: cabinet.php?success=" . urlencode("Telegram успешно привязан!"));
    exit;

} catch (Exception $e) {
    header("Location: cabinet.php?error=" . urlencode($e->getMessage()));
    exit;
}