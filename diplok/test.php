<?php
// test.php – страница прохождения теста (с сохранением результатов)
require_once 'config.php';
require_once 'functions.php';

session_start();

// Функция нормализации пути к изображению
function normalizeImagePath($path) {
    if (empty($path)) return '';
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#^diplok/#', '', $path);
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return $path;
}

$test_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем информацию о тесте
$stmt = $db->prepare("SELECT * FROM tests WHERE id = ?");
$stmt->bind_param("i", $test_id);
$stmt->execute();
$test = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$test) {
    $not_found = true;
} else {
    $not_found = false;
    // Получаем все вопросы теста
    $questions = [];
    $stmt = $db->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id");
    $stmt->bind_param("i", $test_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $row['image_path'] = normalizeImagePath($row['image_path'] ?? '');
        $questions[] = $row;
    }
    $stmt->close();
    $total_questions = count($questions);

    $session_key = 'test_' . $test_id;
    if (!isset($_SESSION[$session_key])) {
        $_SESSION[$session_key] = [
            'current_index' => 0,
            'answers' => [],
            'score' => 0
        ];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_answer') {
        $current = $_SESSION[$session_key]['current_index'];
        if ($current < $total_questions) {
            $question = $questions[$current];
            $qid = $question['id'];
            $answer_data = ['points' => 0];

            if ($question['question_type'] === 'text') {
                $text = trim($_POST['answer_text'] ?? '');
                $answer_data['text'] = $text;
            } else {
                $selected = $_POST['answers'] ?? [];
                if (!is_array($selected)) {
                    $selected = [$selected];
                }
                $selected = array_map('intval', $selected);
                $answer_data['answer_ids'] = $selected;

                $stmt = $db->prepare("SELECT id, points FROM answers WHERE question_id = ?");
                $stmt->bind_param("i", $qid);
                $stmt->execute();
                $ans_res = $stmt->get_result();
                $points = 0;
                while ($ans = $ans_res->fetch_assoc()) {
                    if (in_array($ans['id'], $selected)) {
                        $points += (int)$ans['points'];
                    }
                }
                $stmt->close();
                $answer_data['points'] = $points;
            }

            $_SESSION[$session_key]['answers'][$qid] = $answer_data;
            $_SESSION[$session_key]['score'] += $answer_data['points'] ?? 0;
            $_SESSION[$session_key]['current_index']++;

            header('Location: test.php?id=' . $test_id);
            exit;
        }
    }

    if (isset($_GET['reset'])) {
        unset($_SESSION[$session_key]);
        header('Location: test.php?id=' . $test_id);
        exit;
    }

    $current_index = $_SESSION[$session_key]['current_index'];
    $is_finished = ($current_index >= $total_questions);
    $result_data = null;

    if ($is_finished && $total_questions > 0) {
        $score = $_SESSION[$session_key]['score'];
        $stmt = $db->prepare("SELECT * FROM results WHERE test_id = ? AND min_score <= ? AND max_score >= ? ORDER BY min_score LIMIT 1");
        $stmt->bind_param("iii", $test_id, $score, $score);
        $stmt->execute();
        $result_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // ========== СОХРАНЕНИЕ РЕЗУЛЬТАТА В БД ==========
        if (isset($_SESSION['user_id']) && !isset($_SESSION[$session_key]['saved'])) {
            $user_id = $_SESSION['user_id'];
            $result_text = $result_data ? $result_data['result_text'] : null;
            
            $stmt = $db->prepare("INSERT INTO user_test_results (user_id, test_id, score, result_text) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iiis", $user_id, $test_id, $score, $result_text);
                if ($stmt->execute()) {
                    $_SESSION[$session_key]['saved'] = true;
                    $new_result_id = $stmt->insert_id; // ID сохранённого результата

                    // ===== СОХРАНЯЕМ ДЕТАЛЬНЫЕ ОТВЕТЫ =====
                    foreach ($_SESSION[$session_key]['answers'] as $qid => $ans_data) {
                        $answer_text = '';
                        if (isset($ans_data['text'])) {
                            $answer_text = $ans_data['text'];
                        } elseif (isset($ans_data['answer_ids'])) {
                            $answer_text = implode(',', $ans_data['answer_ids']);
                        }
                        $points = $ans_data['points'] ?? 0;
                        
                        $stmt_ans = $db->prepare("INSERT INTO user_answers (result_id, question_id, answer_text, points_earned) VALUES (?, ?, ?, ?)");
                        if ($stmt_ans) {
                            $stmt_ans->bind_param("iisi", $new_result_id, $qid, $answer_text, $points);
                            $stmt_ans->execute();
                            $stmt_ans->close();
                        }
                    }
                    // ======================================

                    // Проверяем, есть ли квесты для этого теста
                    $check_quests = $db->prepare("SELECT COUNT(*) as cnt FROM test_quests WHERE test_id = ?");
                    $check_quests->bind_param("i", $test_id);
                    $check_quests->execute();
                    $cnt_row = $check_quests->get_result()->fetch_assoc();
                    $check_quests->close();
                    if ($cnt_row['cnt'] > 0) {
                        $_SESSION[$session_key]['test_result_id'] = $new_result_id;
                    }
                }
                $stmt->close();
            }
        }
        // =================================================
    }
}

require_once 'header.php';

if ($not_found):
    echo '<div style="max-width:800px; margin:100px auto; text-align:center;"><h1>Тест не найден</h1><p><a href="tests.php" class="btn btn-primary">Вернуться к списку тестов</a></p></div>';
    require_once 'footer.php';
    exit;
endif;
?>

<style>
    :root {
        --primary: #2563eb;
        --primary-light: #3b82f6;
        --surface: #ffffff;
        --text: #0f172a;
        --text-light: #475569;
        --border: #e2e8f0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow: 0 4px 12px rgba(0,0,0,0.08);
        --radius: 24px;
        --radius-sm: 12px;
    }

    body {
        background: #f8fafc;
    }

    .test-container {
        max-width: 800px;
        margin: 100px auto 40px;
        background: var(--surface);
        border-radius: var(--radius);
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }

    .test-header {
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--border);
        padding-bottom: 1rem;
    }

    .test-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .test-meta {
        color: var(--text-light);
        font-size: 0.95rem;
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .test-meta i {
        color: var(--primary);
        margin-right: 0.3rem;
    }

    .test-description {
        background: #f1f5f9;
        padding: 1.5rem;
        border-radius: var(--radius-sm);
        margin: 1.5rem 0;
        font-size: 1rem;
        color: var(--text);
        border: 1px solid var(--border);
    }

    .question-card {
        margin-top: 2rem;
    }

    .progress {
        margin-bottom: 1.5rem;
        color: var(--text-light);
        font-size: 0.95rem;
    }

    .question-text {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--primary);
    }

    .question-image {
        max-width: 100%;
        max-height: 300px;
        margin: 1rem 0;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
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
        transition: background 0.2s;
        border: 1px solid var(--border);
    }

    .option-item label:hover {
        background: #e2e8f0;
    }

    .option-item input[type="radio"],
    .option-item input[type="checkbox"] {
        width: 1.2rem;
        height: 1.2rem;
        accent-color: var(--primary);
    }

    .text-answer {
        width: 100%;
        padding: 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 1rem;
        margin: 1rem 0;
        background: #f1f5f9;
        transition: border-color 0.2s;
    }

    .text-answer:focus {
        outline: none;
        border-color: var(--primary);
    }

    .btn {
        display: inline-block;
        padding: 0.7rem 1.8rem;
        border-radius: 40px;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        box-shadow: var(--shadow-sm);
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        box-shadow: var(--shadow);
        transform: translateY(-1px);
    }

    .btn-outline {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text);
    }

    .btn-outline:hover {
        background: #f1f5f9;
        border-color: var(--text-light);
    }

    .result-box {
        background: #f1f5f9;
        border-radius: var(--radius-sm);
        padding: 2rem;
        margin: 2rem 0;
        border-left: 4px solid #10b981;
        border: 1px solid var(--border);
    }

    .result-score {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
    }

    .result-text {
        font-size: 1.1rem;
        margin-top: 1rem;
        color: var(--text-light);
    }

    .actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .test-container {
            margin: 90px 15px 30px;
            padding: 1.5rem;
        }

        .test-title {
            font-size: 1.6rem;
        }

        .test-meta {
            gap: 1rem;
        }
    }
</style>

<div class="test-container">
    <div class="test-header">
        <h1 class="test-title"><?= htmlspecialchars($test['title']) ?></h1>
        <div class="test-meta">
            <span><i class="fas fa-question-circle"></i> <?= $total_questions ?> вопросов</span>
            <span><i class="fas fa-clock"></i> <?= (int)$test['duration_min'] ?> мин</span>
            <?php if ($test['age_limit'] > 0): ?>
                <span><i class="fas fa-user"></i> <?= (int)$test['age_limit'] ?>+</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($total_questions === 0): ?>
        <p class="text-center">В этом тесте пока нет вопросов.</p>
        <a href="tests.php" class="btn btn-outline">← К списку тестов</a>
    <?php elseif (!$is_finished): ?>
        <?php
        $question = $questions[$current_index];
        $qid = $question['id'];
        $stmt = $db->prepare("SELECT * FROM answers WHERE question_id = ?");
        $stmt->bind_param("i", $qid);
        $stmt->execute();
        $answers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        ?>
        <div class="progress">
            Вопрос <?= $current_index + 1 ?> из <?= $total_questions ?>
        </div>
        <form method="post" class="question-card">
            <input type="hidden" name="action" value="submit_answer">
            <div class="question-text"><?= htmlspecialchars($question['question_text']) ?></div>
            <?php if (!empty($question['image_path'])): ?>
                <img src="<?= htmlspecialchars($question['image_path']) ?>" alt="Иллюстрация" class="question-image" onerror="this.onerror=null; this.style.display='none';">
            <?php endif; ?>

            <?php if ($question['question_type'] === 'text'): ?>
                <textarea name="answer_text" class="text-answer" rows="4" placeholder="Введите ваш ответ..."></textarea>
            <?php else: ?>
                <ul class="options-list">
                    <?php foreach ($answers as $ans): ?>
                        <li class="option-item">
                            <label>
                                <?php if ($question['question_type'] === 'single'): ?>
                                    <input type="radio" name="answers[]" value="<?= $ans['id'] ?>" required>
                                <?php else: ?>
                                    <input type="checkbox" name="answers[]" value="<?= $ans['id'] ?>">
                                <?php endif; ?>
                                <?= htmlspecialchars($ans['answer_text']) ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Далее</button>
        </form>

    <?php else: ?>
        <h2>Ваш результат</h2>
        <?php if ($result_data): ?>
            <div class="result-box">
                <div class="result-score"><?= $_SESSION[$session_key]['score'] ?> баллов</div>
                <div class="result-text"><?= nl2br(htmlspecialchars($result_data['result_text'])) ?></div>
            </div>
        <?php else: ?>
            <p>Результат не определён для набранных баллов (<?= $_SESSION[$session_key]['score'] ?>).</p>
        <?php endif; ?>

        <!-- Кнопка запуска квеста (если есть) -->
        <?php if (isset($_SESSION[$session_key]['test_result_id'])): ?>
            <div style="margin: 20px 0;">
                <a href="quest.php?result_id=<?= $_SESSION[$session_key]['test_result_id'] ?>" class="btn btn-primary" style="background: #8b5cf6;">🚀 Начать профориентационный квест</a>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="test.php?id=<?= $test_id ?>&reset=1" class="btn btn-primary">Пройти заново</a>
            <a href="tests.php" class="btn btn-outline">← Все тесты</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="btn btn-primary" style="background: var(--primary);">👤 Мой профиль</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>