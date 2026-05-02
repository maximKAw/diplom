<?php
declare(strict_types=1);
require_once __DIR__ . '/_init.php';
admin_require_login();
require_once __DIR__ . '/layout.php';

$stats = [];
$tables = ['users', 'articles', 'tests', 'professions', 'languages'];
foreach ($tables as $t) {
    $r = $db->query("SELECT COUNT(*) AS c FROM `$t`");
    $stats[$t] = $r ? (int) $r->fetch_assoc()['c'] : 0;
}

admin_layout_start('Главная');
?>
<h1>Панель управления</h1>
<p class="muted">Обзор данных и быстрый переход в разделы. Полный список — в навигации сверху.</p>

<div class="admin-dash-grid">
    <a class="admin-dash-card" href="crud.php?table=users">
        <div class="admin-dash-card__head admin-dash-card__head--blue">
            <i class="fas fa-users"></i>
            <span>Пользователи</span>
        </div>
        <div class="admin-dash-card__body">
            <div class="admin-dash-card__stat"><?= (int) $stats['users'] ?></div>
            Учётные записи, профили и доступ к тестам. Управляйте списком зарегистрированных пользователей.
        </div>
    </a>
    <a class="admin-dash-card" href="crud.php?table=tests">
        <div class="admin-dash-card__head admin-dash-card__head--orange">
            <i class="fas fa-clipboard-list"></i>
            <span>Тесты и сценарии</span>
        </div>
        <div class="admin-dash-card__body">
            <div class="admin-dash-card__stat"><?= (int) $stats['tests'] ?></div>
            Настройка тестов, вопросов и результатов — ядро профориентационного симулятора.
        </div>
    </a>
    <a class="admin-dash-card" href="crud.php?table=articles">
        <div class="admin-dash-card__head admin-dash-card__head--green">
            <i class="fas fa-newspaper"></i>
            <span>Статьи и материалы</span>
        </div>
        <div class="admin-dash-card__body">
            <div class="admin-dash-card__stat"><?= (int) $stats['articles'] ?></div>
            Публикации для обучения: редактируйте тексты, категории и отображение на сайте.
        </div>
    </a>
    <a class="admin-dash-card" href="crud.php?table=languages">
        <div class="admin-dash-card__head admin-dash-card__head--red">
            <i class="fas fa-graduation-cap"></i>
            <span>Справочники IT</span>
        </div>
        <div class="admin-dash-card__body">
            <div class="admin-dash-card__stat"><?= (int) $stats['languages'] ?></div>
            Справочник языков и <?= (int) $stats['professions'] ?> карточек профессий — контент для профориентации и обучения.
        </div>
    </a>
</div>

<p class="muted" style="margin-bottom:12px">Сводка по сущностям</p>
<table class="admin-table" style="max-width:640px">
    <tr><th>Сущность</th><th>Записей</th></tr>
    <tr><td>Пользователи</td><td><?= $stats['users'] ?></td></tr>
    <tr><td>Статьи</td><td><?= $stats['articles'] ?></td></tr>
    <tr><td>Тесты</td><td><?= $stats['tests'] ?></td></tr>
    <tr><td>Профессии</td><td><?= $stats['professions'] ?></td></tr>
    <tr><td>Языки программирования</td><td><?= $stats['languages'] ?></td></tr>
</table>
<?php
admin_layout_end();
