<?php
declare(strict_types=1);
require_once __DIR__ . '/_init.php';
admin_require_login();
require_once __DIR__ . '/layout.php';

$flashOk = '';
$flashErr = '';
$testId = (int) ($_POST['test_id'] ?? $_GET['test_id'] ?? 0);

$testsRes = $db->query('SELECT id, title FROM tests ORDER BY id');
$tests = [];
if ($testsRes) {
    while ($t = $testsRes->fetch_assoc()) {
        $tests[(int) $t['id']] = $t['title'];
    }
}

if ($testId <= 0 && !empty($tests)) {
    reset($tests);
    $testId = (int) key($tests);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_verify_csrf()) {
    if (isset($_POST['delete_answer'])) {
        $aid = (int) $_POST['delete_answer'];
        $st = $db->prepare('DELETE FROM answers WHERE id = ?');
        $st->bind_param('i', $aid);
        $st->execute();
        $st->close();
        $flashOk = 'Ответ удалён.';
    } elseif (isset($_POST['delete_question'])) {
        $qid = (int) $_POST['delete_question'];
        $st = $db->prepare('DELETE FROM questions WHERE id = ? AND test_id = ?');
        $st->bind_param('ii', $qid, $testId);
        $st->execute();
        $st->close();
        $flashOk = 'Вопрос и связанные ответы удалены.';
    } elseif (isset($_POST['save_question'])) {
        $qid = isset($_POST['question_id']) ? (int) $_POST['question_id'] : 0;
        $text = trim((string) ($_POST['question_text'] ?? ''));
        $qtype = (string) ($_POST['question_type'] ?? 'single');
        if (!in_array($qtype, ['single', 'multiple', 'text'], true)) {
            $qtype = 'single';
        }
        $img = trim((string) ($_POST['image_path'] ?? ''));
        if ($text === '') {
            $flashErr = 'Введите текст вопроса.';
        } else {
            if ($qid > 0) {
                $st = $db->prepare('UPDATE questions SET question_text = ?, question_type = ?, image_path = ? WHERE id = ? AND test_id = ?');
                $imgNull = $img === '' ? null : $img;
                $st->bind_param('sssii', $text, $qtype, $imgNull, $qid, $testId);
            } else {
                $st = $db->prepare('INSERT INTO questions (test_id, question_text, question_type, image_path) VALUES (?, ?, ?, ?)');
                $imgNull = $img === '' ? null : $img;
                $st->bind_param('isss', $testId, $text, $qtype, $imgNull);
            }
            if ($st->execute()) {
                $flashOk = 'Вопрос сохранён.';
            } else {
                $flashErr = esc($db->error);
            }
            $st->close();
        }
    } elseif (isset($_POST['save_answer'])) {
        $aid = isset($_POST['answer_id']) ? (int) $_POST['answer_id'] : 0;
        $qid = (int) ($_POST['answer_question_id'] ?? 0);
        $atext = trim((string) ($_POST['answer_text'] ?? ''));
        $points = (int) ($_POST['points'] ?? 0);
        $next = ($_POST['next_question_id'] ?? '') === '' ? null : (int) $_POST['next_question_id'];
        if ($qid <= 0 || $atext === '') {
            $flashErr = 'Укажите вопрос и текст ответа.';
        } else {
            $chk = $db->prepare('SELECT id FROM questions WHERE id = ? AND test_id = ?');
            $chk->bind_param('ii', $qid, $testId);
            $chk->execute();
            if (!$chk->get_result()->fetch_assoc()) {
                $flashErr = 'Вопрос не из этого теста.';
            } else {
                if ($aid > 0) {
                    $st = $db->prepare('UPDATE answers SET question_id = ?, answer_text = ?, points = ?, next_question_id = ? WHERE id = ?');
                    $st->bind_param('isiii', $qid, $atext, $points, $next, $aid);
                } else {
                    $st = $db->prepare('INSERT INTO answers (question_id, answer_text, points, next_question_id) VALUES (?, ?, ?, ?)');
                    $st->bind_param('isii', $qid, $atext, $points, $next);
                }
                if ($st->execute()) {
                    $flashOk = 'Ответ сохранён.';
                } else {
                    $flashErr = esc($db->error);
                }
                $st->close();
            }
            $chk->close();
        }
    }
}

$editQ = null;
$editA = null;
if (isset($_GET['edit_q'])) {
    $eq = (int) $_GET['edit_q'];
    $st = $db->prepare('SELECT * FROM questions WHERE id = ? AND test_id = ?');
    $st->bind_param('ii', $eq, $testId);
    $st->execute();
    $editQ = $st->get_result()->fetch_assoc();
    $st->close();
}
if (isset($_GET['edit_a'])) {
    $ea = (int) $_GET['edit_a'];
    $st = $db->prepare(
        'SELECT a.* FROM answers a INNER JOIN questions q ON q.id = a.question_id WHERE a.id = ? AND q.test_id = ?'
    );
    $st->bind_param('ii', $ea, $testId);
    $st->execute();
    $editA = $st->get_result()->fetch_assoc();
    $st->close();
}

admin_layout_start('Вопросы и ответы');

if ($flashOk) {
    echo '<div class="alert alert-ok">' . esc($flashOk) . '</div>';
}
if ($flashErr) {
    echo '<div class="alert alert-err">' . esc($flashErr) . '</div>';
}
?>
<h1>Вопросы и ответы</h1>
<form method="get" class="admin-toolbar" style="align-items:flex-end">
    <label>Тест
        <select name="test_id" onchange="this.form.submit()" style="min-width:220px;margin-top:6px;padding:8px">
            <?php foreach ($tests as $tid => $ttitle): ?>
                <option value="<?= $tid ?>" <?= $tid === $testId ? 'selected' : '' ?>><?= esc($ttitle) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<?php if ($testId <= 0): ?>
    <p class="muted">Нет тестов в базе. Создайте тест в разделе «Тесты».</p>
<?php else:
    $qst = $db->prepare('SELECT * FROM questions WHERE test_id = ? ORDER BY id');
    $qst->bind_param('i', $testId);
    $qst->execute();
    $qres = $qst->get_result();
    ?>

    <h2 style="margin:24px 0 12px;font-size:1.1rem"><?= $editQ ? 'Редактирование вопроса' : 'Новый вопрос' ?></h2>
    <form class="admin-form" method="post" style="margin-bottom:28px" action="questions.php?test_id=<?= $testId ?>">
        <input type="hidden" name="csrf" value="<?= esc(admin_csrf_token()) ?>">
        <input type="hidden" name="test_id" value="<?= $testId ?>">
        <input type="hidden" name="save_question" value="1">
        <?php if ($editQ): ?>
            <input type="hidden" name="question_id" value="<?= (int) $editQ['id'] ?>">
        <?php endif; ?>
        <label>Текст вопроса
            <textarea name="question_text" required><?= esc((string) ($editQ['question_text'] ?? '')) ?></textarea>
        </label>
        <label>Тип
            <select name="question_type">
                <?php foreach (['single' => 'Один ответ', 'multiple' => 'Несколько', 'text' => 'Текст'] as $k => $lab): ?>
                    <option value="<?= esc($k) ?>" <?= (($editQ['question_type'] ?? 'single') === $k) ? 'selected' : '' ?>><?= esc($lab) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Путь к изображению (опц.)
            <input type="text" name="image_path" value="<?= esc((string) ($editQ['image_path'] ?? '')) ?>">
        </label>
        <div class="admin-toolbar" style="margin-top:16px">
            <button type="submit" class="btn btn-primary">Сохранить вопрос</button>
            <?php if ($editQ): ?>
                <a class="btn btn-secondary" href="questions.php?test_id=<?= $testId ?>">Отмена</a>
            <?php endif; ?>
        </div>
    </form>

    <?php while ($q = $qres->fetch_assoc()):
        $qid = (int) $q['id'];
        $ast = $db->prepare('SELECT * FROM answers WHERE question_id = ? ORDER BY id');
        $ast->bind_param('i', $qid);
        $ast->execute();
        $ares = $ast->get_result();
        ?>
        <div style="background:#fff;padding:16px;border-radius:10px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.06)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap">
                <strong>#<?= $qid ?></strong>
                <span class="muted"><?= esc($q['question_type']) ?></span>
                <div style="margin-left:auto">
                    <a class="btn btn-sm btn-secondary" href="questions.php?test_id=<?= $testId ?>&edit_q=<?= $qid ?>">Изм. вопрос</a>
                    <form method="post" action="questions.php?test_id=<?= $testId ?>" style="display:inline" onsubmit="return confirm('Удалить вопрос и ответы?');">
                        <input type="hidden" name="csrf" value="<?= esc(admin_csrf_token()) ?>">
                        <input type="hidden" name="test_id" value="<?= $testId ?>">
                        <input type="hidden" name="delete_question" value="<?= $qid ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить вопрос</button>
                    </form>
                </div>
            </div>
            <p style="margin:10px 0"><?= nl2br(esc($q['question_text'])) ?></p>
            <table class="admin-table" style="margin-top:10px">
                <tr><th>ID</th><th>Ответ</th><th>Баллы</th><th>next_question_id</th><th></th></tr>
                <?php while ($a = $ares->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $a['id'] ?></td>
                    <td><?= esc($a['answer_text']) ?></td>
                    <td><?= (int) $a['points'] ?></td>
                    <td><?= $a['next_question_id'] !== null ? (int) $a['next_question_id'] : '—' ?></td>
                    <td>
                        <a class="btn btn-sm btn-secondary" href="questions.php?test_id=<?= $testId ?>&edit_a=<?= (int) $a['id'] ?>">Изм.</a>
                        <form method="post" action="questions.php?test_id=<?= $testId ?>" style="display:inline" onsubmit="return confirm('Удалить ответ?');">
                            <input type="hidden" name="csrf" value="<?= esc(admin_csrf_token()) ?>">
                            <input type="hidden" name="test_id" value="<?= $testId ?>">
                            <input type="hidden" name="delete_answer" value="<?= (int) $a['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">×</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <?php
        $ast->close();
    endwhile;
    $qst->close();
    ?>

    <h2 style="margin:24px 0 12px;font-size:1.1rem"><?= $editA ? 'Редактирование ответа' : 'Новый ответ' ?></h2>
    <form class="admin-form" method="post" action="questions.php?test_id=<?= $testId ?>">
        <input type="hidden" name="csrf" value="<?= esc(admin_csrf_token()) ?>">
        <input type="hidden" name="test_id" value="<?= $testId ?>">
        <input type="hidden" name="save_answer" value="1">
        <?php if ($editA): ?>
            <input type="hidden" name="answer_id" value="<?= (int) $editA['id'] ?>">
        <?php endif; ?>
        <label>ID вопроса (из списка выше)
            <input type="number" name="answer_question_id" required value="<?= (int) ($editA['question_id'] ?? '') ?>">
        </label>
        <label>Текст ответа
            <textarea name="answer_text" required><?= esc((string) ($editA['answer_text'] ?? '')) ?></textarea>
        </label>
        <label>Баллы
            <input type="number" name="points" value="<?= (int) ($editA['points'] ?? 0) ?>">
        </label>
        <label>Следующий вопрос ID (опц., для ветвления)
            <input type="number" name="next_question_id" value="<?= $editA && $editA['next_question_id'] !== null ? (int) $editA['next_question_id'] : '' ?>" placeholder="пусто = нет">
        </label>
        <div class="admin-toolbar" style="margin-top:16px">
            <button type="submit" class="btn btn-primary">Сохранить ответ</button>
            <?php if ($editA): ?>
                <a class="btn btn-secondary" href="questions.php?test_id=<?= $testId ?>">Отмена</a>
            <?php endif; ?>
        </div>
    </form>
<?php endif; ?>

<?php admin_layout_end(); ?>
