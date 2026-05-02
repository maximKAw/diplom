<?php
// trenasher.php – Тренажёр по программированию
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/config.php';

$user = null;
if (isset($_SESSION['user_id'])) {
    $id = (int)$_SESSION['user_id'];
    $res = $db->query("SELECT * FROM users WHERE id = $id");
    $user = $res->fetch_assoc();
}
$loggedIn = (bool)$user;
?>
<script src="https://cdn.jsdelivr.net/npm/skulpt@1.2.0/dist/skulpt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/skulpt@1.2.0/dist/skulpt-stdlib.js"></script>
<style>
        .trainer-root {
            background: #f8fafc;
            padding-bottom: 40px;
        }
        .trainer-root .trainer-page,
        .trainer-root .trainer-page * {
            box-sizing: border-box;
        }
        .trainer-page {
            font-family: 'Segoe UI', Roboto, system-ui, sans-serif;
        }
        .trainer-page {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .trainer-shell {
            background: #fff;
            border-radius: 0;
            box-shadow: 0 4px 24px rgba(30, 40, 80, 0.08);
            border: none;
            border-bottom: 1px solid rgba(230, 232, 245, 0.9);
            overflow: hidden;
            margin-bottom: 0;
        }
        .guest-banner {
            background: #fff8e6;
            border: 1px solid #f0d78c;
            color: #856404;
            padding: 12px 18px;
            border-radius: 12px;
            margin: 16px max(16px, 3vw);
            font-size: 0.95rem;
            line-height: 1.45;
        }
        .guest-banner a { color: #1a4d8c; font-weight: 600; }
        .stats-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fc;
            padding: 14px 22px;
            border-radius: 999px;
            margin: 16px max(16px, 3vw) 20px;
            border: 1px solid #e9ecef;
            flex-wrap: wrap;
            gap: 12px;
        }
        .stats-panel.stats-panel--guest .score-box { background: #5a6f8c; }
        .stats-panel.stats-panel--guest .level-box { background: #6b9080; }
        .score-box {
            background: #1a4d8c;
            color: white;
            padding: 8px 25px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1.4rem;
        }
        .level-box {
            background: #28a745;
            color: white;
            padding: 8px 25px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1.4rem;
        }
        .reset-stats {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 25px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            font-size: 1rem;
        }
        .reset-stats:hover {
            background: #c82333;
        }
        .auth-panel {
            background: #f8f9fc;
            padding: 12px 22px;
            border-radius: 14px;
            margin: 16px max(16px, 3vw) 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            border: 1px solid #e9ecef;
        }
        .auth-panel .user-info {
            font-weight: 600;
            color: #410cdf;
        }
        .auth-panel button, .auth-panel a {
            padding: 8px 20px;
            border-radius: 40px;
            border: none;
            background: linear-gradient(135deg, #410cdf 0%, #5b2ef0 100%);
            color: white;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .auth-panel a {
            background: #6c757d;
        }
        .auth-panel--compact {
            justify-content: flex-end;
            margin-top: 12px;
        }
        .trainer-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0;
            padding: 14px max(14px, 3vw) 0;
            background: #f1f3f7;
            border-bottom: 1px solid #dee2e6;
        }
        .trainer-tab {
            padding: 12px 25px;
            background: #e9ecef;
            border-radius: 40px 40px 0 0;
            font-weight: 600;
            color: #495057;
            cursor: pointer;
            transition: 0.2s;
            border: 2px solid transparent;
            border-bottom: none;
            font-size: 1rem;
        }
        .trainer-tab.active {
            background: white;
            color: #410cdf;
            border-color: #410cdf;
            border-bottom: 2px solid white;
            margin-bottom: -2px;
        }
        .trainer-tab:hover:not(.active) {
            background: #dee2e6;
        }
        .trainer-container {
            display: none;
            background: #fff;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
            margin: 0;
        }
        .trainer-container.active {
            display: block;
        }
        .command-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 15px max(15px, 3vw);
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .command-btn {
            padding: 8px 16px;
            background: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 30px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
            font-size: 0.9rem;
        }
        .command-btn:hover {
            background: #dee2e6;
        }
        .command-btn.primary {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }
        .command-btn.primary:hover {
            background: #218838;
        }
        .command-btn.warning {
            background: #ffc107;
            color: #212529;
            border-color: #ffc107;
        }
        .command-btn.warning:hover {
            background: #e0a800;
        }
        .command-btn.secondary {
            background: #17a2b8;
            color: white;
            border-color: #17a2b8;
        }
        .command-btn.secondary:hover {
            background: #138496;
        }
        .command-btn.undo {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        .command-btn.undo:hover {
            background: #5a6268;
        }
        /* Поле спрайта — отдельный визуальный язык команд */
        .sprite-workspace {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            padding: 14px max(15px, 3vw) 10px;
            background: linear-gradient(180deg, #f8fafc 0%, #fff 40%);
            border-bottom: 1px solid #e2e8f0;
            align-items: flex-start;
        }
        .sprite-palette {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            max-width: min(720px, 100%);
        }
        .sprite-chip {
            border: none;
            border-radius: 12px;
            padding: 10px 14px;
            color: #fff;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            box-shadow: inset 0 -3px rgba(0,0,0,0.15), 0 2px 6px rgba(15, 23, 42, 0.08);
            transition: transform 0.12s, filter 0.12s;
        }
        .sprite-chip:hover { filter: brightness(1.05); transform: translateY(-1px); }
        .sprite-chip--move { background: linear-gradient(180deg, #14b8a6, #0d9488); }
        .sprite-chip--turn { background: linear-gradient(180deg, #a78bfa, #7c3aed); }
        .sprite-chip--voice { background: linear-gradient(180deg, #f97316, #c2410c); }
        .sprite-chip--look { background: linear-gradient(180deg, #f472b6, #db2777); }
        .sprite-chip--sys { background: linear-gradient(180deg, #64748b, #475569); }
        .sprite-queue-panel {
            flex: 1;
            min-width: 240px;
            background: #f1f5f9;
            border-radius: 16px;
            padding: 14px;
            border: 2px solid #cbd5e1;
        }
        .sprite-queue-title {
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 10px;
        }
        .sprite-queue {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 6px;
            min-height: 88px;
        }
        .sprite-step {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-radius: 12px;
            padding: 9px 12px;
            color: #fff;
            font-weight: 600;
            font-size: 0.86rem;
            box-shadow: inset 0 -2px rgba(0,0,0,0.12);
        }
        .sprite-step--move { background: linear-gradient(180deg, #14b8a6, #0d9488); }
        .sprite-step--turn { background: linear-gradient(180deg, #a78bfa, #7c3aed); }
        .sprite-step--voice { background: linear-gradient(180deg, #f97316, #c2410c); }
        .sprite-step--look { background: linear-gradient(180deg, #f472b6, #db2777); }
        .sprite-step--sys { background: linear-gradient(180deg, #64748b, #475569); }
        .sprite-step-remove {
            border: none;
            background: rgba(255,255,255,0.22);
            color: #fff;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 800;
            line-height: 1;
        }
        .canvas-area--maze canvas {
            max-width: min(440px, 92vw) !important;
        }
        /* Инструкции / подсказки: акцент #410cdf, сворачивание */
        .sim-instruction-outer {
            margin: 0 max(16px, 3vw) 12px;
        }
        .sim-instruction {
            padding: 12px 16px;
            background: rgba(65, 12, 223, 0.06);
            border: 2px solid #410cdf;
            border-radius: 14px;
            font-size: 0.9rem;
            line-height: 1.55;
            color: #410cdf;
        }
        .sim-instruction-outer.is-collapsed .sim-instruction {
            padding: 10px 14px;
        }
        .sim-instruction-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px 12px;
            margin-bottom: 10px;
        }
        .sim-instruction-outer.is-collapsed .sim-instruction-head {
            margin-bottom: 0;
        }
        .sim-instruction-head strong {
            font-size: 1rem;
            color: #410cdf;
            margin: 0;
        }
        .sim-instruction-toggle {
            flex-shrink: 0;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 999px;
            border: 2px solid #410cdf;
            background: #fff;
            color: #410cdf;
            cursor: pointer;
        }
        .sim-instruction-toggle:hover {
            background: rgba(65, 12, 223, 0.08);
        }
        .sim-instruction-outer.is-collapsed .sim-instruction-body {
            display: none;
        }
        .sim-instruction ul {
            margin: 8px 0 0 1.1em;
            padding: 0;
        }
        .sim-instruction li { margin-bottom: 6px; }
        .sim-instruction code {
            background: rgba(65, 12, 223, 0.12);
            padding: 1px 6px;
            border-radius: 6px;
            font-size: 0.85em;
            color: #410cdf;
        }
        .sim-instruction-body > p:first-child { margin-top: 0; }
        .sim-instruction-body p { margin: 0 0 8px; }
        .sim-instruction-body p:last-child { margin-bottom: 0; }
        .sim-instruction-body--sandbox {
            white-space: pre-line;
        }
        .sim-instruction-body--sandbox strong {
            display: block;
            margin-bottom: 6px;
            color: #410cdf;
        }
        .paint-panel {
            padding: 12px max(15px, 3vw);
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .paint-cmd-input {
            width: 100%;
            min-height: 100px;
            padding: 12px max(12px, 2vw);
            border-radius: 10px;
            border: 1px solid #ced4da;
            font-family: Consolas, monospace;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .paint-help {
            font-size: 0.88rem;
            color: #410cdf;
            margin-bottom: 10px;
            line-height: 1.45;
        }
        .sim-instruction .paint-hint-box {
            margin: 10px 0 0;
            padding: 10px 12px;
            background: rgba(65, 12, 223, 0.08);
            border: 1px solid rgba(65, 12, 223, 0.35);
            border-radius: 10px;
            font-size: 0.95rem;
            line-height: 1.45;
            color: #410cdf;
        }
        .sim-instruction .paint-hint-box #paintHintText {
            margin-left: 8px;
            display: inline;
            white-space: pre-line;
        }
        .command-list {
            background: #fff;
            padding: 15px max(15px, 3vw);
            border-bottom: 1px solid #dee2e6;
            min-height: 80px;
            font-family: monospace;
            white-space: pre-wrap;
        }
        .command-item {
            display: inline-block;
            background: #e2e3e5;
            padding: 4px 12px;
            margin: 4px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .canvas-area {
            background: #f1f3f5;
            padding: 20px max(16px, 3vw);
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        .canvas-area canvas {
            display: block;
            margin: 0 auto;
            width: 100%;
            max-width: min(1200px, 96vw);
            height: auto;
            border: 2px solid #adb5bd;
            background: white;
            box-shadow: 0 5px 10px rgba(0,0,0,0.05);
        }
        .output-area {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px max(20px, 3vw);
            font-family: 'Courier New', monospace;
            font-size: 14px;
            border-radius: 0;
            min-height: 70px;
            white-space: pre-wrap;
        }
        .trainer-page .leaderboard {
            background: white;
            border-radius: 12px;
            padding: 20px max(20px, 3vw);
            margin: 24px max(16px, 3vw) 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        .leaderboard h3 {
            color: #1a4d8c;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        .leaderboard table {
            width: 100%;
            border-collapse: collapse;
        }
        .leaderboard th, .leaderboard td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .leaderboard th {
            background: #1a4d8c;
            color: white;
            font-weight: 600;
        }
        .leaderboard tr:hover {
            background: #f1f3f5;
        }
        .sandbox-selector {
            display: flex;
            gap: 10px;
            margin: 0 max(16px, 3vw) 10px;
        }
        .sandbox-selector button {
            flex: 1;
            padding: 10px;
            border: 1px solid #ced4da;
            background: #e9ecef;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
        }
        .sandbox-selector button.active {
            background: #1a4d8c;
            color: white;
            border-color: #1a4d8c;
        }
        .command-panel input#calcExpression {
            flex: 2;
            min-width: 180px;
            padding: 12px 16px;
            border-radius: 30px;
            border: 1px solid #ced4da;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 1rem;
        }
        .trainer-container > div[style*="background:#e9ecef"] {
            padding-left: max(10px, 2vw) !important;
            padding-right: max(10px, 2vw) !important;
        }
        /* Словоискатель (поиск слов в сетке) */
        .wordgrid-shell {
            padding: 14px max(15px, 3vw);
            background: linear-gradient(180deg, #f8fafc 0%, #fff 55%);
            border-bottom: 1px solid #dee2e6;
        }
        .wordgrid-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }
        .wordgrid-top strong { color: #173a5e; }
        .wordgrid-top .hint {
            color: #410cdf;
            font-size: 0.92rem;
            line-height: 1.35;
        }
        .wordgrid-controls {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .wordgrid-controls label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #173a5e;
            background: rgba(65, 12, 223, 0.06);
            border: 1px solid rgba(65, 12, 223, 0.14);
            padding: 8px 12px;
            border-radius: 999px;
        }
        .wordgrid-controls select {
            border: 1px solid rgba(65, 12, 223, 0.22);
            border-radius: 999px;
            padding: 6px 10px;
            background: #fff;
            font-weight: 800;
            color: #410cdf;
            outline: none;
        }
        .wordgrid-layout {
            display: grid;
            grid-template-columns: 1fr minmax(220px, 320px);
            gap: 16px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .wordgrid-layout { grid-template-columns: 1fr; }
        }
        .wordgrid-grid {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(30, 40, 80, 0.06);
        }
        .wordgrid-grid-inner {
            display: grid;
            user-select: none;
            touch-action: none;
        }
        .wg-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #0f172a;
            border-right: 1px solid rgba(230, 232, 245, 0.9);
            border-bottom: 1px solid rgba(230, 232, 245, 0.9);
            background: #fff;
        }
        .wg-cell.is-active { background: rgba(65, 12, 223, 0.12); }
        .wg-cell.is-found { background: rgba(40, 167, 69, 0.18); color: #0f3e75; }
        .wordgrid-side {
            background: #fff;
            border: 1px solid rgba(65, 12, 223, 0.12);
            border-radius: 14px;
            padding: 12px;
            box-shadow: 0 4px 16px rgba(65, 12, 223, 0.06);
        }
        .wordgrid-words {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .wg-word {
            padding: 6px 10px;
            border-radius: 999px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            font-weight: 700;
            font-size: 0.9rem;
            color: #334155;
        }
        .wg-word.is-found {
            background: rgba(40, 167, 69, 0.16);
            border-color: rgba(40, 167, 69, 0.45);
            color: #0f3e75;
            text-decoration: line-through;
        }
        @media (max-width: 768px) {
            .stats-panel { flex-direction: column; align-items: stretch; text-align: center; border-radius: 16px; }
        }
    </style>

<div class="trainer-root">
<div class="trainer-page">
<div class="trainer-shell">

    <?php if (!$user): ?>
    <div class="auth-panel auth-panel--compact" id="authPanel">
        <div>
            <button type="button" onclick="location.href='login.php'">Войти</button>
            <button type="button" onclick="location.href='register.php'">Регистрация</button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!$loggedIn): ?>
    <div class="guest-banner">
        <strong>Режим гостя:</strong> прогресс не сохраняется. <a href="register.php">Зарегистрируйтесь</a>, чтобы очки и уровни шли в профиль и таблицу лидеров.
    </div>
    <?php endif; ?>

    <div class="stats-panel<?= !$loggedIn ? ' stats-panel--guest' : '' ?>" id="statsPanel">
        <div class="score-box" id="scoreDisplay">🏆 <?= $loggedIn ? 'Общие очки' : 'Очки (сессия)' ?>: 0</div>
        <div class="level-box" id="levelDisplay">⭐ <?= $loggedIn ? 'Общий уровень' : 'Уровень (сессия)' ?>: 1</div>
        <button type="button" class="reset-stats" id="resetStatsBtn">♻ Сбросить <?= $loggedIn ? 'прогресс' : 'сессию' ?></button>
    </div>

    <div class="trainer-tabs">
        <div class="trainer-tab active" data-tab="calc">🧮 Калькулятор</div>
        <div class="trainer-tab" data-tab="sprite">🎮 Поле спрайта</div>
        <div class="trainer-tab" data-tab="wordgrid">🔎 Словоискатель</div>
        <div class="trainer-tab" data-tab="robotmaze">🤖 Робо-лабиринт</div>
        <div class="trainer-tab" data-tab="turtle">🐢 Черепашка</div>
        <div class="trainer-tab" data-tab="paint">🎨 Раскраска</div>
        <div class="trainer-tab" data-tab="sandbox">💻 Песочница Python/Pascal</div>
    </div>

    <div id="trainer-calc" class="trainer-container active">
        <div class="sim-instruction-outer" data-sim-instruction>
            <div class="sim-instruction">
                <div class="sim-instruction-head">
                    <strong>Калькулятор</strong>
                    <button type="button" class="sim-instruction-toggle" aria-expanded="true">Свернуть</button>
                </div>
                <div class="sim-instruction-body">
                    <p>Введи полную строку примера в формате «выражение = ответ», например <code>10 - 4 = 6</code>. Нажми «Проверить». Сложность растёт с уровнем.</p>
                </div>
            </div>
        </div>
        <div class="command-panel">
            <input type="text" id="calcExpression" placeholder="Полная строка, напр.: 10 - 4 = 6" autocomplete="off">
            <button class="command-btn primary" onclick="runCalc()">✅ Проверить</button>
            <button class="command-btn warning" onclick="resetCalc()">↺ Новый пример</button>
        </div>
        <pre id="calcOutput" class="output-area">Ожидание примера...</pre>
        <div style="padding:10px; background:#e9ecef; text-align:center;" id="calcLevelDisplay">🧮 Уровень «Калькулятор»: 1</div>
    </div>

    <!-- Поле спрайта: очередь шагов -->
    <div id="trainer-sprite" class="trainer-container">
        <div class="sim-instruction-outer" data-sim-instruction>
            <div class="sim-instruction">
                <div class="sim-instruction-head">
                    <strong>Поле спрайта</strong>
                    <button type="button" class="sim-instruction-toggle" aria-expanded="true">Свернуть</button>
                </div>
                <div class="sim-instruction-body">
                    <p>Собери <strong>очередь шагов</strong> слева — они выполняются по порядку. Цель уровня: попасть в зелёный <strong>Финиш</strong> (без «воды» и без звезды).</p>
                </div>
            </div>
        </div>
        <div class="sprite-workspace">
            <div class="sprite-palette">
                <button type="button" class="sprite-chip sprite-chip--move" onclick="addScratchCommand('move', 10)">шаг 10</button>
                <button type="button" class="sprite-chip sprite-chip--move" onclick="addScratchCommand('move', 20)">шаг 20</button>
                <button type="button" class="sprite-chip sprite-chip--move" onclick="addScratchCommand('move', 40)">шаг 40</button>
                <button type="button" class="sprite-chip sprite-chip--move" onclick="addScratchCommand('move', -18)">шаг −18</button>
                <button type="button" class="sprite-chip sprite-chip--move" onclick="addScratchCommand('jump')">рывок</button>
                <button type="button" class="sprite-chip sprite-chip--turn" onclick="addScratchCommand('turn', 'left', 90)">↺ 90°</button>
                <button type="button" class="sprite-chip sprite-chip--turn" onclick="addScratchCommand('turn', 'right', 90)">↻ 90°</button>
                <button type="button" class="sprite-chip sprite-chip--turn" onclick="addScratchCommand('turn', 'left', 45)">↺ 45°</button>
                <button type="button" class="sprite-chip sprite-chip--turn" onclick="addScratchCommand('turn', 'right', 30)">↻ 30°</button>
                <button type="button" class="sprite-chip sprite-chip--turn" onclick="addScratchCommand('angle', 0)">угол 0°</button>
                <button type="button" class="sprite-chip sprite-chip--turn" onclick="addScratchCommand('angle', 90)">угол 90°</button>
                <button type="button" class="sprite-chip sprite-chip--turn" onclick="addScratchCommand('angle', -90)">угол −90°</button>
                <button type="button" class="sprite-chip sprite-chip--voice" onclick="addScratchCommand('say', 'Привет!')">реплика «Привет!»</button>
                <button type="button" class="sprite-chip sprite-chip--voice" onclick="addScratchCommand('say', 'Готово!')">реплика «Готово!»</button>
                <button type="button" class="sprite-chip sprite-chip--voice" onclick="addScratchCommand('wait')">пауза в логе</button>
                <button type="button" class="sprite-chip sprite-chip--look" onclick="addScratchCommand('color')">новый оттенок</button>
                <button type="button" class="sprite-chip sprite-chip--look" onclick="addScratchCommand('randomHue')">случайный цвет</button>
                <button type="button" class="sprite-chip sprite-chip--look" onclick="addScratchCommand('scale', 0.12)">крупнее</button>
                <button type="button" class="sprite-chip sprite-chip--look" onclick="addScratchCommand('scale', -0.12)">меньше</button>
                <button type="button" class="sprite-chip sprite-chip--look" onclick="addScratchCommand('nudge')">лёгкая дрожь</button>
                <button type="button" class="sprite-chip sprite-chip--sys" onclick="addScratchCommand('home')">в центр поля</button>
                <button type="button" class="sprite-chip sprite-chip--sys" onclick="addScratchCommand('trail', true)">вкл. след</button>
                <button type="button" class="sprite-chip sprite-chip--sys" onclick="addScratchCommand('trail', false)">выкл. след</button>
            </div>
            <div class="sprite-queue-panel">
                <div class="sprite-queue-title">Очередь выполнения</div>
                <div class="sprite-queue command-list" id="scratchCommands" style="border:none; min-height:80px;"></div>
            </div>
        </div>
        <div class="command-panel">
            <button type="button" class="command-btn undo" onclick="undoScratch()">↩ Убрать последний шаг</button>
            <button type="button" class="command-btn primary" onclick="runScratch()">▶ Запустить очередь</button>
            <button type="button" class="command-btn warning" onclick="resetScratch()">↺ Очистить очередь</button>
        </div>
        <div class="canvas-area">
            <canvas id="scratchCanvas" width="400" height="300"></canvas>
        </div>
        <div id="scratchOutput" class="output-area"></div>
        <div style="padding:10px; background:#e9ecef; text-align:center;" id="scratchLevelDisplay">🎮 Уровень «Поле спрайта»: 1</div>
    </div>

    <!-- Словоискатель: найди слова в сетке -->
    <div id="trainer-wordgrid" class="trainer-container">
        <div class="sim-instruction-outer" data-sim-instruction>
            <div class="sim-instruction">
                <div class="sim-instruction-head">
                    <strong>Словоискатель</strong>
                    <button type="button" class="sim-instruction-toggle" aria-expanded="true">Свернуть</button>
                </div>
                <div class="sim-instruction-body">
                    <p>Выделяй слова в сетке: нажми на букву и веди по прямой (по горизонтали/вертикали/диагонали). Слова берутся случайно из базы и обновляются каждый уровень.</p>
                </div>
            </div>
        </div>
        <div class="wordgrid-shell">
            <div class="wordgrid-top">
                <div>
                    <strong>Задача:</strong> найти все слова справа.
                    <div class="hint" id="wordgridStatus">Генерируем сетку...</div>
                </div>
                <div class="wordgrid-controls">
                    <label>
                        Размер
                        <select id="wordgridSizeSelect" onchange="setWordGridSize(this.value)">
                            <option value="14">14×14</option>
                            <option value="16">16×16</option>
                            <option value="18">18×18</option>
                            <option value="20" selected>20×20</option>
                            <option value="22">22×22</option>
                            <option value="24">24×24</option>
                        </select>
                    </label>
                    <button type="button" class="command-btn secondary" onclick="newWordGridWords()">🎲 Новые слова</button>
                    <button type="button" class="command-btn primary" onclick="newWordGrid()">↺ Новая сетка</button>
                </div>
            </div>
            <div class="wordgrid-layout">
                <div class="wordgrid-grid">
                    <div class="wordgrid-grid-inner" id="wordgridGrid" aria-label="Сетка слов"></div>
                </div>
                <div class="wordgrid-side">
                    <div style="font-weight:800; margin: 0 0 10px; color:#173a5e;">Слова</div>
                    <div class="wordgrid-words" id="wordgridWords"></div>
                </div>
            </div>
        </div>
        <div id="wordgridOutput" class="output-area"></div>
        <div style="padding:10px; background:#e9ecef; text-align:center;" id="wordgridLevelDisplay">🔎 Уровень «Словоискатель»: 1</div>
    </div>

    <!-- Побег из дома: сетка — ключ, дверь, зелёный угол -->
    <div id="trainer-robotmaze" class="trainer-container">
        <div class="sim-instruction-outer" data-sim-instruction>
            <div class="sim-instruction">
                <div class="sim-instruction-head">
                    <strong>Робо-лабиринт — «Побег из дома»</strong>
                    <button type="button" class="sim-instruction-toggle" aria-expanded="true" aria-controls="robotManualBody">Свернуть</button>
                </div>
                <div class="sim-instruction-body" id="robotManualBody">
                    <p><strong>Главный герой</strong> — на поле это <strong>синий квадрат с жёлтой стрелкой</strong>: робот, которым ты управляешь программой (стрелка показывает, куда пойдёт «Вперёд»).</p>
                    <p>Робот выполняет <strong>программу</strong> слева направо. На каждом уровне цель одна: <strong>уйти из дома</strong>, взять <strong>ключ в сундуке</strong>, <strong>открыть дверь</strong> (встань к коричневой двери лицом и выполни «Открыть дверь») и <strong>дойти до зелёного квадрата</strong>.</p>
                    <ul>
                        <li><strong>Дом</strong> — тёплая клетка в центре, отсюда стартуешь. <strong>Сундук</strong> — клетка с ящиком: команда <strong>«Забрать ключ»</strong> только стоя на сундуке (ключ нужен для двери).</li>
                        <li><strong>Дверь</strong> — закрыта, пока не возьмёшь ключ и не откроешь её командой; дрель по двери не действует.</li>
                        <li><strong>Финиш</strong> — зелёный угол <code>(последняя колонка, последняя строка)</code>. Зачёт только если ключ взят и дверь открыта.</li>
                        <li><strong>Вперёд N</strong> — по направлению стрелки; <strong>Вверх / Вниз</strong> — на одну клетку по сетке (вверх — строка −1).</li>
                        <li><strong>Шипы</strong> (красный крест) — возврат <strong>в центр (дом)</strong> и <strong>−10</strong> очков.</li>
                        <li><strong>Дрель</strong> — ломает обычную серую стену перед роботом (не дверь).</li>
                    </ul>
                    <p>Соберите программу и нажмите <strong>«Выполнить программу»</strong>. Размер поля растёт с уровнем (5×5 … 9×9).</p>
                </div>
            </div>
        </div>
        <div class="command-panel">
            <button type="button" class="command-btn" onclick="addMazeCommand('move', 1)">➡️ Вперёд 1</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('move', 2)">➡️ Вперёд 2</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('move', 3)">➡️ Вперёд 3</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('turn', 'left')">↺ Налево</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('turn', 'right')">↻ Направо</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('up')">⬆️ Вверх</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('down')">⬇️ Вниз</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('grab')">🗝️ Забрать ключ</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('open')">🚪 Открыть дверь</button>
            <button type="button" class="command-btn" onclick="addMazeCommand('drill')" id="mazeDrillBtn" style="display:none;">🔧 Дрель</button>
            <button type="button" class="command-btn undo" onclick="undoMaze()">↩ Убрать последнюю</button>
            <button type="button" class="command-btn primary" onclick="runMaze()">▶ Выполнить программу</button>
            <button type="button" class="command-btn warning" onclick="resetMaze()">↺ Сброс программы</button>
        </div>
        <div class="command-list" id="mazeCommands"></div>
        <div class="canvas-area canvas-area--maze">
            <canvas id="mazeCanvas" width="360" height="300"></canvas>
        </div>
        <div id="mazeOutput" class="output-area"></div>
        <div style="padding:10px; background:#e9ecef; text-align:center;" id="mazeLevelDisplay">🤖 Робо-лабиринт — уровень 1</div>
    </div>

    <!-- Черепашка (Turtle) -->
    <div id="trainer-turtle" class="trainer-container">
        <div class="sim-instruction-outer" data-sim-instruction>
            <div class="sim-instruction">
                <div class="sim-instruction-head">
                    <strong>Черепашка</strong>
                    <button type="button" class="sim-instruction-toggle" aria-expanded="true">Свернуть</button>
                </div>
                <div class="sim-instruction-body">
                    <p>Команды собираются в список слева, затем нажми «Выполнить». <strong>Вперёд / Назад</strong> — шаг в пикселях, <strong>повороты</strong> — в градусах. Перо рисует линию, пока опущено.</p>
                </div>
            </div>
        </div>
        <div class="command-panel">
            <button class="command-btn" onclick="addTurtleCommand('forward', 20)">⬆️ Вперёд 20</button>
            <button class="command-btn" onclick="addTurtleCommand('back', 20)">⬇️ Назад 20</button>
            <button class="command-btn" onclick="addTurtleCommand('left', 90)">↺ Налево 90°</button>
            <button class="command-btn" onclick="addTurtleCommand('right', 90)">↻ Направо 90°</button>
            <button class="command-btn" onclick="addTurtleCommand('penup')">✋ Поднять перо</button>
            <button class="command-btn" onclick="addTurtleCommand('pendown')">✏️ Опустить перо</button>
            <button class="command-btn" onclick="addTurtleCommand('color')">🎨 Сменить цвет</button>
            <button class="command-btn" onclick="addTurtleCommand('clear')">🧽 Очистить</button>
            <button class="command-btn undo" onclick="undoTurtle()">↩ Отмена</button>
            <button class="command-btn primary" onclick="runTurtle()">▶ Выполнить</button>
            <button class="command-btn warning" onclick="resetTurtle()">↺ Сброс команд</button>
        </div>
        <div class="command-list" id="turtleCommands"></div>
        <div class="canvas-area">
            <canvas id="turtleCanvas" width="400" height="300"></canvas>
        </div>
        <div id="turtleOutput" class="output-area"></div>
        <div style="padding:10px; background:#e9ecef; text-align:center;" id="turtleLevelDisplay">🐢 Уровень «Черепашка»: 1</div>
    </div>

    <!-- Раскраска по командам: домик → собачка → машина -->
    <div id="trainer-paint" class="trainer-container">
        <div class="paint-panel">
            <div class="sim-instruction-outer" data-sim-instruction>
                <div class="sim-instruction">
                    <div class="sim-instruction-head">
                        <strong>Раскраска — задание</strong>
                        <button type="button" class="sim-instruction-toggle" aria-expanded="true">Свернуть</button>
                    </div>
                    <div class="sim-instruction-body">
                        <p class="paint-help" id="paintTaskTitle"><strong>Уровень 1 — Домик.</strong> Вводи строки: <code>строка столбец цвет</code> (с нуля), например <code>1 4 оранжевый</code> или <code>0,5 #d35400</code>. Контур закрашивать нельзя.</p>
                        <div class="paint-hint-box" id="paintHintBox"><strong>💡 Подсказка</strong><span id="paintHintText"></span></div>
                    </div>
                </div>
            </div>
            <textarea id="paintCommandInput" class="paint-cmd-input" placeholder="# Пример:
1 3 коричневый
3 6 голубой
4 2 #eceff1" autocomplete="off"></textarea>
            <div class="command-panel" style="padding-left:0;padding-right:0;border:none;background:transparent;">
                <button type="button" class="command-btn primary" onclick="runPaintCommands()">▶ Применить команды</button>
                <button type="button" class="command-btn warning" onclick="resetPaintUser()">🧽 Сбросить только краски</button>
                <button type="button" class="command-btn secondary" onclick="showPaintHint()">💡 Подсказка</button>
                <button type="button" class="command-btn secondary" onclick="fillPaintTemplate()">📄 Шаблон</button>
            </div>
        </div>
        <div class="canvas-area">
            <canvas id="paintCanvas" width="440" height="320"></canvas>
        </div>
        <div class="canvas-area">
            <div class="sim-instruction-outer" data-sim-instruction>
                <div class="sim-instruction">
                    <div class="sim-instruction-head">
                        <strong>Эталон картинки</strong>
                        <button type="button" class="sim-instruction-toggle" aria-expanded="true" data-label-show="Развернуть эталон" data-label-hide="Свернуть">Свернуть</button>
                    </div>
                    <div class="sim-instruction-body">
                        <div class="paint-help" style="margin:0 0 10px;">Что получится (как будет, если закрасить правильно):</div>
                        <canvas id="paintPreviewCanvas" width="440" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <pre id="paintOutput" class="output-area"></pre>
        <div style="padding:10px; background:#e9ecef; text-align:center;" id="paintLevelDisplay">🎨 Уровень «Раскраска»: 1</div>
    </div>

    <!-- Песочница с выбором языка -->
    <div id="trainer-sandbox" class="trainer-container">
        <div class="sandbox-selector">
            <button id="pythonModeBtn" class="active" onclick="setSandboxMode('python')">🐍 Python</button>
            <button id="pascalModeBtn" onclick="setSandboxMode('pascal')">🎯 Pascal</button>
        </div>
        <div class="sim-instruction-outer" data-sim-instruction>
            <div class="sim-instruction">
                <div class="sim-instruction-head">
                    <strong>Песочница — подсказка</strong>
                    <button type="button" class="sim-instruction-toggle" aria-expanded="true">Свернуть</button>
                </div>
                <div class="sim-instruction-body sim-instruction-body--sandbox" id="sandboxHintBox"><strong>💡 Подсказка</strong><span id="sandboxHintText"></span></div>
            </div>
        </div>
        <div class="command-panel">
            <textarea id="sandboxCode" rows="8" style="width:100%; padding:12px max(12px,2vw); border-radius:10px; border:1px solid #ced4da; font-family:monospace; font-size:14px;">print("Hello, world!")</textarea>
            <button class="command-btn primary" onclick="runSandbox()">▶ Выполнить</button>
            <button class="command-btn warning" onclick="resetSandbox()">↺ Очистить</button>
        </div>
        <pre id="sandboxOutput" class="output-area"></pre>
        <div style="padding:10px; background:#e9ecef; text-align:center;" id="sandboxLevelDisplay">💻 Уровень «Песочница»: 1</div>
    </div>

</div><!-- /.trainer-shell -->

    <div class="leaderboard">
        <h3><i class="fas fa-trophy"></i> Доска почёта (все пользователи)</h3>
        <table id="leaderboardTable">
            <thead><tr><th>Имя</th><th>Очки</th><th>Уровень</th></tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div><!-- /.trainer-page -->

</div><!-- /.trainer-root -->

<script>
    (function() {
        // ================= ГЛОБАЛЬНЫЕ ПЕРЕМЕННЫЕ =================
        const isLoggedIn = <?= $user ? 'true' : 'false' ?>;
        const userId = <?= $user ? $user['id'] : 'null' ?>;

        let player = {
            name: '<?= $user ? addslashes($user['username']) : 'Аноним' ?>',
            totalScore: 0,
            level: 1,
            scratchLevel: 1,
            mazeLevel: 1,
            wordgridLevel: 1,
            turtleLevel: 1,
            calcLevel: 1,
            pythonLevel: 1,
            pascalLevel: 1,
            paintLevel: 1
        };

        // Для гостей загружаем из localStorage
        if (!isLoggedIn) {
            loadGuestProgress();
        } else {
            loadServerProgress();
        }

        function loadGuestProgress() {
            /* Гости: без сохранения между визитами; старый локальный прогресс не используем */
            try { localStorage.removeItem('trainerProgress'); } catch (e) {}
            updateUI();
            initLevels();
        }

        function loadServerProgress() {
            fetch('load_progress.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const pl = (v, def, max) => {
                            let n = parseInt(v, 10);
                            if (!Number.isFinite(n) || n < 1) n = def;
                            return Math.min(max, n);
                        };
                        player.totalScore = parseInt(data.total_score, 10);
                        if (!Number.isFinite(player.totalScore) || player.totalScore < 0) player.totalScore = 0;
                        player.level = pl(data.level, 1, 9999);
                        player.scratchLevel = pl(data.scratch_level, 1, 10);
                        player.mazeLevel = pl(data.lego_level, 1, 10);
                        player.wordgridLevel = pl(data.wordgrid_level, 1, 10);
                        player.turtleLevel = pl(data.turtle_level, 1, 10);
                        player.calcLevel = pl(data.calc_level, 1, 10);
                        player.pythonLevel = pl(data.python_level, 1, 10);
                        player.pascalLevel = pl(data.pascal_level, 1, 10);
                        player.paintLevel = pl(data.paint_level, 1, 3);
                        player.name = data.username || 'Пользователь';
                    }
                    updateUI();
                    initLevels();
                })
                .catch(() => {
                    updateUI();
                    initLevels();
                });
        }

        function saveProgress() {
            if (isLoggedIn) {
                fetch('save_progress.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        total_score: player.totalScore,
                        level: player.level,
                        scratch_level: player.scratchLevel,
                        lego_level: player.mazeLevel,      // маппинг
                        wordgrid_level: player.wordgridLevel,
                        turtle_level: player.turtleLevel,
                        calc_level: player.calcLevel,
                        python_level: player.pythonLevel,
                        pascal_level: player.pascalLevel,
                        paint_level: player.paintLevel
                    })
                });
            }
        }

        function updateUI() {
            const scoreLab = isLoggedIn ? 'Общие очки' : 'Очки (сессия)';
            const lvlLab = isLoggedIn ? 'Общий уровень' : 'Уровень (сессия)';
            document.getElementById('scoreDisplay').innerHTML = `🏆 ${scoreLab}: ${player.totalScore}`;
            document.getElementById('levelDisplay').innerHTML = `⭐ ${lvlLab}: ${player.level}`;
            document.getElementById('scratchLevelDisplay').innerHTML = `🎮 Уровень «Поле спрайта»: ${player.scratchLevel}`;
            const wgld = document.getElementById('wordgridLevelDisplay');
            if (wgld) wgld.innerHTML = `🔎 Уровень «Словоискатель»: ${player.wordgridLevel}`;
            document.getElementById('mazeLevelDisplay').innerHTML = `🤖 Робо-лабиринт — уровень ${player.mazeLevel}`;
            document.getElementById('turtleLevelDisplay').innerHTML = `🐢 Уровень «Черепашка»: ${player.turtleLevel}`;
            document.getElementById('calcLevelDisplay').innerHTML = `🧮 Уровень «Калькулятор»: ${player.calcLevel}`;
            document.getElementById('sandboxLevelDisplay').innerHTML = `💻 Уровень «Песочница»: ${player.pythonLevel}`;
            const pld = document.getElementById('paintLevelDisplay');
            if (pld) pld.innerHTML = `🎨 Уровень «Раскраска»: ${player.paintLevel}`;
        }

        function addScore(points) {
            player.totalScore += points;
            player.level = Math.floor(player.totalScore / 100) + 1;
            updateUI();
            saveProgress();
        }

        function advanceLevel(game) {
            const maxLv = (game === 'paint') ? 3 : 10;
            let current = player[`${game}Level`];
            if (game === 'paint' && current === maxLv) {
                addScore(50);
                alert('Поздравляем! Домик, собачка и машина раскрашены.');
                updateUI();
                saveProgress();
                return false;
            }
            if (current >= maxLv) {
                alert(`Поздравляем! Вы прошли все уровни в игре ${game}!`);
                return false;
            }
            player[`${game}Level`] = current + 1;
            addScore(50);
            if (game === 'scratch') loadScratchLevel(player.scratchLevel);
            else if (game === 'wordgrid') loadWordGridLevel(player.wordgridLevel);
            else if (game === 'maze') loadMazeLevel(player.mazeLevel);
            else if (game === 'turtle') loadTurtleLevel(player.turtleLevel);
            else if (game === 'calc') loadCalcLevel(player.calcLevel);
            else if (game === 'paint') loadPaintLevel(player.paintLevel);
            else if (game === 'python' || game === 'pascal') loadSandboxLevel(player[`${game}Level`], game);
            updateUI();
            saveProgress();
            return true;
        }

        // Сброс прогресса
        document.getElementById('resetStatsBtn').addEventListener('click', () => {
            const msg = isLoggedIn ? 'Сбросить весь сохранённый прогресс в аккаунте?' : 'Обнулить очки и уровни в этой сессии?';
            if (confirm(msg)) {
                if (isLoggedIn) {
                    fetch('save_progress.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            total_score: 0, level: 1,
                            scratch_level: 1, lego_level: 1, wordgrid_level: 1, turtle_level: 1, calc_level: 1,
                            python_level: 1, pascal_level: 1, paint_level: 1
                        })
                    });
                }
                player.totalScore = 0; player.level = 1;
                player.scratchLevel = 1; player.mazeLevel = 1; player.wordgridLevel = 1; player.turtleLevel = 1;
                player.calcLevel = 1; player.pythonLevel = 1; player.pascalLevel = 1; player.paintLevel = 1;
                updateUI();
                resetAllTrainers();
            }
        });

        function resetAllTrainers() {
            loadScratchLevel(player.scratchLevel);
            loadWordGridLevel(player.wordgridLevel);
            loadMazeLevel(player.mazeLevel);
            loadTurtleLevel(player.turtleLevel);
            loadCalcLevel(player.calcLevel);
            loadPaintLevel(player.paintLevel);
            loadSandboxLevel(player.pythonLevel, 'python');
        }

        // ================= СЛОВОИСКАТЕЛЬ (поиск слов в сетке) =================
        const WG = {
            size: 20,
            userSize: null,
            level: 1,
            grid: [],
            words: [],
            found: new Set(),
            activeCells: [],
            isDragging: false,
            start: null,
            direction: null
        };

        function wgParams(level) {
            const lv = Math.max(1, Math.min(10, parseInt(level, 10) || 1));
            const targetWords = Math.min(80, 60 + (lv - 1) * 2);
            const autoSize = Math.min(24, 20 + Math.floor((targetWords - 60) / 10)); // 20..22
            const size = (WG.userSize && Number.isFinite(WG.userSize)) ? WG.userSize : autoSize;
            return { targetWords, size };
        }

        function wgSetStatus(text) {
            const el = document.getElementById('wordgridStatus');
            if (el) el.textContent = text;
        }

        function wgRandomLetter() {
            const letters = 'АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЫЭЮЯ';
            return letters[Math.floor(Math.random() * letters.length)];
        }

        function wgInBounds(x, y, size) {
            return x >= 0 && y >= 0 && x < size && y < size;
        }

        function wgTryPlaceWord(grid, size, word) {
            const dirs = [
                { dx: 1, dy: 0 }, { dx: -1, dy: 0 }, { dx: 0, dy: 1 }, { dx: 0, dy: -1 },
                { dx: 1, dy: 1 }, { dx: 1, dy: -1 }, { dx: -1, dy: 1 }, { dx: -1, dy: -1 }
            ];
            const len = word.length;
            const maxAttempts = 120;
            for (let a = 0; a < maxAttempts; a++) {
                const dir = dirs[Math.floor(Math.random() * dirs.length)];
                const x0 = Math.floor(Math.random() * size);
                const y0 = Math.floor(Math.random() * size);
                const x1 = x0 + dir.dx * (len - 1);
                const y1 = y0 + dir.dy * (len - 1);
                if (!wgInBounds(x1, y1, size)) continue;
                let ok = true;
                for (let i = 0; i < len; i++) {
                    const x = x0 + dir.dx * i;
                    const y = y0 + dir.dy * i;
                    const cur = grid[y][x];
                    if (cur !== '' && cur !== word[i]) { ok = false; break; }
                }
                if (!ok) continue;
                for (let i = 0; i < len; i++) {
                    const x = x0 + dir.dx * i;
                    const y = y0 + dir.dy * i;
                    grid[y][x] = word[i];
                }
                return { x0, y0, dx: dir.dx, dy: dir.dy };
            }
            return null;
        }

        async function wgFetchWordPool() {
            const res = await fetch('get_wordgrid_words.php?limit=280', { cache: 'no-store' });
            const data = await res.json();
            if (!data || !data.success || !Array.isArray(data.words)) return [];
            return data.words.map(w => String(w).trim()).filter(Boolean);
        }

        function wgPickWords(pool, count) {
            const seen = new Set();
            const out = [];
            for (const w of pool) {
                const ww = String(w).toUpperCase();
                if (ww.length < 4 || ww.length > 12) continue;
                if (seen.has(ww)) continue;
                seen.add(ww);
                out.push(ww);
                if (out.length >= count) break;
            }
            return out;
        }

        function wgBuildGrid(size, words) {
            // Пытаемся собрать сетку так, чтобы разместить максимум слов.
            // Если не удалось — вернём то, что удалось разместить, и caller зафиксирует подмножество.
            const grid = Array.from({ length: size }, () => Array.from({ length: size }, () => ''));
            const placed = [];
            const sorted = [...words].sort((a, b) => b.length - a.length);
            for (const w of sorted) {
                const pos = wgTryPlaceWord(grid, size, w);
                if (pos) placed.push(w);
            }
            for (let y = 0; y < size; y++) {
                for (let x = 0; x < size; x++) {
                    if (!grid[y][x]) grid[y][x] = wgRandomLetter();
                }
            }
            return { grid, placed };
        }

        function wgResetRoundUI(level) {
            WG.found = new Set();
            WG.start = null;
            WG.direction = null;
            WG.activeCells = [];
            const outEl = document.getElementById('wordgridOutput');
            if (outEl) outEl.textContent = '';
            wgSetStatus(`Уровень ${level}. Генерируем сетку...`);
        }

        async function wgEnsureWordsForLevel(level) {
            const { targetWords } = wgParams(level);
            if (Array.isArray(WG.words) && WG.words.length) return;

            wgSetStatus(`Уровень ${level}. Подбираем ${targetWords} слов...`);
            let pool = [];
            try {
                pool = await wgFetchWordPool();
            } catch (e) {
                pool = [];
            }
            if (!pool.length) {
                pool = [
                    'ПРОГРАММА', 'АЛГОРИТМ', 'ПЕРЕМЕННАЯ', 'ФУНКЦИЯ', 'ЦИКЛ', 'УСЛОВИЕ', 'МАССИВ', 'СТРОКА',
                    'ЧИСЛО', 'КЛАСС', 'ОБЪЕКТ', 'МЕТОД', 'КОД', 'ЛОГИКА', 'ДАННЫЕ', 'ОТЛАДКА', 'КОМАНДА'
                ];
            }
            WG.words = wgPickWords(pool, targetWords);
        }

        function wgRegenerateGridForCurrentWords(level) {
            const { size, targetWords } = wgParams(level);
            WG.size = size;
            wgResetRoundUI(level);

            // Пробуем несколько раз собрать сетку, чтобы разместить ВСЕ слова из списка.
            // Если упорно не выходит — фиксируем подмножество (один раз) и дальше оно остаётся стабильным.
            let best = null;
            for (let attempt = 0; attempt < 8; attempt++) {
                const built = wgBuildGrid(size, WG.words);
                if (!best || built.placed.length > best.placed.length) best = built;
                if (built.placed.length === WG.words.length) { best = built; break; }
            }
            WG.grid = best ? best.grid : [];

            if (best && best.placed.length) {
                // Фиксируем реальные слова, которые точно есть в сетке (и не меняем их при "Новая сетка")
                const minOk = Math.max(20, Math.min(targetWords, Math.floor(targetWords * 0.7)));
                if (best.placed.length < minOk) {
                    // Если совсем мало разместилось — оставим сколько есть, но не будем "прыгать" составом дальше.
                    WG.words = best.placed.slice(0, Math.min(best.placed.length, targetWords));
                } else {
                    WG.words = best.placed.slice(0, targetWords);
                }
            }

            renderWordGrid();
            renderWordGridWords();
            wgSetStatus(`Найдено: 0 / ${WG.words.length}. Размер: ${size}×${size}`);
        }

        async function loadWordGridLevel(level) {
            // Подтянем select в состояние (если пользователь менял размер)
            const sizeSel = document.getElementById('wordgridSizeSelect');
            if (sizeSel) {
                const sv = parseInt(sizeSel.value, 10);
                if (Number.isFinite(sv)) WG.userSize = sv;
            }
            WG.level = parseInt(level, 10) || 1;
            // При смене уровня — подбираем новый список слов. При "Новая сетка" слова сохраняются.
            if (!WG.words.length) {
                await wgEnsureWordsForLevel(WG.level);
            }
            wgRegenerateGridForCurrentWords(WG.level);
        }

        function renderWordGrid() {
            const gridEl = document.getElementById('wordgridGrid');
            if (!gridEl) return;
            const size = WG.size;
            const cellSize = Math.max(22, Math.min(40, Math.floor(520 / size)));
            gridEl.style.gridTemplateColumns = `repeat(${size}, ${cellSize}px)`;
            gridEl.style.gridTemplateRows = `repeat(${size}, ${cellSize}px)`;
            gridEl.innerHTML = '';

            for (let y = 0; y < size; y++) {
                for (let x = 0; x < size; x++) {
                    const d = document.createElement('div');
                    d.className = 'wg-cell';
                    d.textContent = WG.grid[y][x];
                    d.dataset.x = String(x);
                    d.dataset.y = String(y);
                    d.style.width = cellSize + 'px';
                    d.style.height = cellSize + 'px';
                    gridEl.appendChild(d);
                }
            }

            const pickCell = (clientX, clientY) => {
                const el = document.elementFromPoint(clientX, clientY);
                if (!(el instanceof HTMLElement)) return null;
                const cell = el.classList.contains('wg-cell') ? el : el.closest?.('.wg-cell');
                if (!(cell instanceof HTMLElement)) return null;
                if (!cell.dataset || cell.dataset.x == null || cell.dataset.y == null) return null;
                return cell;
            };

            gridEl.onpointerdown = (e) => {
                const t = pickCell(e.clientX, e.clientY);
                if (!t) return;
                WG.isDragging = true;
                WG.start = { x: parseInt(t.dataset.x, 10), y: parseInt(t.dataset.y, 10) };
                WG.direction = null;
                clearWGActive();
                setWGActiveTo(WG.start.x, WG.start.y);
                try { gridEl.setPointerCapture(e.pointerId); } catch (_) {}
            };

            gridEl.onpointermove = (e) => {
                if (!WG.isDragging || !WG.start) return;
                const t = pickCell(e.clientX, e.clientY);
                if (!t) return;
                const x = parseInt(t.dataset.x, 10);
                const y = parseInt(t.dataset.y, 10);
                updateWGSelection(x, y);
            };

            gridEl.onpointerup = () => {
                if (!WG.isDragging) return;
                WG.isDragging = false;
                finalizeWGSelection();
                clearWGActive();
                WG.start = null;
                WG.direction = null;
            };

            gridEl.onpointercancel = () => {
                if (!WG.isDragging) return;
                WG.isDragging = false;
                clearWGActive();
                WG.start = null;
                WG.direction = null;
            };

            gridEl.onpointerleave = () => {
                if (!WG.isDragging) clearWGActive();
            };
        }

        function renderWordGridWords() {
            const el = document.getElementById('wordgridWords');
            if (!el) return;
            el.innerHTML = WG.words.map(w => {
                const cls = WG.found.has(w) ? 'wg-word is-found' : 'wg-word';
                return `<span class="${cls}" data-word="${w}">${w}</span>`;
            }).join('');
        }

        function clearWGActive() {
            WG.activeCells.forEach(c => c.classList.remove('is-active'));
            WG.activeCells = [];
        }

        function setWGActiveTo(x, y) {
            const gridEl = document.getElementById('wordgridGrid');
            if (!gridEl) return;
            const idx = y * WG.size + x;
            const cell = gridEl.children[idx];
            if (cell && cell.classList) {
                cell.classList.add('is-active');
                WG.activeCells.push(cell);
            }
        }

        function updateWGSelection(x, y) {
            if (!WG.start) return;
            const dxRaw = x - WG.start.x;
            const dyRaw = y - WG.start.y;
            const dx = dxRaw === 0 ? 0 : (dxRaw > 0 ? 1 : -1);
            const dy = dyRaw === 0 ? 0 : (dyRaw > 0 ? 1 : -1);
            if (dx === 0 && dy === 0) return;
            // Только прямые линии: если и dx, и dy != 0, то должны быть равны по модулю (диагональ)
            if (dx !== 0 && dy !== 0 && Math.abs(dxRaw) !== Math.abs(dyRaw)) return;
            WG.direction = { dx, dy };

            clearWGActive();
            const steps = Math.max(Math.abs(dxRaw), Math.abs(dyRaw));
            for (let i = 0; i <= steps; i++) {
                const xx = WG.start.x + dx * i;
                const yy = WG.start.y + dy * i;
                if (!wgInBounds(xx, yy, WG.size)) break;
                setWGActiveTo(xx, yy);
            }
        }

        function finalizeWGSelection() {
            if (!WG.start || !WG.direction || !WG.activeCells.length) return;
            const letters = WG.activeCells.map(c => c.textContent || '').join('');
            const word = letters.toUpperCase();
            const rev = word.split('').reverse().join('');
            const target = WG.words.includes(word) ? word : (WG.words.includes(rev) ? rev : null);
            if (!target || WG.found.has(target)) return;

            WG.found.add(target);
            // помечаем клетки "найдено"
            WG.activeCells.forEach(c => c.classList.add('is-found'));
            renderWordGridWords();

            addScore(2);
            wgSetStatus(`Найдено: ${WG.found.size} / ${WG.words.length}`);

            const outEl = document.getElementById('wordgridOutput');
            if (outEl) outEl.textContent = `✅ Найдено слово: ${target}\n` + (outEl.textContent || '');

            if (WG.found.size >= WG.words.length) {
                if (outEl) outEl.textContent = `🏁 Все слова найдены! +50\n` + (outEl.textContent || '');
                addScore(50);
                advanceLevel('wordgrid');
            }
        }

        window.newWordGrid = () => {
            WG.level = player.wordgridLevel;
            // Перестраиваем только сетку, без смены слов
            wgRegenerateGridForCurrentWords(WG.level);
            addScore(1);
        };

        window.newWordGridWords = async () => {
            // Явная смена слов
            WG.words = [];
            WG.level = player.wordgridLevel;
            await loadWordGridLevel(WG.level);
            addScore(1);
        };

        window.setWordGridSize = (val) => {
            const n = parseInt(val, 10);
            if (!Number.isFinite(n) || n < 10 || n > 30) return;
            WG.userSize = n;
            // Перегенерируем текущий уровень под новый размер (слова оставляем)
            WG.level = player.wordgridLevel;
            wgRegenerateGridForCurrentWords(WG.level);
        };

        // ================= ПОЛЕ СПРАЙТА (самостоятельный блоковый исполнитель, не Scratch) =================
        let scratchCommands = [];
        let scratchX = 200, scratchY = 150, scratchAngle = 0;
        let scratchScale = 1.0;
        let scratchHue = 0;
        let scratchTrail = [];
        let scratchRecordTrail = true;
        /* Только зона финиша: без «воды» и без звезды/солнца */
        const scratchLevels = [
            { finishX: 92, finishY: 78, finishR: 24 },
            { finishX: 328, finishY: 72, finishR: 24 },
            { finishX: 72, finishY: 232, finishR: 23 },
            { finishX: 350, finishY: 228, finishR: 23 },
            { finishX: 200, finishY: 48, finishR: 22 },
            { finishX: 200, finishY: 252, finishR: 22 },
            { finishX: 48, finishY: 150, finishR: 22 },
            { finishX: 352, finishY: 150, finishR: 22 },
            { finishX: 260, finishY: 110, finishR: 21 },
            { finishX: 140, finishY: 200, finishR: 21 }
        ];
        let scratchFinishX = 200, scratchFinishY = 120, scratchFinishR = 24;

        function loadScratchLevel(level) {
            let idx = level - 1;
            if (idx < 0) idx = 0;
            if (idx >= scratchLevels.length) idx = scratchLevels.length - 1;
            let lvl = scratchLevels[idx];
            scratchFinishX = lvl.finishX;
            scratchFinishY = lvl.finishY;
            scratchFinishR = lvl.finishR != null ? lvl.finishR : 24;
            resetScratch(true);
            document.getElementById('scratchOutput').innerText =
                `Уровень ${level}. Собери очередь и останови спрайта в круге «Финиш». Препятствий нет — только геометрия и шаги.`;
        }

        window.addScratchCommand = (type, ...args) => {
            scratchCommands.push({ type, args });
            renderScratchCommands();
        };

        function spriteStepKind(type) {
            if (type === 'move' || type === 'jump' || type === 'home') return 'move';
            if (type === 'turn' || type === 'angle' || type === 'nudge') return 'turn';
            if (type === 'say' || type === 'wait') return 'voice';
            if (type === 'color' || type === 'randomHue' || type === 'scale') return 'look';
            return 'sys';
        }

        function renderScratchCommands() {
            const div = document.getElementById('scratchCommands');
            div.innerHTML = scratchCommands.map((c, i) => {
                const kind = spriteStepKind(c.type);
                let label = '';
                if (c.type === 'move') label = `шаг ${c.args[0]}`;
                else if (c.type === 'turn') label = `↺/↻ ${c.args[0] === 'left' ? '⟲' : '⟳'} ${c.args[1]}°`;
                else if (c.type === 'say') label = `реплика: «${c.args[0]}»`;
                else if (c.type === 'wait') label = 'отметка паузы';
                else if (c.type === 'color') label = 'новый оттенок';
                else if (c.type === 'randomHue') label = 'случайный цвет';
                else if (c.type === 'scale') label = c.args[0] > 0 ? 'масштаб +' : 'масштаб −';
                else if (c.type === 'jump') label = 'рывок';
                else if (c.type === 'home') label = 'в центр поля';
                else if (c.type === 'angle') label = `угол ${c.args[0]}°`;
                else if (c.type === 'nudge') label = 'лёгкая дрожь';
                else if (c.type === 'trail') label = c.args[0] ? 'след: вкл.' : 'след: выкл.';
                else label = c.type;
                return `<div class="sprite-step sprite-step--${kind}"><span>${label}</span><button type="button" class="sprite-step-remove" onclick="removeScratchAt(${i})">×</button></div>`;
            }).join('');
        }

        window.removeScratchAt = (i) => {
            if (i >= 0 && i < scratchCommands.length) {
                scratchCommands.splice(i, 1);
                renderScratchCommands();
            }
        };

        function pushScratchTrail() {
            if (!scratchRecordTrail) return;
            const last = scratchTrail[scratchTrail.length - 1];
            if (last && Math.hypot(last.x - scratchX, last.y - scratchY) < 0.75) return;
            scratchTrail.push({ x: scratchX, y: scratchY });
        }

        window.runScratch = () => {
            let output = '';
            for (let cmd of scratchCommands) {
                if (cmd.type === 'move') {
                    let steps = cmd.args[0];
                    let rad = scratchAngle * Math.PI / 180;
                    scratchX += steps * Math.cos(rad);
                    scratchY += steps * Math.sin(rad);
                    scratchX = Math.min(380, Math.max(20, scratchX));
                    scratchY = Math.min(280, Math.max(20, scratchY));
                    pushScratchTrail();
                } else if (cmd.type === 'turn') {
                    let dir = cmd.args[0];
                    let angle = cmd.args[1];
                    if (dir === 'left') scratchAngle -= angle;
                    else scratchAngle += angle;
                } else if (cmd.type === 'say') {
                    output += 'Реплика: ' + cmd.args[0] + '\n';
                } else if (cmd.type === 'wait') {
                    output += '… (отметка в журнале)\n';
                } else if (cmd.type === 'color') {
                    scratchHue = (scratchHue + 38) % 360;
                } else if (cmd.type === 'randomHue') {
                    scratchHue = Math.floor(Math.random() * 360);
                } else if (cmd.type === 'scale') {
                    let delta = cmd.args[0];
                    scratchScale = Math.max(0.35, Math.min(2.0, scratchScale + delta));
                } else if (cmd.type === 'jump') {
                    let rad = scratchAngle * Math.PI / 180;
                    scratchX += 52 * Math.cos(rad);
                    scratchY += 52 * Math.sin(rad);
                    scratchX = Math.min(380, Math.max(20, scratchX));
                    scratchY = Math.min(280, Math.max(20, scratchY));
                    pushScratchTrail();
                } else if (cmd.type === 'home') {
                    scratchX = 200; scratchY = 150;
                    pushScratchTrail();
                } else if (cmd.type === 'angle') {
                    let a = Number(cmd.args[0]);
                    scratchAngle = ((a % 360) + 360) % 360;
                } else if (cmd.type === 'nudge') {
                    scratchAngle += (Math.random() - 0.5) * 14;
                } else if (cmd.type === 'trail') {
                    scratchRecordTrail = !!cmd.args[0];
                    if (scratchRecordTrail) pushScratchTrail();
                }
            }
            drawScratch();
            if (Math.hypot(scratchX - scratchFinishX, scratchY - scratchFinishY) < scratchFinishR) {
                output += 'Зона «Финиш» достигнута! +50 очков\n';
                addScore(50);
                if (advanceLevel('scratch')) output += '✨ Следующий уровень.\n';
            }
            document.getElementById('scratchOutput').innerText = output || 'Очередь выполнена.';
        };

        function drawScratch() {
            const canvas = document.getElementById('scratchCanvas');
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, 400, 300);
            ctx.fillStyle = '#fafafa';
            ctx.fillRect(0, 0, 400, 300);

            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 1;
            for (let gx = 0; gx <= 400; gx += 20) {
                ctx.beginPath(); ctx.moveTo(gx, 0); ctx.lineTo(gx, 300); ctx.stroke();
            }
            for (let gy = 0; gy <= 300; gy += 20) {
                ctx.beginPath(); ctx.moveTo(0, gy); ctx.lineTo(400, gy); ctx.stroke();
            }

            if (scratchTrail.length > 1) {
                ctx.beginPath();
                ctx.moveTo(scratchTrail[0].x, scratchTrail[0].y);
                for (let i = 1; i < scratchTrail.length; i++) ctx.lineTo(scratchTrail[i].x, scratchTrail[i].y);
                ctx.strokeStyle = 'rgba(71, 85, 105, 0.35)';
                ctx.lineWidth = 2;
                ctx.stroke();
            }

            ctx.fillStyle = 'rgba(34, 197, 94, 0.14)';
            ctx.beginPath();
            ctx.arc(scratchFinishX, scratchFinishY, scratchFinishR, 0, 2 * Math.PI);
            ctx.fill();
            ctx.strokeStyle = '#15803d';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.arc(scratchFinishX, scratchFinishY, scratchFinishR, 0, 2 * Math.PI);
            ctx.stroke();
            ctx.fillStyle = '#14532d';
            ctx.font = 'bold 11px Segoe UI, system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Финиш', scratchFinishX, scratchFinishY + scratchFinishR + 15);
            ctx.textAlign = 'left';

            ctx.save();
            ctx.translate(scratchX, scratchY);
            ctx.rotate(scratchAngle * Math.PI/180);
            ctx.scale(scratchScale, scratchScale);

            const bodyColor = `hsl(${scratchHue}, 80%, 55%)`;
            const earColor = `hsl(${(scratchHue + 18) % 360}, 75%, 45%)`;
            const pupilColor = '#111';

            // Хвост
            ctx.strokeStyle = bodyColor;
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.moveTo(-18, 3);
            ctx.quadraticCurveTo(-30, 0, -20, -10);
            ctx.stroke();

            // Тело
            ctx.fillStyle = bodyColor;
            ctx.beginPath();
            ctx.ellipse(0, 6, 18, 14, 0, 0, 2 * Math.PI);
            ctx.fill();

            // Голова
            ctx.beginPath();
            ctx.ellipse(8, -8, 14, 12, 0, 0, 2 * Math.PI);
            ctx.fill();

            // Уши
            ctx.fillStyle = earColor;
            ctx.beginPath();
            ctx.moveTo(3, -17);
            ctx.lineTo(9, -30);
            ctx.lineTo(12, -16);
            ctx.closePath();
            ctx.fill();
            ctx.beginPath();
            ctx.moveTo(12, -16);
            ctx.lineTo(18, -30);
            ctx.lineTo(22, -17);
            ctx.closePath();
            ctx.fill();

            // Глаза
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(10, -10, 3, 0, 2 * Math.PI);
            ctx.fill();
            ctx.beginPath();
            ctx.arc(16, -10, 3, 0, 2 * Math.PI);
            ctx.fill();
            ctx.fillStyle = pupilColor;
            ctx.beginPath();
            ctx.arc(11, -10, 1.4, 0, 2 * Math.PI);
            ctx.fill();
            ctx.beginPath();
            ctx.arc(17, -10, 1.4, 0, 2 * Math.PI);
            ctx.fill();

            // Носик и рот
            ctx.fillStyle = earColor;
            ctx.beginPath();
            ctx.arc(14, -2, 2, 0, 2 * Math.PI);
            ctx.fill();
            ctx.strokeStyle = '#333';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(13, -0.5);
            ctx.quadraticCurveTo(14, 3, 11, 2);
            ctx.stroke();
            ctx.restore();
        }

        window.resetScratch = (skipAddScore = false) => {
            scratchCommands = [];
            scratchX = 200; scratchY = 150; scratchAngle = 0;
            scratchScale = 1.0; scratchHue = 0;
            scratchRecordTrail = true;
            scratchTrail = [{ x: scratchX, y: scratchY }];
            renderScratchCommands();
            drawScratch();
            document.getElementById('scratchOutput').innerText = '';
            if (!skipAddScore) addScore(5);
        };
        window.undoScratch = () => { scratchCommands.pop(); renderScratchCommands(); };

        // ================= Побег из дома: сундук → ключ → дверь → зелёный угол =================
        let mazeCommands = [];
        let mazeX = 2, mazeY = 2, mazeDir = 0;
        let mazeFieldSize = 5;
        let mazeWalls = [];
        let mazeSpikes = [];
        let mazeLevelCurrent = null;
        let mazeHasKey = false;
        let mazeDoorOpened = false;
        /* Эталон уровней (клонируется при загрузке — дрель/дверь не портят исходник). door обязан входить в walls.
           Уровни сделаны "коридорами", чтобы было удобнее и без тупых ловушек. */
        const MAZE_LEVEL_DATA = [
            {
                size: 7,
                chest: [0, 1],
                door: [5, 6],
                walls: [
                    [1,0],[1,1],[1,2],[1,4],[1,5],
                    [3,1],[3,2],[3,4],[3,5],
                    [5,0],[5,1],[5,3],[5,4],[5,5],[5,6],
                    [2,3],[4,2],[2,6],[4,6]
                ],
                spikes: [[6,0],[0,6]],
                drill: false
            },
            {
                size: 7,
                chest: [0, 0],
                door: [6, 5],
                walls: [
                    [2,0],[2,1],[2,3],[2,4],[2,5],
                    [4,1],[4,2],[4,4],[4,5],
                    [6,1],[6,2],[6,3],[6,4],[6,5],
                    [1,2],[3,3],[5,0],[5,6],[0,4]
                ],
                spikes: [[3,0],[1,6]],
                drill: false
            },
            {
                size: 8,
                chest: [0, 2],
                door: [7, 6],
                walls: [
                    [1,0],[1,1],[1,3],[1,4],[1,6],[1,7],
                    [3,1],[3,2],[3,4],[3,5],[3,7],
                    [5,0],[5,2],[5,3],[5,5],[5,6],
                    [7,1],[7,2],[7,3],[7,4],[7,5],[7,6],
                    [2,6],[4,1],[6,6]
                ],
                spikes: [[0,7],[6,0]],
                drill: true
            },
            {
                size: 8,
                chest: [0, 0],
                door: [6, 7],
                walls: [
                    [2,1],[2,2],[2,4],[2,5],[2,6],
                    [4,0],[4,1],[4,3],[4,4],[4,6],
                    [6,1],[6,2],[6,4],[6,5],[6,6],[6,7],
                    [1,3],[3,5],[5,2],[7,3],[0,6],[7,0]
                ],
                spikes: [[3,0],[0,7]],
                drill: true
            },
            {
                size: 9,
                chest: [0, 1],
                door: [8, 7],
                walls: [
                    [1,0],[1,1],[1,3],[1,4],[1,6],[1,7],
                    [3,1],[3,2],[3,4],[3,5],[3,7],
                    [5,0],[5,2],[5,3],[5,5],[5,6],[5,8],
                    [7,1],[7,2],[7,4],[7,5],[7,7],
                    [8,6],[8,7],
                    [2,6],[4,1],[6,6],[0,5],[4,8]
                ],
                spikes: [[0,8],[8,0],[6,1]],
                drill: true
            },
            {
                size: 9,
                chest: [1, 0],
                door: [7, 8],
                walls: [
                    [2,0],[2,1],[2,3],[2,4],[2,6],[2,7],
                    [4,1],[4,2],[4,4],[4,5],[4,7],
                    [6,0],[6,2],[6,3],[6,5],[6,6],[6,8],
                    [8,1],[8,2],[8,3],[8,4],[8,5],
                    [7,8],
                    [0,6],[1,5],[3,3],[5,4],[7,1]
                ],
                spikes: [[0,0],[8,0],[1,8]],
                drill: true
            },
            {
                size: 9,
                chest: [0, 0],
                door: [8, 7],
                walls: [
                    [1,1],[1,2],[1,4],[1,5],[1,7],
                    [3,0],[3,1],[3,3],[3,4],[3,6],[3,7],
                    [5,1],[5,2],[5,4],[5,5],[5,7],
                    [7,0],[7,2],[7,3],[7,5],[7,6],[7,7],
                    [8,7],
                    [0,6],[2,5],[4,8],[6,4]
                ],
                spikes: [[2,0],[0,8],[8,0]],
                drill: true
            },
            {
                size: 9,
                chest: [0, 2],
                door: [8, 7],
                walls: [
                    [2,1],[2,2],[2,4],[2,5],[2,7],
                    [4,0],[4,1],[4,3],[4,4],[4,6],[4,7],
                    [6,1],[6,2],[6,4],[6,5],[6,7],
                    [8,1],[8,2],[8,3],[8,4],[8,5],[8,6],[8,7],
                    [1,6],[3,5],[5,2],[7,6],[0,7]
                ],
                spikes: [[0,0],[6,0],[1,8]],
                drill: true
            },
            {
                size: 9,
                chest: [0, 0],
                door: [8, 7],
                walls: [
                    [1,0],[1,2],[1,3],[1,5],[1,6],[1,8],
                    [3,1],[3,2],[3,4],[3,6],[3,7],
                    [5,0],[5,1],[5,3],[5,5],[5,6],[5,8],
                    [7,2],[7,3],[7,4],[7,6],[7,7],
                    [8,7],
                    [2,6],[4,2],[6,6],[0,4]
                ],
                spikes: [[8,0],[0,8],[2,1]],
                drill: true
            },
            {
                size: 9,
                chest: [0, 1],
                door: [8, 7],
                walls: [
                    [2,0],[2,1],[2,3],[2,4],[2,6],[2,7],
                    [4,1],[4,2],[4,4],[4,5],[4,7],
                    [6,0],[6,2],[6,3],[6,5],[6,6],[6,8],
                    [8,1],[8,2],[8,3],[8,4],[8,5],[8,7],
                    [3,8],[5,8],
                    [0,5],[1,6],[7,1]
                ],
                spikes: [[0,0],[6,1],[1,8]],
                drill: true
            }
        ];

        function cloneMazeLevel(idx) {
            let i = parseInt(idx, 10);
            if (!Number.isFinite(i) || i < 0) i = 0;
            if (i >= MAZE_LEVEL_DATA.length) i = MAZE_LEVEL_DATA.length - 1;
            return JSON.parse(JSON.stringify(MAZE_LEVEL_DATA[i]));
        }

        function loadMazeLevel(level) {
            let lev = parseInt(level, 10);
            if (!Number.isFinite(lev) || lev < 1) lev = 1;
            if (lev > MAZE_LEVEL_DATA.length) lev = MAZE_LEVEL_DATA.length;
            let idx = lev - 1;
            mazeLevelCurrent = cloneMazeLevel(idx);
            let lvl = mazeLevelCurrent;
            mazeFieldSize = lvl.size;
            const [dx, dy] = lvl.door;
            if (!lvl.walls.some(([x, y]) => x === dx && y === dy))
                lvl.walls.push([dx, dy]);
            mazeWalls = lvl.walls.map(w => w.join(','));
            mazeSpikes = lvl.spikes.map(s => s.join(','));
            mazeHasKey = false;
            mazeDoorOpened = false;
            const mazeDrillEl = document.getElementById('mazeDrillBtn');
            // Дрель доступна всегда: ломает только серые стены, не дверь.
            if (mazeDrillEl) mazeDrillEl.style.display = 'inline-block';
            mazeX = Math.floor(mazeFieldSize/2);
            mazeY = Math.floor(mazeFieldSize/2);
            mazeDir = 0;
            mazeCommands = [];
            renderMazeCommands();
            drawMaze();
            const sz = lvl.size;
            const drillHint = ' Дрель ломает только серые стены перед роботом, дверь — только ключом.';
            const [cx, cy] = lvl.chest;
            document.getElementById('mazeOutput').innerText =
                `Уровень ${lev} — ${sz}×${sz}. Сюжет: выйди из дома (центр), забери ключ в сундуке (${cx}, ${cy}), открой дверь у клетки (${dx}, ${dy}), доберись до зелёного угла. Шипы — дом и −10.${drillHint}`;
        }

        function ensureMazeReady() {
            if (mazeLevelCurrent) return true;
            try {
                loadMazeLevel(player.mazeLevel);
                return !!mazeLevelCurrent;
            } catch (e) {
                console.error('ensureMazeReady', e);
                return false;
            }
        }

        window.addMazeCommand = (type, ...args) => {
            if (!ensureMazeReady()) return;
            mazeCommands.push({ type, args });
            renderMazeCommands();
        };

        function renderMazeCommands() {
            const div = document.getElementById('mazeCommands');
            if (!div) return;
            const labelOf = (c) => {
                if (c.type === 'move') return `➡️ вперёд ${c.args[0]}`;
                if (c.type === 'turn') return `↺ ${c.args[0] === 'left' ? 'налево' : 'направо'}`;
                if (c.type === 'up') return '⬆️ вверх';
                if (c.type === 'down') return '⬇️ вниз';
                if (c.type === 'grab') return '🗝️ забрать ключ';
                if (c.type === 'open') return '🚪 открыть дверь';
                if (c.type === 'drill') return '🔧 дрель';
                return '';
            };
            div.innerHTML = mazeCommands.map((c, i) => {
                const label = labelOf(c);
                if (!label) return '';
                return `<span class="command-item" style="display:inline-flex; align-items:center; gap:8px;">
                    <span>${label}</span>
                    <button type="button" aria-label="Удалить команду" onclick="removeMazeAt(${i})"
                        style="border:none; background:rgba(0,0,0,0.08); width:22px; height:22px; border-radius:999px; cursor:pointer; font-weight:900; line-height:1;">×</button>
                </span>`;
            }).join(' ');
        }

        window.removeMazeAt = (i) => {
            if (!ensureMazeReady()) return;
            const idx = parseInt(i, 10);
            if (!Number.isFinite(idx) || idx < 0 || idx >= mazeCommands.length) return;
            mazeCommands.splice(idx, 1);
            renderMazeCommands();
        };

        window.runMaze = () => {
            if (!ensureMazeReady()) {
                const out = document.getElementById('mazeOutput');
                if (out) out.innerText = 'Не удалось загрузить уровень. Обнови страницу.';
                return;
            }
            let output = '';
            const homeX = Math.floor(mazeFieldSize / 2);
            const homeY = Math.floor(mazeFieldSize / 2);
            const stepForward = () => {
                let nx = mazeX, ny = mazeY;
                if (mazeDir === 0) nx++;
                else if (mazeDir === 1) ny++;
                else if (mazeDir === 2) nx--;
                else ny--;
                if (nx < 0 || nx >= mazeFieldSize || ny < 0 || ny >= mazeFieldSize) {
                    output += '🚫 Граница поля!\n';
                    return false;
                }
                if (mazeWalls.includes(`${nx},${ny}`)) {
                    output += '🚫 Стена!\n';
                    return false;
                }
                mazeX = nx; mazeY = ny;
                if (mazeSpikes.includes(`${mazeX},${mazeY}`)) {
                    output += '⚠️ Шип! Возврат домой в центр. -10 очков\n';
                    mazeX = homeX; mazeY = homeY;
                    addScore(-10);
                }
                return true;
            };
            const stepAbs = (dx, dy) => {
                const nx = mazeX + dx;
                const ny = mazeY + dy;
                if (nx < 0 || nx >= mazeFieldSize || ny < 0 || ny >= mazeFieldSize) {
                    output += '🚫 Граница!\n';
                    return false;
                }
                if (mazeWalls.includes(`${nx},${ny}`)) {
                    output += '🚫 Стена!\n';
                    return false;
                }
                mazeX = nx; mazeY = ny;
                if (mazeSpikes.includes(`${mazeX},${mazeY}`)) {
                    output += '⚠️ Шип! Возврат домой в центр. -10 очков\n';
                    mazeX = homeX; mazeY = homeY;
                    addScore(-10);
                }
                return true;
            };
            for (let cmd of mazeCommands) {
                if (cmd.type === 'move') {
                    let cells = cmd.args[0];
                    let n = parseInt(cells, 10);
                    if (!Number.isFinite(n) || n < 1) n = 1;
                    for (let i = 0; i < n; i++) {
                        const ok = stepForward();
                        if (!ok) break;
                    }
                } else if (cmd.type === 'turn') {
                    let dir = cmd.args[0];
                    if (dir === 'left') mazeDir = (mazeDir + 3) % 4;
                    else mazeDir = (mazeDir + 1) % 4;
                } else if (cmd.type === 'up') {
                    stepAbs(0, -1);
                } else if (cmd.type === 'down') {
                    stepAbs(0, 1);
                } else if (cmd.type === 'grab') {
                    const [cx, cy] = mazeLevelCurrent.chest;
                    if (mazeX === cx && mazeY === cy) {
                        if (mazeHasKey) output += '🗝️ Ключ уже у робота.\n';
                        else {
                            mazeHasKey = true;
                            output += '🗝️ Ключ из сундука! +20\n';
                            addScore(20);
                        }
                    } else output += '📦 Сундук в другой клетке — подойди к ящику (см. подсказку).\n';
                } else if (cmd.type === 'open') {
                    const [ddx, ddy] = mazeLevelCurrent.door;
                    if (mazeDoorOpened) output += '🚪 Дверь уже открыта.\n';
                    else if (!mazeHasKey) output += '🚪 Нужен ключ из сундука!\n';
                    else {
                        let nx = mazeX, ny = mazeY;
                        if (mazeDir === 0) nx++;
                        else if (mazeDir === 1) ny++;
                        else if (mazeDir === 2) nx--;
                        else ny--;
                        if (nx === ddx && ny === ddy) {
                            mazeWalls = mazeWalls.filter(w => w !== `${ddx},${ddy}`);
                            mazeLevelCurrent.walls = mazeLevelCurrent.walls.filter(([x, y]) => !(x === ddx && y === ddy));
                            mazeDoorOpened = true;
                            output += '🚪 Дверь открыта! +10\n';
                            addScore(10);
                        } else output += '🚪 Повернись лицом к коричневой двери и встань вплотную к ней.\n';
                    }
                } else if (cmd.type === 'drill') {
                    let nx = mazeX, ny = mazeY;
                    if (mazeDir === 0) nx++;
                    else if (mazeDir === 1) ny++;
                    else if (mazeDir === 2) nx--;
                    else ny--;
                    if (nx >= 0 && nx < mazeFieldSize && ny >= 0 && ny < mazeFieldSize) {
                        const [ddx, ddy] = mazeLevelCurrent.door;
                        if (!mazeDoorOpened && nx === ddx && ny === ddy)
                            output += '🚪 Дверь открывается ключом, дрель не подходит.\n';
                        else if (mazeWalls.includes(`${nx},${ny}`)) {
                            mazeWalls = mazeWalls.filter(w => w !== `${nx},${ny}`);
                            mazeLevelCurrent.walls = mazeLevelCurrent.walls.filter(([x, y]) => !(x === nx && y === ny));
                            output += '💥 Стена разрушена!\n';
                        } else output += '🔹 Там нет стены\n';
                    }
                }
            }
            drawMaze();
            if (mazeX === mazeFieldSize-1 && mazeY === mazeFieldSize-1) {
                if (!mazeHasKey || !mazeDoorOpened) {
                    output += '\n⚠️ Побег не завершён: сначала ключ из сундука и открытая дверь, потом зелёный квадрат.';
                } else {
                    output += '\n🎉 Ты выбрался: ключ, дверь и угол! +50';
                    addScore(50);
                    if (advanceLevel('maze')) output += '\n✨ Следующий эпизод!';
                }
            }
            document.getElementById('mazeOutput').innerText = output || 'Выполнено.';
        };

        function drawMaze() {
            if (!mazeLevelCurrent) return;
            const lvl = mazeLevelCurrent;
            const canvas = document.getElementById('mazeCanvas');
            if (!canvas || typeof canvas.getContext !== 'function') return;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;
            const W = canvas.width, H = canvas.height;
            ctx.clearRect(0, 0, W, H);
            const offsetY = 10;
            const pad = 6;
            const availW = W - pad * 2;
            const availH = H - offsetY - pad;
            const cellSize = Math.min(availW / mazeFieldSize, availH / mazeFieldSize);
            const gridPx = cellSize * mazeFieldSize;
            const offX = (W - gridPx) / 2;
            const [hx, hy] = [Math.floor(mazeFieldSize / 2), Math.floor(mazeFieldSize / 2)];
            ctx.fillStyle = '#fff5e6';
            ctx.fillRect(offX + hx * cellSize + 1, offsetY + hy * cellSize + 1, cellSize - 2, cellSize - 2);
            ctx.strokeStyle = '#ccc';
            for (let i = 0; i <= mazeFieldSize; i++) {
                ctx.beginPath(); ctx.moveTo(offX + i * cellSize, offsetY); ctx.lineTo(offX + i * cellSize, offsetY + gridPx); ctx.stroke();
                ctx.beginPath(); ctx.moveTo(offX, offsetY + i * cellSize); ctx.lineTo(offX + gridPx, offsetY + i * cellSize); ctx.stroke();
            }
            const [dox, doy] = lvl.door;
            lvl.walls.forEach(([x, y]) => {
                const isClosedDoor = !mazeDoorOpened && x === dox && y === doy;
                ctx.fillStyle = isClosedDoor ? '#92400e' : '#6c757d';
                ctx.fillRect(offX + x * cellSize, offsetY + y * cellSize, cellSize - 2, cellSize - 2);
                if (isClosedDoor) {
                    ctx.strokeStyle = '#451a03';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(offX + x * cellSize + 4, offsetY + y * cellSize + 4, cellSize - 10, cellSize - 10);
                }
            });
            lvl.spikes.forEach(([x, y]) => {
                ctx.beginPath();
                ctx.moveTo(offX + x * cellSize + 4, offsetY + y * cellSize + 4);
                ctx.lineTo(offX + x * cellSize + cellSize - 4, offsetY + y * cellSize + cellSize - 4);
                ctx.moveTo(offX + x * cellSize + cellSize - 4, offsetY + y * cellSize + 4);
                ctx.lineTo(offX + x * cellSize + 4, offsetY + y * cellSize + cellSize - 4);
                ctx.strokeStyle = '#dc3545';
                ctx.lineWidth = 2;
                ctx.stroke();
            });
            ctx.fillStyle = '#28a745';
            ctx.fillRect(offX + (mazeFieldSize - 1) * cellSize, offsetY + (mazeFieldSize - 1) * cellSize, cellSize - 2, cellSize - 2);
            if (!mazeHasKey && lvl.chest) {
                const [sx, sy] = lvl.chest;
                const px = offX + sx * cellSize + 4, py = offsetY + sy * cellSize + 4;
                ctx.fillStyle = '#78350f';
                ctx.fillRect(px, py + cellSize * 0.2, cellSize - 8, cellSize * 0.5);
                ctx.fillStyle = '#b45309';
                ctx.fillRect(px, py, cellSize - 8, cellSize * 0.35);
                ctx.strokeStyle = '#451a03';
                ctx.strokeRect(px, py, cellSize - 8, cellSize * 0.85);
            }
            ctx.fillStyle = '#3498db';
            ctx.fillRect(offX + mazeX * cellSize, offsetY + mazeY * cellSize, cellSize - 2, cellSize - 2);
            ctx.fillStyle = '#f1c40f';
            let rcx = offX + mazeX * cellSize + cellSize / 2;
            let rcy = offsetY + mazeY * cellSize + cellSize / 2;
            let arrowSize = cellSize / 4;
            if (mazeDir === 0) ctx.fillRect(rcx + arrowSize, rcy - arrowSize / 2, arrowSize, arrowSize);
            else if (mazeDir === 1) ctx.fillRect(rcx - arrowSize / 2, rcy + arrowSize, arrowSize, arrowSize);
            else if (mazeDir === 2) ctx.fillRect(rcx - 2 * arrowSize, rcy - arrowSize / 2, arrowSize, arrowSize);
            else ctx.fillRect(rcx - arrowSize / 2, rcy - 2 * arrowSize, arrowSize, arrowSize);
        }

        window.resetMaze = (skipAddScore = false) => {
            loadMazeLevel(player.mazeLevel);
            if (!skipAddScore) addScore(5);
        };
        window.undoMaze = () => { if (!ensureMazeReady()) return; mazeCommands.pop(); renderMazeCommands(); };

        // ================= ЧЕРЕПАШКА (Turtle) =================
        let turtleCommands = [];
        let turtleX = 200, turtleY = 150, turtleAngle = 0;
        let turtlePenDown = true;
        let turtleColor = '#000000';
        let turtlePaths = [];

        const turtleTasks = [
            { desc: "Квадрат: ровно 4 стороны одной длины", check: (paths) => paths.length >= 4 },
            { desc: "Пятиугольник: не меньше 5 отрезков", check: (paths) => paths.length >= 5 },
            { desc: "Шестиугольник: не меньше 6 отрезков", check: (paths) => paths.length >= 6 },
            { desc: "«Домик»: нижний контур и крыша — не меньше 7 отрезков", check: (paths) => paths.length >= 7 },
            { desc: "Окружность: много коротких шагов (вперёд 1 + поворот 1° много раз)", check: (paths) => paths.length >= 200 },
            { desc: "Сложная фигура: не меньше 10 отрезков", check: (paths) => paths.length >= 10 },
            { desc: "Ломаная: не меньше 16 отрезков", check: (paths) => paths.length >= 16 },
            { desc: "Восьмиугольник: не меньше 8 отрезков", check: (paths) => paths.length >= 8 },
            { desc: "Два контура: подними перо между ними — не меньше 12 отрезков", check: (paths) => paths.length >= 12 },
            { desc: "Мастер: не меньше 24 отрезка", check: (paths) => paths.length >= 24 }
        ];

        function loadTurtleLevel(level) {
            resetTurtle(true);
            let task = turtleTasks[(level-1) % turtleTasks.length];
            document.getElementById('turtleOutput').innerText = `Уровень ${level}: ${task.desc}`;
        }

        window.addTurtleCommand = (type, val) => {
            turtleCommands.push({ type, val });
            renderTurtleCommands();
        };

        function renderTurtleCommands() {
            const div = document.getElementById('turtleCommands');
            div.innerHTML = turtleCommands.map(c => {
                if (c.type === 'forward') return `<span class="command-item">⬆️ вперёд ${c.val}</span>`;
                if (c.type === 'back') return `<span class="command-item">⬇️ назад ${c.val}</span>`;
                if (c.type === 'left') return `<span class="command-item">↺ налево ${c.val}°</span>`;
                if (c.type === 'right') return `<span class="command-item">↻ направо ${c.val}°</span>`;
                if (c.type === 'penup') return `<span class="command-item">✋ перо вверх</span>`;
                if (c.type === 'pendown') return `<span class="command-item">✏️ перо вниз</span>`;
                if (c.type === 'color') return `<span class="command-item">🎨 сменить цвет</span>`;
                if (c.type === 'clear') return `<span class="command-item">🧽 очистить</span>`;
                return '';
            }).join(' ');
        }

        window.runTurtle = () => {
            for (let cmd of turtleCommands) {
                if (cmd.type === 'forward' || cmd.type === 'back') {
                    let dist = cmd.val * (cmd.type === 'back' ? -1 : 1);
                    let rad = turtleAngle * Math.PI / 180;
                    let newX = turtleX + dist * Math.cos(rad);
                    let newY = turtleY + dist * Math.sin(rad);
                    if (turtlePenDown) {
                        turtlePaths.push({
                            x1: turtleX, y1: turtleY,
                            x2: newX, y2: newY,
                            color: turtleColor
                        });
                    }
                    turtleX = newX;
                    turtleY = newY;
                } else if (cmd.type === 'left') {
                    turtleAngle -= cmd.val;
                } else if (cmd.type === 'right') {
                    turtleAngle += cmd.val;
                } else if (cmd.type === 'penup') {
                    turtlePenDown = false;
                } else if (cmd.type === 'pendown') {
                    turtlePenDown = true;
                } else if (cmd.type === 'color') {
                    turtleColor = '#' + Math.floor(Math.random()*16777215).toString(16);
                } else if (cmd.type === 'clear') {
                    turtlePaths = [];
                }
            }
            drawTurtle();
            let task = turtleTasks[(player.turtleLevel-1) % turtleTasks.length];
            if (task.check(turtlePaths)) {
                document.getElementById('turtleOutput').innerText = '✅ Задание выполнено! +50';
                addScore(50);
                advanceLevel('turtle');
            } else {
                document.getElementById('turtleOutput').innerText =
                    'Ещё не условие: ' + task.desc + '\n(сейчас отрезков: ' + turtlePaths.length + ')';
            }
        };

        function drawTurtle() {
            const canvas = document.getElementById('turtleCanvas');
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, 400, 300);
            for (let p of turtlePaths) {
                ctx.beginPath();
                ctx.moveTo(p.x1, p.y1);
                ctx.lineTo(p.x2, p.y2);
                ctx.strokeStyle = p.color;
                ctx.lineWidth = 2;
                ctx.stroke();
            }
            ctx.save();
            ctx.translate(turtleX, turtleY);
            ctx.rotate(turtleAngle * Math.PI/180);
            ctx.fillStyle = '#2ecc71';
            ctx.fillRect(-10, -5, 20, 10);
            ctx.fillStyle = '#27ae60';
            ctx.beginPath();
            ctx.moveTo(10, 0);
            ctx.lineTo(20, -5);
            ctx.lineTo(20, 5);
            ctx.closePath();
            ctx.fill();
            ctx.restore();
        }

        window.resetTurtle = (skipAddScore = false) => {
            turtleCommands = [];
            turtleX = 200; turtleY = 150; turtleAngle = 0;
            turtlePenDown = true;
            turtleColor = '#000000';
            turtlePaths = [];
            drawTurtle();
            document.getElementById('turtleOutput').innerText = '';
            if (!skipAddScore) addScore(5);
        };
        window.undoTurtle = () => { turtleCommands.pop(); renderTurtleCommands(); };

        // ================= РАСКРАСКА (домик → собачка → машина) =================
        const PAINT_COLOR_ALIASES = {
            'красный': '#e53935', 'оранжевый': '#fb8c00', 'жёлтый': '#fdd835', 'желтый': '#fdd835',
            'зелёный': '#43a047', 'зеленый': '#43a047', 'синий': '#1e88e5', 'голубой': '#29b6f6',
            'фиолетовый': '#8e24aa', 'коричневый': '#6d4c41', 'чёрный': '#212121', 'черный': '#212121',
            'белый': '#fafafa', 'серый': '#9e9e9e', 'серебро': '#eceff1',
            'red': '#e53935', 'orange': '#fb8c00', 'yellow': '#fdd835', 'green': '#43a047', 'blue': '#1e88e5',
            'brown': '#6d4c41', 'white': '#fafafa', 'black': '#212121', 'gray': '#9e9e9e', 'grey': '#9e9e9e'
        };

        const PAINT_CHAR_MAP = {
            '.': null,
            'X': 'line',
            'K': '#e65100',
            'S': '#eceff1',
            'O': '#42a5f5',
            'D': '#5d4037',
            'B': '#8d6e63',
            'W': '#ffe0b2',
            '@': '#263238',
            'E': '#4e342e',
            'R': '#90a4ae',
            'M': '#c62828',
            'P': '#fbc02d',
            'T': '#37474f'
        };

        const paintFigures = [
            {
                title: 'Домик',
                teaser: 'Крыша K — оранжевый/контур K, стены S — светло-серый, окна O — голубой, дверь D — коричневый.',
                rows: [
                    '.....KKKK.....',
                    '....KKKKKK....',
                    '...KKKKKKKK...',
                    'XXSSOOOOSSXX',
                    'XXSSOOOOSSXX',
                    'XXSSDDDDSSXX',
                    'XXSSSSSSSSXX',
                    '..............'
                ]
            },
            {
                title: 'Собачка',
                teaser: 'Уши E — тёмно-коричневые, морда W — беж, тело B — коричневое, глаз/нос @ — тёмный.',
                rows: [
                    '......EE......',
                    '....BB@@BB....',
                    '...BBBBBBBB...',
                    '..BBWW@@@@WWBB',
                    '..BBWW@@@@WWBB',
                    '..BBBBBBBBBBBB',
                    '...BBBBBBBB...',
                    '..............'
                ]
            },
            {
                title: 'Машина',
                teaser: 'Корпус M — красный, стёкла R — серый, фары P — жёлтый, колёса T — тёмно-серый.',
                rows: [
                    '.....MMMMMM.....',
                    '....RRRRRRRR....',
                    '...RRRRRRRRRR...',
                    '...MMMMMMMMMM...',
                    '.TTTTTTTTTTTTTT.',
                    '.TTMMMMMMMMMMTT.',
                    '.TTMMMMMMMMMMTT.',
                    '................'
                ]
            }
        ];

        let paintModel = { rows: 0, cols: 0, target: [], user: [], isLine: [], outside: [] };

        function paintNormalizeColor(s) {
            s = String(s).trim();
            if (!s) return null;
            if (s[0] === '#') {
                if (s.length === 4) return ('#' + s[1] + s[1] + s[2] + s[2] + s[3] + s[3]).toLowerCase();
                return s.toLowerCase();
            }
            const key = s.toLowerCase();
            return PAINT_COLOR_ALIASES[key] || null;
        }

        function buildPaintModel(figure) {
            const rowStrs = figure.rows;
            const rows = rowStrs.length;
            const cols = Math.max(...rowStrs.map(r => r.length));
            const target = [], user = [], isLine = [], outside = [];
            for (let r = 0; r < rows; r++) {
                target[r] = []; user[r] = []; isLine[r] = []; outside[r] = [];
                const row = rowStrs[r].padEnd(cols, '.');
                for (let c = 0; c < cols; c++) {
                    const ch = row[c];
                    const mapped = PAINT_CHAR_MAP[ch];
                    if (ch === '.' || mapped === null || mapped === undefined) {
                        target[r][c] = null; isLine[r][c] = false; outside[r][c] = true; user[r][c] = null;
                    } else if (mapped === 'line') {
                        target[r][c] = null; isLine[r][c] = true; outside[r][c] = false; user[r][c] = null;
                    } else {
                        target[r][c] = mapped; isLine[r][c] = false; outside[r][c] = false; user[r][c] = null;
                    }
                }
            }
            return { title: figure.title, teaser: figure.teaser, rows, cols, target, user, isLine, outside };
        }

        function loadPaintLevel(level) {
            let idx = Math.max(0, Math.min(paintFigures.length - 1, level - 1));
            const fig = paintFigures[idx];
            paintModel = buildPaintModel(fig);
            document.getElementById('paintTaskTitle').innerHTML =
                `<strong>Уровень ${level} — ${fig.title}.</strong> ${fig.teaser} Координаты с нуля: <code>строка столбец цвет</code>.`;
            const ta = document.getElementById('paintCommandInput');
            if (ta) ta.value = '';
            document.getElementById('paintOutput').innerText = 'Введи команды и нажми «Применить».';
            drawPaintCanvas();
            const hintEl = document.getElementById('paintHintText');
            if (hintEl) hintEl.textContent = 'Нажмите «Подсказка», чтобы увидеть примеры команд.';
        }

        function paintIsComplete() {
            for (let r = 0; r < paintModel.rows; r++) {
                for (let c = 0; c < paintModel.cols; c++) {
                    const t = paintModel.target[r][c];
                    if (!t) continue;
                    const u = paintModel.user[r][c];
                    if (!u || u.toLowerCase() !== t.toLowerCase()) return false;
                }
            }
            return true;
        }

        function drawPaintCanvas() {
            const canvas = document.getElementById('paintCanvas');
            if (!canvas || !paintModel.rows) return;
            const ctx = canvas.getContext('2d');
            const W = canvas.width, H = canvas.height;
            ctx.clearRect(0, 0, W, H);
            const pad = 8;
            const cw = (W - pad * 2) / paintModel.cols;
            const ch = (H - pad * 2) / paintModel.rows;
            const cell = Math.min(cw, ch);
            const ox = (W - cell * paintModel.cols) / 2;
            const oy = (H - cell * paintModel.rows) / 2;
            ctx.font = `${Math.max(9, cell * 0.22)}px sans-serif`;
            ctx.fillStyle = '#eceff1';
            ctx.fillRect(0, 0, W, H);

            for (let r = 0; r < paintModel.rows; r++) {
                for (let c = 0; c < paintModel.cols; c++) {
                    const x = ox + c * cell;
                    const y = oy + r * cell;
                    if (paintModel.outside[r][c]) {
                        ctx.fillStyle = '#f5f5f5';
                        ctx.fillRect(x, y, cell - 1, cell - 1);
                    } else if (paintModel.isLine[r][c]) {
                        ctx.fillStyle = '#fff';
                        ctx.fillRect(x, y, cell - 1, cell - 1);
                        ctx.strokeStyle = '#263238';
                        ctx.lineWidth = 2;
                        ctx.strokeRect(x, y, cell - 1, cell - 1);
                    } else {
                        const u = paintModel.user[r][c];
                        const t = paintModel.target[r][c];
                        ctx.fillStyle = u || '#ffffff';
                        ctx.fillRect(x, y, cell - 1, cell - 1);
                        ctx.strokeStyle = u ? '#666' : '#bbb';
                        ctx.lineWidth = 1;
                        ctx.strokeRect(x, y, cell - 1, cell - 1);
                        if (!u && t) {
                            ctx.fillStyle = 'rgba(0,0,0,0.06)';
                            ctx.fillRect(x, y, cell - 1, cell - 1);
                        }
                    }
                }
            }
            ctx.fillStyle = '#546e7a';
            for (let r = 0; r < paintModel.rows; r++) {
                for (let c = 0; c < paintModel.cols; c++) {
                    if (!paintModel.outside[r][c]) {
                        ctx.fillText(r + ',' + c, ox + c * cell + 2, oy + r * cell + 12);
                    }
                }
            }

            // Второе превью-«фото» без сетки
            drawPaintPreviewCanvas();
        }

        function drawPaintPreviewCanvas() {
            const canvas = document.getElementById('paintPreviewCanvas');
            if (!canvas || !paintModel.rows) return;
            const ctx = canvas.getContext('2d');
            const W = canvas.width, H = canvas.height;
            ctx.clearRect(0, 0, W, H);
            ctx.fillStyle = '#fafafa';
            ctx.fillRect(0, 0, W, H);

            const pad = 10;
            const availW = W - pad * 2;
            const availH = H - pad * 2;
            const cell = Math.min(availW / paintModel.cols, availH / paintModel.rows);
            const ox = (W - cell * paintModel.cols) / 2;
            const oy = (H - cell * paintModel.rows) / 2;

            for (let r = 0; r < paintModel.rows; r++) {
                for (let c = 0; c < paintModel.cols; c++) {
                    if (paintModel.outside[r][c]) continue;
                    const x = ox + c * cell;
                    const y = oy + r * cell;

                    if (paintModel.isLine[r][c]) {
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(x, y, cell, cell);
                        ctx.strokeStyle = '#263238';
                        ctx.lineWidth = Math.max(2, cell * 0.08);
                        ctx.strokeRect(x, y, cell, cell);
                        continue;
                    }

                    const t = paintModel.target[r][c];
                    if (!t) continue;
                    const u = paintModel.user[r][c];
                    if (u) {
                        ctx.fillStyle = u;
                        ctx.fillRect(x, y, cell, cell);
                        ctx.strokeStyle = 'rgba(40,40,40,0.25)';
                        ctx.lineWidth = Math.max(1, cell * 0.05);
                        ctx.strokeRect(x, y, cell, cell);
                    } else {
                        // Показываем «как получится»: целевой цвет очень прозрачно
                        ctx.save();
                        ctx.globalAlpha = 0.22;
                        ctx.fillStyle = t;
                        ctx.fillRect(x, y, cell, cell);
                        ctx.restore();
                        ctx.strokeStyle = 'rgba(40,40,40,0.12)';
                        ctx.lineWidth = Math.max(1, cell * 0.05);
                        ctx.strokeRect(x, y, cell, cell);
                    }
                }
            }
        }

        window.runPaintCommands = () => {
            const beforeDone = paintIsComplete();
            const out = [];
            const raw = document.getElementById('paintCommandInput').value.split(/\n/);
            for (let line of raw) {
                line = line.trim();
                if (!line || line.startsWith('#')) continue;
                let m = line.match(/^(\d+)\s*[,;\s]\s*(\d+)\s+(.+)$/);
                if (!m) m = line.match(/^(\d+)\s+(\d+)\s+(.+)$/);
                if (!m) { out.push('Пропуск строки: ' + line); continue; }
                const r = parseInt(m[1], 10), c = parseInt(m[2], 10), rest = m[3];
                const col = paintNormalizeColor(rest);
                if (!col) { out.push('Неизвестный цвет: ' + rest); continue; }
                if (r < 0 || c < 0 || r >= paintModel.rows || c >= paintModel.cols) {
                    out.push(`Вне поля: ${r} ${c}`); continue;
                }
                if (paintModel.outside[r][c]) { out.push(`(${r},${c}) — пусто за фигурой`); continue; }
                if (paintModel.isLine[r][c]) { out.push(`(${r},${c}) — контур, не крась`); continue; }
                if (!paintModel.target[r][c]) { out.push(`(${r},${c}) — нечего красить`); continue; }
                paintModel.user[r][c] = col;
                out.push(`OK (${r},${c}) → ${col}`);
            }
            drawPaintCanvas();
            const el = document.getElementById('paintOutput');
            el.innerText = out.join('\n') || 'Команды применены.';
            if (!beforeDone && paintIsComplete()) {
                el.innerText += '\n✅ Вся фигура совпала с эталоном!';
                advanceLevel('paint');
            }
        };

        window.resetPaintUser = () => {
            if (!paintModel.rows) return;
            for (let r = 0; r < paintModel.rows; r++) {
                for (let c = 0; c < paintModel.cols; c++) {
                    if (paintModel.target[r][c]) paintModel.user[r][c] = null;
                }
            }
            document.getElementById('paintCommandInput').value = '';
            drawPaintCanvas();
            document.getElementById('paintOutput').innerText = 'Краски сброшены.';
        };

        window.showPaintHint = () => {
            const hintEl = document.getElementById('paintHintText');
            if (!hintEl) return;
            const lines = [];
            lines.push('Формат: строка столбец цвет (цвет можно hex, например #fb8c00).');
            lines.push('Контур (X) красить нельзя.');
            lines.push('');
            lines.push('Примеры (можно переписать в шаблон):');
            let n = 0;
            for (let r = 0; r < paintModel.rows && n < 6; r++) {
                for (let c = 0; c < paintModel.cols && n < 6; c++) {
                    const t = paintModel.target[r][c];
                    if (!t) continue;
                    lines.push(`${r} ${c} ${t}`);
                    n++;
                }
            }
            hintEl.textContent = lines.join('\n');
        };

        window.fillPaintTemplate = () => {
            const ta = document.getElementById('paintCommandInput');
            if (!ta || !paintModel.rows) return;
            const lines = [];
            lines.push('# Шаблон команд: заполняй координаты цвета (r c цвет).');
            let count = 0;
            for (let r = 0; r < paintModel.rows; r++) {
                for (let c = 0; c < paintModel.cols; c++) {
                    const t = paintModel.target[r][c];
                    if (!t) continue;
                    lines.push(`${r} ${c} ${t}`);
                    count++;
                }
            }
            ta.value = lines.join('\n');
        };

        // ================= КАЛЬКУЛЯТОР (исправленный: ввод ответа) =================
        const calcProblems = [
            { desc: "2 + 2", expected: 4 },
            { desc: "5 * 3", expected: 15 },
            { desc: "10 - 4", expected: 6 },
            { desc: "(2 + 3) * 4", expected: 20 },
            { desc: "100 / 5 + 3", expected: 23 },
            { desc: "7 * 8", expected: 56 },
            { desc: "12 + 8", expected: 20 },
            { desc: "9 * 9", expected: 81 },
            { desc: "45 / 5", expected: 9 },
            { desc: "100 - 33", expected: 67 }
        ];
        let calcCurrentProblem = null;

        function normalizeCalcExpr(s) {
            return String(s).replace(/\s+/g, '').replace(/,/g, '.');
        }

        function loadCalcLevel(level) {
            let idx = (level-1) % calcProblems.length;
            calcCurrentProblem = calcProblems[idx];
            document.getElementById('calcOutput').innerText =
                `Уровень ${level}: ${calcCurrentProblem.desc} = ?\n` +
                'Введите полную строку с «=», как в программе: выражение = результат';
            document.getElementById('calcExpression').value = '';
        }

        window.runCalc = () => {
            let raw = document.getElementById('calcExpression').value.trim();
            if (raw === '') {
                document.getElementById('calcOutput').innerText = 'Введите строку вида: ' + calcCurrentProblem.desc + ' = <число>';
                return;
            }
            if (!raw.includes('=')) {
                document.getElementById('calcOutput').innerText =
                    'Нужен знак «=»: сначала выражение из задания, затем «=» и ответ (как присваивание в коде).';
                return;
            }
            const eqIdx = raw.indexOf('=');
            const left = raw.slice(0, eqIdx).trim();
            const right = raw.slice(eqIdx + 1).trim();
            if (!right.length) {
                document.getElementById('calcOutput').innerText = 'После «=» напишите числовой результат.';
                return;
            }
            const rightNum = Number(normalizeCalcExpr(right));
            if (Number.isNaN(rightNum)) {
                document.getElementById('calcOutput').innerText = 'Справа от «=» должно быть число.';
                return;
            }
            if (normalizeCalcExpr(left) !== normalizeCalcExpr(calcCurrentProblem.desc)) {
                document.getElementById('calcOutput').innerText =
                    'Левая часть должна совпадать с заданием (без изменений): ' + calcCurrentProblem.desc;
                return;
            }
            if (Math.abs(rightNum - calcCurrentProblem.expected) < 1e-9) {
                document.getElementById('calcOutput').innerText = '✅ Верно! +50';
                addScore(50);
                if (advanceLevel('calc')) {
                    document.getElementById('calcOutput').innerText += '\n✨ Уровень повышен!';
                }
            } else {
                document.getElementById('calcOutput').innerText = '❌ Неверный результат справа от «=».';
            }
        };

        window.resetCalc = () => {
            loadCalcLevel(player.calcLevel);
            addScore(5);
        };

        // ================= ПЕСОЧНИЦА (с подсказками; задания сложнее, чем раньше) =================
        let sandboxMode = 'python';
        let pythonTasks = [
            { task: 'Выведи ровно строку Hello, world!', expected: 'Hello, world!', hint: 'Строка в двойных кавычках: print("Hello, world!")', starter: 'print("Hello, world!")' },
            { task: 'Выведи числом результат 15 + 27', expected: '42', hint: 'Числа без кавычек, иначе получится склейка строк: print(15 + 27)', starter: '# print(15 + 27)\n' },
            { task: 'Выведи квадрат числа 11 (121)', expected: '121', hint: 'Умножение или степень: print(11 * 11) либо print(11 ** 2)', starter: '# print(11 * 11)\n' },
            { task: 'Выведи три строки: A, затем B, затем C (каждая с новой строки)', expected: 'A\nB\nC', hint: 'Три вызова print подряд — или одна строка с \\n: print("A\\nB\\nC")', starter: 'print("A")\n# выведи B и C отдельными print\n' },
            { task: 'Выведи числа 1, 2, 3, 4 столбиком', expected: '1\n2\n3\n4', hint: 'Цикл for i in range(1, 5): и внутри print(i). Отступ у print обязателен.', starter: 'for i in range(1, 5):\n    \n' },
            { task: 'Выведи сумму чисел от 1 до 5 (ответ 15)', expected: '15', hint: 'print(sum(range(1, 6))) — range до 6 не включительно.', starter: '# print(sum(range(1, 6)))\n' },
            { task: 'Выведи остаток от деления 23 на 4', expected: '3', hint: 'Оператор модуля в Python: print(23 % 4)', starter: '# print(23 % 4)\n' },
            { task: 'Выведи результат сравнения 100 > 1 (одно слово True или False)', expected: 'True', hint: 'Сравнение без кавычек: print(100 > 1)', starter: '# print(100 > 1)\n' }
        ];
        let pascalTasks = [
            { task: "Строка Hello, world!", expected: 'Hello, world!', hint: "writeln('Hello, world!'); между begin и end.", starter: "begin\n  writeln('Hello, world!');\nend." },
            { task: 'Выведи число 256', expected: '256', hint: 'writeln(256); или writeln(\'256\'); для проверки — число проще.', starter: "begin\n  writeln(256);\nend." },
            { task: 'Две строки: OK затем FAIL с новой строки', expected: 'OK\nFAIL', hint: 'Два writeln подряд, каждый с точкой с запятой.', starter: "begin\n  writeln('OK');\n  writeln('FAIL');\nend." },
            { task: 'Числа 2, 4, 6 в столбик', expected: '2\n4\n6', hint: 'writeln(2); writeln(4); writeln(6);', starter: "begin\n  writeln(2);\n  writeln(4);\n  writeln(6);\nend." },
            { task: 'Строка Free Pascal', expected: 'Free Pascal', hint: "Один writeln с текстом в апострофах: writeln('Free Pascal');", starter: "begin\n  writeln('Free Pascal');\nend." }
        ];
        let currentSandboxTask = null;

        function sandboxNormalizeOutput(s) {
            return String(s).replace(/\r\n/g, '\n').replace(/\r/g, '\n').trim();
        }

        function sandboxOutputsMatch(actual, expected) {
            return sandboxNormalizeOutput(actual) === sandboxNormalizeOutput(expected);
        }

        function loadSandboxLevel(level, mode) {
            let tasks = mode === 'python' ? pythonTasks : pascalTasks;
            let idx = (level-1) % tasks.length;
            currentSandboxTask = tasks[idx];
            document.getElementById('sandboxOutput').innerText = `Уровень ${level} (${mode}): ${currentSandboxTask.task}`;
            const hintEl = document.getElementById('sandboxHintText');
            if (hintEl) hintEl.textContent = ' ' + currentSandboxTask.hint;
            document.getElementById('sandboxCode').value =
                currentSandboxTask.starter != null ? currentSandboxTask.starter :
                (mode === 'python' ? 'print("Hi")\n' : "begin\nend.");
        }

        window.setSandboxMode = (mode) => {
            sandboxMode = mode;
            document.getElementById('pythonModeBtn').classList.toggle('active', mode === 'python');
            document.getElementById('pascalModeBtn').classList.toggle('active', mode === 'pascal');
            loadSandboxLevel(player[mode + 'Level'], mode);
        };

        function skulptBuiltinRead(name) {
            if (Sk.builtinFiles === undefined || Sk.builtinFiles['files'][name] === undefined) {
                throw new Error('Не найден файл для import: ' + name);
            }
            return Sk.builtinFiles['files'][name];
        }

        window.runSandbox = () => {
            const code = document.getElementById('sandboxCode').value;
            let output = '';
            if (sandboxMode === 'python') {
                if (typeof Sk === 'undefined' || !Sk.configure || !Sk.misceval || !Sk.misceval.asyncToPromise) {
                    processSandboxOutput(
                        'Python (Skulpt) ещё не загрузился. Подождите секунду и нажмите «Выполнить» снова, проверьте интернет или отключите блокировку CDN jsDelivr.'
                    );
                    return;
                }
                Sk.pre = 'output';
                output = '';
                Sk.configure({
                    output: (text) => { output += text; },
                    read: skulptBuiltinRead,
                    __future__: Sk.python3
                });
                Sk.misceval.asyncToPromise(function () {
                    return Sk.importMainWithBody('<stdin>', false, code, true);
                }).then(function () {
                    processSandboxOutput(output);
                }, function (err) {
                    processSandboxOutput('Ошибка: ' + (err && err.toString ? err.toString() : String(err)));
                });
            } else {
                let parts = [];
                const re = /writeln\s*\(\s*(?:'([^']*)'|"([^"]*)"|(\d+))\s*\)\s*;?/gi;
                let m;
                while ((m = re.exec(code)) !== null) {
                    if (m[1] !== undefined && m[1] !== '') parts.push(m[1]);
                    else if (m[2] !== undefined && m[2] !== '') parts.push(m[2]);
                    else if (m[3] !== undefined) parts.push(m[3]);
                }
                output = parts.join('\n');
                if (!output && /writeln/i.test(code)) {
                    output = 'Не удалось разобрать writeln — используй writeln(\'текст\'); или writeln(число);';
                }
                if (!output) output = 'Добавь writeln(...) в блок begin..end.';
                processSandboxOutput(output);
            }
        };

        function processSandboxOutput(output) {
            output = sandboxNormalizeOutput(output);
            const el = document.getElementById('sandboxOutput');
            el.innerText = output;
            if (sandboxOutputsMatch(output, currentSandboxTask.expected)) {
                el.innerText += '\n✅ Задание выполнено! +50';
                addScore(50);
                if (advanceLevel(sandboxMode)) {
                    el.innerText += '\n✨ Уровень повышен!';
                }
            } else if (output) {
                el.innerText += '\n❌ Вывод не совпадает с заданием. Смотри подсказку выше.';
            }
        }

        window.resetSandbox = () => {
            loadSandboxLevel(player[sandboxMode + 'Level'], sandboxMode);
            addScore(5);
        };

        // ================= ПЕРЕКЛЮЧЕНИЕ ТАБОВ =================
        document.querySelectorAll('.trainer-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.trainer-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                let tabName = this.dataset.tab;
                document.querySelectorAll('.trainer-container').forEach(c => c.classList.remove('active'));
                const panel = document.getElementById('trainer-' + tabName);
                if (panel) panel.classList.add('active');
                else console.error('trainer panel not found:', tabName);
                if (tabName === 'robotmaze') {
                    requestAnimationFrame(() => {
                        try {
                            if (!mazeLevelCurrent) loadMazeLevel(player.mazeLevel);
                            drawMaze();
                        } catch (e) { console.error('robotmaze tab draw', e); }
                    });
                }
            });
        });

        (function initSimInstructions() {
            document.querySelectorAll('[data-sim-instruction]').forEach((outer) => {
                const btn = outer.querySelector('.sim-instruction-toggle');
                if (!btn) return;
                const expandLabel = btn.dataset.labelShow || 'Развернуть';
                const collapseLabel = btn.dataset.labelHide || 'Свернуть';
                btn.addEventListener('click', () => {
                    const collapsed = outer.classList.toggle('is-collapsed');
                    btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    btn.textContent = collapsed ? expandLabel : collapseLabel;
                });
            });
        })();

        function initLevels() {
            const run = (name, fn) => {
                try { fn(); } catch (e) { console.error('initLevels:', name, e); }
            };
            run('scratch', () => loadScratchLevel(player.scratchLevel));
            run('wordgrid', () => loadWordGridLevel(player.wordgridLevel));
            run('maze', () => loadMazeLevel(player.mazeLevel));
            run('turtle', () => loadTurtleLevel(player.turtleLevel));
            run('calc', () => loadCalcLevel(player.calcLevel));
            run('paint', () => loadPaintLevel(player.paintLevel));
            run('sandbox', () => setSandboxMode('python'));
        }

        // Загрузка таблицы лидеров из БД
        fetch('get_leaderboard.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderLeaderboard(data.leaderboard);
                }
            });

        function renderLeaderboard(entries) {
            const tbody = document.querySelector('#leaderboardTable tbody');
            if (!tbody || !entries || !Array.isArray(entries)) return;
            tbody.innerHTML = '';
            entries.forEach(entry => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${escapeHtml(entry.name)}</td><td>${entry.totalScore}</td><td>${entry.level}</td>`;
                tbody.appendChild(tr);
            });
        }

        function escapeHtml(unsafe) {
            return unsafe.replace(/[&<>"]/g, function(m) {
                if(m === '&') return '&amp;'; if(m === '<') return '&lt;'; if(m === '>') return '&gt;'; if(m === '"') return '&quot;';
                return m;
            });
        }
    })();
</script>

<?php require_once __DIR__ . '/footer.php'; ?>