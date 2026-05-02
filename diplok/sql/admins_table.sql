-- Таблица `admins`: логин и пароль без хеширования (колонка `password` — открытый текст).
-- База: prof_simulator (выберите её в phpMyAdmin перед выполнением).

SET NAMES utf8mb4;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ВНИМАНИЕ: удалит существующую таблицу и все данные в ней.
DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Пример записи (пароль хранится как есть, без bcrypt):
-- INSERT INTO `admins` (`username`, `password`, `email`)
-- VALUES ('admin', 'ваш_пароль', 'email@example.com');
