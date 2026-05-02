<?php
require_once __DIR__ . '/header.php';
?>
<style>
        /* Полноширинный блок: светлый фон, акцент #410cdf только полосой сверху */
        .sim-pro-bleed {
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            background: #f8fafc;
            border-top: 4px solid #410cdf;
            overflow-x: hidden;
            padding: clamp(12px, 2.5vw, 24px) 0 clamp(28px, 4vw, 48px);
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
        }
        .sim-pro-bleed .game-portal,
        .sim-pro-bleed .game-portal * {
            box-sizing: border-box;
        }
        .game-portal {
            width: 100%;
            max-width: none;
            margin: 0;
            display: flex;
            flex-direction: column;
            border-radius: 0;
            overflow: visible;
            background: transparent;
            box-shadow: none;
            border: none;
            max-height: none;
        }
        .game-portal__body {
            flex: 1;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: clamp(12px, 2.5vw, 28px) clamp(16px, 4vw, 48px) clamp(20px, 3vw, 36px);
            background: transparent;
        }
        .sim-menu-title {
            color: #1e1b4b;
            font-size: clamp(1.15rem, 2.5vw, 1.45rem);
            font-weight: 700;
            margin: 0 0 22px;
        }
        .game-menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: clamp(16px, 2.5vw, 28px);
            margin-top: 0;
        }
        @media (max-width: 900px) {
            .game-menu {
                grid-template-columns: 1fr;
            }
        }
        .game-card {
            background: #fff;
            padding: clamp(22px, 3vw, 32px) clamp(18px, 2.5vw, 26px);
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(65, 12, 223, 0.08);
            border: 1px solid rgba(65, 12, 223, 0.12);
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.2s;
            text-align: center;
        }
        .game-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 36px rgba(65, 12, 223, 0.15);
            border-color: rgba(65, 12, 223, 0.35);
        }
        .game-card h3 {
            color: #173a5e;
            margin: 12px 0 12px;
            font-size: clamp(1rem, 2vw, 1.15rem);
            font-weight: 700;
        }
        .game-card p {
            color: #5c6470;
            font-size: 0.95rem;
            line-height: 1.55;
            margin: 0;
        }
        .game-screen-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 22px;
        }
        .game-screen-head h2 {
            margin: 0;
            color: #173a5e;
            font-size: clamp(1.2rem, 2.5vw, 1.5rem);
        }
        .progressBox {
            background: #fff;
            padding: 20px 22px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(30, 40, 80, 0.07);
            border: 1px solid rgba(230, 232, 245, 0.9);
        }
        .progressBar {
            width: 100%;
            height: 25px;
            background: #ddd;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 10px;
        }
        .progressFill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #5b2ef0, #7c3aed);
            transition: width 0.4s ease;
        }
        .levelCard {
            background: #fff;
            padding: 22px 24px;
            border-radius: 16px;
            margin-bottom: 22px;
            box-shadow: 0 4px 18px rgba(30, 40, 80, 0.07);
            border: 1px solid rgba(230, 232, 245, 0.9);
        }
        .levelCard > h2 {
            color: #173a5e;
            font-size: 1.2rem;
            margin: 0 0 8px;
        }
        .task {
            background: #f7fbff;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
            cursor: pointer;
            transition: 0.3s;
            border-left: 4px solid #0f3e75;
        }
        .task:hover {
            background: #eaf6ff;
            transform: scale(1.02) translateX(5px);
            box-shadow: 0 5px 15px rgba(0,63,117,0.2);
        }
        .task.completed {
            background: #d4edda;
            border-left-color: #28a745;
            opacity: 0.8;
            pointer-events: none;
        }
        .task-wrap {
            margin: 10px 0;
        }
        .task-panel {
            display: none;
            margin-top: 10px;
            padding: 16px 18px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #cfe2f3;
            box-shadow: 0 4px 14px rgba(15, 62, 117, 0.08);
        }
        .task-panel.is-open {
            display: block;
        }
        .task-panel .answer-display {
            margin-top: 12px;
        }
        #gameArea {
            display: none;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            margin-top: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: #0f3e75;
            color: #fff;
            cursor: pointer;
            margin: 5px;
            transition: 0.2s;
        }
        button:hover {
            background: #092b52;
            transform: translateY(-2px);
        }
        button.correct {
            background: #28a745 !important;
            animation: pulse 0.5s;
        }
        button.incorrect {
            background: #dc3545 !important;
            animation: shake 0.3s;
        }
        .hint-btn, .show-answer-btn {
            background: #6c757d;
        }
        .answer-display {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fc;
            border-radius: 8px;
            font-family: monospace;
            display: none;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-3px); }
            100% { transform: translateX(0); }
        }
        textarea, input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .badge {
            display: inline-block;
            padding: 6px 10px;
            background: #0f3e75;
            color: #fff;
            border-radius: 6px;
            font-size: 12px;
            margin-left: 10px;
        }
        .timer {
            font-weight: bold;
            color: #c0392b;
            margin-top: 10px;
        }
        .back-btn {
            background: #c73e50;
            margin-top: 0;
        }
        .back-btn:hover {
            background: #a82d3e;
        }
        .sim-instruction-outer {
            margin: 0 0 18px;
        }
        .sim-instruction {
            padding: 12px 16px;
            background: rgba(65, 12, 223, 0.06);
            border: 2px solid #410cdf;
            border-radius: 14px;
            font-size: 0.9rem;
            line-height: 1.55;
            color: #410cdf;
        }
        .sim-instruction-outer.is-collapsed .sim-instruction {
            padding: 10px 14px;
        }
        .sim-instruction-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px 12px;
            margin-bottom: 10px;
        }
        .sim-instruction-outer.is-collapsed .sim-instruction-head {
            margin-bottom: 0;
        }
        .sim-instruction-head strong {
            color: #410cdf;
            font-size: 1rem;
        }
        .sim-instruction-toggle {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 999px;
            border: 2px solid #410cdf;
            background: #fff;
            color: #410cdf;
            cursor: pointer;
        }
        .sim-instruction-toggle:hover {
            background: rgba(65, 12, 223, 0.08);
        }
        .sim-instruction-outer.is-collapsed .sim-instruction-body {
            display: none;
        }
        .sim-instruction-body p {
            margin: 0 0 8px;
            color: #410cdf;
        }
        .sim-instruction-body p:last-child {
            margin-bottom: 0;
        }
    </style>

<div class="sim-pro-bleed">
<div class="game-portal">
    <div class="game-portal__body">
    <!-- Экран выбора игры -->
    <div id="menuScreen">
        <h2 class="sim-menu-title">Выбери режим:</h2>
        <div class="sim-instruction-outer" data-sim-instruction>
            <div class="sim-instruction">
                <div class="sim-instruction-head">
                    <strong>Как играть</strong>
                    <button type="button" class="sim-instruction-toggle" aria-expanded="true">Свернуть</button>
                </div>
                <div class="sim-instruction-body">
                    <p>Выбери режим на карточке ниже. В игре отвечай на вопросы, копи XP и уровень. Подсказки и просмотр ответа — кнопки под заданием. Прогресс можно сбросить в блоке статистики.</p>
                </div>
            </div>
        </div>
        <div class="game-menu">
            <div class="game-card" data-game="pro">
                <h3>👨‍💻 PRO MAX Симулятор</h3>
                <p>Классический день программиста. 6 разделов, 35 заданий.</p>
            </div>
            <div class="game-card" data-game="logo">
                <h3>🔍 Угадай логотип</h3>
                <p>Угадай язык программирования по эмодзи. 15 заданий.</p>
            </div>
            <div class="game-card" data-game="student">
                <h3>🎓 Студенческий уровень</h3>
                <p>Сложные вопросы для 7-11 классов и первого курса. 8 разделов, 40 заданий.</p>
            </div>
        </div>
    </div>

    <!-- Экран игры (изначально скрыт) -->
    <div id="gameScreen" style="display: none;">
        <div class="game-screen-head">
            <h2 id="gameTitle"></h2>
            <button id="backToMenu" class="back-btn" type="button">← В меню</button>
        </div>

        <!-- Прогресс-бар и информация -->
        <div class="progressBox">
            <div style="display: flex; justify-content: space-between;">
                <span>Уровень: <span id="levelName">Школьник</span></span>
                <span class="badge" id="rankBadge">Level 1</span>
            </div>
            <div>XP: <span id="xp">0</span></div>
            <div class="progressBar">
                <div id="xpBar" class="progressFill"></div>
            </div>
            <button class="reset-btn" id="resetProgress" style="background: #6c757d; margin-top: 10px;">Сбросить прогресс</button>
        </div>

        <div class="sim-instruction-outer" data-sim-instruction>
            <div class="sim-instruction">
                <div class="sim-instruction-head">
                    <strong>Инструкция</strong>
                    <button type="button" class="sim-instruction-toggle" aria-expanded="true">Свернуть</button>
                </div>
                <div class="sim-instruction-body">
                    <p>Читай условие, выбирай ответ или вводи текст/число и нажимай «Проверить». «Подсказка» не даёт ответ сразу — только подсказку. «Посмотреть ответ» раскрывает эталон для зачёта.</p>
                </div>
            </div>
        </div>

        <!-- Контейнер для уровней -->
        <div id="levelsContainer"></div>
        <div id="gameArea"></div>
    </div>
    </div>
</div>
</div>

<script>
(function() {
    // ====================== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ======================
    function generateButtons(id, xp, options, hint, correctAnswer) {
        let html = '';
        options.forEach(opt => {
            html += `<button onclick="window.game.handleAnswer(${id}, ${xp}, ${opt.correct}, this)">${opt.text}</button><br>`;
        });
        html += `<button class="hint-btn" onclick="window.game.showHint('${hint}')">💡 Подсказка</button>`;
        html += `<button class="show-answer-btn" onclick="window.game.showAnswer('${correctAnswer}', ${id})">👁️ Посмотреть ответ</button>`;
        html += `<div id="answerDisplay-${id}" class="answer-display"></div>`;
        return html;
    }

    function generateTextCheck(id, xp, elementId, keyword, hint, correctAnswer) {
        return `<textarea id="${elementId}"></textarea>
                <button onclick="window.game.checkText(${id}, ${xp}, '${elementId}', '${keyword}')">Проверить</button>
                <button class="hint-btn" onclick="window.game.showHint('${hint}')">💡 Подсказка</button>
                <button class="show-answer-btn" onclick="window.game.showAnswer('${correctAnswer}', ${id})">👁️ Посмотреть ответ</button>
                <div id="answerDisplay-${id}" class="answer-display"></div>`;
    }

    function generateNumberCheck(id, xp, hint, correctAnswer) {
        return `<input id="num${id}" type="number">
                <button onclick="window.game.checkEven(${id}, ${xp})">Проверить</button>
                <button class="hint-btn" onclick="window.game.showHint('${hint}')">💡 Подсказка</button>
                <button class="show-answer-btn" onclick="window.game.showAnswer('${correctAnswer}', ${id})">👁️ Посмотреть ответ</button>
                <div id="answerDisplay-${id}" class="answer-display"></div>`;
    }

    // ====================== ДАННЫЕ ИГР ======================
    const games = {
        // PRO MAX Симулятор – 35 заданий, 6 разделов
        pro: {
            name: 'PRO MAX Симулятор',
            levels: [
                { // Раздел 1: Основы JavaScript (6 заданий)
                    name: 'Основы JavaScript',
                    tasks: [
                        { id: 1, title: 'Как объявить переменную в JavaScript?',
                          content: generateButtons(1, 20, [
                              { text: 'let x;', correct: true },
                              { text: 'x := 5;', correct: false },
                              { text: 'int x;', correct: false }
                          ], 'Используют let, const или var', 'let x; или var x;') },
                        { id: 2, title: 'Что выведет console.log(2 + "2")?',
                          content: generateButtons(2, 20, [
                              { text: '4', correct: false },
                              { text: '22', correct: true },
                              { text: 'Ошибка', correct: false }
                          ], 'Конкатенация строки и числа', '22') },
                        { id: 3, title: 'Какой тип данных у true?',
                          content: generateButtons(3, 20, [
                              { text: 'boolean', correct: true },
                              { text: 'string', correct: false },
                              { text: 'number', correct: false }
                          ], 'Логический тип', 'boolean') },
                        { id: 4, title: 'Что означает null?',
                          content: generateButtons(4, 20, [
                              { text: 'Пустое значение', correct: true },
                              { text: '0', correct: false },
                              { text: 'undefined', correct: false }
                          ], 'Намеренное отсутствие значения', 'Пустое значение') },
                        { id: 5, title: 'Как проверить тип переменной?',
                          content: generateButtons(5, 20, [
                              { text: 'typeof', correct: true },
                              { text: 'type', correct: false },
                              { text: 'instanceof', correct: false }
                          ], 'Оператор typeof', 'typeof') },
                        { id: 6, title: 'Какое значение у переменной, объявленной без инициализации?',
                          content: generateButtons(6, 20, [
                              { text: 'undefined', correct: true },
                              { text: 'null', correct: false },
                              { text: '0', correct: false }
                          ], 'По умолчанию undefined', 'undefined') }
                    ]
                },
                { // Раздел 2: Условия и циклы (6 заданий)
                    name: 'Условия и циклы',
                    tasks: [
                        { id: 7, title: 'Как записать условие "если a равно 5"?',
                          content: generateButtons(7, 30, [
                              { text: 'if (a = 5)', correct: false },
                              { text: 'if (a == 5)', correct: true },
                              { text: 'if a == 5', correct: false }
                          ], 'Оператор сравнения ==', 'if (a == 5)') },
                        { id: 8, title: 'Какой цикл выполняется хотя бы один раз?',
                          content: generateButtons(8, 30, [
                              { text: 'for', correct: false },
                              { text: 'while', correct: false },
                              { text: 'do...while', correct: true }
                          ], 'do...while сначала выполняет, потом проверяет', 'do...while') },
                        { id: 9, title: 'Как записать логическое И?',
                          content: generateButtons(9, 30, [
                              { text: '||', correct: false },
                              { text: '&&', correct: true },
                              { text: '!', correct: false }
                          ], 'Амперсанд', '&&') },
                        { id: 10, title: 'Сколько раз выполнится цикл for (let i=0; i<3; i++)?',
                          content: generateButtons(10, 30, [
                              { text: '2', correct: false },
                              { text: '3', correct: true },
                              { text: '4', correct: false }
                          ], 'i принимает значения 0,1,2', '3') },
                        { id: 11, title: 'Как прервать выполнение цикла досрочно?',
                          content: generateButtons(11, 30, [
                              { text: 'break', correct: true },
                              { text: 'continue', correct: false },
                              { text: 'exit', correct: false }
                          ], 'break выходит из цикла', 'break') },
                        { id: 12, title: 'Как пропустить текущую итерацию цикла?',
                          content: generateButtons(12, 30, [
                              { text: 'break', correct: false },
                              { text: 'continue', correct: true },
                              { text: 'skip', correct: false }
                          ], 'continue переходит к следующему шагу', 'continue') }
                    ]
                },
                { // Раздел 3: Функции (6 заданий)
                    name: 'Функции',
                    tasks: [
                        { id: 13, title: 'Как объявить функцию с именем foo?',
                          content: generateButtons(13, 40, [
                              { text: 'function foo() {}', correct: true },
                              { text: 'def foo() {}', correct: false },
                              { text: 'func foo()', correct: false }
                          ], 'Ключевое слово function', 'function foo() {}') },
                        { id: 14, title: 'Что вернёт функция, если нет return?',
                          content: generateButtons(14, 40, [
                              { text: 'null', correct: false },
                              { text: 'undefined', correct: true },
                              { text: '0', correct: false }
                          ], 'По умолчанию undefined', 'undefined') },
                        { id: 15, title: 'Как вызвать функцию foo?',
                          content: generateButtons(15, 40, [
                              { text: 'foo()', correct: true },
                              { text: 'call foo', correct: false },
                              { text: 'foo', correct: false }
                          ], 'Круглые скобки', 'foo()') },
                        { id: 16, title: 'Что такое стрелочная функция?',
                          content: generateButtons(16, 40, [
                              { text: '() => {}', correct: true },
                              { text: 'function*', correct: false },
                              { text: 'async function', correct: false }
                          ], 'Синтаксис =>', '() => {}') },
                        { id: 17, title: 'Что такое замыкание?',
                          content: generateButtons(17, 40, [
                              { text: 'Функция, запоминающая окружение', correct: true },
                              { text: 'Закрытая функция', correct: false },
                              { text: 'Цикл', correct: false }
                          ], 'Функция + внешние переменные', 'Функция, запоминающая окружение') },
                        { id: 18, title: 'Как передать функцию в качестве аргумента?',
                          content: generateButtons(18, 40, [
                              { text: 'callback()', correct: true },
                              { text: 'function(callback)', correct: true },
                              { text: 'Имя функции без скобок', correct: true }
                          ], 'Можно передать имя функции', 'Любой из вариантов подходит') }
                    ]
                },
                { // Раздел 4: Массивы (6 заданий)
                    name: 'Массивы',
                    tasks: [
                        { id: 19, title: 'Как создать пустой массив?',
                          content: generateButtons(19, 40, [
                              { text: '[]', correct: true },
                              { text: '{}', correct: false },
                              { text: 'new Array()', correct: true }
                          ], 'Квадратные скобки', '[] или new Array()') },
                        { id: 20, title: 'Как получить длину массива arr?',
                          content: generateButtons(20, 40, [
                              { text: 'arr.length', correct: true },
                              { text: 'arr.size', correct: false },
                              { text: 'len(arr)', correct: false }
                          ], 'Свойство length', 'arr.length') },
                        { id: 21, title: 'Как добавить элемент в конец массива?',
                          content: generateButtons(21, 40, [
                              { text: 'arr.push()', correct: true },
                              { text: 'arr.pop()', correct: false },
                              { text: 'arr.unshift()', correct: false }
                          ], 'Метод push', 'arr.push()') },
                        { id: 22, title: 'Как удалить последний элемент массива?',
                          content: generateButtons(22, 40, [
                              { text: 'arr.shift()', correct: false },
                              { text: 'arr.pop()', correct: true },
                              { text: 'arr.slice()', correct: false }
                          ], 'pop() удаляет последний', 'arr.pop()') },
                        { id: 23, title: 'Как перебрать все элементы массива?',
                          content: generateButtons(23, 40, [
                              { text: 'for (let i of arr)', correct: true },
                              { text: 'arr.forEach()', correct: true },
                              { text: 'arr.map()', correct: true }
                          ], 'Много способов', 'for, forEach, map и др.') },
                        { id: 24, title: 'Что вернёт arr.indexOf(5), если 5 нет в массиве?',
                          content: generateButtons(24, 40, [
                              { text: '-1', correct: true },
                              { text: '0', correct: false },
                              { text: 'null', correct: false }
                          ], 'Индекс не найден = -1', '-1') }
                    ]
                },
                { // Раздел 5: Объекты (6 заданий)
                    name: 'Объекты',
                    tasks: [
                        { id: 25, title: 'Как создать объект с полем name?',
                          content: generateButtons(25, 40, [
                              { text: '{name: "Alex"}', correct: true },
                              { text: 'Object("Alex")', correct: false },
                              { text: 'new Object(name)', correct: false }
                          ], 'Фигурные скобки', '{name: "Alex"}') },
                        { id: 26, title: 'Как обратиться к свойству obj.name?',
                          content: generateButtons(26, 40, [
                              { text: 'obj.name', correct: true },
                              { text: 'obj["name"]', correct: true },
                              { text: 'obj->name', correct: false }
                          ], 'Точечная или скобочная нотация', 'obj.name или obj["name"]') },
                        { id: 27, title: 'Как удалить свойство из объекта?',
                          content: generateButtons(27, 40, [
                              { text: 'delete obj.name', correct: true },
                              { text: 'obj.name = null', correct: false },
                              { text: 'remove obj.name', correct: false }
                          ], 'Оператор delete', 'delete obj.name') },
                        { id: 28, title: 'Что такое this в методе объекта?',
                          content: generateButtons(28, 40, [
                              { text: 'Ссылка на текущий объект', correct: true },
                              { text: 'Глобальный объект', correct: false },
                              { text: 'Контекст вызова', correct: true }
                          ], 'Зависит от контекста', 'Ссылка на объект или контекст') },
                        { id: 29, title: 'Как проверить, есть ли свойство в объекте?',
                          content: generateButtons(29, 40, [
                              { text: 'property in obj', correct: true },
                              { text: 'obj.hasOwnProperty(property)', correct: true },
                              { text: 'obj.property !== undefined', correct: true }
                          ], 'Оператор in или hasOwnProperty', 'in или hasOwnProperty') },
                        { id: 30, title: 'Что такое JSON?',
                          content: generateButtons(30, 40, [
                              { text: 'Формат обмена данными', correct: true },
                              { text: 'Объект JavaScript', correct: false },
                              { text: 'Метод', correct: false }
                          ], 'JavaScript Object Notation', 'Формат обмена данными') }
                    ]
                },
                { // Раздел 6: Продвинутые концепции (5 заданий, всего 35)
                    name: 'Продвинутые концепции',
                    tasks: [
                        { id: 31, title: 'Что такое замыкание?',
                          content: generateButtons(31, 50, [
                              { text: 'Функция с доступом к внешней области видимости', correct: true },
                              { text: 'Закрытая функция', correct: false },
                              { text: 'Цикл', correct: false }
                          ], 'Функция + внешние переменные', 'Функция с доступом к внешней области') },
                        { id: 32, title: 'Что такое прототип?',
                          content: generateButtons(32, 50, [
                              { text: 'Механизм наследования в JS', correct: true },
                              { text: 'Тип функции', correct: false },
                              { text: 'Объект', correct: false }
                          ], 'Объект, от которого наследуются', 'Механизм наследования') },
                        { id: 33, title: 'Что делает метод .map()?',
                          content: generateButtons(33, 50, [
                              { text: 'Преобразует каждый элемент массива', correct: true },
                              { text: 'Фильтрует массив', correct: false },
                              { text: 'Ищет элемент', correct: false }
                          ], 'Возвращает новый массив', 'Преобразует каждый элемент') },
                        { id: 34, title: 'Что такое промис?',
                          content: generateButtons(34, 50, [
                              { text: 'Объект для асинхронных операций', correct: true },
                              { text: 'Функция обратного вызова', correct: false },
                              { text: 'Цикл', correct: false }
                          ], 'Promise', 'Объект для асинхронных операций') },
                        { id: 35, title: 'Что такое async/await?',
                          content: generateButtons(35, 50, [
                              { text: 'Синтаксис для работы с промисами', correct: true },
                              { text: 'Объявление функции', correct: false },
                              { text: 'Таймер', correct: false }
                          ], 'Упрощает асинхронный код', 'Синтаксис для промисов') }
                    ]
                }
            ]
        },

        // Угадай логотип – 15 заданий
        logo: {
            name: 'Угадай логотип',
            levels: [
                {
                    name: 'Языки и технологии',
                    tasks: [
                        { id: 101, title: '🐍', content: generateButtons(101, 30, [
                            { text: 'Python', correct: true },
                            { text: 'Java', correct: false },
                            { text: 'C++', correct: false }
                        ], 'Змея — Python', 'Python') },
                        { id: 102, title: '☕', content: generateButtons(102, 30, [
                            { text: 'Java', correct: true },
                            { text: 'JavaScript', correct: false },
                            { text: 'C#', correct: false }
                        ], 'Чашка кофе — Java', 'Java') },
                        { id: 103, title: '🕸️', content: generateButtons(103, 30, [
                            { text: 'HTML', correct: true },
                            { text: 'CSS', correct: false },
                            { text: 'PHP', correct: false }
                        ], 'Паутина — веб', 'HTML') },
                        { id: 104, title: '🎨', content: generateButtons(104, 30, [
                            { text: 'CSS', correct: true },
                            { text: 'JavaScript', correct: false },
                            { text: 'Python', correct: false }
                        ], 'Стили — CSS', 'CSS') },
                        { id: 105, title: '⚙️', content: generateButtons(105, 30, [
                            { text: 'C++', correct: true },
                            { text: 'Java', correct: false },
                            { text: 'Ruby', correct: false }
                        ], 'Шестерёнка — C++', 'C++') },
                        { id: 106, title: '🐘', content: generateButtons(106, 30, [
                            { text: 'PHP', correct: true },
                            { text: 'Perl', correct: false },
                            { text: 'Python', correct: false }
                        ], 'Слон — PHP', 'PHP') },
                        { id: 107, title: '🔷', content: generateButtons(107, 30, [
                            { text: 'Ruby', correct: true },
                            { text: 'Rust', correct: false },
                            { text: 'Go', correct: false }
                        ], 'Ромб — Ruby', 'Ruby') },
                        { id: 108, title: '🦀', content: generateButtons(108, 30, [
                            { text: 'Rust', correct: true },
                            { text: 'Crab', correct: false },
                            { text: 'C++', correct: false }
                        ], 'Краб — Rust', 'Rust') },
                        { id: 109, title: '🐿️', content: generateButtons(109, 30, [
                            { text: 'Swift', correct: true },
                            { text: 'Kotlin', correct: false },
                            { text: 'Objective-C', correct: false }
                        ], 'Белка — Swift', 'Swift') },
                        { id: 110, title: '🔥', content: generateButtons(110, 30, [
                            { text: 'Firebase', correct: true },
                            { text: 'Flask', correct: false },
                            { text: 'Django', correct: false }
                        ], 'Пламя — Firebase', 'Firebase') },
                        { id: 111, title: '📱', content: generateButtons(111, 30, [
                            { text: 'Swift', correct: true },
                            { text: 'Kotlin', correct: true },
                            { text: 'Java', correct: true }
                        ], 'Мобильная разработка', 'Swift/Kotlin/Java') },
                        { id: 112, title: '🌐', content: generateButtons(112, 30, [
                            { text: 'JavaScript', correct: true },
                            { text: 'HTML', correct: false },
                            { text: 'CSS', correct: false }
                        ], 'Интернет — JS', 'JavaScript') },
                        { id: 113, title: '🐚', content: generateButtons(113, 30, [
                            { text: 'Bash', correct: true },
                            { text: 'PowerShell', correct: false },
                            { text: 'Zsh', correct: false }
                        ], 'Ракушка — командная оболочка', 'Bash') },
                        { id: 114, title: '📦', content: generateButtons(114, 30, [
                            { text: 'npm', correct: true },
                            { text: 'pip', correct: false },
                            { text: 'composer', correct: false }
                        ], 'Пакетный менеджер Node.js', 'npm') },
                        { id: 115, title: '🐳', content: generateButtons(115, 30, [
                            { text: 'Docker', correct: true },
                            { text: 'Kubernetes', correct: false },
                            { text: 'Jenkins', correct: false }
                        ], 'Кит — Docker', 'Docker') }
                    ]
                }
            ]
        },

        // Студенческий уровень – 40 заданий, 8 разделов по 5
        student: {
            name: 'Студенческий уровень',
            levels: [
                { // Алгоритмы
                    name: 'Алгоритмы и сложность',
                    tasks: [
                        { id: 201, title: 'Что такое O(n)?', content: generateButtons(201, 50, [
                            { text: 'Линейная сложность', correct: true },
                            { text: 'Квадратичная сложность', correct: false },
                            { text: 'Логарифмическая сложность', correct: false }
                        ], 'Пропорционально n', 'Линейная сложность') },
                        { id: 202, title: 'Какой алгоритм сортировки самый быстрый в среднем?', content: generateButtons(202, 50, [
                            { text: 'Пузырьковая', correct: false },
                            { text: 'Быстрая (QuickSort)', correct: true },
                            { text: 'Вставками', correct: false }
                        ], 'Обычно QuickSort или MergeSort', 'Быстрая (QuickSort)') },
                        { id: 203, title: 'Что такое бинарный поиск?', content: generateButtons(203, 50, [
                            { text: 'Поиск в отсортированном массиве делением пополам', correct: true },
                            { text: 'Поиск перебором', correct: false },
                            { text: 'Поиск в ширину', correct: false }
                        ], 'Требует сортировки', 'Поиск делением пополам') },
                        { id: 204, title: 'Какова сложность бинарного поиска?', content: generateButtons(204, 50, [
                            { text: 'O(log n)', correct: true },
                            { text: 'O(n)', correct: false },
                            { text: 'O(n log n)', correct: false }
                        ], 'Логарифмическая', 'O(log n)') },
                        { id: 205, title: 'Что такое рекурсия?', content: generateButtons(205, 50, [
                            { text: 'Функция, вызывающая саму себя', correct: true },
                            { text: 'Цикл', correct: false },
                            { text: 'Тип данных', correct: false }
                        ], 'Само-вызов', 'Функция, вызывающая себя') }
                    ]
                },
                { // Структуры данных
                    name: 'Структуры данных',
                    tasks: [
                        { id: 206, title: 'Какой принцип у стека?', content: generateButtons(206, 50, [
                            { text: 'LIFO', correct: true },
                            { text: 'FIFO', correct: false },
                            { text: 'FILO', correct: true }
                        ], 'Last In First Out', 'LIFO') },
                        { id: 207, title: 'Какой принцип у очереди?', content: generateButtons(207, 50, [
                            { text: 'FIFO', correct: true },
                            { text: 'LIFO', correct: false },
                            { text: 'FILO', correct: false }
                        ], 'First In First Out', 'FIFO') },
                        { id: 208, title: 'Что такое хеш-таблица?', content: generateButtons(208, 50, [
                            { text: 'Структура для хранения пар ключ-значение', correct: true },
                            { text: 'Сортированный массив', correct: false },
                            { text: 'Связанный список', correct: false }
                        ], 'Быстрый доступ по ключу', 'Ключ-значение') },
                        { id: 209, title: 'Что такое дерево?', content: generateButtons(209, 50, [
                            { text: 'Иерархическая структура с узлами', correct: true },
                            { text: 'Линейный список', correct: false },
                            { text: 'Циклический граф', correct: false }
                        ], 'Корень, ветви, листья', 'Иерархическая структура') },
                        { id: 210, title: 'Что такое граф?', content: generateButtons(210, 50, [
                            { text: 'Совокупность вершин и рёбер', correct: true },
                            { text: 'Двумерный массив', correct: false },
                            { text: 'Очередь', correct: false }
                        ], 'Связи между объектами', 'Вершины и рёбра') }
                    ]
                },
                { // ООП
                    name: 'Объектно-ориентированное программирование',
                    tasks: [
                        { id: 211, title: 'Что такое инкапсуляция?', content: generateButtons(211, 60, [
                            { text: 'Скрытие внутренней реализации', correct: true },
                            { text: 'Наследование свойств', correct: false },
                            { text: 'Перегрузка методов', correct: false }
                        ], 'Сокрытие данных', 'Скрытие реализации') },
                        { id: 212, title: 'Что такое наследование?', content: generateButtons(212, 60, [
                            { text: 'Класс получает свойства другого класса', correct: true },
                            { text: 'Создание объекта', correct: false },
                            { text: 'Вызов метода', correct: false }
                        ], 'Родитель-потомок', 'Наследование свойств') },
                        { id: 213, title: 'Что такое полиморфизм?', content: generateButtons(213, 60, [
                            { text: 'Один интерфейс — разная реализация', correct: true },
                            { text: 'Множественное наследование', correct: false },
                            { text: 'Переопределение методов', correct: true }
                        ], 'Много форм', 'Один интерфейс — разная реализация') },
                        { id: 214, title: 'Что такое абстрактный класс?', content: generateButtons(214, 60, [
                            { text: 'Класс, который нельзя инстанцировать', correct: true },
                            { text: 'Класс без методов', correct: false },
                            { text: 'Финальный класс', correct: false }
                        ], 'Шаблон для других классов', 'Нельзя создать объект') },
                        { id: 215, title: 'Что такое интерфейс?', content: generateButtons(215, 60, [
                            { text: 'Контракт, который должен реализовать класс', correct: true },
                            { text: 'Абстрактный класс', correct: false },
                            { text: 'Набор методов без реализации', correct: true }
                        ], 'Определяет поведение', 'Контракт для класса') }
                    ]
                },
                { // SQL
                    name: 'Базы данных и SQL',
                    tasks: [
                        { id: 216, title: 'Какой запрос выбирает все данные из таблицы users?', content: generateButtons(216, 60, [
                            { text: 'SELECT * FROM users', correct: true },
                            { text: 'GET * FROM users', correct: false },
                            { text: 'SELECT users', correct: false }
                        ], 'SELECT ... FROM', 'SELECT * FROM users') },
                        { id: 217, title: 'Как добавить новую запись в таблицу?', content: generateButtons(217, 60, [
                            { text: 'INSERT INTO ... VALUES ...', correct: true },
                            { text: 'ADD INTO ...', correct: false },
                            { text: 'UPDATE ...', correct: false }
                        ], 'INSERT', 'INSERT INTO ... VALUES ...') },
                        { id: 218, title: 'Как обновить данные?', content: generateButtons(218, 60, [
                            { text: 'UPDATE ... SET ...', correct: true },
                            { text: 'ALTER ...', correct: false },
                            { text: 'MODIFY ...', correct: false }
                        ], 'UPDATE', 'UPDATE ... SET ...') },
                        { id: 219, title: 'Как удалить запись?', content: generateButtons(219, 60, [
                            { text: 'DELETE FROM ... WHERE ...', correct: true },
                            { text: 'REMOVE ...', correct: false },
                            { text: 'DROP ...', correct: false }
                        ], 'DELETE', 'DELETE FROM ... WHERE ...') },
                        { id: 220, title: 'Как объединить две таблицы?', content: generateButtons(220, 60, [
                            { text: 'JOIN', correct: true },
                            { text: 'MERGE', correct: false },
                            { text: 'UNION', correct: false }
                        ], 'INNER JOIN, LEFT JOIN и т.д.', 'JOIN') }
                    ]
                },
                { // Сети
                    name: 'Сети и интернет',
                    tasks: [
                        { id: 221, title: 'Какой протокол используется для передачи гипертекста?', content: generateButtons(221, 60, [
                            { text: 'HTTP', correct: true },
                            { text: 'FTP', correct: false },
                            { text: 'TCP', correct: false }
                        ], 'HyperText Transfer Protocol', 'HTTP') },
                        { id: 222, title: 'Что такое IP-адрес?', content: generateButtons(222, 60, [
                            { text: 'Уникальный идентификатор устройства в сети', correct: true },
                            { text: 'Доменное имя', correct: false },
                            { text: 'Маршрутизатор', correct: false }
                        ], 'Internet Protocol', 'Идентификатор устройства') },
                        { id: 223, title: 'Что такое DNS?', content: generateButtons(223, 60, [
                            { text: 'Сервер доменных имён', correct: true },
                            { text: 'Протокол передачи файлов', correct: false },
                            { text: 'Система шифрования', correct: false }
                        ], 'Преобразует имена в IP', 'Domain Name System') },
                        { id: 224, title: 'Какой порт по умолчанию у HTTP?', content: generateButtons(224, 60, [
                            { text: '80', correct: true },
                            { text: '443', correct: false },
                            { text: '21', correct: false }
                        ], 'Обычно 80', '80') },
                        { id: 225, title: 'Что такое TCP?', content: generateButtons(225, 60, [
                            { text: 'Протокол с установлением соединения', correct: true },
                            { text: 'Протокол без соединения', correct: false },
                            { text: 'Протокол электронной почты', correct: false }
                        ], 'Transmission Control Protocol', 'Протокол с соединением') }
                    ]
                },
                { // ОС
                    name: 'Операционные системы',
                    tasks: [
                        { id: 226, title: 'Какая ОС является открытой?', content: generateButtons(226, 60, [
                            { text: 'Linux', correct: true },
                            { text: 'Windows', correct: false },
                            { text: 'macOS', correct: false }
                        ], 'Open source', 'Linux') },
                        { id: 227, title: 'Что такое ядро ОС?', content: generateButtons(227, 60, [
                            { text: 'Центральная часть ОС, управляющая ресурсами', correct: true },
                            { text: 'Графический интерфейс', correct: false },
                            { text: 'Командная оболочка', correct: false }
                        ], 'Kernel', 'Центральная часть') },
                        { id: 228, title: 'Что такое файловая система?', content: generateButtons(228, 60, [
                            { text: 'Способ организации данных на диске', correct: true },
                            { text: 'Программа для работы с файлами', correct: false },
                            { text: 'Тип файла', correct: false }
                        ], 'FAT32, NTFS, ext4', 'Организация данных') },
                        { id: 229, title: 'Что такое процесс?', content: generateButtons(229, 60, [
                            { text: 'Выполняющаяся программа', correct: true },
                            { text: 'Файл', correct: false },
                            { text: 'Поток данных', correct: false }
                        ], 'Запущенная программа', 'Выполняющаяся программа') },
                        { id: 230, title: 'Что такое многозадачность?', content: generateButtons(230, 60, [
                            { text: 'Одновременное выполнение нескольких задач', correct: true },
                            { text: 'Последовательное выполнение', correct: false },
                            { text: 'Параллелизм', correct: true }
                        ], 'ОС переключает процессы', 'Одновременное выполнение') }
                    ]
                },
                { // Инструменты
                    name: 'Инструменты разработчика',
                    tasks: [
                        { id: 231, title: 'Какая система контроля версий самая популярная?', content: generateButtons(231, 60, [
                            { text: 'Git', correct: true },
                            { text: 'SVN', correct: false },
                            { text: 'Mercurial', correct: false }
                        ], 'git', 'Git') },
                        { id: 232, title: 'Что такое Docker?', content: generateButtons(232, 60, [
                            { text: 'Платформа для контейнеризации', correct: true },
                            { text: 'Виртуальная машина', correct: false },
                            { text: 'Сборщик проектов', correct: false }
                        ], 'Контейнеры', 'Платформа для контейнеризации') },
                        { id: 233, title: 'Какой редактор кода разработан Microsoft?', content: generateButtons(233, 60, [
                            { text: 'VS Code', correct: true },
                            { text: 'Sublime Text', correct: false },
                            { text: 'Atom', correct: false }
                        ], 'Visual Studio Code', 'VS Code') },
                        { id: 234, title: 'Что такое IDE?', content: generateButtons(234, 60, [
                            { text: 'Интегрированная среда разработки', correct: true },
                            { text: 'Текстовый редактор', correct: false },
                            { text: 'Компилятор', correct: false }
                        ], 'Integrated Development Environment', 'Интегрированная среда') },
                        { id: 235, title: 'Что такое CI/CD?', content: generateButtons(235, 60, [
                            { text: 'Непрерывная интеграция и доставка', correct: true },
                            { text: 'Система контроля версий', correct: false },
                            { text: 'Язык программирования', correct: false }
                        ], 'Continuous Integration / Continuous Delivery', 'Непрерывная интеграция и доставка') }
                    ]
                },
                { // Парадигмы
                    name: 'Парадигмы программирования',
                    tasks: [
                        { id: 236, title: 'Что такое императивное программирование?', content: generateButtons(236, 60, [
                            { text: 'Программа описывает последовательность команд', correct: true },
                            { text: 'Программа описывает что нужно сделать, а не как', correct: false },
                            { text: 'Функциональное', correct: false }
                        ], 'Как сделать', 'Последовательность команд') },
                        { id: 237, title: 'Что такое декларативное программирование?', content: generateButtons(237, 60, [
                            { text: 'Программа описывает желаемый результат', correct: true },
                            { text: 'Программа использует классы', correct: false },
                            { text: 'Программа на ассемблере', correct: false }
                        ], 'Что сделать', 'Описывает результат') },
                        { id: 238, title: 'Что такое функциональное программирование?', content: generateButtons(238, 60, [
                            { text: 'Программа строится из чистых функций', correct: true },
                            { text: 'Программа использует объекты', correct: false },
                            { text: 'Программа на Python', correct: false }
                        ], 'Нет побочных эффектов', 'Чистые функции') },
                        { id: 239, title: 'Что такое логическое программирование?', content: generateButtons(239, 60, [
                            { text: 'Программа задаёт факты и правила', correct: true },
                            { text: 'Программа на Java', correct: false },
                            { text: 'Программа на ассемблере', correct: false }
                        ], 'Prolog', 'Факты и правила') },
                        { id: 240, title: 'Что такое процедурное программирование?', content: generateButtons(240, 60, [
                            { text: 'Программа разбивается на процедуры', correct: true },
                            { text: 'Программа состоит из функций', correct: true },
                            { text: 'Программа на C', correct: true }
                        ], 'Подпрограммы', 'Разбиение на процедуры') }
                    ]
                }
            ]
        }
    };

    // ====================== ОСНОВНОЙ ОБЪЕКТ ИГРЫ ======================
    window.game = {
        currentGame: null,
        xp: 0,
        completed: new Set(),
        levelsData: [],

        init() {
            document.querySelectorAll('.game-card').forEach(card => {
                card.addEventListener('click', (e) => {
                    const game = card.dataset.game;
                    this.startGame(game);
                });
            });
            document.getElementById('backToMenu').addEventListener('click', () => this.showMenu());
            document.getElementById('resetProgress').addEventListener('click', () => this.resetProgress());
        },

        startGame(gameId) {
            this.currentGame = gameId;
            this.levelsData = games[gameId].levels;
            this.loadProgress();
            this.renderLevels();
            this.updateUI();
            document.getElementById('menuScreen').style.display = 'none';
            document.getElementById('gameScreen').style.display = 'block';
            document.getElementById('gameTitle').innerText = games[gameId].name;
        },

        showMenu() {
            document.getElementById('menuScreen').style.display = 'block';
            document.getElementById('gameScreen').style.display = 'none';
        },

        loadProgress() {
            const key = `progress_${this.currentGame}`;
            const saved = localStorage.getItem(key);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    this.xp = data.xp || 0;
                    this.completed = new Set(data.completed || []);
                } catch (e) {}
            } else {
                this.xp = 0;
                this.completed.clear();
            }
        },

        saveProgress() {
            const key = `progress_${this.currentGame}`;
            const data = {
                xp: this.xp,
                completed: Array.from(this.completed)
            };
            localStorage.setItem(key, JSON.stringify(data));
        },

        renderLevels() {
            const container = document.getElementById('levelsContainer');
            container.innerHTML = '';
            this.levelsData.forEach(level => {
                const card = document.createElement('div');
                card.className = 'levelCard';
                const heading = document.createElement('h2');
                heading.textContent = level.name;
                card.appendChild(heading);
                level.tasks.forEach(task => {
                    const wrap = document.createElement('div');
                    wrap.className = 'task-wrap';
                    const taskDiv = document.createElement('div');
                    taskDiv.className = `task ${this.completed.has(task.id) ? 'completed' : ''}`;
                    taskDiv.textContent = task.title;
                    const panel = document.createElement('div');
                    panel.className = 'task-panel';
                    panel.innerHTML = `
                        ${task.content}
                        <div style="margin-top:14px">
                            <button type="button" class="task-panel-close">Свернуть</button>
                        </div>
                    `;
                    const closePanel = () => {
                        panel.classList.remove('is-open');
                    };
                    panel.querySelector('.task-panel-close').addEventListener('click', (e) => {
                        e.stopPropagation();
                        closePanel();
                    });
                    if (!this.completed.has(task.id)) {
                        taskDiv.addEventListener('click', () => {
                            card.querySelectorAll('.task-panel.is-open').forEach(p => {
                                if (p !== panel) p.classList.remove('is-open');
                            });
                            const willOpen = !panel.classList.contains('is-open');
                            panel.classList.toggle('is-open', willOpen);
                            if (willOpen) {
                                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            }
                        });
                    }
                    wrap.appendChild(taskDiv);
                    wrap.appendChild(panel);
                    card.appendChild(wrap);
                });
                container.appendChild(card);
            });
        },

        closeTask() {
            document.querySelectorAll('.task-panel.is-open').forEach(p => p.classList.remove('is-open'));
            const gameArea = document.getElementById('gameArea');
            if (gameArea) gameArea.style.display = 'none';
        },

        handleAnswer(id, xp, isCorrect, btnElement) {
            if (this.completed.has(id)) {
                alert('Это задание уже выполнено!');
                return;
            }
            if (isCorrect) {
                btnElement.classList.add('correct');
                setTimeout(() => {
                    this.addXP(xp, id);
                    this.closeTask();
                }, 500);
            } else {
                btnElement.classList.add('incorrect');
                setTimeout(() => btnElement.classList.remove('incorrect'), 500);
                alert('Неправильно!');
            }
        },

        checkText(id, xp, elementId, keyword) {
            if (this.completed.has(id)) return;
            const val = document.getElementById(elementId)?.value || '';
            if (val.includes(keyword)) {
                alert('Верно!');
                this.addXP(xp, id);
                this.closeTask();
            } else {
                alert('Ошибка, попробуйте ещё.');
            }
        },

        checkEven(id, xp) {
            if (this.completed.has(id)) return;
            const val = parseInt(document.getElementById(`num${id}`)?.value);
            if (!isNaN(val) && val % 2 === 0) {
                alert('Чётное!');
                this.addXP(xp, id);
                this.closeTask();
            } else {
                alert('Нечётное или не число');
            }
        },

        handleClickXP(xp, id) {
            if (this.completed.has(id)) return;
            this.addXP(xp, id);
            this.closeTask();
        },

        handleRandom(xp, id) {
            if (this.completed.has(id)) return;
            const r = Math.floor(Math.random() * 10);
            alert(`Сгенерировано: ${r}`);
            this.addXP(xp, id);
            this.closeTask();
        },

        startTimer(id) {
            if (this.completed.has(id)) return;
            let time = 3;
            const el = document.getElementById('timer10');
            const interval = setInterval(() => {
                el.innerText = `Осталось: ${time}`;
                time--;
                if (time < 0) {
                    clearInterval(interval);
                    el.innerText = 'Готово!';
                    this.addXP(30, id);
                    this.closeTask();
                }
            }, 1000);
        },

        reactionTest(id) {
            if (this.completed.has(id)) return;
            setTimeout(() => {
                this.addXP(50, id);
                alert('Отличная реакция');
                this.closeTask();
            }, 1000);
        },

        checkGuess(id, xp) {
            if (this.completed.has(id)) return;
            const guess = parseInt(document.getElementById('guess13')?.value);
            if (guess === 5) {
                this.addXP(xp, id);
                alert('Верно!');
                this.closeTask();
            } else {
                alert('Неверно');
            }
        },

        showHint(hint) {
            alert('💡 Подсказка: ' + hint);
        },

        showAnswer(answer, id) {
            const display = document.getElementById(`answerDisplay-${id}`);
            if (display) {
                display.style.display = 'block';
                display.innerText = '✅ Правильный ответ: ' + answer;
            } else {
                alert('Ответ: ' + answer);
            }
        },

        addXP(amount, id) {
            if (this.completed.has(id)) return;
            this.completed.add(id);
            this.xp += amount;
            this.saveProgress();
            this.updateUI();
            this.renderLevels();
        },

        updateUI() {
            let level = "Школьник";
            let badge = "Level 1";
            let percent = 0;
            const xp = this.xp;

            if (xp >= 1200) { level = "Pro Developer"; badge = "Level 6"; percent = ((xp - 1200) / 300) * 100; }
            else if (xp >= 800) { level = "Middle"; badge = "Level 5"; percent = ((xp - 800) / 400) * 100; }
            else if (xp >= 500) { level = "Junior"; badge = "Level 4"; percent = ((xp - 500) / 300) * 100; }
            else if (xp >= 250) { level = "Стажёр"; badge = "Level 3"; percent = ((xp - 250) / 250) * 100; }
            else if (xp >= 100) { level = "Новичок"; badge = "Level 2"; percent = ((xp - 100) / 150) * 100; }
            else percent = xp;

            if (percent > 100) percent = 100;

            document.getElementById("xp").innerText = xp;
            document.getElementById("levelName").innerText = level;
            document.getElementById("rankBadge").innerText = badge;
            document.getElementById("xpBar").style.width = percent + "%";
        },

        finishGame() {
            if (this.xp >= 1200) {
                alert("🔥 Поздравляем! Ты стал Pro Developer!");
                document.getElementById('gameArea').innerHTML = `
                    <div style="text-align:center; background:gold; padding:30px; border-radius:20px;">
                        <h2>🏆 ДИПЛОМ PRO MAX 🏆</h2>
                        <p>Выдаётся за выдающиеся достижения</p>
                    </div>
                `;
                document.getElementById('gameArea').style.display = 'block';
            } else {
                alert("Нужно больше XP!");
            }
        },

        resetProgress() {
            if (confirm('Сбросить прогресс текущей игры?')) {
                this.xp = 0;
                this.completed.clear();
                this.saveProgress();
                this.renderLevels();
                this.updateUI();
            }
        }
    };

    // Инициализация
    window.addEventListener('load', () => {
        document.querySelectorAll('[data-sim-instruction]').forEach((outer) => {
            const btn = outer.querySelector('.sim-instruction-toggle');
            if (!btn) return;
            const expandLabel = btn.dataset.labelShow || 'Развернуть';
            const collapseLabel = btn.dataset.labelHide || 'Свернуть';
            btn.addEventListener('click', () => {
                const collapsed = outer.classList.toggle('is-collapsed');
                btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                btn.textContent = collapsed ? expandLabel : collapseLabel;
            });
        });
        window.game.init();
        // Привязываем методы к объекту game для использования в onclick
        const methods = [
            'handleAnswer', 'checkText', 'checkEven', 'handleClickXP',
            'handleRandom', 'startTimer', 'reactionTest', 'checkGuess',
            'showHint', 'showAnswer', 'finishGame', 'closeTask'
        ];
        methods.forEach(m => {
            window.game[m] = window.game[m].bind(window.game);
        });
    });
})();
</script>
<?php require_once __DIR__ . '/footer.php'; ?>