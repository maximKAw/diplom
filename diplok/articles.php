<?php
// articles.php – список всех статей с фильтрацией и поиском
require_once 'header.php';

// Функция нормализации пути к изображению
function normalizeImagePath($path) {
    if (empty($path)) return '';
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#^diplok/#', '', $path);
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return $path;
}

// Получаем уникальные категории для фильтра
$categories = [];
$catRes = $db->query("SELECT DISTINCT category_name FROM articles WHERE category_name IS NOT NULL AND category_name != '' ORDER BY category_name");
if ($catRes) {
    while ($row = $catRes->fetch_assoc()) {
        $categories[] = $row['category_name'];
    }
}

// Получаем параметры фильтрации и сортировки
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest'; // newest, oldest, popular

// Базовый SQL запрос
$sql = "SELECT * FROM articles WHERE is_published = 1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR content LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if (!empty($category)) {
    $sql .= " AND category_name = ?";
    $params[] = $category;
    $types .= "s";
}

// Сортировка
switch ($sort) {
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    case 'popular':
        $sql .= " ORDER BY views DESC, created_at DESC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY created_at DESC";
        break;
}

$stmt = $db->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<style>
    :root {
        --primary: #410cdf;
        --primary-light: #5b2ef0;
        --surface: #ffffff;
        --text: #0f172a;
        --text-light: #475569;
        --border: rgba(65, 12, 223, 0.14);
        --shadow-sm: 0 4px 16px rgba(65, 12, 223, 0.08);
        --shadow: 0 16px 40px rgba(65, 12, 223, 0.14);
        --radius: 24px;
        --radius-sm: 16px;
    }

    body {
        background: #f8fafc;
    }

    .articles-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 100px 20px 40px;
        min-height: calc(100vh - 80px);
    }

    .articles-page__title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 2rem;
        position: relative;
        display: inline-block;
    }

    .articles-page__title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60px;
        height: 4px;
        background: var(--primary);
        border-radius: 4px;
    }

    /* Фильтры */
    .filters-section {
        background: var(--surface);
        padding: 1.5rem;
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-sm);
        margin-bottom: 2rem;
        border: 1px solid var(--border);
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1 1 200px;
    }

    .filter-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.3rem;
        color: var(--text);
        font-size: 0.9rem;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 0.6rem 1rem;
        border: 1px solid var(--border);
        border-radius: 40px;
        font-size: 0.95rem;
        background: var(--surface);
        color: var(--text);
        transition: border-color 0.2s;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: var(--primary);
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .btn {
        display: inline-block;
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        box-shadow: var(--shadow-sm);
        text-decoration: none;
        text-align: center;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        box-shadow: var(--shadow);
        transform: translateY(-1px);
    }

    .btn-outline {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text);
    }

    .btn-outline:hover {
        background: #f1f5f9;
        border-color: var(--text-light);
    }

    .btn-sm {
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
    }

    /* Сетка статей */
    .articles-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin: 2rem 0;
    }

    @media (max-width: 1200px) {
        .articles-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 900px) {
        .articles-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .articles-grid {
            grid-template-columns: 1fr;
        }
    }

    .article-card {
        display: block;
        background: var(--surface);
        border-radius: var(--radius-sm);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
        border: 1px solid var(--border);
    }

    .article-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow);
        border-color: var(--primary);
    }

    .article-card__img-wrapper {
        display: block;
        height: 160px;
        overflow: hidden;
        background: #f1f5f9;
        position: relative;
    }

    .article-card__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .article-card:hover .article-card__img {
        transform: scale(1.03);
    }

    .article-card__no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--text-light);
    }

    .article-card__body {
        padding: 1.25rem;
        display: block;
    }

    .article-card__title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text);
        display: block;
        line-height: 1.4;
        transition: color 0.2s;
    }

    .article-card:hover .article-card__title {
        color: var(--primary);
    }

    .article-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        font-size: 0.8rem;
        color: var(--text-light);
        margin-bottom: 0.7rem;
    }

    .article-card__meta i {
        margin-right: 0.3rem;
        color: var(--primary);
    }

    .article-card__desc {
        font-size: 0.9rem;
        color: var(--text-light);
        margin-bottom: 1rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .article-card__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: var(--text-light);
        border-top: 1px solid var(--border);
        padding-top: 0.8rem;
    }

    .tag--bg {
        background: #f1f5f9;
        padding: 0.2rem 0.8rem;
        border-radius: 40px;
        color: var(--text);
        font-weight: 500;
        font-size: 0.75rem;
        border: 1px solid var(--border);
    }

    .text-center {
        text-align: center;
        color: var(--text-light);
        grid-column: 1 / -1;
        padding: 3rem;
        background: var(--surface);
        border-radius: var(--radius-sm);
        border: 1px dashed var(--border);
    }

    .stats-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        color: var(--text-light);
    }
</style>

<div class="articles-page">
    <h1 class="articles-page__title">Все статьи</h1>

    <!-- Блок фильтрации и поиска -->
    <div class="filters-section">
        <form method="get" class="filter-form">
            <div class="filter-group">
                <label for="search"><i class="fas fa-search"></i> Поиск</label>
                <input type="text" id="search" name="search" placeholder="Название, описание..." value="<?= htmlspecialchars($search) ?>">
            </div>
            
            <?php if (!empty($categories)): ?>
            <div class="filter-group">
                <label for="category"><i class="fas fa-folder"></i> Категория</label>
                <select id="category" name="category">
                    <option value="">Все категории</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="filter-group">
                <label for="sort"><i class="fas fa-sort-amount-down"></i> Сортировка</label>
                <select id="sort" name="sort">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Сначала новые</option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Сначала старые</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Популярные (по просмотрам)</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Применить</button>
                <?php if ($search || $category || $sort !== 'newest'): ?>
                    <a href="articles.php" class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Сбросить</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Сетка статей -->
    <div class="articles-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($article = $result->fetch_assoc()): 
                $image_url = normalizeImagePath($article['image_path'] ?? '');
            ?>
                <a href="article.php?id=<?= $article['id'] ?>" class="article-card">
                    <span class="article-card__img-wrapper">
                        <?php if (!empty($image_url) && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_url)): ?>
                            <img class="article-card__img" src="<?= htmlspecialchars($image_url) ?>" 
                                 alt="<?= htmlspecialchars($article['title']) ?>"
                                 onerror="this.onerror=null; this.src='/photos/article-placeholder.jpg';">
                        <?php else: ?>
                            <img class="article-card__img" src="/photos/article-placeholder.jpg" alt="Заглушка">
                        <?php endif; ?>
                    </span>
                    
                    <span class="article-card__body">
                        <span class="article-card__title"><?= htmlspecialchars($article['title']) ?></span>
                        
                        <span class="article-card__meta">
                            <?php if (!empty($article['author'])): ?>
                                <span><i class="fas fa-user"></i> <?= htmlspecialchars($article['author']) ?></span>
                            <?php endif; ?>
                            <span><i class="fas fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($article['created_at'])) ?></span>
                            <?php if ($article['reading_time']): ?>
                                <span><i class="fas fa-clock"></i> <?= $article['reading_time'] ?> мин</span>
                            <?php endif; ?>
                            <span><i class="fas fa-eye"></i> <?= $article['views'] ?></span>
                        </span>
                        
                        <span class="article-card__desc">
                            <?= htmlspecialchars(mb_substr($article['description'] ?? '', 0, 150)) ?>...
                        </span>
                        
                        <span class="article-card__footer">
                            <?php if (!empty($article['category_name'])): ?>
                                <span class="tag--bg"><?= htmlspecialchars($article['category_name']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($article['tags'])): ?>
                                <span class="stats-badge" title="Теги"><?= htmlspecialchars(implode(', ', array_slice(explode(',', $article['tags']), 0, 2))) ?>…</span>
                            <?php endif; ?>
                        </span>
                    </span>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">По вашему запросу ничего не найдено.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>