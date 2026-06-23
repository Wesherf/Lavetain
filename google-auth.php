<?php
session_start();
require_once "config/db.php"; // Подключаем базу данных ($conn)

// Твои актуальные данные из Google Cloud Console
define("GOOGLE_CLIENT_ID", "615245111395-aa1bkp5rfkqnhbn43brnrh2ahju3s3kd.apps.googleusercontent.com");
define("GOOGLE_CLIENT_SECRET", "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");
// Оставляем только ОДИН правильный рабочий URI с .ru
define('GOOGLE_REDIRECT_URI',  'http://lavetain.ru/google-auth.php');

// Блок 1 — Перенаправление пользователя на страницу выбора аккаунта Google
if (!isset($_GET["code"])) {
    $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
        "client_id"     => GOOGLE_CLIENT_ID,
        "redirect_uri"  => GOOGLE_REDIRECT_URI,
        "response_type" => "code",
        "scope"         => "email profile",
        "access_type"   => "online",
    ]);

    header("Location: " . $url);
    exit;
}

// Блок 2 — Безопасный обмен полученного $_GET['code'] на реальный Access Token через cURL
$code = $_GET["code"];

$ch = curl_init("https://oauth2.googleapis.com/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    "code"          => $code,
    "client_id"     => GOOGLE_CLIENT_ID,
    "client_secret" => GOOGLE_CLIENT_SECRET,
    "redirect_uri"  => GOOGLE_REDIRECT_URI,
    "grant_type"    => "authorization_code",
]));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response, true);

// НАЧАЛО ОТЛАДКИ: Если токен не пришел, выводим реальный ответ от серверов Google
if (!isset($token["access_token"])) {
    echo "<h3>Ошибка авторизации Google OAuth</h3>";
    echo "<p>Google отклонил запрос. Вот что он пишет детально:</p>";
    echo "<pre>";
    print_r($token);
    echo "</pre>";
    echo "<p><b>Твой текущий REDIRECT_URI в коде:</b> " . GOOGLE_REDIRECT_URI . "</p>";
    exit;
}
// КОНЕЦ ОТЛАДКИ

$access_token = $token["access_token"];
// Блок 3 — Автоматическое получение данных вошедшего Google-пользователя
$ch = curl_init("https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $access_token);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$user_data = curl_exec($ch);
curl_close($ch);

$google_user = json_decode($user_data, true);

if (!isset($google_user["id"])) {
    die("Ошибка получения данных пользователя от Google.");
}

// Извлекаем НАСТОЯЩИЕ данные профиля из Google
$google_id  = $google_user["id"];
$avatar_url = $google_user["picture"] ?? null;
$name       = $google_user["name"] ?? "Google User";
$email      = $google_user["email"] ?? null;


// Блок 4 — Запись в базу данных ($conn)
// Проверяем, заходил ли этот аккаунт Google раньше
$stmt = $conn->prepare("SELECT id FROM users WHERE google_id = ?");
$stmt->bind_param("s", $google_id);
$stmt->execute();
$res = $stmt->get_result();
$existing = $res->fetch_assoc();
$stmt->close();

if ($existing) {
    // Если пользователь уже есть — обновляем данные
    $stmt = $conn->prepare("UPDATE users SET name = ?, avatar = ? WHERE google_id = ?");
    $stmt->bind_param("sss", $name, $avatar_url, $google_id);
    $stmt->execute();
    $user_id = $existing["id"];
    $stmt->close();
} else {
    // Если зашел впервые — создаем новую строчку (поле password уйдет в базу как NULL)
    $stmt = $conn->prepare("INSERT INTO users (name, email, google_id, avatar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $google_id, $avatar_url);
    $stmt->execute();
    $user_id = $conn->insert_id;
    $stmt->close();
}

// Записываем сессию авторизации
$_SESSION['user_id']     = $user_id;
$_SESSION['user_name']   = $name;
$_SESSION['user_avatar'] = $avatar_url;
$_SESSION['auth_type']   = 'google';

// Редирект в личный кабинет
header("Location: cabinet.php");
exit;