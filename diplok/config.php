<?php
// config.php - настройки подключения к БД
$db_host = 'localhost';
$db_user = 'root';        // ваш пользователь MySQL
$db_pass = '';            // ваш пароль (в OpenServer часто пустой)
$db_name = 'prof_simulator';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($db->connect_error) {
    die("Ошибка подключения к БД: " . $db->connect_error);
}
$db->set_charset("utf8");

/**
 * Резервный вход в админ-панель (если совпадает логин/пароль — берётся первая подходящая запись admins).
 * Основной вход: логин + пароль из таблицы admins (колонка password, без хеша).
 */
if (!defined('ADMIN_PANEL_USERNAME')) {
    define('ADMIN_PANEL_USERNAME', 'admin');
    define('ADMIN_PANEL_PASSWORD', 'admin123');
}
?>