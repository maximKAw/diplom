<?php
// profile.php – личный кабинет пользователя с результатами тестов и квестами
require_once 'config.php';
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'];

// Параметры сортировки
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';
if ($sort === 'date_asc') {
    $order_by = 'r.completed_at ASC';
} elseif ($sort === 'score_desc') {
    $order_by = 'r.score DESC';
} elseif ($sort === 'score_asc') {
    $order_by = 'r.score ASC';
} else {
    $order_by = 'r.completed_at DESC'; // date_desc по умолчанию
}

// Получаем аватар пользователя
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
} else {
    error_log("Ошибка подготовки запроса в profile.php: " . $db->error);
}

// Получаем все результаты тестов пользователя с сортировкой
$stmt = $db->prepare("
    SELECT r.*, t.title as test_title 
    FROM user_test_results r
    JOIN tests t ON r.test_id = t.id
    WHERE r.user_id = ?
    ORDER BY $order_by
");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $results = $stmt->get_result();
    $stmt->close();
} else {
    error_log("Ошибка подготовки запроса результатов: " . $db->error);
    $results = null;
}

require_once 'header.php';

// Дата регистрации из БД (если есть поле created_at)
$member_since = date('Y');
$stmt_reg = $db->prepare("SELECT created_at FROM users WHERE id = ?");
if ($stmt_reg) {
    $stmt_reg->bind_param("i", $user_id);
    $stmt_reg->execute();
    $reg_row = $stmt_reg->get_result()->fetch_assoc();
    $stmt_reg->close();
    if (!empty($reg_row['created_at'])) {
        $member_since = date('Y', strtotime($reg_row['created_at']));
    }
}
?>

<div class="app-shell app-shell--lift">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if (!empty($avatar) && file_exists(__DIR__ . '/' . $avatar)): ?>
                    <img src="<?= htmlspecialchars($avatar) ?>" alt="аватар">
                <?php else: ?>
                    <i class="fa-regular fa-user"></i>
                <?php endif; ?>
            </div>
            <div class="profile-title">
                <h2 class="profile-name"><?= htmlspecialchars($username) ?></h2>
                <p><i class="fa-regular fa-calendar" style="margin-right: 6px;"></i> Участник с <?= htmlspecialchars($member_since) ?> года</p>
            </div>
            <div class="profile-actions">
                <a href="edit_avatar.php" class="btn btn-outline"><i class="fa-regular fa-image"></i> Сменить аватар</a>
                <a href="edit_profile.php" class="btn btn-primary"><i class="fa-regular fa-pen-to-square"></i> Редактировать</a>
            </div>
        </div>

        <div class="profile-content">
            <div class="section-header">
                <div class="section-title">
                    <i class="fa-regular fa-chart-bar"></i>
                    <span>Мои результаты тестов</span>
                </div>
                <div class="sort-dropdown">
                    <a href="?sort=date_desc" class="sort-btn <?= $sort == 'date_desc' ? 'active' : '' ?>">Новые</a>
                    <a href="?sort=date_asc" class="sort-btn <?= $sort == 'date_asc' ? 'active' : '' ?>">Старые</a>
                    <a href="?sort=score_desc" class="sort-btn <?= $sort == 'score_desc' ? 'active' : '' ?>">Баллы (выс)</a>
                    <a href="?sort=score_asc" class="sort-btn <?= $sort == 'score_asc' ? 'active' : '' ?>">Баллы (низ)</a>
                </div>
            </div>

            <?php if ($results && $results->num_rows > 0): ?>
                <div class="results-grid">
                    <?php while ($row = $results->fetch_assoc()): 
                        // Получаем детальные ответы на тест
                        $test_answers = [];
                        $stmt_a = $db->prepare("
                            SELECT ua.*, q.question_text 
                            FROM user_answers ua
                            JOIN questions q ON ua.question_id = q.id
                            WHERE ua.result_id = ?
                        ");
                        if ($stmt_a) {
                            $stmt_a->bind_param("i", $row['id']);
                            $stmt_a->execute();
                            $ans_res = $stmt_a->get_result();
                            while ($ans = $ans_res->fetch_assoc()) {
                                $test_answers[] = $ans;
                            }
                            $stmt_a->close();
                        }

                        // Получаем информацию о квестах для этого результата
                        $quest_count = 0;
                        $quest_done = 0;
                        $quest_results = [];
                        
                        $stmt_q = $db->prepare("SELECT COUNT(*) as total FROM test_quests WHERE test_id = ?");
                        if ($stmt_q) {
                            $stmt_q->bind_param("i", $row['test_id']);
                            $stmt_q->execute();
                            $total_q_res = $stmt_q->get_result();
                            if ($total_q = $total_q_res->fetch_assoc()) {
                                $quest_count = $total_q['total'];
                            }
                            $stmt_q->close();
                        }

                        if ($quest_count > 0) {
                            $stmt_d = $db->prepare("
                                SELECT uqr.*, tq.title, tq.description, tq.type, tq.order_index
                                FROM user_quest_results uqr
                                JOIN test_quests tq ON uqr.quest_id = tq.id
                                WHERE uqr.test_result_id = ?
                                ORDER BY tq.order_index
                            ");
                            if ($stmt_d) {
                                $stmt_d->bind_param("i", $row['id']);
                                $stmt_d->execute();
                                $quest_res = $stmt_d->get_result();
                                while ($qr = $quest_res->fetch_assoc()) {
                                    $quest_results[] = $qr;
                                }
                                $quest_done = count($quest_results);
                                $stmt_d->close();
                            }
                        }

                        $total_quest_points = array_sum(array_column($quest_results, 'points_earned'));
                        $test_points = $row['score'];
                        $grand_total = $test_points + $total_quest_points;
                    ?>
                        <div class="result-card" onclick="this.classList.toggle('expanded')">
                            <div class="result-header">
                                <div class="result-info">
                                    <h3><?= htmlspecialchars($row['test_title']) ?></h3>
                                    <div class="result-meta">
                                        <span><i class="fa-regular fa-star"></i> Тест: <?= (int)$row['score'] ?> баллов</span>
                                        <span><i class="fa-regular fa-clock"></i> <?= date('d.m.Y H:i', strtotime($row['completed_at'])) ?></span>
                                        <?php if ($quest_count > 0): ?>
                                            <span><i class="fa-regular fa-flag"></i> Квест: <?= $quest_done ?>/<?= $quest_count ?> этапов</span>
                                            <span><i class="fa-regular fa-coins"></i> Квест: +<?= $total_quest_points ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="grand-total">
                                    Итого: <?= $grand_total ?>
                                </div>
                            </div>

                            <div class="result-details">
                                <!-- Ответы на основной тест -->
                                <?php if (!empty($test_answers)): ?>
                                    <h4 style="margin-bottom: 1rem; color: var(--text);">Ответы на тест</h4>
                                    <div class="test-answers">
                                        <?php foreach ($test_answers as $ans): ?>
                                            <div class="answer-item">
                                                <div class="question-text"><?= htmlspecialchars($ans['question_text']) ?></div>
                                                <div class="user-answer">
                                                    Ответ: <?= htmlspecialchars($ans['answer_text']) ?>
                                                </div>
                                                <div class="answer-points">Баллов: <?= $ans['points_earned'] ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Пройденные этапы квеста -->
                                <?php if (!empty($quest_results)): ?>
                                    <h4 style="margin: 1.5rem 0 1rem; color: var(--text);">Пройденные этапы квеста</h4>
                                    <?php foreach ($quest_results as $qr): ?>
                                        <div class="stage-item">
                                            <div class="stage-title">
                                                <span>Этап <?= $qr['order_index'] ?>: <?= htmlspecialchars($qr['title'] ?? '') ?></span>
                                                <span class="stage-points">+<?= $qr['points_earned'] ?> баллов</span>
                                            </div>
                                            <div class="stage-date">Пройдено: <?= date('d.m.Y H:i', strtotime($qr['completed_at'])) ?></div>
                                            <?php 
                                            $answer_data = json_decode($qr['answer_data'], true);
                                            if ($answer_data): 
                                            ?>
                                                <div class="stage-answer">
                                                    <strong>Детали:</strong>
                                                    <?php if ($qr['type'] === 'quiz'): ?>
                                                        <ul style="margin-top: 0.5rem; padding-left: 1.2rem;">
                                                            <?php foreach ($answer_data as $qid => $ans): 
                                                                if (is_array($ans)) {
                                                                    echo "<li>Вопрос $qid: " . implode(', ', array_map('htmlspecialchars', $ans)) . "</li>";
                                                                } else {
                                                                    echo "<li>Вопрос $qid: " . htmlspecialchars($ans) . "</li>";
                                                                }
                                                            endforeach; ?>
                                                        </ul>
                                                    <?php elseif ($qr['type'] === 'choice' && isset($answer_data['choice'])): ?>
                                                        <p>Выбор: <?= htmlspecialchars($answer_data['choice']) ?></p>
                                                    <?php elseif ($qr['type'] === 'text' && isset($answer_data['text'])): ?>
                                                        <p><?= nl2br(htmlspecialchars($answer_data['text'])) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-regular fa-clipboard"></i>
                    <p>Вы ещё не прошли ни одного теста. Самое время попробовать!</p>
                    <a href="tests.php" class="btn btn-primary"><i class="fa-regular fa-flask"></i> Перейти к тестам</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Для предотвращения конфликтов с кликом по ссылкам внутри карточки
    document.querySelectorAll('.result-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) return;
            this.classList.toggle('expanded');
        });
    });
</script>

<?php require_once 'footer.php'; ?>