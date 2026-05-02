<?php
require_once __DIR__ . '/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$message = '';
$error = '';

$avatar = null;
$stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $avatar = $row['avatar'];
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['avatar']['type'] ?? '';
        if (!in_array($file_type, $allowed_types, true)) {
            $error = 'Можно загружать только изображения (JPEG, PNG, GIF, WEBP)';
        } else {
            $target_dir = 'uploads/avatars/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $user_id . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $filename;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                if ($avatar && file_exists(__DIR__ . '/' . $avatar) && $avatar !== $target_file) {
                    @unlink(__DIR__ . '/' . $avatar);
                }

                $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $target_file, $user_id);
                    if ($stmt->execute()) {
                        $message = 'Аватар успешно обновлён!';
                        $avatar = $target_file;
                    } else {
                        $error = 'Ошибка базы данных';
                    }
                    $stmt->close();
                } else {
                    $error = 'Ошибка подготовки запроса';
                }
            } else {
                $error = 'Ошибка загрузки файла';
            }
        }
    } else {
        $error = 'Выберите файл для загрузки';
    }
}
?>

<div class="app-shell app-shell--lift">
    <div class="app-shell__card">
        <?php if ($message): ?>
            <div class="app-alert app-alert--success"><?= esc($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="app-alert app-alert--error"><?= esc($error) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="app-form app-form--avatar" autocomplete="off">
            <div class="app-avatar-row">
                <div class="app-avatar-row__preview">
                    <?php if (!empty($avatar) && file_exists(__DIR__ . '/' . $avatar)): ?>
                        <img src="<?= esc($avatar) ?>" alt="" class="app-avatar-row__img">
                    <?php else: ?>
                        <div class="app-avatar-row__placeholder" aria-hidden="true"><i class="fas fa-user-circle"></i></div>
                    <?php endif; ?>
                </div>
                <div class="app-avatar-row__meta">
                    <p class="app-avatar-row__label">Текущее фото</p>
                    <p class="hint">Форматы: JPEG, PNG, GIF или WEBP.</p>
                </div>
            </div>

            <div class="app-form__row">
                <label for="avatar">Новое изображение</label>
                <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/gif,image/webp" required>
            </div>

            <div class="app-form__actions">
                <button type="submit" class="btn-app btn-app--primary"><i class="fas fa-upload"></i> Загрузить</button>
                <a href="profile.php" class="btn-app btn-app--outline">К профилю</a>
                <a href="edit_profile.php" class="btn-app btn-app--outline"><i class="fas fa-user-edit"></i> Данные и пароль</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
