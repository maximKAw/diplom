-- Выполните один раз в БД «Проф симулятор», если колонки ещё нет:
ALTER TABLE progress ADD COLUMN paint_level INT NOT NULL DEFAULT 1;
