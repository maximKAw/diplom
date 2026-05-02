<?php
// final.php – финальный этап с 15 вопросами после квеста
require_once 'config.php';
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$test_result_id = isset($_GET['result_id']) ? (int)$_GET['result_id'] : 0;

// Проверяем, что такой результат существует и принадлежит пользователю
$stmt = $db->prepare("SELECT * FROM user_test_results WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $test_result_id, $user_id);
$stmt->execute();
$test_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$test_result) {
    die('Результат теста не найден');
}

$test_id = $test_result['test_id'];

// Проверяем, есть ли финальные вопросы для этого теста
$questions = [];
$stmt = $db->prepare("SELECT * FROM test_final WHERE test_id = ? ORDER BY order_index");
$stmt->bind_param("i", $test_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();

if (empty($questions)) {
    header('Location: test_result.php?id=' . $test_result_id);
    exit;
}

// Проверяем, не проходил ли уже финал
$stmt = $db->prepare("SELECT id FROM user_final_results WHERE test_result_id = ?");
$stmt->bind_param("i", $test_result_id);
$stmt->execute();
$already = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($already) {
    // Уже пройдено – показываем результат
    header('Location: final_result.php?result_id=' . $test_result_id);
    exit;
}

// Инициализируем сессию для текущего финала
$session_key = 'final_' . $test_result_id;
if (!isset($_SESSION[$session_key])) {
    $_SESSION[$session_key] = [
        'current_index' => 0,
        'answers' => [],
        'score' => 0,
        'max_score' => array_sum(array_column($questions, 'points'))
    ];
}

// Обработка отправки ответа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_answer') {
    $current = $_SESSION[$session_key]['current_index'];
    if ($current < count($questions)) {
        $question = $questions[$current];
        $qid = $question['id'];
        $answer_data = [];
        $points_earned = 0;

        if ($question['question_type'] === 'single') {
            $selected = $_POST['answer'] ?? '';
            $options = json_decode($question['options'], true);
            foreach ($options as $opt) {
                if ($opt['id'] == $selected) {
                    if (!empty($opt['correct']) && $opt['correct'] === true) {
                        $points_earned = $question['points'];
                    }
                    break;
                }
            }
            $answer_data['selected'] = $selected;
        } elseif ($question['question_type'] === 'multiple') {
            $selected = $_POST['answers'] ?? [];
            if (!is_array($selected)) $selected = [$selected];
            $options = json_decode($question['options'], true);
            $correct_ids = [];
            foreach ($options as $opt) {
                if (!empty($opt['correct']) && $opt['correct'] === true) {
                    $correct_ids[] = $opt['id'];
                }
            }
            // Сравниваем массивы без учёта порядка
            sort($selected);
            sort($correct_ids);
            if ($selected == $correct_ids) {
                $points_earned = $question['points'];
            }
            $answer_data['selected'] = $selected;
        } elseif ($question['question_type'] === 'text') {
            $text = trim($_POST['answer_text'] ?? '');
            // Здесь можно добавить более сложную проверку, но пока просто даём баллы за любой непустой ответ
            if (!empty($text)) {
                $points_earned = $question['points'];
            }
            $answer_data['text'] = $text;
        } elseif ($question['question_type'] === 'match') {
            // Для сопоставлений можно реализовать позже
            $answer_data['note'] = 'Тип match пока не реализован';
        }

        $_SESSION[$session_key]['answers'][$qid] = $answer_data;
        $_SESSION[$session_key]['score'] += $points_earned;
        $_SESSION[$session_key]['current_index']++;

        header('Location: final.php?result_id=' . $test_result_id);
        exit;
    }
}

$current_index = $_SESSION[$session_key]['current_index'];
$is_finished = ($current_index >= count($questions));

if ($is_finished) {
    // Сохраняем результаты в БД
    $answers_json = json_encode($_SESSION[$session_key]['answers'], JSON_UNESCAPED_UNICODE);
    $score = $_SESSION[$session_key]['score'];
    $max_score = $_SESSION[$session_key]['max_score'];

    $stmt = $db->prepare("INSERT INTO user_final_results (user_id, test_result_id, answers, score, max_score) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisii", $user_id, $test_result_id, $answers_json, $score, $max_score);
    $stmt->execute();
    $stmt->close();

    // Очищаем сессию
    unset($_SESSION[$session_key]);

    // Переходим на страницу результата
    header('Location: final_result.php?result_id=' . $test_result_id);
    exit;
}

require_once 'header.php';
?>

<style>
    .final-container {
        max-width: 700px;
        margin: 100px auto 40px;
        background: #fff;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .progress {
        margin-bottom: 1rem;
        color: #6c757d;
    }
    .question-text {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #1e3c72;
    }
    .options-list {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0;
    }
    .option-item {
        margin-bottom: 0.75rem;
    }
    .option-item label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f1f5f9;
        border-radius: 40px;
        cursor: pointer;
        border: 1px solid #e2e8f0;
    }
    .option-item input[type="radio"],
    .option-item input[type="checkbox"] {
        width: 1.2rem;
        height: 1.2rem;
        accent-color: #2563eb;
    }
    .text-answer {
        width: 100%;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        background: #f1f5f9;
        margin: 1rem 0;
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
</style>

<div class="final-container">
    <?php
    $current_question = $questions[$current_index];
    ?>
    <div class="progress">Вопрос <?= $current_index + 1 ?> из <?= count($questions) ?></div>
    <div class="question-text"><?= htmlspecialchars($current_question['question_text']) ?></div>

    <form method="post">
        <input type="hidden" name="action" value="submit_answer">

        <?php if ($current_question['question_type'] === 'single'): ?>
            <?php $options = json_decode($current_question['options'], true); ?>
            <ul class="options-list">
                <?php foreach ($options as $opt): ?>
                    <li class="option-item">
                        <label>
                            <input type="radio" name="answer" value="<?= htmlspecialchars($opt['id']) ?>" required>
                            <?= htmlspecialchars($opt['text']) ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($current_question['question_type'] === 'multiple'): ?>
            <?php $options = json_decode($current_question['options'], true); ?>
            <ul class="options-list">
                <?php foreach ($options as $opt): ?>
                    <li class="option-item">
                        <label>
                            <input type="checkbox" name="answers[]" value="<?= htmlspecialchars($opt['id']) ?>">
                            <?= htmlspecialchars($opt['text']) ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($current_question['question_type'] === 'text'): ?>
            <textarea name="answer_text" class="text-answer" rows="4" placeholder="Введите ваш ответ..."></textarea>
        <?php elseif ($current_question['question_type'] === 'match'): ?>
            <p>Тип вопроса "сопоставление" пока не реализован. Пропускаем.</p>
        <?php endif; ?>

        <button type="submit" class="btn">Далее</button>
        <a href="test_result.php?id=<?= $test_result_id ?>" class="btn-outline" style="margin-left: 1rem;">Прервать</a>
    </form>
</div>

<?php require_once 'footer.php'; ?>