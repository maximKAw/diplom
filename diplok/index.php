<?php
// index.php – главная страница (с каруселью тестов и поднятым блоком статей)
require_once 'config.php';
require_once 'functions.php';
require_once 'header.php';
?>

<style>
.carousel {
    position: relative;
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 8px 0 12px;
}

.carousel__container {
    flex: 1;
    overflow: hidden;
    min-width: 0;
    padding: 4px 2px 12px;
}

.carousel__track {
    display: flex;
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    gap: 20px;
}

.carousel__slide {
    flex: 0 0 calc(33.333% - 13.333px);
    min-width: 250px;
    box-sizing: border-box;
}

.carousel__btn {
    flex-shrink: 0;
    background: #fff;
    border: 1px solid rgba(65, 12, 223, 0.18);
    border-radius: 50%;
    width: 46px;
    height: 46px;
    font-size: 14px;
    color: #410cdf;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, background 0.2s, border-color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(65, 12, 223, 0.12);
}

.carousel__btn:hover:not(:disabled):not(.disabled) {
    background: linear-gradient(135deg, #410cdf 0%, #5b2ef0 100%);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 8px 24px rgba(65, 12, 223, 0.35);
    transform: scale(1.05);
}

.carousel__btn:disabled,
.carousel__btn.disabled {
    opacity: 0.35;
    pointer-events: none;
    box-shadow: none;
}

@media (max-width: 992px) {
    .carousel__slide {
        flex: 0 0 calc(50% - 10px);
    }
}

@media (max-width: 576px) {
    .carousel__slide {
        flex: 0 0 100%;
    }
    .carousel__btn {
        width: 40px;
        height: 40px;
    }
    .carousel {
        gap: 8px;
    }
}

.tabs__pane {
    display: none;
}
.tabs__pane.active {
    display: block;
}
</style>

<!-- СЛАЙДЕР (динамический из БД) -->
<div class="bg">
    <div class="wrapper">
        <div class="intro">
            <div class="intro__slider" id="slider">
                <div class="intro__slides" id="slidesContainer">
                    <?php
                    // Получаем активные слайды, отсортированные по порядку
                    $slides_result = $db->query("SELECT * FROM slides WHERE active = 1 ORDER BY sort_order");
                    $slides = [];
                    if ($slides_result && $slides_result->num_rows > 0) {
                        while ($row = $slides_result->fetch_assoc()) {
                            $slides[] = $row;
                        }
                    }

                    if (!empty($slides)):
                        foreach ($slides as $slide):
                            $img = !empty($slide['image_path']) ? htmlspecialchars($slide['image_path']) : 'photos/slide-placeholder.jpg';
                            $test_link = !empty($slide['test_id']) ? 'test.php?id=' . (int)$slide['test_id'] : '#';
                    ?>
                            <div class="intro__slide">
                                <div class="intro__body">
                                    <div class="intro__title"><?= htmlspecialchars($slide['title']) ?></div>
                                    <div class="intro__text">
                                        <p><?= nl2br(htmlspecialchars($slide['description'] ?? '')) ?></p>
                                        <div class="intro__links btns">
                                            <a class="primary-btn" href="<?= $test_link ?>">Начать тест</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="intro__media">
                                    <img class="intro__img" src="/<?= $img ?>" alt="<?= htmlspecialchars($slide['title']) ?>" onerror="this.src='https://via.placeholder.com/602x430?text=Slide'">
                                </div>
                            </div>
                    <?php
                        endforeach;
                    else:
                        // Заглушка, если слайдов нет
                    ?>
                        <div class="intro__slide">
                            <div class="intro__body">
                                <div class="intro__title">Добро пожаловать в Проф симулятор!</div>
                                <div class="intro__text">
                                    <p>Здесь будут отображаться актуальные тесты и материалы. Добавьте слайды в админке.</p>
                                    <div class="intro__links btns">
                                        <a class="primary-btn" href="tests.php">Перейти к тестам</a>
                                    </div>
                                </div>
                            </div>
                            <div class="intro__media">
                                <img class="intro__img" src="/photos/placeholder.jpg" alt="Placeholder">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Пагинация -->
            <div class="intro__pagination">
                <div class="intro__pagination-list" id="pagination">
                    <?php if (!empty($slides)): ?>
                        <?php foreach ($slides as $index => $slide): 
                            $active_class = ($index === 0) ? 'active' : '';
                            $short_title = mb_substr($slide['title'], 0, 20) . (mb_strlen($slide['title']) > 20 ? '…' : '');
                        ?>
                            <div class="intro__pagination-btn <?= $active_class ?>" data-index="<?= $index ?>">
                                <span><?= htmlspecialchars($short_title) ?></span>
                                <span class="intro__pagination-num"><?= $index + 1 ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="intro__pagination-btn active" data-index="0">
                            <span>Добро пожаловать</span>
                            <span class="intro__pagination-num">1</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ЛУЧШИЕ ТЕСТЫ (карусель + вкладки) -->
<div class="wrapper">
    <section class="page-section">
        <h2 class="page-section__title">Лучшие тесты</h2>
        <div class="tabs">
            <div class="tabs__controls">
                <button class="tabs__btn _current" data-tab="free">Бесплатные</button>
                <button class="tabs__btn" data-tab="paid">Платные</button>
                <a class="tabs__more-link" href="tests.php">посмотреть все тесты</a>
            </div>

            <!-- Бесплатные тесты (вкладка) -->
            <div class="tabs__pane active" id="tab-free">
                <?php
                $sql_free = "SELECT id, title, description, questions_count, duration_min, age_limit, price, image_path 
                             FROM tests WHERE price = 0 ORDER BY rating DESC LIMIT 10";
                $res_free = $db->query($sql_free);
                $free_tests = [];
                if ($res_free && $res_free->num_rows > 0) {
                    while ($row = $res_free->fetch_assoc()) $free_tests[] = $row;
                }
                ?>
                <div class="carousel" data-carousel>
                    <button type="button" class="carousel__btn carousel__btn--prev" data-carousel-prev aria-label="Назад"><i class="fas fa-chevron-left"></i></button>
                    <div class="carousel__container" data-carousel-container>
                        <div class="carousel__track" data-carousel-track>
                            <?php foreach ($free_tests as $test): 
                                $img = $test['image_path'] ?: 'photos/default_test.jpg'; ?>
                                <a class="test-card carousel__slide" href="test.php?id=<?= $test['id'] ?>">
                                    <span class="test-card__img-wrapper">
                                        <img class="test-card__img" src="/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($test['title']) ?>" onerror="this.src='/photos/default_test.jpg'">
                                        <span class="test-card__tags"><span class="tag">Профориентационный</span></span>
                                    </span>
                                    <span class="test-card__body">
                                        <span class="test-card__info">
                                            <span><?= (int)$test['questions_count'] ?> вопросов</span>
                                            <span><?= (int)$test['duration_min'] ?> мин</span>
                                            <span><?= (int)$test['age_limit'] ?>+</span>
                                        </span>
                                        <span class="test-card__title"><?= htmlspecialchars($test['title']) ?></span>
                                        <span class="test-card__desc"><?= htmlspecialchars(mb_substr($test['description'] ?? '', 0, 100)) ?>...</span>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="button" class="carousel__btn carousel__btn--next" data-carousel-next aria-label="Вперёд"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <!-- Платные тесты (вкладка) -->
            <div class="tabs__pane" id="tab-paid">
                <?php
                $sql_paid = "SELECT id, title, description, questions_count, duration_min, age_limit, price, image_path 
                             FROM tests WHERE price > 0 ORDER BY rating DESC LIMIT 10";
                $res_paid = $db->query($sql_paid);
                $paid_tests = [];
                if ($res_paid && $res_paid->num_rows > 0) {
                    while ($row = $res_paid->fetch_assoc()) $paid_tests[] = $row;
                }
                ?>
                <div class="carousel" data-carousel>
                    <button type="button" class="carousel__btn carousel__btn--prev" data-carousel-prev aria-label="Назад"><i class="fas fa-chevron-left"></i></button>
                    <div class="carousel__container" data-carousel-container>
                        <div class="carousel__track" data-carousel-track>
                            <?php foreach ($paid_tests as $test): 
                                $img = $test['image_path'] ?: 'photos/default_test.jpg'; ?>
                                <a class="test-card carousel__slide" href="test.php?id=<?= $test['id'] ?>">
                                    <span class="test-card__img-wrapper">
                                        <img class="test-card__img" src="/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($test['title']) ?>" onerror="this.src='/photos/default_test.jpg'">
                                        <span class="test-card__tags"><span class="tag">Профориентационный</span></span>
                                    </span>
                                    <span class="test-card__body">
                                        <span class="test-card__info">
                                            <span><?= (int)$test['questions_count'] ?> вопросов</span>
                                            <span><?= (int)$test['duration_min'] ?> мин</span>
                                            <span><?= (int)$test['age_limit'] ?>+</span>
                                        </span>
                                        <span class="test-card__title"><?= htmlspecialchars($test['title']) ?></span>
                                        <span class="test-card__desc"><?= htmlspecialchars(mb_substr($test['description'] ?? '', 0, 100)) ?>...</span>
                                        <span class="test-card__footer">
                                            <span class="state-label--warning">Платно (<?= (int)$test['price'] ?> ₽)</span>
                                        </span>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="button" class="carousel__btn carousel__btn--next" data-carousel-next aria-label="Вперёд"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ПОЛЕЗНЫЕ СТАТЬИ -->
<div class="wrapper">
    <section class="page-section page-section--articles-tight">
        <div class="page-section__head">
            <h2 class="page-section__title page-section__title--inline">Полезные статьи</h2>
            <a class="accent-link" href="articles.php">Все статьи</a>
        </div>
        <div class="articles-grid">
            <?php
            $sql = "SELECT id, title, description, image_path, category_name, created_at FROM articles ORDER BY created_at DESC LIMIT 3";
            $res = $db->query($sql);
            if ($res && $res->num_rows > 0) {
                while ($article = $res->fetch_assoc()) {
                    $img = $article['image_path'] ?: 'photos/default_article.jpg';
                    ?>
                    <a class="article-card" href="article.php?id=<?= $article['id'] ?>">
                        <span class="article-card__img-wrapper">
                            <img class="article-card__img" src="/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($article['title']) ?>" onerror="this.src='/photos/default_article.jpg'">
                        </span>
                        <span class="article-card__body">
                            <span class="article-card__title"><?= htmlspecialchars($article['title']) ?></span>
                            <span class="article-card__desc"><?= htmlspecialchars(mb_substr($article['description'] ?? '', 0, 100)) ?>...</span>
                            <span class="article-card__footer">
                                <span class="tag tag--bg"><?= htmlspecialchars($article['category_name'] ?? 'Без категории') ?></span>
                                <time><?= date('d.m.Y', strtotime($article['created_at'])) ?></time>
                            </span>
                        </span>
                    </a>
                    <?php
                }
            } else {
                echo '<p>Статьи пока не добавлены</p>';
            }
            ?>
        </div>
    </section>
</div>

<!-- СКРИПТЫ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------- Слайдер ----------
    const slidesContainer = document.getElementById('slidesContainer');
    const slides = document.querySelectorAll('.intro__slide');
    const paginationBtns = document.querySelectorAll('.intro__pagination-btn');
    let currentIndex = 0;
    const totalSlides = slides.length;
    let intervalId;

    if (totalSlides > 0) {
        function showSlide(index) {
            if (index < 0) index = 0;
            if (index >= totalSlides) index = totalSlides - 1;
            slidesContainer.style.transform = `translateX(-${index * 100}%)`;
            paginationBtns.forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            currentIndex = index;
        }

        function nextSlide() {
            let next = (currentIndex + 1) % totalSlides;
            showSlide(next);
        }

        paginationBtns.forEach((btn, index) => {
            btn.addEventListener('click', () => {
                showSlide(index);
                clearInterval(intervalId);
                intervalId = setInterval(nextSlide, 2000);
            });
        });

        intervalId = setInterval(nextSlide, 2000);

        slidesContainer.addEventListener('mouseenter', () => {
            clearInterval(intervalId);
        });
        slidesContainer.addEventListener('mouseleave', () => {
            intervalId = setInterval(nextSlide, 2000);
        });
    }

    // ---------- Вкладки и карусель ----------
    const tabBtns = document.querySelectorAll('.tabs__btn[data-tab]');
    const panes = {
        free: document.getElementById('tab-free'),
        paid: document.getElementById('tab-paid')
    };

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('_current'));
            btn.classList.add('_current');

            const tab = btn.dataset.tab;
            Object.values(panes).forEach(pane => pane.classList.remove('active'));
            if (panes[tab]) panes[tab].classList.add('active');

            initCarousels();
        });
    });

    function initCarousels() {
        document.querySelectorAll('[data-carousel]').forEach(carousel => {
            const container = carousel.querySelector('[data-carousel-container]');
            const track = carousel.querySelector('[data-carousel-track]');
            const prevBtn = carousel.querySelector('[data-carousel-prev]');
            const nextBtn = carousel.querySelector('[data-carousel-next]');
            if (!container || !track || !prevBtn || !nextBtn) return;

            let slideWidth = 0;
            let maxOffset = 0;
            let currentOffset = 0;

            function updateCarousel() {
                const firstSlide = track.querySelector('.carousel__slide');
                if (!firstSlide) return;

                const trackWidth = track.scrollWidth;
                const slidesCount = track.children.length;
                if (slidesCount === 0) return;
                slideWidth = trackWidth / slidesCount;

                const containerWidth = container.offsetWidth;
                maxOffset = Math.max(0, trackWidth - containerWidth);
                currentOffset = Math.min(currentOffset, maxOffset);
                currentOffset = Math.max(currentOffset, 0);

                track.style.transform = `translateX(-${currentOffset}px)`;
                prevBtn.classList.toggle('disabled', currentOffset <= 0);
                nextBtn.classList.toggle('disabled', currentOffset >= maxOffset);
            }

            prevBtn.addEventListener('click', () => {
                currentOffset = Math.max(currentOffset - slideWidth, 0);
                updateCarousel();
            });

            nextBtn.addEventListener('click', () => {
                currentOffset = Math.min(currentOffset + slideWidth, maxOffset);
                updateCarousel();
            });

            window.addEventListener('resize', updateCarousel);
            updateCarousel();
        });
    }

    initCarousels();
});
</script>

<?php require_once 'footer.php'; ?>