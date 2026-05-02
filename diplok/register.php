<?php
// register.php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Все поля обязательны для заполнения.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email.';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают.';
    } else {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Пользователь с таким email уже зарегистрирован.';
        } else {
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $username;
                $_SESSION['user_email'] = $email;
                header('Location: index.php');
                exit;
            } else {
                $error = 'Ошибка регистрации: ' . $db->error;
            }
        }
        $stmt->close();
    }
}

require_once 'header.php';
?>

<style>
/* Стили для формы регистрации */
.register-container {
    max-width: 500px;
    margin: 3rem auto;
    background: #fff;
    border-radius: 24px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.register-title {
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
.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.text-center {
    text-align: center;
    margin-top: 1rem;
}
</style>

<div class="register-container">
    <h1 class="register-title">Регистрация</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="username">Имя пользователя</label>
            <input type="text" id="username" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Подтверждение пароля</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
    </form>
    <p class="text-center">
        Уже есть аккаунт? <a href="login.php" style="color:#1e3c72;">Войдите</a>
    </p>
</div>

<?php require_once 'footer.php'; ?>