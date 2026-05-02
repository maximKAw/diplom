<?php
$pageTitle = 'Карта сайта';
require_once 'header.php';
?>

<div class="app-shell app-shell--lift">
    <div class="sitemap-page">
        <p class="sitemap-intro">Основные разделы портала «Проф симулятор». Перейдите по ссылке, чтобы открыть страницу.</p>

        <section class="sitemap-section" aria-labelledby="sitemap-main">
            <h2 id="sitemap-main"><i class="fas fa-home" aria-hidden="true"></i> Главная и материалы</h2>
            <ul class="sitemap-list">
                <li><a href="index.php"><i class="fas fa-door-open" aria-hidden="true"></i> Главная страница</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle" aria-hidden="true"></i> О проекте</a></li>
                <li><a href="articles.php"><i class="fas fa-newspaper" aria-hidden="true"></i> Статьи</a></li>
            </ul>
        </section>

        <section class="sitemap-section" aria-labelledby="sitemap-tests">
            <h2 id="sitemap-tests"><i class="fas fa-clipboard-check" aria-hidden="true"></i> Тесты и обучение</h2>
            <ul class="sitemap-list">
                <li><a href="tests.php"><i class="fas fa-list" aria-hidden="true"></i> Каталог тестов</a></li>
                <li><a href="professions.php"><i class="fas fa-briefcase" aria-hidden="true"></i> Профессии в IT</a></li>
                <li><a href="languages.php"><i class="fas fa-code" aria-hidden="true"></i> Языки программирования</a></li>
            </ul>
        </section>

        <section class="sitemap-section" aria-labelledby="sitemap-sim">
            <h2 id="sitemap-sim"><i class="fas fa-gamepad" aria-hidden="true"></i> Тренажёры и симуляторы</h2>
            <ul class="sitemap-list">
                <li><a href="simulator_pro_max.php"><i class="fas fa-graduation-cap" aria-hidden="true"></i> PRO MAX Симулятор</a></li>
                <li><a href="trenasher.php"><i class="fas fa-laptop-code" aria-hidden="true"></i> Тренажёр (симулятор заданий)</a></li>
            </ul>
        </section>

        <section class="sitemap-section" aria-labelledby="sitemap-account">
            <h2 id="sitemap-account"><i class="fas fa-user" aria-hidden="true"></i> Личный кабинет</h2>
            <ul class="sitemap-list">
                <li><a href="login.php"><i class="fas fa-sign-in-alt" aria-hidden="true"></i> Вход</a></li>
                <li><a href="register.php"><i class="fas fa-user-plus" aria-hidden="true"></i> Регистрация</a></li>
                <li><a href="profile.php"><i class="fas fa-id-card" aria-hidden="true"></i> Профиль</a></li>
            </ul>
        </section>

        <section class="sitemap-section" aria-labelledby="sitemap-legal">
            <h2 id="sitemap-legal"><i class="fas fa-balance-scale" aria-hidden="true"></i> Информация</h2>
            <ul class="sitemap-list">
                <li><a href="user-agreement.php"><i class="fas fa-file-contract" aria-hidden="true"></i> Пользовательское соглашение</a></li>
                <li><a href="privacy.php"><i class="fas fa-shield-alt" aria-hidden="true"></i> Политика конфиденциальности</a></li>
                <li><a href="sitemap.php"><i class="fas fa-sitemap" aria-hidden="true"></i> Карта сайта</a></li>
            </ul>
        </section>
    </div>
</div>

<?php require_once 'footer.php'; ?>
