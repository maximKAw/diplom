<?php
// save_progress.php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Получаем JSON из тела запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

// Извлекаем поля с приведением к int
$total_score = isset($data['total_score']) ? (int)$data['total_score'] : 0;
$level = isset($data['level']) ? (int)$data['level'] : 1;
$scratch_level = isset($data['scratch_level']) ? (int)$data['scratch_level'] : 1;
$lego_level = isset($data['lego_level']) ? (int)$data['lego_level'] : 1;
$wordgrid_level = isset($data['wordgrid_level']) ? (int)$data['wordgrid_level'] : 1;
$turtle_level = isset($data['turtle_level']) ? (int)$data['turtle_level'] : 1;
$calc_level = isset($data['calc_level']) ? (int)$data['calc_level'] : 1;
$python_level = isset($data['python_level']) ? (int)$data['python_level'] : 1;
$pascal_level = isset($data['pascal_level']) ? (int)$data['pascal_level'] : 1;
$paint_level = isset($data['paint_level']) ? (int)$data['paint_level'] : 1;

// Вставляем или обновляем запись (нужна колонка paint_level: ALTER TABLE progress ADD COLUMN paint_level INT NOT NULL DEFAULT 1;)
$stmt = $db->prepare("
    INSERT INTO progress (user_id, total_score, level, scratch_level, lego_level, wordgrid_level, turtle_level, calc_level, python_level, pascal_level, paint_level)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        total_score = VALUES(total_score),
        level = VALUES(level),
        scratch_level = VALUES(scratch_level),
        lego_level = VALUES(lego_level),
        wordgrid_level = VALUES(wordgrid_level),
        turtle_level = VALUES(turtle_level),
        calc_level = VALUES(calc_level),
        python_level = VALUES(python_level),
        pascal_level = VALUES(pascal_level),
        paint_level = VALUES(paint_level)
");

$stmt->bind_param(
    "iiiiiiiiiii",
    $user_id,
    $total_score,
    $level,
    $scratch_level,
    $lego_level,
    $wordgrid_level,
    $turtle_level,
    $calc_level,
    $python_level,
    $pascal_level,
    $paint_level
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $db->error]);
}

$stmt->close();