<?php
declare(strict_types=1);
require_once __DIR__ . '/_init.php';
require_once __DIR__ . '/schemas.php';
admin_require_login();
require_once __DIR__ . '/layout.php';

$table = preg_replace('/[^a-z0-9_]/', '', (string) ($_GET['table'] ?? ''));
$schema = $table !== '' ? admin_get_schema($table) : null;
if (!$schema) {
    http_response_code(404);
    echo 'Неизвестная таблица';
    exit;
}

$pk = $schema['pk'];
$label = $schema['label'];
$flashOk = '';
$flashErr = '';

function admin_bind_params(mysqli_stmt $stmt, string $types, array $values): bool
{
    $refs = [];
    foreach ($values as $k => $_) {
        $refs[$k] = &$values[$k];
    }
    array_unshift($refs, $types);
    return call_user_func_array([$stmt, 'bind_param'], $refs);
}

/** @param mysqli $db */
function admin_collect_post_values(array $schema, mysqli $db, bool $isEdit, ?array $row, string $table): array
{
    $out = ['values' => [], 'types' => '', 'error' => '', 'columns' => []];
    $fields = $schema['fields'];

    foreach ($fields as $col => $meta) {
        $type = $meta['type'];
        if ($type === 'admin_password') {
            continue;
        }
        if ($type === 'user_password' && $isEdit) {
            $pw = (string) ($_POST['password'] ?? '');
            if ($pw === '') {
                continue;
            }
            $out['values'][] = $pw;
            $out['types'] .= 's';
            $out['columns'][] = 'password';
            continue;
        }
        if ($type === 'user_password' && !$isEdit) {
            $pw = (string) ($_POST['password'] ?? '');
            if ($pw === '') {
                $out['error'] = 'Для нового пользователя укажите пароль.';
                return $out;
            }
            $out['values'][] = $pw;
            $out['types'] .= 's';
            $out['columns'][] = 'password';
            continue;
        }

        $raw = $_POST['f'][$col] ?? null;
        if ($type === 'bool') {
            $val = isset($_POST['f'][$col]) && $_POST['f'][$col] === '1' ? 1 : 0;
            $out['values'][] = $val;
            $out['types'] .= 'i';
            $out['columns'][] = $col;
            continue;
        }
        if ($type === 'int') {
            $s = trim((string) $raw);
            if ($s === '' && !empty($meta['nullable'])) {
                $out['values'][] = null;
                $out['types'] .= 's';
                $out['columns'][] = $col;
                continue;
            }
            if ($s === '' && !empty($meta['required'])) {
                $out['error'] = 'Заполните обязательные числовые поля.';
                return $out;
            }
            $out['values'][] = (int) $s;
            $out['types'] .= 'i';
            $out['columns'][] = $col;
            continue;
        }
        if ($type === 'float') {
            $out['values'][] = (float) str_replace(',', '.', (string) $raw);
            $out['types'] .= 'd';
            $out['columns'][] = $col;
            continue;
        }
        if ($type === 'decimal') {
            $out['values'][] = (string) $raw;
            $out['types'] .= 's';
            $out['columns'][] = $col;
            continue;
        }
        if ($type === 'select') {
            $out['values'][] = (string) $raw;
            $out['types'] .= 's';
            $out['columns'][] = $col;
            continue;
        }
        if ($type === 'json') {
            $s = trim((string) $raw);
            if ($s === '' && !empty($meta['nullable'])) {
                $out['values'][] = null;
                $out['types'] .= 's';
                $out['columns'][] = $col;
                continue;
            }
            if ($s === '' && !empty($meta['required'])) {
                $out['error'] = "Поле «{$meta['label']}» обязательно.";
                return $out;
            }
            if ($s !== '' && json_decode($s) === null && json_last_error() !== JSON_ERROR_NONE) {
                $out['error'] = "Поле «{$meta['label']}»: невалидный JSON.";
                return $out;
            }
            $out['values'][] = $s;
            $out['types'] .= 's';
            $out['columns'][] = $col;
            continue;
        }
        $s = (string) $raw;
        if ($type === 'textarea' || $type === 'text') {
            if (!empty($meta['required']) && trim($s) === '') {
                $out['error'] = 'Заполните обязательные поля.';
                return $out;
            }
        }
        $out['values'][] = $s;
        $out['types'] .= 's';
        $out['columns'][] = $col;
    }

    if ($table === 'admins') {
        $pw = (string) ($_POST['admin_password'] ?? '');
        if ($isEdit) {
            if ($pw !== '') {
                $out['values'][] = $pw;
                $out['types'] .= 's';
                $out['columns'][] = 'password';
            }
        } else {
            $out['values'][] = $pw;
            $out['types'] .= 's';
            $out['columns'][] = 'password';
        }
    }

    return $out;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_verify_csrf()) {
    if (isset($_POST['delete_id'])) {
        $id = (int) $_POST['delete_id'];
        if ($table === 'admins' && isset($_SESSION['admin_id']) && $id === (int) $_SESSION['admin_id']) {
            $flashErr = 'Нельзя удалить свою учётную запись.';
        } else {
            $stmt = $db->prepare("DELETE FROM `$table` WHERE `$pk` = ?");
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                $flashOk = 'Запись удалена.';
            } else {
                $flashErr = 'Ошибка удаления: ' . esc($db->error);
            }
            $stmt->close();
        }
    } elseif (isset($_POST['save'])) {
        $editId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $isEdit = $editId > 0;
        $row = null;
        if ($isEdit) {
            $st = $db->prepare("SELECT * FROM `$table` WHERE `$pk` = ?");
            $st->bind_param('i', $editId);
            $st->execute();
            $row = $st->get_result()->fetch_assoc();
            $st->close();
            if (!$row) {
                $flashErr = 'Запись не найдена.';
            }
        }
        if ($flashErr === '') {
            $collected = admin_collect_post_values($schema, $db, $isEdit, $row, $table);
            if ($collected['error'] !== '') {
                $flashErr = $collected['error'];
            } elseif (empty($collected['columns'])) {
                $flashErr = 'Нет данных для сохранения.';
            } else {
                $cols = $collected['columns'];
                $vals = $collected['values'];
                $types = $collected['types'];

                if ($isEdit) {
                    $set = implode(', ', array_map(static function ($c) {
                        return "`$c` = ?";
                    }, $cols));
                    $types .= 'i';
                    $vals[] = $editId;
                    $sql = "UPDATE `$table` SET $set WHERE `$pk` = ?";
                    $stmt = $db->prepare($sql);
                    if (!$stmt) {
                        $flashErr = 'Ошибка подготовки запроса: ' . esc($db->error);
                    } elseif (!admin_bind_params($stmt, $types, $vals)) {
                        $flashErr = 'Ошибка привязки параметров.';
                        $stmt->close();
                    } elseif ($stmt->execute()) {
                        $flashOk = 'Сохранено.';
                        $stmt->close();
                    } else {
                        $flashErr = 'Ошибка: ' . esc($db->error);
                        $stmt->close();
                    }
                } else {
                    if ($table === 'admins' && !in_array('password', $cols, true)) {
                        $flashErr = 'Укажите пароль для нового администратора.';
                    } else {
                        $placeholders = implode(', ', array_fill(0, count($cols), '?'));
                        $colSql = implode(', ', array_map(static function ($c) {
                            return "`$c`";
                        }, $cols));
                        if (!empty($schema['auto_created'])) {
                            $ac = $schema['auto_created'];
                            $colSql .= ", `$ac`";
                            $placeholders .= ', NOW()';
                        }
                        $sql = "INSERT INTO `$table` ($colSql) VALUES ($placeholders)";
                        $stmt = $db->prepare($sql);
                        if (!$stmt) {
                            $flashErr = 'Ошибка подготовки запроса: ' . esc($db->error);
                        } elseif (!admin_bind_params($stmt, $types, $vals)) {
                            $flashErr = 'Ошибка привязки параметров.';
                            $stmt->close();
                        } elseif ($stmt->execute()) {
                            $newId = (int) $stmt->insert_id;
                            $stmt->close();
                            header('Location: crud.php?table=' . urlencode($table) . '&edit=' . $newId);
                            exit;
                        } else {
                            $flashErr = 'Ошибка: ' . esc($db->error);
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }
}

$editRow = null;
if (isset($_GET['edit'])) {
    $eid = (int) $_GET['edit'];
    $st = $db->prepare("SELECT * FROM `$table` WHERE `$pk` = ?");
    $st->bind_param('i', $eid);
    $st->execute();
    $editRow = $st->get_result()->fetch_assoc();
    $st->close();
}

$isNew = isset($_GET['new']);

admin_layout_start($label);

if ($flashOk) {
    echo '<div class="alert alert-ok">' . esc($flashOk) . '</div>';
}
if ($flashErr) {
    echo '<div class="alert alert-err">' . esc($flashErr) . '</div>';
}

if ($editRow || $isNew) {
    $row = $editRow ?? [];
    ?>
    <div class="admin-toolbar">
        <a class="btn btn-secondary" href="crud.php?table=<?= esc($table) ?>">← К списку</a>
    </div>
    <h1><?= $isNew ? 'Новая запись' : 'Редактирование #' . (int) ($row[$pk] ?? 0) ?></h1>
    <form class="admin-form" method="post">
        <input type="hidden" name="csrf" value="<?= esc(admin_csrf_token()) ?>">
        <input type="hidden" name="save" value="1">
        <?php if (!$isNew && isset($row[$pk])): ?>
            <input type="hidden" name="id" value="<?= (int) $row[$pk] ?>">
        <?php endif; ?>

        <?php foreach ($schema['fields'] as $col => $meta):
            $type = $meta['type'];
            if ($type === 'admin_password'): ?>
                <label><?= esc($meta['label']) ?>
                    <input type="password" name="admin_password" autocomplete="new-password" <?= $isNew ? 'required minlength="6"' : '' ?>>
                </label>
                <?php
                continue;
            endif;
            if ($type === 'user_password'): ?>
                <label><?= esc($meta['label']) ?>
                    <input type="password" name="password" autocomplete="new-password" <?= $isNew ? 'required' : '' ?>>
                </label>
                <?php
                continue;
            endif;
            $val = $row[$col] ?? '';
            $cls = $meta['class'] ?? '';
            ?>
            <label><?= esc($meta['label']) ?>
            <?php if ($type === 'textarea'): ?>
                <textarea class="<?= esc($cls) ?>" name="f[<?= esc($col) ?>]" <?= !empty($meta['required']) ? 'required' : '' ?>><?= esc((string) $val) ?></textarea>
            <?php elseif ($type === 'bool'): ?>
                <div class="row-check">
                    <input type="hidden" name="f[<?= esc($col) ?>]" value="0">
                    <input type="checkbox" name="f[<?= esc($col) ?>]" value="1" <?= (int) $val === 1 ? 'checked' : '' ?>>
                    <span>Да</span>
                </div>
            <?php elseif ($type === 'select'):
                $opts = $meta['options'] ?? []; ?>
                <select name="f[<?= esc($col) ?>]">
                    <?php foreach ($opts as $ov => $ol): ?>
                        <option value="<?= esc((string) $ov) ?>" <?= (string) $val === (string) $ov ? 'selected' : '' ?>><?= esc((string) $ol) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($type === 'int'): ?>
                <input type="number" name="f[<?= esc($col) ?>]" value="<?= esc((string) $val) ?>">
            <?php elseif ($type === 'float'): ?>
                <input type="text" name="f[<?= esc($col) ?>]" value="<?= esc((string) $val) ?>">
            <?php elseif ($type === 'decimal'): ?>
                <input type="text" name="f[<?= esc($col) ?>]" value="<?= esc((string) $val) ?>">
            <?php else: ?>
                <input type="text" name="f[<?= esc($col) ?>]" value="<?= esc((string) $val) ?>" <?= !empty($meta['required']) ? 'required' : '' ?>>
            <?php endif; ?>
            </label>
        <?php endforeach; ?>

        <div class="admin-toolbar" style="margin-top:20px">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
    <?php
} else {
    ?>
    <div class="admin-toolbar">
        <a class="btn btn-primary" href="crud.php?table=<?= esc($table) ?>&new=1">+ Добавить</a>
    </div>
    <h1><?= esc($label) ?></h1>
    <?php
    $listCols = $schema['list'];
    $sel = array_map(static function ($c) {
        return "`$c`";
    }, $listCols);
    $res = $db->query('SELECT ' . implode(',', $sel) . " FROM `$table` ORDER BY `$pk` DESC LIMIT 300");
    ?>
    <table class="admin-table">
        <tr>
            <?php foreach ($listCols as $c): ?>
                <th><?= esc($c) ?></th>
            <?php endforeach; ?>
            <th style="width:140px">Действия</th>
        </tr>
        <?php if ($res):
            while ($r = $res->fetch_assoc()): ?>
            <tr>
                <?php foreach ($listCols as $c):
                    $cell = $r[$c] ?? '';
                    if (is_string($cell) && mb_strlen($cell) > 80) {
                        $cell = mb_substr($cell, 0, 80) . '…';
                    }
                    ?>
                    <td><?= esc((string) $cell) ?></td>
                <?php endforeach; ?>
                <td>
                    <a class="btn btn-sm btn-secondary" href="crud.php?table=<?= esc($table) ?>&edit=<?= (int) $r[$pk] ?>">Изменить</a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Удалить запись?');">
                        <input type="hidden" name="csrf" value="<?= esc(admin_csrf_token()) ?>">
                        <input type="hidden" name="delete_id" value="<?= (int) $r[$pk] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
            <?php endwhile;
        endif; ?>
    </table>
    <?php
}

admin_layout_end();
