<?php
// article.php – детальная страница статьи
require_once 'header.php';

function normalizeImagePath($path) {
    if (empty($path)) return '';
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#^diplok/#', '', $path);
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return $path;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Увеличиваем счётчик просмотров
$stmt = $db->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Получаем статью
$stmt = $db->prepare("SELECT * FROM articles WHERE id = ? AND is_published = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();
$stmt->close();

if (!$article) {
    header('HTTP/1.0 404 Not Found');
    echo '<div style="max-width:900px; margin:100px auto; text-align:center;"><h1>Статья не найдена</h1><p><a href="articles.php" class="btn btn-primary">Вернуться к списку статей</a></p></div>';
    require_once 'footer.php';
    exit;
}

$image_url = normalizeImagePath($article['image_path'] ?? '');
?>

<style>
    :root {
        --primary: #2563eb;
        --primary-light: #3b82f6;
        --surface: #ffffff;
        --text: #0f172a;
        --text-light: #475569;
        --border: #e2e8f0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow: 0 4px 12px rgba(0,0,0,0.08);
        --radius: 24px;
        --radius-sm: 12px;
    }

    body {
        background: #f8fafc;
    }

    .article-container {
        max-width: 900px;
        margin: 100px auto 40px;
        background: var(--surface);
        border-radius: var(--radius);
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }

    .article-header {
        margin-bottom: 2rem;
    }

    .article-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 0.5rem;
        line-height: 1.2;
    }

    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        color: var(--text-light);
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
        padding-bottom: 1.5rem;
    }

    .article-meta i {
        margin-right: 0.4rem;
        color: var(--primary);
    }

    .article-meta-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .article-category {
        background: #f1f5f9;
        padding: 0.2rem 1rem;
        border-radius: 40px;
        display: inline-block;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text);
        border: 1px solid var(--border);
    }

    .tags {
        margin-top: 0.5rem;
    }

    .tag {
        display: inline-block;
        background: #f1f5f9;
        padding: 0.2rem 0.8rem;
        border-radius: 40px;
        font-size: 0.8rem;
        color: var(--text);
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        border: 1px solid var(--border);
    }

    .article-image {
        margin-bottom: 2rem;
        border-radius: var(--radius-sm);
        overflow: hidden;
        max-height: 500px;
        display: flex;
        justify-content: center;
        background: #f1f5f9;
        border: 1px solid var(--border);
    }

    .article-image img {
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }

    .article-description {
        font-size: 1.1rem;
        color: var(--text);
        background: #f1f5f9;
        padding: 1.5rem;
        border-radius: var(--radius-sm);
        margin-bottom: 2rem;
        border-left: 4px solid var(--primary);
        font-style: italic;
        border: 1px solid var(--border);
    }

    /* Умеренные стили для контента */
    .article-content {
        font-size: 1rem;
        line-height: 1.6;
        color: var(--text);
    }

    .article-content p {
        margin-bottom: 1rem;
    }

    .article-content h2,
    .article-content h3,
    .article-content h4 {
        color: var(--primary);
        margin-top: 1.8rem;
        margin-bottom: 0.8rem;
        font-weight: 600;
    }

    .article-content h2 {
        font-size: 1.8rem;
    }

    .article-content h3 {
        font-size: 1.5rem;
    }

    .article-content ul,
    .article-content ol {
        margin-bottom: 1rem;
        padding-left: 1.8rem;
    }

    .article-content li {
        margin-bottom: 0.2rem;
    }

    .article-content pre {
        background: #f1f5f9;
        padding: 1rem;
        border-radius: var(--radius-sm);
        overflow-x: auto;
        margin-bottom: 1rem;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        border: 1px solid var(--border);
    }

    .article-content code {
        background: #f1f5f9;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        color: #d63384;
    }

    .article-content blockquote {
        border-left: 4px solid var(--primary);
        padding: 0.3rem 1.2rem;
        margin: 1rem 0;
        background: #f1f5f9;
        font-style: italic;
        color: var(--text-light);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }

    .article-footer {
        margin-top: 3rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .btn {
        display: inline-block;
        padding: 0.7rem 1.8rem;
        border-radius: 40px;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        box-shadow: var(--shadow-sm);
        text-decoration: none;
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

    .share-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .share-buttons a {
        color: var(--text-light);
        font-size: 1.3rem;
        transition: color 0.2s;
    }

    .share-buttons a:hover {
        color: var(--primary);
    }

    @media (max-width: 768px) {
        .article-container {
            margin: 90px 15px 30px;
            padding: 1.5rem;
        }

        .article-title {
            font-size: 1.8rem;
        }

        .article-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .share-buttons {
            margin-top: 0.5rem;
        }
    }
</style>

<div class="article-container">
    <div class="article-header">
        <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
        
        <div class="article-meta">
            <?php if (!empty($article['author'])): ?>
                <span class="article-meta-item"><i class="fas fa-user"></i> <?= htmlspecialchars($article['author']) ?></span>
            <?php endif; ?>
            
            <span class="article-meta-item"><i class="fas fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($article['created_at'])) ?></span>
            
            <?php if ($article['reading_time']): ?>
                <span class="article-meta-item"><i class="fas fa-clock"></i> <?= (int)$article['reading_time'] ?> мин чтения</span>
            <?php endif; ?>
            
            <span class="article-meta-item"><i class="fas fa-eye"></i> <?= (int)$article['views'] ?> просмотров</span>
            
            <?php if (!empty($article['category_name'])): ?>
                <span class="article-category"><?= htmlspecialchars($article['category_name']) ?></span>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($article['tags'])): ?>
            <div class="tags">
                <?php 
                $tags = explode(',', $article['tags']);
                foreach ($tags as $tag): 
                    $tag = trim($tag);
                    if (!empty($tag)):
                ?>
                    <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($image_url)): ?>
        <div class="article-image">
            <img src="<?= htmlspecialchars($image_url) ?>" 
                 alt="<?= htmlspecialchars($article['title']) ?>"
                 onerror="this.onerror=null; this.src='/photos/article-placeholder.jpg';">
        </div>
    <?php else: ?>
        <div class="article-image">
            <img src="/photos/article-placeholder.jpg" alt="Заглушка">
        </div>
    <?php endif; ?>

    <?php if (!empty($article['description'])): ?>
        <div class="article-description">
            <?= nl2br(htmlspecialchars($article['description'])) ?>
        </div>
    <?php endif; ?>

    <div class="article-content">
        <?= $article['content'] ?? '' ?>
    </div>

    <div class="article-footer">
        <a href="articles.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> К списку статей</a>
        <div class="share-buttons">
            <span style="color: var(--text-light); margin-right:0.5rem;">Поделиться:</span>
            <a href="https://vk.com/share.php?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" title="ВКонтакте"><i class="fab fa-vk"></i></a>
            <a href="https://t.me/share/url?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($article['title']) ?>" target="_blank" title="Telegram"><i class="fab fa-telegram-plane"></i></a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>