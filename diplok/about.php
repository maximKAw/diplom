<?php
require_once 'header.php';

// Получаем статистику
$stats = [];

$res = $db->query("SELECT COUNT(*) as cnt FROM tests");
$stats['tests'] = $res->fetch_assoc()['cnt'];

$res = $db->query("SELECT COUNT(*) as cnt FROM professions");
$stats['professions'] = $res->fetch_assoc()['cnt'];

$res = $db->query("SELECT COUNT(*) as cnt FROM articles");
$stats['articles'] = $res->fetch_assoc()['cnt'];
?>

<!-- Встроенные стили для страницы "О проекте" -->
<style>
.about-content {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem 0;
}
.about-section {
    margin-bottom: 3rem;
    padding: 2rem;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.about-section h2 {
    color: #1e3c72;
    margin-top: 0;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.75rem;
}
.about-section h2 i {
    color: #2a5298;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    text-align: center;
}
.stat-item {
    background: #f8f9fc;
    border-radius: 40px;
    padding: 1.5rem 0.5rem;
}
.stat-number {
    display: block;
    font-size: 3rem;
    font-weight: 700;
    color: #1e3c72;
    line-height: 1.2;
}
.stat-label {
    font-size: 1rem;
    color: #6c757d;
}
.pros-list, .cons-list, .future-list {
    list-style: none;
    padding: 0;
}
.pros-list li, .cons-list li, .future-list li {
    margin-bottom: 1rem;
    padding-left: 1.8rem;
    position: relative;
    line-height: 1.6;
}
.pros-list li::before {
    content: "✓";
    color: #28a745;
    font-weight: bold;
    position: absolute;
    left: 0;
}
.cons-list li::before {
    content: "•";
    color: #dc3545;
    font-weight: bold;
    position: absolute;
    left: 0;
}
.future-list li i {
    color: #17a2b8;
    width: 1.5rem;
    margin-right: 0.5rem;
}
</style>

<h1 class="page-title">О проекте «Проф симулятор»</h1>

<div class="about-content">
    <!-- Блок: О компании -->
    <section class="about-section">
        <h2><i class="fas fa-info-circle"></i> Кто мы</h2>
        <p>«Проф симулятор» — это современный профориентационный портал, созданный в 2025 году командой энтузиастов и экспертов в области образования и IT. Мы помогаем школьникам, студентам и взрослым людям осознанно выбрать профессию в сфере информационных технологий, понять свои сильные стороны и получить практические знания через интерактивные тесты и полезные материалы.</p>
        <p>Наша миссия — сделать процесс выбора профессии простым, увлекательным и эффективным, используя современные технологии и актуальные данные о рынке труда.</p>
    </section>

    <!-- Блок: Наши достижения (статистика) -->
    <section class="about-section stats-section">
        <h2><i class="fas fa-chart-line"></i> Мы в цифрах</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number"><?= $stats['tests'] ?></span>
                <span class="stat-label">профориентационных тестов</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $stats['professions'] ?></span>
                <span class="stat-label">описанных профессий</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $stats['articles'] ?></span>
                <span class="stat-label">полезных статей</span>
            </div>
        </div>
    </section>

    <!-- Блок: Плюсы -->
    <section class="about-section">
        <h2><i class="fas fa-thumbs-up"></i> Наши преимущества</h2>
        <ul class="pros-list">
            <li><strong>Актуальность:</strong> все профессии и тесты составлены на основе реальных требований работодателей и анализа современного рынка IT.</li>
            <li><strong>Бесплатность:</strong> основная часть материалов доступна без оплаты, чтобы каждый мог попробовать себя в мире IT.</li>
            <li><strong>Интерактивность:</strong> тесты не только оценивают знания, но и моделируют реальные рабочие ситуации.</li>
            <li><strong>Экспертность:</strong> материалы создаются при участии практикующих специалистов из ведущих IT-компаний.</li>
            <li><strong>Обратная связь:</strong> мы учитываем отзывы пользователей и регулярно обновляем контент под актуальные запросы рынка.</li>
        </ul>
    </section>

    <!-- Блок: Минусы (честно о сложностях) -->
    <section class="about-section">
        <h2><i class="fas fa-thumbs-down"></i> Над чем мы работаем</h2>
        <ul class="cons-list">
            <li><strong>Ограниченный охват:</strong> пока мы фокусируемся только на IT-профессиях, но в будущем планируем расширяться на другие сферы.</li>
            <li><strong>Недостаток персонализации:</strong> текущие рекомендации основаны на баллах, но мы хотим добавить адаптивные алгоритмы под каждого пользователя.</li>
            <li><strong>Отсутствие мобильного приложения:</strong> сайт адаптирован под мобильные, но нативное приложение позволит улучшить опыт.</li>
            <li><strong>Молодость проекта:</strong> нам чуть больше года, поэтому база знаний ещё пополняется, и мы открыты к предложениям.</li>
        </ul>
    </section>

    <!-- Блок: Планы на будущее -->
    <section class="about-section">
        <h2><i class="fas fa-rocket"></i> Что дальше?</h2>
        <p>Мы не собираемся останавливаться на достигнутом. Вот лишь некоторые направления развития, которые мы планируем реализовать в ближайшие 2–3 года:</p>
        <ul class="future-list">
            <li><i class="fas fa-robot"></i> Внедрение искусственного интеллекта для построения индивидуальных образовательных траекторий.</li>
            <li><i class="fas fa-graduation-cap"></i> Создание онлайн-курсов на базе самых востребованных профессий.</li>
            <li><i class="fas fa-briefcase"></i> База стажировок и вакансий для выпускников и начинающих специалистов.</li>
            <li><i class="fas fa-globe"></i> Расширение на другие профессиональные области (дизайн, маркетинг, управление).</li>
            <li><i class="fas fa-mobile-alt"></i> Запуск мобильного приложения с офлайн-доступом к тестам.</li>
            <li><i class="fas fa-landmark"></i> Интеграция с государственными центрами занятости и образовательными программами.</li>
        </ul>
    </section>

</div>

<?php require_once 'footer.php'; ?>