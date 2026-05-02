-- Миграция: уже есть таблица с колонкой password_hash → переименовать в password (без хеша).
-- Если таблицы нет или нужна чистая установка — используйте admins_table.sql

SET NAMES utf8mb4;

ALTER TABLE `admins`
  CHANGE `password_hash` `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';

-- Старое значение могло быть bcrypt — задайте пароль открытым текстом:
-- UPDATE `admins` SET `password` = 'новый_пароль' WHERE `id` = 1;
