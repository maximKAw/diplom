-- Добавить прогресс для игры "поиск слов" (Словоискатель)
-- Выполнить один раз в БД prof_simulator

ALTER TABLE `progress`
  ADD COLUMN `wordgrid_level` int NOT NULL DEFAULT '1' AFTER `lego_level`;

