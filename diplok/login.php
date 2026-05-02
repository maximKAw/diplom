<?php
// login.php
session_start();
require_once 'config.php';

$error = '';

// Запоминаем страницу, с которой пришли (если есть)
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Введите email и пароль.';
    } else {
        $stmt = $db->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // Прямое сравнение паролей
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];

            // Если есть redirect, отправляем туда, иначе на главную
            if (!empty($redirect)) {
                header('Location: ' . $redirect);
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Неверный email или пароль.';
        }
    }
}

require_once 'header.php';
?>

<style>
/* стили для формы входа */
.login-container {
    max-width: 500px;
    margin: 3rem auto;
    background: #fff;
    border-radius: 24px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.login-title {
    font-size: 2rem;
    color: #1e3c72;
    margin-bottom: 1.5rem;
    text-align: center;
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #333;
}
.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 40px;
    font-size: 1rem;
    transition: border 0.2s;
}
.form-control:focus {
    outline: none;
    border-color: #1e3c72;
}
.btn-block {
    width: 100%;
    padding: 0.9rem;
    font-size: 1.1rem;
}
.btn-primary {
    background: #1e3c72;
    color: #fff;
    border: none;
}
.btn-primary:hover {
    background: #2a5298;
    transform: translateY(-2px);
}
.alert {
    padding: 1rem;
    border-radius: 40px;
    margin-bottom: 1.5rem;
    text-align: center;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.text-center {
    text-align: center;
    margin-top: 1rem;
}
</style>

<div class="login-container">
    <h1 class="login-title">Вход</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Войти</button>
    </form>
    <p class="text-center">
        Нет аккаунта? <a href="register.php" style="color:#1e3c72;">Зарегистрируйтесь</a>
    </p>
</div>

<?php require_once 'footer.php'; ?>