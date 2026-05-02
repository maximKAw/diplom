<?php
// get_leaderboard.php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Выбираем всех пользователей, у которых есть прогресс, сортируем по total_score
$query = "
    SELECT u.username, p.total_score, p.level
    FROM users u
    JOIN progress p ON u.id = p.user_id
    ORDER BY p.total_score DESC
    LIMIT 50
";

$result = $db->query($query);

if (!$result) {
    echo json_encode(['success' => false, 'error' => $db->error]);
    exit;
}

$leaderboard = [];
while ($row = $result->fetch_assoc()) {
    $leaderboard[] = [
        'name' => htmlspecialchars($row['username']),
        'totalScore' => (int)$row['total_score'],
        'level' => (int)$row['level']
    ];
}

echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);