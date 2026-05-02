<?php
declare(strict_types=1);
require_once __DIR__ . '/_init.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$firstSetup = false;
$cntRes = $db->query('SELECT COUNT(*) AS c FROM admins');
$adminCount = $cntRes ? (int) $cntRes->fetch_assoc()['c'] : 0;
if ($adminCount === 0) {
    $firstSetup = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($firstSetup && isset($_POST['setup'])) {
        $u = trim((string) ($_POST['username'] ?? ''));
        $e = trim((string) ($_POST['email'] ?? ''));
        $p = (string) ($_POST['password'] ?? '');
        if ($u === '') {
            $error = 'Укажите логин.';
        } elseif ($p === '') {
            $error = 'Укажите пароль.';
        } else {
            $stmt = $db->prepare('INSERT INTO admins (username, `password`, email) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $u, $p, $e);
            if ($stmt->execute()) {
                $_SESSION['admin_id'] = $stmt->insert_id;
                $_SESSION['admin_username'] = $u;
                header('Location: index.php');
                exit;
            }
            $error = 'Не удалось создать администратора: ' . esc($db->error);
            $stmt->close();
        }
    } elseif (isset($_POST['login'])) {
        $u = trim((string) ($_POST['username'] ?? ''));
        $p = (string) ($_POST['password'] ?? '');

        $row = null;
        $stmt = $db->prepare('SELECT id, username FROM admins WHERE username = ? AND `password` = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('ss', $u, $p);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }

        if ($row === null
            && defined('ADMIN_PANEL_USERNAME') && defined('ADMIN_PANEL_PASSWORD')
            && ADMIN_PANEL_USERNAME !== '' && ADMIN_PANEL_PASSWORD !== ''
            && hash_equals(ADMIN_PANEL_USERNAME, $u) && hash_equals(ADMIN_PANEL_PASSWORD, $p)) {
            $stmt2 = $db->prepare('SELECT id, username FROM admins WHERE username = ? LIMIT 1');
            $stmt2->bind_param('s', ADMIN_PANEL_USERNAME);
            $stmt2->execute();
            $row = $stmt2->get_result()->fetch_assoc();
            $stmt2->close();
            if ($row === null) {
                $resFirst = $db->query('SELECT id, username FROM admins ORDER BY id ASC LIMIT 1');
                $row = $resFirst ? $resFirst->fetch_assoc() : null;
            }
        }

        if ($row !== null) {
            $_SESSION['admin_id'] = (int) $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header('Location: index.php');
            exit;
        }
        $error = 'Неверный логин или пароль.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #410cdf 0%, #6c4dff 100%);
            margin: 0;
        }
        .card {
            background: #fff;
            padding: 32px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 12px 40px rgba(0,0,0,.2);
        }
        h1 { font-size: 1.25rem; margin-bottom: 8px; color: #1a1a2e; }
        p.sub { color: #666; font-size: 0.9rem; margin-bottom: 20px; }
        label { display: block; margin-top: 14px; font-size: 0.88rem; font-weight: 500; }
        input {
            width: 100%;
            margin-top: 6px;
            padding: 10px 12px;
            border: 1px solid #d0d4e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            background: #410cdf;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover { background: #3509b5; }
        .err { background: #fde8e8; color: #c0392b; padding: 10px; border-radius: 8px; margin-bottom: 14px; font-size: 0.9rem; }
    </style>
</head>
<body>
<div class="card">
    <?php if ($firstSetup): ?>
        <h1>Первый вход</h1>
        <p class="sub">В таблице admins нет записей. Задайте логин, пароль (хранится в БД открытым текстом) и email. Дополнительно можно задать резервный вход в <strong>config.php</strong>.</p>
        <?php if ($error): ?><div class="err"><?= $error ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="setup" value="1">
            <label>Логин<input type="text" name="username" required autocomplete="username"></label>
            <label>Пароль<input type="password" name="password" required minlength="1" autocomplete="new-password"></label>
            <label>Email<input type="email" name="email" autocomplete="email"></label>
            <button type="submit">Создать и войти</button>
        </form>
    <?php else: ?>
        <h1>Вход</h1>
        <p class="sub">Вход по логину и паролю из таблицы <strong>admins</strong> или по резервным константам в <strong>config.php</strong>.</p>
        <?php if ($error): ?><div class="err"><?= $error ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="login" value="1">
            <label>Логин<input type="text" name="username" required autocomplete="username"></label>
            <label>Пароль<input type="password" name="password" required autocomplete="current-password"></label>
            <button type="submit">Войти</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
