<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$message = '';
$error = '';

$stmt = $db->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $new_password2 = $_POST['new_password2'] ?? '';

    if ($username === '' || $email === '') {
        $error = 'Имя и email обязательны для заполнения.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email.';
    } else {
        $check = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $error = 'Этот email уже занят другим пользователем.';
        }
        $check->close();
    }

    if ($error === '' && ($new_password !== '' || $new_password2 !== '')) {
        if ($new_password !== $new_password2) {
            $error = 'Новый пароль и подтверждение не совпадают.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Пароль должен быть не короче 6 символов.';
        }
    }

    if ($error === '') {
        if ($new_password !== '') {
            $upd = $db->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $upd->bind_param("sssi", $username, $email, $new_password, $user_id);
        } else {
            $upd = $db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $upd->bind_param("ssi", $username, $email, $user_id);
        }
        if ($upd->execute()) {
            $_SESSION['user_name'] = $username;
            $_SESSION['user_email'] = $email;
            $message = 'Данные успешно сохранены.';
            $user['username'] = $username;
            $user['email'] = $email;
        } else {
            $error = 'Ошибка сохранения: ' . esc($db->error);
        }
        $upd->close();
    }
}
?>

<div class="app-shell app-shell--lift">
    <div class="app-shell__card">
        <form method="post" class="app-form" autocomplete="on">
            <?php if ($message): ?>
                <div class="app-alert app-alert--success"><?= esc($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="app-alert app-alert--error"><?= esc($error) ?></div>
            <?php endif; ?>

            <div class="app-form__section">
                <h2 class="app-form__section-title">Личные данные</h2>
                <div class="app-form__row">
                    <label for="username">Имя (отображается на сайте)</label>
                    <input type="text" name="username" id="username" value="<?= esc($user['username']) ?>" required maxlength="50">
                </div>
                <div class="app-form__row">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= esc($user['email']) ?>" required maxlength="100">
                </div>
                <?php if (!empty($user['created_at'])): ?>
                    <p class="hint">Регистрация: <?= esc(date('d.m.Y', strtotime($user['created_at']))) ?></p>
                <?php endif; ?>
            </div>

            <div class="app-form__section">
                <h2 class="app-form__section-title">Смена пароля</h2>
                <p class="hint" style="margin-bottom: 1rem;">Оставьте поля пустыми, если не хотите менять пароль.</p>
                <div class="app-form__row">
                    <label for="new_password">Новый пароль</label>
                    <input type="password" name="new_password" id="new_password" autocomplete="new-password" minlength="6" placeholder="Не меньше 6 символов">
                </div>
                <div class="app-form__row">
                    <label for="new_password2">Повторите пароль</label>
                    <input type="password" name="new_password2" id="new_password2" autocomplete="new-password" minlength="6" placeholder="Повторите новый пароль">
                </div>
            </div>

            <input type="hidden" name="save_profile" value="1">

            <div class="app-form__actions">
                <button type="submit" class="btn-app btn-app--primary"><i class="fas fa-save"></i> Сохранить</button>
                <a href="profile.php" class="btn-app btn-app--outline">К профилю</a>
                <a href="edit_avatar.php" class="btn-app btn-app--outline"><i class="fas fa-image"></i> Сменить аватар</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
