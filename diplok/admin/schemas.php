<?php
declare(strict_types=1);

/**
 * Описание таблиц для CRUD. Ключ — имя таблицы в БД.
 *
 * @return array<string, array>
 */
function admin_table_schemas(): array
{
    return [
        'articles' => [
            'label' => 'Статьи',
            'pk' => 'id',
            'list' => ['id', 'title', 'category_name', 'author', 'is_published', 'views'],
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Заголовок', 'required' => true],
                'description' => ['type' => 'textarea', 'label' => 'Краткое описание'],
                'content' => ['type' => 'textarea', 'label' => 'Контент (HTML)', 'class' => 'tall'],
                'image_path' => ['type' => 'text', 'label' => 'Путь к изображению'],
                'category_name' => ['type' => 'text', 'label' => 'Категория'],
                'author' => ['type' => 'text', 'label' => 'Автор'],
                'views' => ['type' => 'int', 'label' => 'Просмотры', 'default' => 0],
                'reading_time' => ['type' => 'int', 'label' => 'Время чтения (мин)', 'nullable' => true],
                'tags' => ['type' => 'text', 'label' => 'Теги'],
                'is_published' => ['type' => 'bool', 'label' => 'Опубликовано'],
            ],
            'auto_created' => 'created_at',
        ],
        'professions' => [
            'label' => 'Профессии',
            'pk' => 'id',
            'list' => ['id', 'title', 'category', 'salary_min', 'salary_max'],
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Название', 'required' => true],
                'category' => ['type' => 'text', 'label' => 'Категория'],
                'short_description' => ['type' => 'textarea', 'label' => 'Краткое описание'],
                'full_description' => ['type' => 'textarea', 'label' => 'Полное описание (HTML)', 'class' => 'tall'],
                'image_path' => ['type' => 'text', 'label' => 'Изображение'],
                'salary_min' => ['type' => 'int', 'label' => 'Зарплата от', 'nullable' => true],
                'salary_max' => ['type' => 'int', 'label' => 'Зарплата до', 'nullable' => true],
            ],
        ],
        'languages' => [
            'label' => 'Языки программирования',
            'pk' => 'id',
            'list' => ['id', 'name', 'category', 'difficulty', 'popularity_rank'],
            'fields' => [
                'name' => ['type' => 'text', 'label' => 'Название', 'required' => true],
                'description' => ['type' => 'textarea', 'label' => 'Краткое описание'],
                'full_description' => ['type' => 'textarea', 'label' => 'Полное описание', 'class' => 'tall'],
                'image_path' => ['type' => 'text', 'label' => 'Изображение'],
                'category' => ['type' => 'text', 'label' => 'Категория'],
                'year_created' => ['type' => 'int', 'label' => 'Год создания', 'nullable' => true],
                'creator' => ['type' => 'text', 'label' => 'Создатель'],
                'paradigm' => ['type' => 'text', 'label' => 'Парадигма'],
                'typing' => ['type' => 'text', 'label' => 'Типизация'],
                'popularity_rank' => ['type' => 'int', 'label' => 'Ранг популярности', 'nullable' => true],
                'difficulty' => [
                    'type' => 'select',
                    'label' => 'Сложность',
                    'options' => ['' => '—', 'easy' => 'easy', 'medium' => 'medium', 'hard' => 'hard'],
                    'nullable' => true,
                ],
                'pros' => ['type' => 'textarea', 'label' => 'Плюсы'],
                'cons' => ['type' => 'textarea', 'label' => 'Минусы'],
            ],
        ],
        'tests' => [
            'label' => 'Тесты',
            'pk' => 'id',
            'list' => ['id', 'title', 'questions_count', 'duration_min', 'show_in_menu', 'slug'],
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Название', 'required' => true],
                'description' => ['type' => 'textarea', 'label' => 'Описание'],
                'questions_count' => ['type' => 'int', 'label' => 'Число вопросов', 'default' => 0],
                'duration_min' => ['type' => 'int', 'label' => 'Длительность (мин)', 'default' => 10],
                'age_limit' => ['type' => 'int', 'label' => 'Возрастной лимит', 'default' => 12],
                'price' => ['type' => 'decimal', 'label' => 'Цена', 'default' => '0.00'],
                'image_path' => ['type' => 'text', 'label' => 'Изображение'],
                'rating' => ['type' => 'float', 'label' => 'Рейтинг', 'default' => 0],
                'show_in_menu' => ['type' => 'bool', 'label' => 'Показывать в меню'],
                'slug' => ['type' => 'text', 'label' => 'Slug (URL)'],
            ],
        ],
        'slides' => [
            'label' => 'Слайды',
            'pk' => 'id',
            'list' => ['id', 'title', 'test_id', 'sort_order', 'active'],
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Заголовок', 'required' => true],
                'description' => ['type' => 'textarea', 'label' => 'Описание'],
                'image_path' => ['type' => 'text', 'label' => 'Изображение'],
                'test_id' => ['type' => 'int', 'label' => 'ID теста (опц.)', 'nullable' => true],
                'sort_order' => ['type' => 'int', 'label' => 'Порядок', 'default' => 0],
                'active' => ['type' => 'bool', 'label' => 'Активен'],
            ],
        ],
        'quiz_logos' => [
            'label' => 'Викторина логотипов',
            'pk' => 'id',
            'list' => ['id', 'question', 'correct_answer', 'xp'],
            'fields' => [
                'question' => ['type' => 'text', 'label' => 'Вопрос / эмодзи', 'required' => true],
                'image_path' => ['type' => 'text', 'label' => 'Путь к картинке', 'required' => true],
                'options' => ['type' => 'json', 'label' => 'Варианты (JSON массив)', 'required' => true],
                'correct_answer' => ['type' => 'text', 'label' => 'Правильный ответ (строка)', 'required' => true],
                'hint' => ['type' => 'text', 'label' => 'Подсказка'],
                'xp' => ['type' => 'int', 'label' => 'XP', 'default' => 30],
            ],
        ],
        'results' => [
            'label' => 'Результаты тестов (диапазоны баллов)',
            'pk' => 'id',
            'list' => ['id', 'test_id', 'min_score', 'max_score', 'result_type'],
            'fields' => [
                'test_id' => ['type' => 'int', 'label' => 'ID теста', 'required' => true],
                'min_score' => ['type' => 'int', 'label' => 'Мин. балл', 'default' => 0],
                'max_score' => ['type' => 'int', 'label' => 'Макс. балл', 'default' => 0],
                'result_text' => ['type' => 'textarea', 'label' => 'Текст результата', 'required' => true],
                'result_type' => [
                    'type' => 'select',
                    'label' => 'Тип',
                    'options' => ['text' => 'text', 'percent' => 'percent'],
                    'default' => 'text',
                ],
            ],
        ],
        'test_final' => [
            'label' => 'Финальные вопросы тестов',
            'pk' => 'id',
            'list' => ['id', 'test_id', 'order_index', 'question_type', 'points'],
            'fields' => [
                'test_id' => ['type' => 'int', 'label' => 'ID теста', 'required' => true],
                'question_text' => ['type' => 'textarea', 'label' => 'Текст вопроса', 'required' => true],
                'question_type' => [
                    'type' => 'select',
                    'label' => 'Тип вопроса',
                    'options' => [
                        'single' => 'single',
                        'multiple' => 'multiple',
                        'text' => 'text',
                        'match' => 'match',
                    ],
                    'default' => 'single',
                ],
                'options' => ['type' => 'json', 'label' => 'Варианты (JSON)', 'nullable' => true],
                'correct_answer' => ['type' => 'json', 'label' => 'Правильный ответ (JSON)', 'nullable' => true],
                'points' => ['type' => 'int', 'label' => 'Баллы', 'default' => 1],
                'order_index' => ['type' => 'int', 'label' => 'Порядок', 'required' => true],
            ],
        ],
        'test_quests' => [
            'label' => 'Квесты (этапы тестов)',
            'pk' => 'id',
            'list' => ['id', 'test_id', 'order_index', 'type', 'title'],
            'fields' => [
                'test_id' => ['type' => 'int', 'label' => 'ID теста', 'required' => true],
                'title' => ['type' => 'text', 'label' => 'Заголовок', 'required' => true],
                'description' => ['type' => 'textarea', 'label' => 'Описание'],
                'type' => [
                    'type' => 'select',
                    'label' => 'Тип',
                    'options' => ['choice' => 'choice', 'text' => 'text', 'info' => 'info'],
                    'default' => 'choice',
                ],
                'content' => ['type' => 'textarea', 'label' => 'Контент / текст', 'required' => true],
                'options' => ['type' => 'json', 'label' => 'Опции (JSON)', 'nullable' => true],
                'points' => ['type' => 'int', 'label' => 'Баллы', 'default' => 0],
                'order_index' => ['type' => 'int', 'label' => 'Порядок', 'required' => true],
            ],
        ],
        'users' => [
            'label' => 'Пользователи',
            'pk' => 'id',
            'list' => ['id', 'username', 'email', 'created_at'],
            'fields' => [
                'username' => ['type' => 'text', 'label' => 'Имя', 'required' => true],
                'email' => ['type' => 'text', 'label' => 'Email', 'required' => true],
                'password' => ['type' => 'user_password', 'label' => 'Пароль (как на сайте; оставьте пустым, чтобы не менять)'],
                'avatar' => ['type' => 'text', 'label' => 'Путь к аватару'],
            ],
            'user_password_plain' => true,
        ],
        'admins' => [
            'label' => 'Администраторы',
            'pk' => 'id',
            'list' => ['id', 'username', 'email', 'created_at'],
            'fields' => [
                'username' => ['type' => 'text', 'label' => 'Логин', 'required' => true],
                'email' => ['type' => 'text', 'label' => 'Email'],
                'password' => ['type' => 'admin_password', 'label' => 'Пароль (открытый текст в БД; для входа в панель)'],
            ],
            'admin_password' => true,
        ],
    ];
}

function admin_get_schema(string $table): ?array
{
    $all = admin_table_schemas();
    return $all[$table] ?? null;
}
