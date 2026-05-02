<!-- ПОДВАЛ (современная версия) -->
<footer class="site-footer">
    <div class="wrapper">
        <div class="footer-grid">
            <!-- Колонка 1: Логотип и соцсети -->
            <div class="footer-col">
                <a class="footer-logo" href="/">Проф симулятор</a>
                <p class="footer-desc">Российский профориентационный портал — ваш надёжный путеводитель в мире IT-профессий.</p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="VK"><i class="fab fa-vk"></i></a>
                    <a href="#" class="social-link" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                    <a href="#" class="social-link" aria-label="Odnoklassniki"><i class="fab fa-odnoklassniki"></i></a>
                </div>
            </div>

            <!-- Колонка 2: Меню -->
            <div class="footer-col">
                <h4 class="footer-title">Навигация</h4>
                <ul class="footer-menu">
                    <li><a href="tests.php">Бесплатные тесты</a></li>
                    <li><a href="professions.php">Каталог профессий</a></li>
                    <li><a href="articles.php">Статьи</a></li>
                    <li><a href="about.php">О нас</a></li>
                </ul>
            </div>

            <!-- Колонка 3: служебный доступ -->
            <div class="footer-col">
                <h4 class="footer-title">Команда проекта</h4>
                <ul class="footer-menu">
                    <li><a href="admin/login.php">Панель управления</a></li>
                </ul>
            </div>

            <!-- Колонка 4: Юридическая информация -->
            <div class="footer-col">
                <h4 class="footer-title">Информация</h4>
                <ul class="footer-menu">
                    <li><a href="user-agreement.php">Пользовательское соглашение</a></li>
                    <li><a href="privacy.php">Политика конфиденциальности</a></li>
                    <li><a href="sitemap.php">Карта сайта</a></li>
                </ul>
            </div>
        </div>

        <!-- Нижняя строка с копирайтом и кнопкой "Наверх" -->
        <div class="footer-bottom">
            <p class="copyright">© 2025–2031 Проф симулятор. Все права защищены.</p>
            <button class="scroll-top" id="scrollTop" aria-label="Наверх"><i class="fas fa-arrow-up"></i></button>
        </div>
    </div>
</footer>

<!-- Добавляем стили для подвала (можно дописать в общий CSS) -->
<style>
    /* ===== ПОДВАЛ ===== */
    .site-footer {
        background: #1e293b; /* тёмно-синий, близкий к слатам */
        color: #cbd5e1;
        padding: 48px 0 52px;
        margin-bottom: 0;
        font-size: 15px;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-col {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .footer-logo {
        font-size: 24px;
        font-weight: 700;
        color: #fff;
        letter-spacing: -0.02em;
    }

    .footer-desc {
        color: #94a3b8;
        line-height: 1.6;
        max-width: 300px;
    }

    .footer-title {
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .footer-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .footer-menu a {
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-menu a:hover {
        color: #fff;
    }

    .social-links {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
        color: #fff;
        transition: background 0.2s ease;
    }

    .social-link:hover {
        background: var(--primary);
    }

    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 28px;
        padding-bottom: 8px;
        border-top: 1px solid #334155;
    }

    .copyright {
        color: #64748b;
        font-size: 14px;
    }

    .scroll-top {
        width: 44px;
        height: 44px;
        background: #334155;
        border: none;
        border-radius: 50%;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
    }

    .scroll-top:hover {
        background: var(--primary);
    }

    /* Адаптивность */
    @media (max-width: 1024px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }
    }

    @media (max-width: 640px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .footer-bottom {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }
    }

    /* Аккордеон для мобильных (скрыто по умолчанию, можно добавить через JS) */
    @media (max-width: 640px) {
        .footer-title {
            cursor: pointer;
            position: relative;
            padding-right: 24px;
        }
        .footer-title::after {
            content: '+';
            position: absolute;
            right: 0;
            top: 0;
            font-size: 20px;
        }
        .footer-title.active::after {
            content: '−';
        }
        .footer-menu {
            display: none;
        }
        .footer-menu.show {
            display: flex;
        }
    }
</style>

<!-- Небольшой JS для аккордеона в подвале и кнопки "Наверх" -->
<script>
    (function() {
        // Кнопка "Наверх"
        const scrollBtn = document.getElementById('scrollTop');
        if (scrollBtn) {
            scrollBtn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Аккордеон для мобильных (если экран меньше 640px)
        function initFooterAccordion() {
            const titles = document.querySelectorAll('.footer-title');
            if (window.innerWidth <= 640) {
                titles.forEach(title => {
                    // Убираем старые обработчики, чтобы не накапливались
                    title.removeEventListener('click', toggleMenu);
                    title.addEventListener('click', toggleMenu);
                });
            } else {
                titles.forEach(title => {
                    title.removeEventListener('click', toggleMenu);
                    // Показываем все меню на десктопе
                    const menu = title.nextElementSibling;
                    if (menu && menu.classList.contains('footer-menu')) {
                        menu.style.display = 'flex';
                    }
                });
            }
        }

        function toggleMenu(e) {
            const title = e.currentTarget;
            const menu = title.nextElementSibling;
            if (menu && menu.classList.contains('footer-menu')) {
                menu.classList.toggle('show');
                title.classList.toggle('active');
            }
        }

        // Инициализация и обработка ресайза
        window.addEventListener('load', initFooterAccordion);
        window.addEventListener('resize', initFooterAccordion);
    })();
</script>