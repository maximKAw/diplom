<?php
// quest.php – прохождение дополнительных этапов (квеста)
require_once 'config.php';
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$test_result_id = isset($_GET['result_id']) ? (int)$_GET['result_id'] : 0;

// Получаем информацию о результате теста
$stmt = $db->prepare("SELECT * FROM user_test_results WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $test_result_id, $user_id);
$stmt->execute();
$test_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$test_result) {
    die('Результат теста не найден');
}

$test_id = $test_result['test_id'];

// Получаем список квестов для этого теста
$quests = [];
$stmt = $db->prepare("SELECT * FROM test_quests WHERE test_id = ? ORDER BY order_index");
$stmt->bind_param("i", $test_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $quests[] = $row;
}
$stmt->close();

if (empty($quests)) {
    header('Location: test_result.php?id=' . $test_result_id);
    exit;
}

// Получаем ID пройденных этапов
$completed_quest_ids = [];
$stmt = $db->prepare("SELECT quest_id FROM user_quest_results WHERE test_result_id = ?");
$stmt->bind_param("i", $test_result_id);
$stmt->execute();
$comp_res = $stmt->get_result();
while ($cr = $comp_res->fetch_assoc()) {
    $completed_quest_ids[] = $cr['quest_id'];
}
$stmt->close();

// Определяем текущий этап: по GET-параметру step или первый непройденный
$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

if (!isset($_GET['step'])) {
    foreach ($quests as $q) {
        if (!in_array($q['id'], $completed_quest_ids)) {
            $current_step = $q['order_index'];
            break;
        }
    }
}

// Находим текущий квест по order_index
$current_quest = null;
foreach ($quests as $q) {
    if ($q['order_index'] == $current_step) {
        $current_quest = $q;
        break;
    }
}

if (!$current_quest) {
    // Все этапы квеста пройдены – переходим к финальному тесту
    header('Location: final.php?result_id=' . $test_result_id);
    exit;
}

// Обработка отправки ответа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_quest') {
    $answer_data = [];
    $points_earned = 0;

    if ($current_quest['type'] === 'choice') {
        $selected = $_POST['choice'] ?? '';
        $options = json_decode($current_quest['options'], true);
        foreach ($options as $opt) {
            if ($opt['value'] == $selected) {
                $points_earned = $opt['points'] ?? 0;
                break;
            }
        }
        $answer_data['choice'] = $selected;
    } elseif ($current_quest['type'] === 'text') {
        $answer_text = trim($_POST['answer_text'] ?? '');
        $answer_data['text'] = $answer_text;
        $points_earned = $current_quest['points'] ?? 0;
    } elseif ($current_quest['type'] === 'quiz') {
        $questions = json_decode($current_quest['content'], true);
        $answers = $_POST['answers'] ?? [];
        $total_points = 0;
        $user_answers = [];

        foreach ($questions as $q) {
            $qid = $q['id'];
            if ($q['type'] === 'single') {
                $selected = $answers[$qid] ?? null;
                $user_answers[$qid] = $selected;
                foreach ($q['options'] as $opt) {
                    if ($opt['text'] == $selected) {
                        $total_points += $opt['points'];
                        break;
                    }
                }
            } elseif ($q['type'] === 'multiple') {
                $selected = $answers[$qid] ?? [];
                $user_answers[$qid] = $selected;
                foreach ($q['options'] as $opt) {
                    if (in_array($opt['text'], $selected)) {
                        $total_points += $opt['points'];
                    }
                }
            } elseif ($q['type'] === 'matching') {
                $selected = $answers[$qid] ?? [];
                $user_answers[$qid] = $selected;
                foreach ($q['pairs'] as $index => $pair) {
                    if (isset($selected[$index]) && $selected[$index] === $pair['right']) {
                        $total_points += 1;
                    }
                }
            }
        }
        $points_earned = $total_points;
        $answer_data = $user_answers;
    } else { // info
        $points_earned = $current_quest['points'] ?? 0;
    }

    // Сохраняем результат
    $stmt = $db->prepare("INSERT INTO user_quest_results (user_id, test_result_id, quest_id, answer_data, points_earned) VALUES (?, ?, ?, ?, ?)");
    $answer_json = json_encode($answer_data, JSON_UNESCAPED_UNICODE);
    $stmt->bind_param("iiiss", $user_id, $test_result_id, $current_quest['id'], $answer_json, $points_earned);
    $stmt->execute();
    $stmt->close();

    // Переход к следующему этапу
    $next_step = $current_step + 1;
    $next_exists = false;
    foreach ($quests as $q) {
        if ($q['order_index'] == $next_step) {
            $next_exists = true;
            break;
        }
    }
    if ($next_exists) {
        header('Location: quest.php?result_id=' . $test_result_id . '&step=' . $next_step);
    } else {
        // Все этапы квеста пройдены – переходим к финальному тесту
        header('Location: final.php?result_id=' . $test_result_id);
    }
    exit;
}

require_once 'header.php';
?>

<style>
    /* Стили из предыдущей версии (оставлены без изменений) */
    .quest-container {
        max-width: 800px;
        margin: 100px auto 40px;
        background: #fff;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .step-indicator {
        color: #6c757d;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }
    .quest-title {
        font-size: 1.8rem;
        color: #1e3c72;
        margin-bottom: 1rem;
    }
    .quest-description {
        background: #f8f9fc;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border-left: 4px solid #2563eb;
    }
    .quest-content {
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }
    .options {
        list-style: none;
        padding: 0;
    }
    .options li {
        margin-bottom: 0.75rem;
    }
    .options label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f1f5f9;
        border-radius: 40px;
        cursor: pointer;
        border: 1px solid #e2e8f0;
    }
    .options input[type="radio"], .options input[type="checkbox"] {
        accent-color: #2563eb;
        width: 1.2rem;
        height: 1.2rem;
    }
    .text-answer {
        width: 100%;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        background: #f1f5f9;
    }
    .quiz-question {
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 12px;
    }
    .quiz-question h3 {
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
    .matching-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .matching-row span {
        min-width: 120px;
    }
    .matching-row select {
        padding: 0.5rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .step-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .step-nav-item {
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        transition: 0.2s;
    }
    .step-nav-current {
        background: #2563eb;
        color: white;
    }
    .step-nav-completed {
        background: #e2e8f0;
        color: #1e3c72;
        text-decoration: none;
    }
    .step-nav-completed:hover {
        background: #cbd5e1;
    }
    .step-nav-locked {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }
    .btn {
        padding: 0.7rem 2rem;
        border-radius: 40px;
        border: none;
        background: #2563eb;
        color: white;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn:hover {
        background: #1d4ed8;
    }
    .btn-outline {
        background: transparent;
        border: 2px solid #2563eb;
        color: #2563eb;
        padding: 0.7rem 2rem;
        border-radius: 40px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-outline:hover {
        background: #2563eb;
        color: white;
    }
</style>

<div class="quest-container">
    <!-- Навигация по этапам -->
    <div class="step-nav">
        <?php for ($i = 1; $i <= count($quests); $i++): 
            // Находим этап с данным order_index
            $quest_for_step = null;
            foreach ($quests as $q) {
                if ($q['order_index'] == $i) {
                    $quest_for_step = $q;
                    break;
                }
            }
            $is_completed = in_array($quest_for_step['id'], $completed_quest_ids);
            $is_current = ($i == $current_step);
        ?>
            <?php if ($is_completed || $is_current): ?>
                <?php if ($is_current): ?>
                    <span class="step-nav-item step-nav-current">Этап <?= $i ?></span>
                <?php else: ?>
                    <a href="quest.php?result_id=<?= $test_result_id ?>&step=<?= $i ?>" class="step-nav-item step-nav-completed">Этап <?= $i ?></a>
                <?php endif; ?>
            <?php else: ?>
                <span class="step-nav-item step-nav-locked">Этап <?= $i ?></span>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

    <div class="step-indicator">Этап <?= $current_step ?> из <?= count($quests) ?></div>
    <h1 class="quest-title"><?= htmlspecialchars($current_quest['title']) ?></h1>
    <div class="quest-description"><?= nl2br(htmlspecialchars($current_quest['description'] ?? '')) ?></div>

    <form method="post">
        <input type="hidden" name="action" value="submit_quest">

        <?php if ($current_quest['type'] === 'choice'): ?>
            <?php $options = json_decode($current_quest['options'], true); ?>
            <?php if (is_array($options)): ?>
                <ul class="options">
                    <?php foreach ($options as $opt): ?>
                        <li>
                            <label>
                                <input type="radio" name="choice" value="<?= htmlspecialchars($opt['value']) ?>" required>
                                <?= htmlspecialchars($opt['label']) ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

        <?php elseif ($current_quest['type'] === 'text'): ?>
            <textarea name="answer_text" class="text-answer" rows="4" placeholder="Введите ваш ответ..."></textarea>

        <?php elseif ($current_quest['type'] === 'quiz'): ?>
            <?php $questions = json_decode($current_quest['content'], true); ?>
            <div class="quiz-content">
                <?php foreach ($questions as $q): ?>
                    <div class="quiz-question">
                        <h3><?= htmlspecialchars($q['question']) ?></h3>
                        <?php if ($q['type'] === 'single'): ?>
                            <?php foreach ($q['options'] as $opt): ?>
                                <label style="display: block; margin-bottom: 0.5rem;">
                                    <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= htmlspecialchars($opt['text']) ?>" required>
                                    <?= htmlspecialchars($opt['text']) ?>
                                </label>
                            <?php endforeach; ?>

                        <?php elseif ($q['type'] === 'multiple'): ?>
                            <?php foreach ($q['options'] as $opt): ?>
                                <label style="display: block; margin-bottom: 0.5rem;">
                                    <input type="checkbox" name="answers[<?= $q['id'] ?>][]" value="<?= htmlspecialchars($opt['text']) ?>">
                                    <?= htmlspecialchars($opt['text']) ?>
                                </label>
                            <?php endforeach; ?>

                        <?php elseif ($q['type'] === 'matching'): ?>
                            <?php foreach ($q['pairs'] as $index => $pair): ?>
                                <div class="matching-row">
                                    <span><?= htmlspecialchars($pair['left']) ?>:</span>
                                    <select name="answers[<?= $q['id'] ?>][<?= $index ?>]" required>
                                        <option value="">-- выберите --</option>
                                        <?php foreach ($pair['options'] as $opt): ?>
                                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn">Далее</button>
        <a href="test_result.php?id=<?= $test_result_id ?>" class="btn-outline" style="margin-left: 1rem;">Прервать квест</a>
    </form>
</div>

<?php require_once 'footer.php'; ?>