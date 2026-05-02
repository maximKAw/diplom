<?php
// final_result.php – итоговый результат финального теста
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
    header('Location: profile.php');
    exit;
}

// Получаем результаты финального теста
$stmt = $db->prepare("SELECT * FROM user_final_results WHERE test_result_id = ?");
$stmt->bind_param("i", $test_result_id);
$stmt->execute();
$final = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$final) {
    // Если финал ещё не пройден, перенаправляем на его прохождение
    header('Location: final.php?result_id=' . $test_result_id);
    exit;
}

// Получаем все результаты этапов квеста для этого test_result
$quest_results = [];
$stmt = $db->prepare("
    SELECT uqr.*, tq.title, tq.description, tq.type, tq.order_index
    FROM user_quest_results uqr
    JOIN test_quests tq ON uqr.quest_id = tq.id
    WHERE uqr.test_result_id = ?
    ORDER BY tq.order_index
");
$stmt->bind_param("i", $test_result_id);
$stmt->execute();
$quest_res = $stmt->get_result();
while ($row = $quest_res->fetch_assoc()) {
    $quest_results[] = $row;
}
$stmt->close();

$total_quest_points = array_sum(array_column($quest_results, 'points_earned'));
$test_points = $test_result['score'];
$final_points = $final['score'];
$grand_total = $test_points + $total_quest_points + $final_points;

require_once 'header.php';
?>

<style>
    .final-container {
        max-width: 800px;
        margin: 100px auto 40px;
        background: #fff;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .final-title {
        font-size: 2rem;
        color: #1e3c72;
        margin-bottom: 1rem;
    }
    .summary-box {
        background: #f8f9fc;
        border-radius: 16px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        border-left: 4px solid #2563eb;
    }
    .stage-item {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #8b5cf6;
    }
    .stage-title {
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
    }
    .stage-points {
        color: #10b981;
        font-weight: 600;
    }
    .stage-date {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
    .grand-total {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        text-align: center;
        margin: 2rem 0;
    }
    .btn {
        display: inline-block;
        padding: 0.7rem 1.8rem;
        border-radius: 40px;
        font-weight: 500;
        text-decoration: none;
        transition: 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-primary {
        background: #2563eb;
        color: white;
    }
    .btn-primary:hover {
        background: #1d4ed8;
    }
    .btn-outline {
        background: transparent;
        border: 2px solid #2563eb;
        color: #2563eb;
    }
    .btn-outline:hover {
        background: #2563eb;
        color: white;
    }
</style>

<div class="final-container">
    <h1 class="final-title">Итоги профориентационного квеста</h1>
    <p>Тест: <strong><?= htmlspecialchars($test_result['test_title'] ?? 'Тест') ?></strong></p>
    <p>Пройден: <?= date('d.m.Y H:i', strtotime($test_result['completed_at'])) ?></p>

    <div class="summary-box">
        <h3 style="margin-bottom: 1rem;">Сводка по этапам</h3>
        <?php foreach ($quest_results as $qr): ?>
            <div class="stage-item">
                <div class="stage-title">
                    <span>Этап <?= $qr['order_index'] ?>: <?= htmlspecialchars($qr['title']) ?></span>
                    <span class="stage-points">+<?= $qr['points_earned'] ?> баллов</span>
                </div>
                <div class="stage-date">Пройдено: <?= date('d.m.Y H:i', strtotime($qr['completed_at'])) ?></div>
            </div>
        <?php endforeach; ?>
        <?php if ($final): ?>
            <div class="stage-item" style="border-left-color: #2563eb;">
                <div class="stage-title">
                    <span>Финальный тест (15 вопросов)</span>
                    <span class="stage-points">+<?= $final['score'] ?> / <?= $final['max_score'] ?> баллов</span>
                </div>
                <div class="stage-date">Пройдено: <?= date('d.m.Y H:i', strtotime($final['completed_at'])) ?></div>
            </div>
        <?php endif; ?>
        <div style="margin-top: 1rem;">
            <strong>Баллы за основной тест:</strong> <?= $test_points ?><br>
            <strong>Баллы за квест:</strong> <?= $total_quest_points ?><br>
            <strong>Баллы за финал:</strong> <?= $final_points ?><br>
        </div>
    </div>

    <div class="grand-total">
        ИТОГО БАЛЛОВ: <?= $grand_total ?>
    </div>

    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="profile.php" class="btn btn-primary">В профиль</a>
        <a href="tests.php" class="btn btn-outline">К тестам</a>
    </div>
</div>

<?php require_once 'footer.php'; ?>