<?php
// profession.php – детальная страница профессии
require_once 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM professions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$prof = $result->fetch_assoc();
$stmt->close();

if (!$prof) {
    header('HTTP/1.0 404 Not Found');
    echo '<div class="profession-detail" style="text-align:center; padding:3rem;"><h1>Профессия не найдена</h1><p><a href="professions.php" class="btn btn-primary">Вернуться к списку</a></p></div>';
    require_once 'footer.php';
    exit;
}

// Преобразуем путь к изображению
$image_url = '';
if (!empty($prof['image_path'])) {
    $image_url = str_replace('\\', '/', $prof['image_path']);
    $image_url = preg_replace('#^diplok/#', '', $image_url);
    if ($image_url[0] !== '/') {
        $image_url = '/' . $image_url;
    }
}
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

    .profession-detail {
        max-width: 1000px;
        margin: 100px auto 40px;
        background: var(--surface);
        border-radius: var(--radius);
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }

    .detail-header {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .detail-image {
        width: 200px;
        height: 200px;
        border-radius: var(--radius-sm);
        overflow: hidden;
        background: #f1f5f9;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
    }

    .detail-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .detail-image.no-image {
        color: var(--text-light);
        font-size: 4rem;
    }

    .detail-title-block h1 {
        margin: 0 0 0.5rem;
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--text);
        line-height: 1.2;
    }

    .detail-category {
        font-size: 1rem;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-salary {
        background: #f1f5f9;
        padding: 0.75rem 1.5rem;
        border-radius: 40px;
        display: inline-block;
        margin-bottom: 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: #10b981;
        border: 1px solid var(--border);
    }

    .detail-salary i {
        margin-right: 0.5rem;
        color: #10b981;
    }

    .detail-section {
        margin-bottom: 2rem;
    }

    .detail-section h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 1rem;
        border-bottom: 2px solid var(--border);
        padding-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-section h2 i {
        color: var(--primary);
        font-size: 1.3rem;
    }

    /* Стили для форматированного HTML-контента */
    .full-description {
        line-height: 1.6;
        font-size: 1rem;
        color: var(--text);
    }

    .full-description h2,
    .full-description h3,
    .full-description h4 {
        color: var(--primary);
        margin: 1.5rem 0 0.75rem;
        font-weight: 600;
    }

    .full-description h2 {
        font-size: 1.4rem;
        border-bottom: 1px solid var(--border);
        padding-bottom: 0.3rem;
    }

    .full-description h3 {
        font-size: 1.2rem;
    }

    .full-description ul,
    .full-description ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }

    .full-description li {
        margin-bottom: 0.3rem;
    }

    .full-description p {
        margin-bottom: 1rem;
    }

    .full-description strong {
        color: var(--primary);
    }

    .full-description table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
    }

    .full-description th,
    .full-description td {
        border: 1px solid var(--border);
        padding: 0.5rem 0.75rem;
        text-align: left;
    }

    .full-description th {
        background: #f1f5f9;
        color: var(--text);
        font-weight: 600;
    }

    .full-description pre {
        background: #f1f5f9;
        padding: 1rem;
        border-radius: var(--radius-sm);
        overflow-x: auto;
        margin-bottom: 1.5rem;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        border: 1px solid var(--border);
    }

    .full-description code {
        background: #f1f5f9;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        color: #d63384;
    }

    .full-description blockquote {
        border-left: 4px solid var(--primary);
        padding: 0.5rem 1.5rem;
        margin: 1.5rem 0;
        background: #f1f5f9;
        font-style: italic;
        color: var(--text-light);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }

    .detail-actions {
        margin-top: 3rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-block;
        padding: 0.7rem 1.8rem;
        border-radius: 40px;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        text-decoration: none;
        font-size: 0.95rem;
        box-shadow: var(--shadow-sm);
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

    @media (max-width: 768px) {
        .profession-detail {
            margin: 90px 15px 30px;
            padding: 1.5rem;
        }

        .detail-header {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .detail-image {
            width: 160px;
            height: 160px;
        }

        .detail-title-block h1 {
            font-size: 1.8rem;
        }

        .detail-actions {
            justify-content: center;
        }
    }
</style>

<div class="profession-detail">
    <div class="detail-header">
        <?php if (!empty($image_url)): ?>
            <div class="detail-image">
                <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($prof['title']) ?>" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\'fas fa-briefcase\'></i>'; this.parentNode.classList.add('no-image');">
            </div>
        <?php else: ?>
            <div class="detail-image no-image">
                <i class="fas fa-briefcase"></i>
            </div>
        <?php endif; ?>
        
        <div class="detail-title-block">
            <h1><?= htmlspecialchars($prof['title']) ?></h1>
            <?php if (!empty($prof['category'])): ?>
                <div class="detail-category"><?= htmlspecialchars($prof['category']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($prof['salary_min'] || $prof['salary_max']): ?>
        <div class="detail-salary">
            <i class="fas fa-ruble-sign"></i>
            <?php if ($prof['salary_min'] && $prof['salary_max']): ?>
                <?= number_format($prof['salary_min'], 0, '', ' ') ?> – <?= number_format($prof['salary_max'], 0, '', ' ') ?> ₽
            <?php elseif ($prof['salary_min']): ?>
                от <?= number_format($prof['salary_min'], 0, '', ' ') ?> ₽
            <?php elseif ($prof['salary_max']): ?>
                до <?= number_format($prof['salary_max'], 0, '', ' ') ?> ₽
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($prof['short_description'])): ?>
        <div class="detail-section">
            <h2><i class="fas fa-info-circle"></i> Кратко о профессии</h2>
            <p><?= nl2br(htmlspecialchars($prof['short_description'])) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($prof['full_description'])): ?>
        <div class="detail-section">
            <h2><i class="fas fa-align-left"></i> Подробное описание</h2>
            <div class="full-description">
                <?= $prof['full_description'] ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="detail-actions">
        <a href="professions.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> К списку профессий</a>
    </div>
</div>

<?php require_once 'footer.php'; ?>