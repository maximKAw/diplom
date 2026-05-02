<?php
require_once 'header.php';

// Получаем уникальные категории (разбиваем по запятой, чтобы получить отдельные значения)
$categories = [];
$cat_res = $db->query("SELECT DISTINCT category FROM languages WHERE category IS NOT NULL AND category != ''");
if ($cat_res) {
    while ($row = $cat_res->fetch_assoc()) {
        $parts = array_map('trim', explode(',', $row['category']));
        foreach ($parts as $part) {
            if (!empty($part) && !in_array($part, $categories)) {
                $categories[] = $part;
            }
        }
    }
    sort($categories);
}

// Уникальные уровни сложности
$difficulties = [];
$diff_res = $db->query("SELECT DISTINCT difficulty FROM languages WHERE difficulty IS NOT NULL");
if ($diff_res) {
    while ($row = $diff_res->fetch_assoc()) {
        $difficulties[] = $row['difficulty'];
    }
}

// Получаем значения фильтров из GET
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$selected_difficulty = isset($_GET['difficulty']) ? trim($_GET['difficulty']) : '';

// Базовый запрос
$sql = "SELECT * FROM languages WHERE 1=1";
$params = [];
$types = "";

if (!empty($selected_category)) {
    $sql .= " AND category LIKE ?";
    $params[] = '%' . $selected_category . '%';
    $types .= "s";
}
if (!empty($selected_difficulty)) {
    $sql .= " AND difficulty = ?";
    $params[] = $selected_difficulty;
    $types .= "s";
}

$sql .= " ORDER BY popularity_rank, name";

$stmt = $db->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<div class="app-shell app-shell--lift">
    <!-- Фильтры -->
    <form method="get" class="app-filter-form">
        <div class="filter-group">
            <label for="category">Категория</label>
            <select name="category" id="category">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $selected_category === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="difficulty">Сложность</label>
            <select name="difficulty" id="difficulty">
                <option value="">Любая</option>
                <option value="easy" <?= $selected_difficulty === 'easy' ? 'selected' : '' ?>>Лёгкий</option>
                <option value="medium" <?= $selected_difficulty === 'medium' ? 'selected' : '' ?>>Средний</option>
                <option value="hard" <?= $selected_difficulty === 'hard' ? 'selected' : '' ?>>Сложный</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Применить</button>
            <?php if (!empty($selected_category) || !empty($selected_difficulty)): ?>
                <a href="languages.php" class="btn btn-outline">Сбросить</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="languages-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($lang = $result->fetch_assoc()): ?>
                <?php 
                $image_url = '';
                if (!empty($lang['image_path'])) {
                    $image_url = str_replace('\\', '/', $lang['image_path']);
                    $image_url = preg_replace('#^diplok/#', '', $image_url);
                    if ($image_url[0] !== '/') {
                        $image_url = '/' . $image_url;
                    }
                }
                $diff_class = $lang['difficulty'] ?? '';
                ?>
                <div class="language-card">
                    <?php if (!empty($image_url)): ?>
                        <div class="card-image">
                            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($lang['name']) ?>" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\'fas fa-code\'></i>'; this.parentNode.classList.add('no-image');">
                        </div>
                    <?php else: ?>
                        <div class="card-image no-image">
                            <i class="fas fa-code"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-content">
                        <h3 class="card-title">
                            <a href="language.php?id=<?= $lang['id'] ?>"><?= htmlspecialchars($lang['name']) ?></a>
                        </h3>
                        
                        <?php if (!empty($lang['category'])): ?>
                            <div class="card-category"><?= htmlspecialchars($lang['category']) ?></div>
                        <?php endif; ?>
                        
                        <p class="card-description">
                            <?= htmlspecialchars(mb_substr($lang['description'] ?? '', 0, 120)) ?>...
                        </p>
                        
                        <div class="card-meta">
                            <?php if (!empty($lang['paradigm'])): ?>
                                <span><?= htmlspecialchars(mb_substr($lang['paradigm'], 0, 20)) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($lang['typing'])): ?>
                                <span><?= htmlspecialchars($lang['typing']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($lang['year_created'])): ?>
                                <span><?= $lang['year_created'] ?> г.</span>
                            <?php endif; ?>
                            <?php if (!empty($lang['difficulty'])): ?>
                                <span class="badge-difficulty <?= $diff_class ?>">
                                    <?php 
                                        $diff_text = ['easy' => 'Лёгкий', 'medium' => 'Средний', 'hard' => 'Сложный'];
                                        echo $diff_text[$lang['difficulty']] ?? $lang['difficulty'];
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="language.php?id=<?= $lang['id'] ?>" class="btn btn-primary">Подробнее</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="app-empty">Языки программирования по заданным фильтрам не найдены.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>