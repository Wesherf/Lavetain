<?php
<<<<<<< HEAD
// 1. Инициализация сессии и базы данных
session_start();
require_once "config/db.php"; 

// 2. Определение пользователя
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}
$user_id = $_SESSION['user_id'];

// 3. Обработка отмены записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {
    $appointment_id = intval($_POST['appointment_id'] ?? 0);

    if ($appointment_id > 0) {
        $stmt = $conn->prepare("UPDATE lavetain_appointments SET status = ? WHERE id = ? AND user_id = ?");
        if ($stmt) {
            $new_status = 'cancelled';
            $stmt->bind_param("sii", $new_status, $appointment_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: cabinet.php');
    exit;
}

// 4. Получение записей для таблицы
$app_res = $conn->query("SELECT id, status, service_type, chest_cm, waist_cm, hips_cm, visit_date FROM lavetain_appointments WHERE user_id = $user_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LAVETAIN — Личный кабинет</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #0a0a0a; color: #fff; display: flex; flex-direction: column; min-height: 100vh; }
        .navbar { background: #000; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a1a1a; }
        .navbar .logo { font-size: 22px; font-weight: bold; letter-spacing: 3px; color: #fff; text-decoration: none; }
        .navbar-links { display: flex; gap: 20px; align-items: center; }
        .navbar-links a { color: #fff; text-decoration: none; font-size: 14px; text-transform: uppercase; }
        .btn-logout { background: #7f56da; padding: 8px 18px; border-radius: 6px; font-weight: bold; }
        .btn-logout:hover { background: #6939cc; }
        
        .main-content { flex: 1; max-width: 1100px; width: 100%; margin: 40px auto; padding: 0 20px; box-sizing: border-box; }
        .profile-header { text-align: center; margin-bottom: 40px; }
        .profile-header h1 { font-size: 32px; font-weight: 500; margin: 0 0 10px 0; }
        .profile-header p { color: #666; margin: 0; font-size: 14px; }
        
        /* Сетка для блоков Личные данные и Безопасность */
        .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .card { background: #111; border: 1px solid #222; padding: 30px; border-radius: 12px; }
        .card h3 { margin-top: 0; font-size: 20px; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid #222; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; gap: 8px; }
        label { font-size: 12px; color: #555; text-transform: uppercase; letter-spacing: 1px; }
        input { padding: 12px; background: #161616; border: 1px solid #2c2c2c; color: #fff; border-radius: 6px; font-size: 14px; }
        input:disabled { color: #444; }
        
        .btn-purple { background: #7f56da; color: #fff; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; transition: 0.2s; width: 100%; }
        .btn-purple:hover { background: #6939cc; }
        .btn-white { background: #fff; color: #000; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; transition: 0.2s; width: 100%; }
        .btn-white:hover { background: #e0e0e0; }
        
        /* Широкие блоки снизу */
        .wide-card { background: #111; border: 1px solid #222; padding: 30px; border-radius: 12px; margin-bottom: 30px; }
        .wide-card h3 { margin-top: 0; font-size: 20px; font-weight: 500; margin-bottom: 15px; }
        .wide-card p { color: #666; font-size: 14px; margin: 0; }
        
        /* Таблица для услуг */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; color: #ccc; font-size: 14px; }
        th { padding: 12px; border: 1px solid #222; background: #161616; text-align: left; color: #888; font-size: 11px; text-transform: uppercase; }
        td { padding: 12px; border: 1px solid #222; }
        
        .btn-link { display: inline-block; margin-top: 15px; background: #161616; border: 1px solid #222; color: #fff; text-decoration: none; padding: 10px 20px; font-size: 13px; font-weight: bold; border-radius: 6px; text-transform: uppercase; }
        .btn-link:hover { background: #222; }
        
        footer { background: #000; text-align: center; padding: 25px; border-top: 1px solid #1a1a1a; color: #444; font-size: 13px; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php" class="logo">LAVETAIN</a>
    <div class="navbar-links">
        <a href="index.php" style="color: #666;">Главная</a>
        <a href="index.php">Каталог одежды</a>
        <a href="contact.php" style="color: #7f56da; font-weight: bold; margin-left: 10px;">Обратная связь</a>
        <span style="color: #666; margin-left: 15px;">Привет, <span style="color: #fff; font-weight: bold;">dsa1</span></span>
        <a href="index.php" style="color: #aaa; margin-left: 10px;">Профиль</a>
        <a href="logout.php" class="btn-logout">Выйти</a>
    </div>
</div>

<div class="main-content">
    
    <div class="profile-header">
        <h1>Личный кабинет</h1>
        <p>Вы с нами с: 25.05.2026 в 13:15</p>
    </div>
    
    <div class="profile-grid">
        <div class="card">
            <h3>Личные данные</h3>
            <form action="#" method="POST">
                <div class="form-group">
                    <label>Ваше имя в системе</label>
                    <input type="text" value="dsa1">
                </div>
                <div class="form-group">
                    <label>Email (изменению не подлежит)</label>
                    <input type="email" value="dsa1@gmail.com" disabled>
                </div>
                <button type="button" class="btn-purple">Сохранить изменения</button>
            </form>
        </div>
        
        <div class="card">
            <h3>Безопасность</h3>
            <form action="#" method="POST">
                <div class="form-group">
                    <input type="password" placeholder="Текущий пароль">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Новый пароль (от 6 симв.)">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Повторите новый пароль">
                </div>
                <button type="button" class="btn-white">Обновить пароль</button>
            </form>
        </div>
    </div>

    <div class="wide-card" style="border-left: 4px solid #7f56da;">
        <h3 style="color: #fff; margin-bottom: 10px;">Мои записи на примерку (Услуги в БД)</h3>
        
        <?php if ($app_res && $app_res->num_rows > 0): ?>
            <table>
    <tr>
        <th>Услуга</th>
        <th>Грудь</th>
        <th>Талия</th>
        <th>Бёдра</th>
        <th>Дата визита</th>
        <th>Действие</th> </tr>
    <?php while($row = $app_res->fetch_assoc()): ?>
        <tr style="border-bottom: 1px solid #1a1a1a;">
            <td style="color:#fff;"><?= htmlspecialchars($row['service_type']) ?></td>
            <td><?= $row['chest_cm'] ?> см</td>
            <td style="color: #7f56da; font-weight: bold;"><?= $row['waist_cm'] ?> см</td>
            <td><?= $row['hips_cm'] ?> см</td>
            <td style="color: #7f56da;"><?= $row['visit_date'] ?></td>
            
            <td>
                <?php 
                // Если статус 'new', 'pending' или пустой (еще не задан в базе) — показываем кнопку
                $status = $row['status'] ?? 'pending';
                if ($status === 'pending' || $status === 'new' || $status === ''): 
                ?>
                    <form method="POST" action="cabinet.php" onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');" style="margin: 0;">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="cancel_appointment" style="background-color: #e74c3c; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; font-size: 12px;">
                            Отменить
                        </button>
                    </form>
                <?php else: ?>
                    <span style="color: #666; font-size: 13px;">Отменена</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
            <a href="booking.php" class="btn-link">Записаться на повторные замеры</a>
        <?php else: ?>
            <p style="margin-bottom: 15px;">Вы ещё не записывались на подбор размера и примерку одежды.</p>
            <a href="booking.php" class="btn-purple" style="display: inline-block; width: auto; padding: 12px 25px;">Заполнить обхват талии и замеры →</a>
        <?php endif; ?>
    </div>

    <div class="wide-card">
        <h3>История ваших покупок</h3>
        <p>Вы еще не оформляли заказы в каталоге одежды Lavetain.</p>
    </div>

</div>

<footer>
    © 2026 Lavetain · Магазин одежды нового поколения
</footer>

=======
session_start();
require_once "config/db.php"; // Подключение (универсальный PDO + MySQLi)

// Проверяем авторизацию. Если сессии нет, для теста ставим ID = 1 (аккаунт dsa1)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}
$user_id = $_SESSION['user_id'];

// --- ОБРАБОТКА POST-ЗАПРОСА НА ОТМЕНУ ЗАПИСИ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {
    $appointment_id = intval($_POST['appointment_id'] ?? 0);

    if ($appointment_id > 0) {
        // Безопасный UPDATE: проверяем ID записи И ID текущего пользователя одновременно!
        $stmt = $conn->prepare("UPDATE lavetain_appointments SET status = 'cancelled' WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $appointment_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Перезагружаем страницу, чтобы пользователь сразу увидел обновленный статус
    header("Location: cabinet.php");
    exit;
}

// Запрос для получения записей на примерку (добавили id и status в выборку)
$app_res = $conn->query("SELECT id, service_type, chest_cm, waist_cm, hips_cm, visit_date, status FROM lavetain_appointments WHERE user_id = $user_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LAVETAIN — Личный кабинет</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #0a0a0a; color: #fff; display: flex; flex-direction: column; min-height: 100vh; }
        .navbar { background: #000; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a1a1a; }
        .navbar .logo { font-size: 22px; font-weight: bold; letter-spacing: 3px; color: #fff; text-decoration: none; }
        .navbar-links { display: flex; gap: 20px; align-items: center; }
        .navbar-links a { color: #fff; text-decoration: none; font-size: 14px; text-transform: uppercase; }
        .btn-logout { background: #7f56da; padding: 8px 18px; border-radius: 6px; font-weight: bold; }
        .btn-logout:hover { background: #6939cc; }
        
        .main-content { flex: 1; max-width: 1100px; width: 100%; margin: 40px auto; padding: 0 20px; box-sizing: border-box; }
        .profile-header { text-align: center; margin-bottom: 40px; }
        .profile-header h1 { font-size: 32px; font-weight: 500; margin: 0 0 10px 0; }
        .profile-header p { color: #666; margin: 0; font-size: 14px; }
        
        .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .card { background: #111; border: 1px solid #222; padding: 30px; border-radius: 12px; }
        .card h3 { margin-top: 0; font-size: 20px; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid #222; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; gap: 8px; }
        label { font-size: 12px; color: #555; text-transform: uppercase; letter-spacing: 1px; }
        input { padding: 12px; background: #161616; border: 1px solid #2c2c2c; color: #fff; border-radius: 6px; font-size: 14px; }
        input:disabled { color: #444; }
        
        .btn-purple { background: #7f56da; color: #fff; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; transition: 0.2s; width: 100%; }
        .btn-purple:hover { background: #6939cc; }
        .btn-white { background: #fff; color: #000; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; transition: 0.2s; width: 100%; }
        .btn-white:hover { background: #e0e0e0; }
        
        .wide-card { background: #111; border: 1px solid #222; padding: 30px; border-radius: 12px; margin-bottom: 30px; }
        .wide-card h3 { margin-top: 0; font-size: 20px; font-weight: 500; margin-bottom: 15px; }
        .wide-card p { color: #666; font-size: 14px; margin: 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; color: #ccc; font-size: 14px; }
        th { padding: 12px; border: 1px solid #222; background: #161616; text-align: left; color: #888; font-size: 11px; text-transform: uppercase; }
        td { padding: 12px; border: 1px solid #222; vertical-align: middle; }
        
        .btn-link { display: inline-block; margin-top: 15px; background: #161616; border: 1px solid #222; color: #fff; text-decoration: none; padding: 10px 20px; font-size: 13px; font-weight: bold; border-radius: 6px; text-transform: uppercase; }
        .btn-link:hover { background: #222; }
        
        /* Стили кнопок и красивых бэджей статусов */
        .btn-danger { background: none; border: 1px solid #ef4444; color: #ef4444; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: 0.2s; text-transform: uppercase; }
        .btn-danger:hover { background: #ef4444; color: #fff; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pending { background: rgba(234, 179, 8, 0.15); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.3); }
        .status-confirmed { background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }
        .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

        footer { background: #000; text-align: center; padding: 25px; border-top: 1px solid #1a1a1a; color: #444; font-size: 13px; }
    </style>
</head>
<body>
<main class="main-container" style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <?php if (isset($_GET['success'])): ?>
        <div style="background: rgba(0, 229, 204, 0.1); border: 1px solid #00E5CC; color: #00E5CC; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center;">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div style="background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; color: #e74c3c; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>
    <h1 style="text-align: center; color: #F2F2F2; margin-bottom: 10px;">Личный кабинет</h1>
    <p style="text-align: center; color: #666; font-size: 14px; margin-bottom: 30px;">Вы с нами с 25.05.2026 в 13:15</p>

    <div class="grid-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="card">
            </div>

        <div class="card">
            </div>
    </div>

    </main>
<div class="navbar">
    <a href="index.php" class="logo">LAVETAIN</a>
    <div class="navbar-links">
        <a href="index.php" style="color: #666;">Главная</a>
        <a href="index.php">Каталог одежды</a>
        <a href="contact.php" style="color: #7f56da; font-weight: bold; margin-left: 10px;">Обратная связь</a>
        <span style="color: #666; margin-left: 15px;">Привет, <span style="color: #fff; font-weight: bold;">dsa1</span></span>
         	
        <a href="index.php" style="color: #aaa; margin-left: 10px;">Профиль</a>
        <a href="logout.php" class="btn-logout">Выйти</a>
    </div>
</div>

<div class="main-content">
    
    <div class="profile-header">
        <h1>Личный кабинет</h1>
        <p>Вы с нами с: 25.05.2026 в 13:15</p>
    </div>
    
    <div class="profile-grid">
        <div class="card">
            <h3>Личные данные</h3>
            <form action="#" method="POST">
                <div class="form-group">
                    <label>Ваше имя в системе</label>
                    <input type="text" value="dsa1">
                </div>
                <div class="form-group">
                    <label>Email (изменению не подлежит)</label>
                    <input type="email" value="dsa1@gmail.com" disabled>
                </div>
                <button type="button" class="btn-purple">Сохранить изменения</button>
            </form>
        </div>
        
       <div class="card">
    <h3>Безопасность</h3>
    <div style="margin-top: 20px; border-top: 1px solid #2A2A2A; padding-top: 15px;">
    <?php if (isset($_SESSION['auth_type']) && $_SESSION['auth_type'] === 'google'): ?>
        <p style="color: #00E5CC; font-size: 14px; display: flex; align-items: center; gap: 8px;">
            <span style="width: 8px; height: 8px; background: #00E5CC; border-radius: 50%;"></span>
            Аккаунт Google успешно привязан
        </p>
    <?php else: ?>
        <a href="google-auth.php" style="display: inline-flex; align-items: center; justify-content: center; background-color: #161616; color: #F2F2F2; border: 1px solid #2A2A2A; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; transition: background 0.3s; width: 100%; box-sizing: border-box;">
            <svg style="width: 16px; height: 16px; margin-right: 10px;" viewBox="0 0 24 24">
                <path fill="#F2F2F2" d="M12.24 10.285V14.4h6.887c-.275 1.565-1.88 4.604-6.887 4.604-4.33 0-7.866-3.577-7.866-8s3.536-8 7.866-8c2.46 0 4.105 1.025 5.047 1.926l3.227-3.11C18.28 1.845 15.548 1 12.24 1 5.48 1 0 6.48 0 13.2s5.48 12.2 12.24 12.2c7.055 0 11.75-4.96 11.75-11.96 0-.81-.08-1.425-.195-2.155H12.24z"/>
            </svg>
            Привязать Google-аккаунт
        </a>
    <?php endif; ?>
</div>
<div class="card" style="margin-top: 20px;">
    <h3>Способы входа (Интеграция)</h3>
    <p style="color: var(--color-text-muted); font-size: 14px; margin-bottom: 15px;">Управляйте привязкой внешних аккаунтов для беспарольного входа.</p>

    <div style="display: flex; flex-direction: column; gap: 12px;">
        
        <div>
            <?php if (isset($_SESSION['auth_type']) && $_SESSION['auth_type'] === 'google'): ?>
                <div style="background: rgba(0, 229, 204, 0.1); border: 1px solid #00E5CC; padding: 10px; border-radius: 6px; color: #00E5CC; font-size: 14px;">
                    ✓ Аккаунт Google успешно подключен
                </div>
            <?php else: ?>
                <a href="google-auth.php" style="display: inline-flex; align-items: center; justify-content: center; background-color: #161616; color: #F2F2F2; border: 1px solid #2A2A2A; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; width: 100%; box-sizing: border-box;">
                    Привязать аккаунт Google
                </a>
            <?php endif; ?>
        </div>

        <div>
            <?php if (isset($_SESSION['auth_type']) && $_SESSION['auth_type'] === 'telegram'): ?>
                <div style="background: rgba(0, 229, 204, 0.1); border: 1px solid #00E5CC; padding: 10px; border-radius: 6px; color: #00E5CC; font-size: 14px;">
                    ✓ Аккаунт Telegram успешно подключен
                </div>
            <?php else: ?>
                <div style="text-align: center; background: #161616; padding: 10px; border-radius: 6px; border: 1px solid #2A2A2A;">
                    <span style="display: block; color: var(--color-text-muted); font-size: 13px; margin-bottom: 8px;">Привязать Telegram:</span>
                    <script async src="https://telegram.org/js/telegram-widget.js?22" 
                            data-telegram-login="truew_auth_bot" 
                            data-size="large" 
                            data-auth-url="http://lavetain.ru/telegram-auth.php" 
                            data-request-access="write">
                    </script>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
    <?php if (isset($_SESSION['auth_type']) && $_SESSION['auth_type'] === 'google'): ?>
        <p style="color: #666; font-size: 14px; margin-top: 15px;">
            Вы авторизованы через Google. Управление паролем в нашей системе недоступно.
        </p>
    <?php else: ?>
        <form action="#" method="POST">
            <div class="form-group">
                <input type="password" placeholder="Текущий пароль">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Новый пароль (от 6 симв.)">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Повторите новый пароль">
            </div>
            <button type="button" class="btn-white">Обновить пароль</button>
        </form>
    <?php endif; ?>
</div>
    <div class="wide-card" style="border-left: 4px solid #7f56da;">
        <h3 style="color: #fff; margin-bottom: 10px;">Мои записи на примерку (Услуги в БД)</h3>
        
        <?php if ($app_res && $app_res->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Услуга</th>
                    <th>Грудь</th>
                    <th>Талия</th>
                    <th>Бёдра</th>
                    <th>Дата визита</th>
                    <th>Статус</th>
                    <th>Действие</th>
                </tr>
                <?php while($row = $app_res->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #1a1a1a;">
                        <td style="color:#fff;"><?= htmlspecialchars($row['service_type']) ?></td>
                        <td><?= intval($row['chest_cm']) ?> см</td>
                        <td style="color: #7f56da; font-weight: bold;"><?= intval($row['waist_cm']) ?> см</td>
                        <td><?= intval($row['hips_cm']) ?> см</td>
                        <td style="color: #7f56da;"><?= htmlspecialchars($row['visit_date']) ?></td>
                        <td>
                            <?php if (!isset($row['status']) || $row['status'] === 'pending'): ?>
                                <span class="status-badge status-pending">Ожидает</span>
                            <?php elseif ($row['status'] === 'confirmed'): ?>
                                <span class="status-badge status-confirmed">Подтверждено</span>
                            <?php else: ?>
                                <span class="status-badge status-cancelled">Отменено</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!isset($row['status']) || $row['status'] === 'pending'): ?>
                                <form method="POST" onsubmit="return confirm('Вы уверены, что хотите отменить эту запись на примерку?');" style="margin:0;">
                                    <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="cancel_appointment" class="btn-danger">Отменить</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #444;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
            <a href="booking.php" class="btn-link">Записаться на повторные замеры</a>
        <?php else: ?>
            <p style="margin-bottom: 15px;">Вы ещё не записывались на подбор размера и примерку одежды.</p>
            <a href="booking.php" class="btn-purple" style="display: inline-block; width: auto; padding: 12px 25px;">Заполнить обхват талии и замеры →</a>
        <?php endif; ?>
    </div>

    <div class="wide-card">
        <h3>История ваших покупок</h3>
        <p>Вы еще не оформляли заказы в каталоге одежды Lavetain.</p>
    </div>

</div>

<footer>
    © 2026 Lavetain · Магазин одежды нового поколения
</footer>

</body>
</html>