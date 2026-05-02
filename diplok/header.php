<?php
// header.php – полная версия с авторизацией, аватаром и правым блоком
session_start();
require_once __DIR__ . '/config.php';

if (!function_exists('esc')) {
    function esc($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

// Получаем аватар пользователя, если он авторизован
$user_avatar = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user_avatar = $row['avatar'];
        }
        $stmt->close();
    } else {
        error_log("Ошибка подготовки запроса в header.php: " . $db->error);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($pageTitle) ? esc($pageTitle) . ' — ' : '' ?>Проф симулятор – выбери свой путь в IT</title>
    <!-- Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== ВАШИ ИСХОДНЫЕ СТИЛИ (ПОЛНОСТЬЮ) ===== */
        /* ===== СБРОС И БАЗОВЫЕ СТИЛИ ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        :root {
            --site-accent: #410cdf;
            --site-accent-hover: #3509b5;
            --site-accent-soft: rgba(65, 12, 223, 0.12);
            --site-accent-muted: rgba(65, 12, 223, 0.06);
            --card-radius: 22px;
            --card-radius-sm: 16px;
            --card-shadow: 0 10px 40px rgba(65, 12, 223, 0.09);
            --card-shadow-hover: 0 20px 48px rgba(65, 12, 223, 0.15);
            --card-border: 1px solid rgba(65, 12, 223, 0.14);
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.5;
            color: #333;
            background-color: #ffffff;
        }
        .wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        ul {
            list-style: none;
        }
        button {
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        /* ===== ШАПКА: одна синяя полоса — меню отступом от края, сразу после «Симулятор» — профиль (одна линия) ===== */
        .site-header {
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .site-header-main {
            background-color: #410cdf;
            padding-top: calc(0.5rem + 1cm);
            padding-bottom: calc(0.5rem + 1cm);
        }
        .site-header-main .wrapper.site-header-main__inner {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: nowrap;
            gap: 0 16px;
            width: 100%;
            max-width: none;
            margin-left: 0;
            margin-right: 0;
            box-sizing: border-box;
            /* на всю ширину экрана: логотип у левого края, профиль у правого */
            padding-left: max(16px, 2.5vw);
            padding-right: max(8px, 1vw);
        }
        .site-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 8px 12px;
            min-height: 44px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 14px;
            font-size: 1.45rem;
            line-height: 1;
            letter-spacing: 0;
            transition: background 0.2s, transform 0.15s;
            text-decoration: none;
            font-family: "Segoe UI Emoji", "Apple Color Emoji", "Noto Color Emoji", sans-serif;
        }
        .site-logo:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
        }
        .site-logo:focus-visible {
            outline: 2px solid #fff;
            outline-offset: 3px;
        }
        /* Меню по центру полосы между логотипом и профилем */
        .site-header-main .main-nav {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            justify-content: center;
        }
        .site-header-main .user-menu {
            margin-left: auto;
            margin-right: 0;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding-left: 12px;
            padding-right: 2px;
            border-left: 1px solid rgba(255, 255, 255, 0.35);
        }
        .main-nav {
            display: flex;
            align-items: center;
            flex: 1 1 auto;
            justify-content: center;
            min-width: 0;
            flex-wrap: nowrap;
            margin-left: 0;
        }
        .main-nav__list {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: nowrap;
        }
        .main-nav__item {
            position: relative;
        }
        .main-nav__link {
            font-weight: 500;
            color: #333;
            padding: 10px 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 40px;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
            line-height: 1.2;
        }
        .main-nav__link i {
            margin-right: 0;
            font-size: 0.9rem;
            color: #1a4d8c;
            flex-shrink: 0;
        }
        .main-nav__link:hover {
            background: rgba(26, 77, 140, 0.1);
            color: #1a4d8c;
        }
        .main-nav__item--parent > .main-nav__link {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: #1430e4;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            min-width: 220px;
            display: none;
            z-index: 100;
            padding: 5px 0;
        }
        .main-nav__item--parent:hover .dropdown-menu {
            display: block;
        }
        .dropdown-menu__item {
            display: block;
            padding: 8px 15px;
            color: #333;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .dropdown-menu__item:hover {
            background: rgba(255,255,255,0.3);
        }

        /* ===== БЛОК АВТОРИЗАЦИИ (верхняя полоса) ===== */
        .user-menu {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 14px;
            flex-wrap: wrap;
            min-height: 40px;
        }
        .user-greeting {
            font-weight: 500;
            color: #1a4d8c;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            line-height: 1.2;
            padding: 0 2px;
        }
        .btn-login, .btn-register, .btn-logout, .btn-profile {
            padding: 0 16px;
            min-height: 40px;
            border-radius: 40px;
            font-weight: 500;
            transition: 0.2s;
            font-size: 0.9rem;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            line-height: 1.2;
            box-sizing: border-box;
        }
        .btn-login {
            background: transparent;
            border: 2px solid #1a4d8c;
            color: #1a4d8c;
        }
        .btn-login:hover {
            background: #1a4d8c;
            color: white;
        }
        .btn-register {
            background: #1a4d8c;
            color: white;
        }
        .btn-register:hover {
            background: #0f3a6b;
        }
        .btn-logout {
            background: #dc3545;
            color: white;
            border: none;
        }
        .btn-logout:hover {
            background: #c82333;
        }
        .btn-profile {
            background: #17a2b8;
            color: white;
            border: none;
        }
        .btn-profile:hover {
            background: #1452d8;
        }

        .site-header__lng {
            margin-left: 20px;
        }
        .lng__current {
            padding: 5px 10px;
            background: #f0f0f0;
            border-radius: 4px;
            font-size: 14px;
        }

        /* ===== СЛАЙДЕР (карточка-слайд) ===== */
        .bg {
            background: linear-gradient(165deg, #f3f0ff 0%, #faf8ff 38%, #ffffff 100%);
            padding: 44px 0 52px;
        }
        .intro {
            position: relative;
        }
        .intro__slider {
            overflow: hidden;
            border-radius: var(--card-radius);
        }
        .intro__slides {
            display: flex;
            transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .intro__slide {
            flex: 0 0 100%;
            display: flex;
            align-items: stretch;
            gap: 36px;
            padding: 32px 36px;
            box-sizing: border-box;
            background: #fff;
            border-radius: var(--card-radius);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
        }
        .intro__body {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .intro__title {
            font-size: clamp(1.35rem, 2.5vw, 2rem);
            font-weight: 700;
            color: var(--site-accent);
            margin-bottom: 16px;
            line-height: 1.25;
            letter-spacing: -0.02em;
        }
        .intro__text p {
            margin-bottom: 12px;
            color: #4b5563;
            font-size: 1.02rem;
            line-height: 1.6;
        }
        .primary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 28px;
            background: linear-gradient(135deg, var(--site-accent) 0%, #5b2ef0 100%);
            color: #fff;
            border-radius: 999px;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 16px rgba(65, 12, 223, 0.35);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(65, 12, 223, 0.45);
            color: #fff;
        }
        .intro__media {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .intro__img {
            max-width: 100%;
            width: 100%;
            height: auto;
            max-height: 320px;
            object-fit: contain;
            border-radius: var(--card-radius-sm);
            box-shadow:
                0 4px 6px rgba(65, 12, 223, 0.06),
                0 20px 40px rgba(65, 12, 223, 0.12);
            border: 1px solid var(--site-accent-soft);
        }
        .intro__pagination {
            display: flex;
            justify-content: center;
            margin-top: 28px;
            padding: 0 8px;
        }
        .intro__pagination-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            max-width: 100%;
        }
        .intro__pagination-btn {
            cursor: pointer;
            padding: 10px 16px 10px 14px;
            background: #fff;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            transition: 0.22s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: var(--card-border);
            box-shadow: 0 2px 8px rgba(65, 12, 223, 0.06);
        }
        .intro__pagination-btn:hover {
            border-color: var(--site-accent-soft);
            color: var(--site-accent);
        }
        .intro__pagination-btn.active {
            background: linear-gradient(135deg, var(--site-accent) 0%, #5b2ef0 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(65, 12, 223, 0.35);
        }
        .intro__pagination-num {
            width: 26px;
            height: 26px;
            background: var(--site-accent-muted);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: var(--site-accent);
        }
        .intro__pagination-btn.active .intro__pagination-num {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        /* ===== БЛОК ТЕСТОВ ===== */
        .page-section {
            padding: 56px 0;
        }
        .page-section--articles-tight {
            padding-top: 36px;
        }
        .page-section__head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }
        .page-section__title--inline {
            margin-bottom: 0;
        }
        .page-section__title {
            font-size: clamp(1.35rem, 2.2vw, 1.75rem);
            margin-bottom: 24px;
            color: #1e1b4b;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .tabs__controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            width: 100%;
        }
        .tabs__btn {
            font-size: 15px;
            font-weight: 600;
            color: #64748b;
            padding: 10px 20px;
            border-radius: 999px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s, border-color 0.2s;
            background: #f1f5f9;
            border: 1px solid rgba(65, 12, 223, 0.1);
        }
        .tabs__btn:hover {
            color: var(--site-accent);
            border-color: var(--site-accent-soft);
        }
        .tabs__btn._current {
            color: #fff;
            background: linear-gradient(135deg, var(--site-accent) 0%, #5b2ef0 100%);
            box-shadow: 0 4px 14px rgba(65, 12, 223, 0.35);
            border-color: transparent;
        }
        .tabs__more-link {
            margin-left: auto;
            color: var(--site-accent);
            font-weight: 600;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 999px;
            transition: background 0.2s;
        }
        .tabs__more-link:hover {
            background: var(--site-accent-muted);
        }
        .accent-link {
            font-weight: 600;
            font-size: 15px;
            color: var(--site-accent);
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--site-accent-soft);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .accent-link:hover {
            background: var(--site-accent-muted);
            box-shadow: 0 4px 14px rgba(65, 12, 223, 0.15);
        }
        .tests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        .test-card {
            background: #fff;
            border-radius: var(--card-radius-sm);
            overflow: hidden;
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s;
            display: flex;
            flex-direction: column;
            height: 100%;
            color: inherit;
        }
        .test-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--card-shadow-hover);
            border-color: var(--site-accent-soft);
        }
        .test-card__img-wrapper {
            display: block;
            position: relative;
            height: 168px;
            background: linear-gradient(145deg, #ede9fe 0%, #f1f5f9 100%);
            overflow: hidden;
        }
        .test-card__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .test-card:hover .test-card__img {
            transform: scale(1.04);
        }
        .test-card__tags {
            position: absolute;
            top: 12px;
            left: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .tag {
            background: rgba(65, 12, 223, 0.92);
            color: #fff;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.02em;
            box-shadow: 0 2px 8px rgba(65, 12, 223, 0.25);
        }
        .test-card__body {
            padding: 18px 18px 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .test-card__info {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 14px;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
        }
        .test-card__title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
            color: #1e1b4b;
            line-height: 1.35;
            transition: color 0.2s;
        }
        .test-card:hover .test-card__title {
            color: var(--site-accent);
        }
        .test-card__desc {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
            flex: 1;
        }
        .test-card__footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid rgba(65, 12, 223, 0.1);
        }
        .state-label--success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #047857;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }
        .state-label--warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            color: #b45309;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(245, 158, 11, 0.45);
        }

        /* ===== ПРОФЕССИИ ===== */
        .professions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        .prof-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.2s;
            display: block;
            color: inherit;
        }
        .prof-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .prof-card__img-wrapper {
            height: 180px;
            background: #eee;
            overflow: hidden;
        }
        .prof-card__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .prof-card__body {
            padding: 20px;
        }
        .prof-card__title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .prof-card__desc {
            font-size: 14px;
            color: #555;
        }

        /* ===== СТАТЬИ ===== */
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }
        .article-card {
            background: #fff;
            border-radius: var(--card-radius-sm);
            overflow: hidden;
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            display: flex;
            flex-direction: column;
            height: 100%;
            color: inherit;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s;
        }
        .article-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--card-shadow-hover);
            border-color: var(--site-accent-soft);
        }
        .article-card__img-wrapper {
            display: block;
            height: 176px;
            background: linear-gradient(145deg, #ede9fe 0%, #f8fafc 100%);
            overflow: hidden;
            position: relative;
        }
        .article-card__img-wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 55%, rgba(65, 12, 223, 0.06) 100%);
            pointer-events: none;
        }
        .article-card__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .article-card:hover .article-card__img {
            transform: scale(1.04);
        }
        .article-card__body {
            padding: 18px 18px 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .article-card__title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1e1b4b;
            line-height: 1.35;
            transition: color 0.2s;
        }
        .article-card:hover .article-card__title {
            color: var(--site-accent);
        }
        .article-card__desc {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 14px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        .article-card__footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid rgba(65, 12, 223, 0.1);
        }
        .article-card__footer time {
            color: #94a3b8;
            font-weight: 600;
        }
        .tag--bg {
            background: var(--site-accent-muted);
            color: var(--site-accent);
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid var(--site-accent-soft);
        }

        /* ===== ПОДВАЛ ===== */
        .page-footer {
            background: #1a2b3c;
            color: #fff;
            padding: 60px 0 30px;
        }
        .page-footer__inner {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }
        .page-footer__logo {
            color: #fff;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            display: inline-block;
        }
        .social {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        .social__link {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .page-footer__desc {
            font-size: 14px;
            opacity: 0.8;
            margin-top: 20px;
        }
        .secondary-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .secondary-menu__link {
            color: #fff;
            opacity: 0.8;
        }
        .secondary-menu__link:hover {
            opacity: 1;
        }
        .page-footer__service-links {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
            opacity: 0.6;
        }
        .page-footer__service-links a:hover {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .intro__slide {
                flex-direction: column;
                padding: 22px 20px;
                gap: 22px;
            }
            .intro__img {
                max-height: 220px;
            }
            .site-header-main .wrapper.site-header-main__inner {
                flex-wrap: wrap;
                gap: 12px 16px;
                align-items: center;
            }
            .site-logo {
                order: 1;
            }
            .site-header-main .user-menu {
                order: 2;
                margin-left: auto;
                width: auto;
                max-width: min(100%, calc(100% - 120px));
                flex-wrap: wrap;
                justify-content: flex-end;
                border-left: none;
                padding-left: 0;
                border-top: none;
                padding-top: 0;
            }
            .site-header-main .main-nav {
                flex: 1 1 100%;
                width: 100%;
                order: 3;
                justify-content: flex-start;
            }
            .main-nav__list {
                flex-wrap: wrap;
                justify-content: flex-start;
            }
            .page-footer__inner {
                grid-template-columns: 1fr;
            }
        }

        /* ===== ДОПОЛНИТЕЛЬНЫЕ СТИЛИ ДЛЯ АВАТАРА И ПРАВОГО БЛОКА ===== */
        .user-avatar {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #1a4d8c;
            overflow: hidden;
            transition: 0.2s;
            flex-shrink: 0;
        }
        .user-avatar:hover {
            opacity: 0.8;
        }
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Синяя полоса: пункты меню — белые */
        .site-header-main .main-nav__link {
            color: #ffffff;
        }
        .site-header-main .main-nav__link i {
            color: rgba(255, 255, 255, 0.92);
        }
        .site-header-main .main-nav__link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        /* Синяя полоса: приветствие, аватар, Профиль, Выход / Вход, Регистрация */
        .site-header-main .user-greeting {
            color: #ffffff;
        }
        .site-header-main .btn-login {
            background: #ffffff;
            border: 2px solid #ffffff;
            color: #1a4d8c;
        }
        .site-header-main .btn-login:hover {
            background: #1a4d8c;
            color: #ffffff;
            border-color: #1a4d8c;
        }
        .site-header-main .btn-register {
            background: #ffffff;
            color: #1a4d8c;
            border: 2px solid #ffffff;
        }
        .site-header-main .btn-register:hover {
            background: #1a4d8c;
            color: #ffffff;
            border-color: #1a4d8c;
        }
        .site-header-main .btn-profile {
            background: #ffffff;
            color: #17a2b8;
            border: 2px solid #ffffff;
        }
        .site-header-main .btn-profile:hover {
            background: #17a2b8;
            color: #ffffff;
            border-color: #17a2b8;
        }
        .site-header-main .btn-logout {
            background: #ffffff;
            color: #dc3545;
            border: 2px solid #ffffff;
        }
        .site-header-main .btn-logout:hover {
            background: #dc3545;
            color: #ffffff;
            border-color: #dc3545;
        }
        .site-header-main .user-avatar {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.45);
        }

        /* Выпадающее меню */
        .dropdown-menu {
            background: #1430e4;
        }
        .dropdown-menu__item {
            color: #ffffff;
        }
        .dropdown-menu__item:hover {
            background: rgba(0, 0, 0, 0.2);
            color: #ffffff;
        }
    </style>
    <link rel="stylesheet" href="site-page.css">
</head>
<body>
<div class="page">
    <header class="site-header">
        <div class="site-header-main">
            <div class="wrapper site-header-main__inner">
                <a href="index.php" class="site-logo" title="Проф симулятор — главная" aria-label="Проф симулятор — на главную"><span class="site-logo__mark" aria-hidden="true">💻</span></a>
                <nav class="main-nav" aria-label="Основное меню">
                    <ul class="main-nav__list">
                        <li class="main-nav__item main-nav__item--parent">
                            <a class="main-nav__link" href="#"><i class="fas fa-question-circle"></i> Тесты</a>
                            <div class="dropdown-menu">
                                <?php
                                $sql = "SELECT id, title, slug FROM tests WHERE show_in_menu = 1 LIMIT 5";
                                $res = $db->query($sql);
                                if ($res && $res->num_rows > 0) {
                                    while ($row = $res->fetch_assoc()) {
                                        echo '<a class="dropdown-menu__item" href="test.php?id=' . $row['id'] . '">' . esc($row['title']) . '</a>';
                                    }
                                } else {
                                    echo '<a class="dropdown-menu__item" href="#">Тестов пока нет</a>';
                                }
                                ?>
                                <a class="dropdown-menu__item" href="tests.php">Все тесты →</a>
                            </div>
                        </li>
                        <li class="main-nav__item">
                            <a class="main-nav__link" href="professions.php"><i class="fas fa-briefcase"></i> Профессии</a>
                        </li>
                        <li class="main-nav__item">
                            <a class="main-nav__link" href="languages.php"><i class="fas fa-code"></i> Языки</a>
                        </li>
                        <li class="main-nav__item">
                            <a class="main-nav__link" href="simulator_pro_max.php"><i class="fas fa-gamepad"></i> Тренажер</a>
                        </li>
                        <li class="main-nav__item">
                            <a class="main-nav__link" href="trenasher.php"><i class="fas fa-gamepad"></i> Симулятор</a>
                        </li>
                    </ul>
                </nav>
                <div class="user-menu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="user-greeting">Привет, <?= esc($_SESSION['user_name']) ?></span>
                        <a href="edit_avatar.php" class="user-avatar" title="Аватар">
                            <?php if (!empty($user_avatar) && file_exists(__DIR__ . '/' . $user_avatar)): ?>
                                <img src="<?= esc($user_avatar) ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                            <?php endif; ?>
                        </a>
                        <a href="profile.php" class="btn-profile"><i class="fas fa-user"></i> Профиль</a>
                        <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Выход</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Вход</a>
                        <a href="register.php" class="btn-register"><i class="fas fa-user-plus"></i> Регистрация</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <!-- Конец шапки -->