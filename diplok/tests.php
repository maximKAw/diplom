<?php
// tests.php – список всех тестов
require_once 'header.php';

// Функция нормализации пути к изображению
function normalizeImagePath($path) {
    if (empty($path)) return '';
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#^diplok/#', '', $path);
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return $path;
}

// Получаем все тесты из базы, сортируем по убыванию ID (новые сверху)
$result = $db->query("SELECT * FROM tests ORDER BY id DESC");
?>

<style>
    :root {
        --primary: #410cdf;
        --primary-light: #5b2ef0;
        --surface: #ffffff;
        --text: #0f172a;
        --text-light: #475569;
        --border: rgba(65, 12, 223, 0.14);
        --shadow-sm: 0 4px 16px rgba(65, 12, 223, 0.08);
        --shadow: 0 16px 40px rgba(65, 12, 223, 0.14);
        --radius: 24px;
        --radius-sm: 16px;
    }

    body {
        background: #f8fafc;
    }

    .tests-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 100px 20px 40px;
        min-height: calc(100vh - 80px);
    }

    .page-section__title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 2rem;
        position: relative;
        display: inline-block;
    }

    .page-section__title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60px;
        height: 4px;
        background: var(--primary);
        border-radius: 4px;
    }

    .tests-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin: 2rem 0;
    }
    
    @media (max-width: 1200px) {
        .tests-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 900px) {
        .tests-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 600px) {
        .tests-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .test-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: var(--surface);
        border-radius: var(--radius-sm);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
        border: 1px solid var(--border);
    }
    
    .test-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow);
        border-color: var(--primary);
    }
    
    .test-card__img-wrapper {
        height: 160px;
        overflow: hidden;
        background: #f1f5f9;
        position: relative;
    }
    
    .test-card__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .test-card:hover .test-card__img {
        transform: scale(1.03);
    }
    
    .test-card__tags {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        gap: 5px;
    }
    
    .tag {
        background: var(--primary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .test-card__body {
        padding: 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .test-card__info {
        display: flex;
        gap: 15px;
        margin-bottom: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-light);
    }

    .test-card__info i {
        color: var(--primary);
        margin-right: 0.2rem;
    }
    
    .test-card__title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text);
        line-height: 1.4;
        transition: color 0.2s;
    }

    .test-card:hover .test-card__title {
        color: var(--primary);
    }
    
    .test-card__desc {
        font-size: 0.9rem;
        color: var(--text-light);
        margin-bottom: 1rem;
        line-height: 1.5;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .test-card__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--border);
        padding-top: 0.8rem;
        margin-top: auto;
    }

    .test-card__footer--end {
        justify-content: flex-end;
    }

    .test-card__more {
        color: var(--primary);
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .text-center {
        text-align: center;
        color: var(--text-light);
        grid-column: 1 / -1;
        padding: 3rem;
        background: var(--surface);
        border-radius: var(--radius-sm);
        border: 1px dashed var(--border);
    }
</style>

<div class="tests-page">
    <h1 class="page-section__title">Все тесты</h1>

    <div class="tests-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($test = $result->fetch_assoc()): 
                $image_url = normalizeImagePath($test['image_path'] ?? '');
            ?>
                <a href="test.php?id=<?= $test['id'] ?>" class="test-card">
                    <span class="test-card__img-wrapper">
                        <?php if (!empty($image_url)): ?>
                            <img class="test-card__img" src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($test['title']) ?>" onerror="this.onerror=null; this.src='/photos/test-placeholder.jpg';">
                        <?php else: ?>
                            <img class="test-card__img" src="/photos/test-placeholder.jpg" alt="Тест">
                        <?php endif; ?>
                        <?php if ($test['price'] > 0): ?>
                            <span class="test-card__tags">
                                <span class="tag">Платный</span>
                            </span>
                        <?php endif; ?>
                    </span>
                    <span class="test-card__body">
                        <span class="test-card__info">
                            <span><i class="fas fa-question-circle"></i> <?= (int)$test['questions_count'] ?></span>
                            <span><i class="fas fa-clock"></i> <?= (int)$test['duration_min'] ?> мин</span>
                            <?php if ($test['rating'] > 0): ?>
                                <span><i class="fas fa-star" style="color: #f59e0b;"></i> <?= number_format($test['rating'], 1) ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="test-card__title"><?= htmlspecialchars($test['title']) ?></span>
                        <span class="test-card__desc"><?= htmlspecialchars(mb_substr($test['description'] ?? '', 0, 100)) ?>...</span>
                        <span class="test-card__footer<?= ($test['price'] <= 0) ? ' test-card__footer--end' : '' ?>">
                            <?php if ($test['price'] > 0): ?>
                                <span class="state-label--warning"><?= number_format($test['price'], 0, '', ' ') ?> ₽</span>
                            <?php endif; ?>
                            <span class="test-card__more">Подробнее →</span>
                        </span>
                    </span>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Пока нет доступных тестов.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>