<?php
// get_wordgrid_words.php — отдать слова для игры "Словоискатель" (поиск слов в сетке)
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

function normalize_token($s) {
    $s = trim($s);
    if ($s === '') return '';
    $s = mb_strtoupper($s, 'UTF-8');
    // Оставляем только буквы (латиница/кириллица), убираем остальное
    $s = preg_replace('/[^A-ZА-ЯЁ]/u', '', $s);
    if ($s === null) return '';
    $len = mb_strlen($s, 'UTF-8');
    if ($len < 4 || $len > 12) return '';
    return $s;
}

function split_to_tokens(string $text): array {
    $text = trim($text);
    if ($text === '') return [];
    // убираем html и приводим пробелы
    $text = strip_tags($text);
    $parts = preg_split('/[\s,.;:()«»"“”„!?\\/\\\\\\-]+/u', $text);
    if (!$parts) return [];
    return $parts;
}

try {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 240;
    if ($limit < 80) $limit = 80;
    if ($limit > 400) $limit = 400;

    $tokens = [];

    // Слова БЕРЁМ ТОЛЬКО по профессиям (таблица professions).
    // Чтобы слов было больше, используем не только title/category, но и описания.
    $rows = min(2000, $limit * 18);
    $sql = "SELECT title, category, short_description, full_description
            FROM professions
            ORDER BY RAND()
            LIMIT " . (int)$rows;

    $res = $db->query($sql);
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            foreach (['title', 'category', 'short_description', 'full_description'] as $k) {
                if (empty($r[$k])) continue;
                foreach (split_to_tokens((string)$r[$k]) as $p) {
                    $t = normalize_token($p);
                    if ($t !== '') $tokens[$t] = true;
                }
            }
            if (count($tokens) >= $limit) break;
        }
    }

    $out = array_keys($tokens);
    shuffle($out);
    if (count($out) > $limit) $out = array_slice($out, 0, $limit);

    echo json_encode([
        'success' => true,
        'words' => $out
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Server error'
    ], JSON_UNESCAPED_UNICODE);
}

