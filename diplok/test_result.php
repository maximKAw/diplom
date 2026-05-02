<?php
// test_result.php – детальный просмотр результата теста и этапов квеста
require_once 'config.php';
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$result_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем результат
$stmt = $db->prepare("
    SELECT r.*, t.title as test_title, t.description as test_description
    FROM user_test_results r
    JOIN tests t ON r.test_id = t.id
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->bind_param("ii", $result_id, $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$test_result = $res->fetch_assoc();
$stmt->close();

if (!$test_result) {
    header('Location: profile.php');
    exit;
}

// Получаем детальные ответы (если есть таблица user_answers)
$answers = null;
$table_check = $db->query("SHOW TABLES LIKE 'user_answers'");
if ($table_check && $table_check->num_rows > 0) {
    $stmt = $db->prepare("
        SELECT ua.*, q.question_text
        FROM user_answers ua
        JOIN questions q ON ua.question_id = q.id
        WHERE ua.result_id = ?
    ");
    if ($stmt) {
        $stmt->bind_param("i", $result_id);
        $stmt->execute();
        $answers = $stmt->get_result();
        $stmt->close();
    }
}

// Получаем пройденные этапы квеста для этого результата
$quest_results = [];
$stmt = $db->prepare("
    SELECT uqr.*, tq.title, tq.description, tq.type, tq.order_index
    FROM user_quest_results uqr
    JOIN test_quests tq ON uqr.quest_id = tq.id
    WHERE uqr.test_result_id = ?
    ORDER BY tq.order_index
");
if ($stmt) {
    $stmt->bind_param("i", $result_id);
    $stmt->execute();
    $quest_res = $stmt->get_result();
    while ($row = $quest_res->fetch_assoc()) {
        $quest_results[] = $row;
    }
    $stmt->close();
}

// Проверяем, есть ли доступные квесты и сколько пройдено
$total_quests = 0;
$stmt = $db->prepare("SELECT COUNT(*) as total FROM test_quests WHERE test_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $test_result['test_id']);
    $stmt->execute();
    $total_q_res = $stmt->get_result();
    if ($row = $total_q_res->fetch_assoc()) {
        $total_quests = $row['total'];
    }
    $stmt->close();
}

$completed_quests = count($quest_results);

require_once 'header.php';
?>

<style>
    .result-detail {
        max-width: 800px;
        margin: 100px auto 40px;
        background: #fff;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .back-link {
        margin-bottom: 1.5rem;
        display: inline-block;
    }
    .test-title {
        font-size: 2rem;
        color: #1e3c72;
        margin-bottom: 0.5rem;
    }
    .meta {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }
    .score-box {
        background: #f8f9fc;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        border-left: 4px solid #28a745;
    }
    .score {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e3c72;
    }
    .result-text {
        font-size: 1.2rem;
        margin-top: 1rem;
    }
    .answer-item {
        background: #f8f9fc;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    .question {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .user-answer {
        color: #28a745;
    }
    .btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        text-decoration: none;
        font-weight: 500;
        transition: 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-outline {
        background: transparent;
        border: 2px solid #1e3c72;
        color: #1e3c72;
    }
    .btn-outline:hover {
        background: #1e3c72;
        color: white;
    }
    .btn-primary {
        background: #2563eb;
        color: white;
        padding: 0.7rem 1.5rem;
    }
    .btn-primary:hover {
        background: #1d4ed8;
    }
</style>

<div class="result-detail">
    <a href="profile.php" class="back-link btn btn-outline">← Назад к профилю</a>
    <h1 class="test-title"><?= htmlspecialchars($test_result['test_title']) ?></h1>
    <div class="meta">Пройден: <?= date('d.m.Y H:i', strtotime($test_result['completed_at'])) ?></div>

    <div class="score-box">
        <div class="score"><?= $test_result['score'] ?> баллов</div>
        <div class="result-text"><?= nl2br(htmlspecialchars($test_result['result_text'])) ?></div>
    </div>

    <!-- Информация о квесте -->
    <?php if ($total_quests > 0): ?>
        <div style="margin: 20px 0;">
            <?php if ($completed_quests < $total_quests): ?>
                <a href="quest.php?result_id=<?= $test_result['id'] ?>" class="btn btn-primary" style="background: #8b5cf6;">🚀 Продолжить квест (<?= $completed_quests ?>/<?= $total_quests ?>)</a>
            <?php else: ?>
                <p style="color: #10b981;">✓ Все этапы квеста пройдены</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Детали ответов на тест (если есть) -->
    <?php if ($answers && $answers->num_rows > 0): ?>
        <h3>Детали ответов:</h3>
        <?php while ($ans = $answers->fetch_assoc()): ?>
            <div class="answer-item">
                <div class="question"><?= htmlspecialchars($ans['question_text']) ?></div>
                <div class="user-answer">
                    Ответ: 
                    <?php if (!empty($ans['answer_text'])): ?>
                        <?= htmlspecialchars($ans['answer_text']) ?>
                    <?php else: ?>
                        <?= htmlspecialchars($ans['answer_id']) ?>
                    <?php endif; ?>
                    (баллов: <?= $ans['points'] ?>)
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <!-- Пройденные этапы квеста -->
    <?php if (!empty($quest_results)): ?>
        <h3 style="margin-top: 2rem;">Пройденные этапы квеста</h3>
        <div style="display: grid; gap: 1rem; margin-top: 1rem;">
            <?php foreach ($quest_results as $qr): ?>
                <div style="background: #f1f5f9; padding: 1rem; border-radius: 12px; border-left: 4px solid #8b5cf6;">
                    <div style="font-weight: 600;"><?= htmlspecialchars($qr['title']) ?></div>
                    <div style="color: #475569; font-size: 0.9rem; margin: 0.5rem 0;">
                        <?= nl2br(htmlspecialchars($qr['description'] ?? '')) ?>
                    </div>
                    <?php 
                    $answer_data = json_decode($qr['answer_data'], true);
                    if ($answer_data): 
                    ?>
                    <div style="background: white; padding: 0.75rem; border-radius: 8px; margin-top: 0.5rem;">
                        <strong>Ваш ответ:</strong>
                        <?php if ($qr['type'] === 'choice' && isset($answer_data['choice'])): ?>
                            <?= htmlspecialchars($answer_data['choice']) ?>
                        <?php elseif ($qr['type'] === 'text' && isset($answer_data['text'])): ?>
                            <?= nl2br(htmlspecialchars($answer_data['text'])) ?>
                        <?php elseif ($qr['type'] === 'quiz'): ?>
                            <ul style="margin-top: 0.5rem; padding-left: 1.2rem;">
                                <?php foreach ($answer_data as $qid => $ans): 
                                    if (is_array($ans)) {
                                        echo "<li><strong>Вопрос $qid:</strong> " . implode(', ', array_map('htmlspecialchars', $ans)) . "</li>";
                                    } else {
                                        echo "<li><strong>Вопрос $qid:</strong> " . htmlspecialchars($ans) . "</li>";
                                    }
                                endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if ($qr['points_earned'] > 0): ?>
                            <span style="float: right; color: #10b981;">+<?= $qr['points_earned'] ?> баллов</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div style="font-size: 0.85rem; color: #6c757d; margin-top: 0.5rem;">
                        Пройдено: <?= date('d.m.Y H:i', strtotime($qr['completed_at'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>