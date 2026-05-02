<?php
// load_progress.php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Получаем имя пользователя
$stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

// Получаем прогресс
$stmt = $db->prepare("SELECT total_score, level, scratch_level, lego_level, wordgrid_level, turtle_level, calc_level, python_level, pascal_level, paint_level FROM progress WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$progress = $result->fetch_assoc();
$stmt->close();

if ($progress) {
    if (!isset($progress['paint_level']) || $progress['paint_level'] === null) {
        $progress['paint_level'] = 1;
    }
    if (!isset($progress['wordgrid_level']) || $progress['wordgrid_level'] === null) {
        $progress['wordgrid_level'] = 1;
    }
    $progress['username'] = $user['username'];
    $progress['success'] = true;
    echo json_encode($progress);
} else {
    // Возвращаем значения по умолчанию
    echo json_encode([
        'success' => true,
        'username' => $user['username'],
        'total_score' => 0,
        'level' => 1,
        'scratch_level' => 1,
        'lego_level' => 1,
        'wordgrid_level' => 1,
        'turtle_level' => 1,
        'calc_level' => 1,
        'python_level' => 1,
        'pascal_level' => 1,
        'paint_level' => 1
    ]);
}