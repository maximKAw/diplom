<?php
declare(strict_types=1);

/**
 * Определяет ключ активного пункта меню для подсветки.
 */
function admin_nav_active_key(): string
{
    $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $table = isset($_GET['table']) ? (string) $_GET['table'] : '';
    if ($script === 'index.php') {
        return 'home';
    }
    if ($script === 'questions.php') {
        return 'questions';
    }
    if ($script === 'crud.php') {
        $map = [
            'articles' => 'articles',
            'professions' => 'professions',
            'languages' => 'languages',
            'slides' => 'slides',
            'tests' => 'tests',
            'results' => 'results',
            'test_final' => 'test_final',
            'test_quests' => 'test_quests',
            'quiz_logos' => 'quiz_logos',
            'users' => 'users',
            'admins' => 'admins',
        ];
        return $map[$table] ?? 'crud';
    }
    return '';
}

function admin_layout_start(string $title = 'Админ-панель'): void
{
    $user = admin_user();
    $nav = admin_nav_active_key();
    ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> — Проф симулятор</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: #e8ecf1;
            color: #1a1a2e;
            min-height: 100vh;
        }
        .admin-topbar {
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .admin-topbar__inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .admin-topbar__row1 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 14px 0 10px;
            border-bottom: 1px solid #eef0f4;
        }
        .admin-brand {
            font-weight: 700;
            font-size: 1.15rem;
            color: #410cdf;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-brand:hover { color: #2d0a9e; }
        .admin-userbar {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 0.9rem;
            color: #555;
        }
        .admin-userbar a.logout {
            color: #c0392b;
            text-decoration: none;
            font-weight: 500;
        }
        .admin-userbar a.logout:hover { text-decoration: underline; }

        /* Сворачиваемая панель навигации + квадратные плитки */
        .admin-nav-details {
            border: none;
            margin: 0;
            padding: 0 0 14px;
        }
        .admin-nav-details > summary {
            list-style: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px 12px;
            padding: 10px 14px;
            margin: 8px 0 0;
            background: #f3f4f6;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #374151;
            user-select: none;
            border: 1px solid #e5e7eb;
        }
        .admin-nav-details > summary::-webkit-details-marker { display: none; }
        .admin-nav-details > summary::after {
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.75rem;
            color: #6b7280;
            transition: transform .2s ease;
        }
        .admin-nav-details[open] > summary::after {
            transform: rotate(-180deg);
        }
        .admin-nav-details > summary:hover {
            background: #eef0f4;
        }
        .admin-nav-box {
            margin-top: 12px;
            padding: 16px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.04);
        }
        .admin-nav-section {
            margin-bottom: 18px;
        }
        .admin-nav-section:last-child { margin-bottom: 0; }
        .admin-nav-section__title {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #9ca3af;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .admin-nav-tiles {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(108px, 1fr));
            gap: 10px;
        }
        @media (min-width: 900px) {
            .admin-nav-tiles {
                grid-template-columns: repeat(auto-fill, minmax(116px, 1fr));
            }
        }
        a.admin-nav-tile {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px 8px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            text-decoration: none;
            color: #374151;
            font-size: 0.72rem;
            font-weight: 500;
            line-height: 1.25;
            transition: background .15s, border-color .15s, transform .12s, box-shadow .15s;
            overflow: hidden;
            hyphens: auto;
            word-break: break-word;
        }
        a.admin-nav-tile i {
            font-size: 1.35rem;
            margin-bottom: 8px;
            color: #410cdf;
            opacity: .9;
        }
        a.admin-nav-tile:hover {
            background: #fff;
            border-color: #c4b5fd;
            box-shadow: 0 4px 12px rgba(65, 12, 223, .12);
            transform: translateY(-1px);
        }
        a.admin-nav-tile.is-active {
            background: linear-gradient(145deg, #410cdf 0%, #5b2dff 100%);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 14px rgba(65, 12, 223, .35);
        }
        a.admin-nav-tile.is-active i {
            color: #fff;
        }
        .admin-main {
            max-width: 1320px;
            margin: 0 auto;
            padding: 28px 20px 40px;
            overflow-x: auto;
        }
        .admin-main h1 { font-size: 1.45rem; margin-bottom: 8px; color: #1a1a2e; font-weight: 700; }
        .admin-main > .muted { margin-bottom: 22px; }
        .admin-toolbar { margin-bottom: 18px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-primary { background: #410cdf; color: #fff; }
        .btn-primary:hover { background: #3509b5; }
        .btn-secondary { background: #e8e8ef; color: #333; }
        .btn-danger { background: #c0392b; color: #fff; }
        .btn-sm { padding: 5px 10px; font-size: 0.8rem; }
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
        }
        .alert-ok { background: #d4edda; color: #155724; }
        .alert-err { background: #f8d7da; color: #721c24; }
        table.admin-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,.06);
        }
        table.admin-table th, table.admin-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 0.88rem;
            vertical-align: top;
        }
        table.admin-table th { background: #f8f9fc; font-weight: 600; }
        table.admin-table tr:last-child td { border-bottom: none; }
        .admin-form {
            background: #fff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,.06);
            max-width: 900px;
        }
        .admin-form label { display: block; margin-top: 14px; font-weight: 500; font-size: 0.88rem; }
        .admin-form label:first-child { margin-top: 0; }
        .admin-form input[type=text], .admin-form input[type=number], .admin-form input[type=email],
        .admin-form input[type=password], .admin-form input[type=url], .admin-form select, .admin-form textarea {
            width: 100%;
            margin-top: 6px;
            padding: 10px 12px;
            border: 1px solid #d0d4e0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
        }
        .admin-form textarea { min-height: 100px; resize: vertical; }
        .admin-form textarea.tall { min-height: 220px; }
        .admin-form .row-check { margin-top: 14px; display: flex; align-items: center; gap: 8px; }
        .admin-form .row-check input { width: auto; margin: 0; }
        .muted { color: #666; font-size: 0.9rem; }

        /* Карточки дашборда (как на макете) */
        .admin-dash-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }
        @media (max-width: 1100px) {
            .admin-dash-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 560px) {
            .admin-dash-grid { grid-template-columns: 1fr; }
        }
        a.admin-dash-card {
            display: block;
            text-decoration: none;
            color: inherit;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 18px rgba(0,0,0,.08);
            transition: transform .18s ease, box-shadow .18s ease;
        }
        a.admin-dash-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(0,0,0,.12);
        }
        .admin-dash-card__head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .admin-dash-card__head i { font-size: 1.05rem; opacity: .95; }
        .admin-dash-card__head--blue { background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%); }
        .admin-dash-card__head--orange { background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); }
        .admin-dash-card__head--green { background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%); }
        .admin-dash-card__head--red { background: linear-gradient(135deg, #f87171 0%, #ef4444 100%); }
        .admin-dash-card__body {
            padding: 16px 18px 18px;
            font-size: 0.88rem;
            line-height: 1.5;
            color: #4b5563;
        }
        .admin-dash-card__stat {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<header class="admin-topbar">
    <div class="admin-topbar__inner">
        <div class="admin-topbar__row1">
            <a class="admin-brand" href="index.php"><i class="fas fa-shield-alt"></i> Админ-панель</a>
            <div class="admin-userbar">
                <?php if ($user): ?>
                    <span><i class="fas fa-user" style="opacity:.5"></i> <?= esc($user['username']) ?></span>
                <?php endif; ?>
                <a class="btn btn-secondary btn-sm" href="../index.php" target="_blank" rel="noopener">Сайт</a>
                <a class="logout" href="logout.php">Выйти</a>
            </div>
        </div>
        <details class="admin-nav-details" id="adminNavPanel" open>
            <summary title="Показать или скрыть меню разделов">
                <span><i class="fas fa-th-large" style="margin-right:8px;color:#410cdf"></i> Разделы админки</span>
            </summary>
            <nav class="admin-nav-box" aria-label="Разделы админки">
                <div class="admin-nav-section">
                    <div class="admin-nav-section__title">Главная</div>
                    <div class="admin-nav-tiles">
                        <a href="index.php" class="admin-nav-tile <?= $nav === 'home' ? 'is-active' : '' ?>"><i class="fas fa-home"></i><span>Главная</span></a>
                    </div>
                </div>
                <div class="admin-nav-section">
                    <div class="admin-nav-section__title">Контент</div>
                    <div class="admin-nav-tiles">
                        <a href="crud.php?table=articles" class="admin-nav-tile <?= $nav === 'articles' ? 'is-active' : '' ?>"><i class="fas fa-newspaper"></i><span>Статьи</span></a>
                        <a href="crud.php?table=professions" class="admin-nav-tile <?= $nav === 'professions' ? 'is-active' : '' ?>"><i class="fas fa-briefcase"></i><span>Профессии</span></a>
                        <a href="crud.php?table=languages" class="admin-nav-tile <?= $nav === 'languages' ? 'is-active' : '' ?>"><i class="fas fa-code"></i><span>Языки</span></a>
                        <a href="crud.php?table=slides" class="admin-nav-tile <?= $nav === 'slides' ? 'is-active' : '' ?>"><i class="fas fa-images"></i><span>Слайды</span></a>
                    </div>
                </div>
                <div class="admin-nav-section">
                    <div class="admin-nav-section__title">Тесты</div>
                    <div class="admin-nav-tiles">
                        <a href="crud.php?table=tests" class="admin-nav-tile <?= $nav === 'tests' ? 'is-active' : '' ?>"><i class="fas fa-clipboard-list"></i><span>Тесты</span></a>
                        <a href="questions.php" class="admin-nav-tile <?= $nav === 'questions' ? 'is-active' : '' ?>"><i class="fas fa-question-circle"></i><span>Вопросы и ответы</span></a>
                        <a href="crud.php?table=results" class="admin-nav-tile <?= $nav === 'results' ? 'is-active' : '' ?>"><i class="fas fa-chart-bar"></i><span>Результаты</span></a>
                        <a href="crud.php?table=test_final" class="admin-nav-tile <?= $nav === 'test_final' ? 'is-active' : '' ?>"><i class="fas fa-flag-checkered"></i><span>Финальные вопросы</span></a>
                        <a href="crud.php?table=test_quests" class="admin-nav-tile <?= $nav === 'test_quests' ? 'is-active' : '' ?>"><i class="fas fa-map-signs"></i><span>Квесты</span></a>
                        <a href="crud.php?table=quiz_logos" class="admin-nav-tile <?= $nav === 'quiz_logos' ? 'is-active' : '' ?>"><i class="fas fa-puzzle-piece"></i><span>Викторина логотипов</span></a>
                    </div>
                </div>
                <div class="admin-nav-section">
                    <div class="admin-nav-section__title">Пользователи</div>
                    <div class="admin-nav-tiles">
                        <a href="crud.php?table=users" class="admin-nav-tile <?= $nav === 'users' ? 'is-active' : '' ?>"><i class="fas fa-users"></i><span>Пользователи</span></a>
                        <a href="crud.php?table=admins" class="admin-nav-tile <?= $nav === 'admins' ? 'is-active' : '' ?>"><i class="fas fa-user-shield"></i><span>Администраторы</span></a>
                    </div>
                </div>
            </nav>
        </details>
    </div>
</header>
<script>
(function () {
    var panel = document.getElementById('adminNavPanel');
    if (!panel || !window.localStorage) return;
    var k = 'adminNavOpen';
    var v = localStorage.getItem(k);
    if (v === '0') panel.removeAttribute('open');
    if (v === '1') panel.setAttribute('open', '');
    panel.addEventListener('toggle', function () {
        localStorage.setItem(k, panel.open ? '1' : '0');
    });
})();
</script>
<main class="admin-main">
    <?php
}

function admin_layout_end(): void
{
    ?>
</main>
</body>
</html>
    <?php
}
