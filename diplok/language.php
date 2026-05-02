<?php
require_once 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM languages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$lang = $result->fetch_assoc();
$stmt->close();

if (!$lang) {
    header('HTTP/1.0 404 Not Found');
    echo '<div style="max-width:1000px; margin:100px auto; text-align:center;"><h1>Язык не найден</h1><p><a href="languages.php" class="btn btn-primary">Вернуться к списку</a></p></div>';
    require_once 'footer.php';
    exit;
}

// Преобразуем путь к изображению
$image_url = '';
if (!empty($lang['image_path'])) {
    $image_url = str_replace('\\', '/', $lang['image_path']);
    $image_url = preg_replace('#^diplok/#', '', $image_url);
    if ($image_url[0] !== '/') {
        $image_url = '/' . $image_url;
    }
}

// Разбиваем преимущества и недостатки на массивы
$pros = !empty($lang['pros']) ? explode("\n", trim($lang['pros'])) : [];
$cons = !empty($lang['cons']) ? explode("\n", trim($lang['cons'])) : [];
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

    .language-detail {
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
        object-fit: contain;
        padding: 10px;
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
        margin-bottom: 1rem;
    }

    .detail-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    .detail-meta-item {
        background: #f1f5f9;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        font-size: 0.9rem;
        color: var(--text);
        border: 1px solid var(--border);
    }

    .detail-meta-item strong {
        color: var(--primary);
        margin-right: 4px;
    }

    .detail-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: var(--surface);
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
    }

    .detail-section h2 {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--text);
        margin-top: 0;
        margin-bottom: 1rem;
        border-bottom: 2px solid var(--border);
        padding-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-section h2 i {
        color: var(--primary);
        font-size: 1.2rem;
    }

    .full-description {
        line-height: 1.6;
        color: var(--text);
        white-space: pre-wrap;
        font-size: 1rem;
    }

    /* Списки преимуществ и недостатков */
    .pros-list, .cons-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pros-list li, .cons-list li {
        margin-bottom: 0.75rem;
        padding-left: 1.8rem;
        position: relative;
        line-height: 1.6;
        color: var(--text);
    }

    .pros-list li::before {
        content: "✓";
        color: #10b981;
        font-weight: bold;
        position: absolute;
        left: 0;
    }

    .cons-list li::before {
        content: "•";
        color: #ef4444;
        font-weight: bold;
        position: absolute;
        left: 0;
    }

    .detail-actions {
        margin-top: 2rem;
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
        .language-detail {
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
    }
</style>

<div class="language-detail">
    <div class="detail-header">
        <?php if (!empty($image_url)): ?>
            <div class="detail-image">
                <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($lang['name']) ?>" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\'fas fa-code\'></i>'; this.parentNode.classList.add('no-image');">
            </div>
        <?php else: ?>
            <div class="detail-image no-image">
                <i class="fas fa-code"></i>
            </div>
        <?php endif; ?>
        
        <div class="detail-title-block">
            <h1><?= htmlspecialchars($lang['name']) ?></h1>
            <?php if (!empty($lang['category'])): ?>
                <div class="detail-category"><?= htmlspecialchars($lang['category']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Мета-информация -->
    <div class="detail-meta">
        <?php if (!empty($lang['year_created'])): ?>
            <span class="detail-meta-item"><strong>Год:</strong> <?= (int)$lang['year_created'] ?></span>
        <?php endif; ?>
        <?php if (!empty($lang['creator'])): ?>
            <span class="detail-meta-item"><strong>Создатель:</strong> <?= htmlspecialchars($lang['creator']) ?></span>
        <?php endif; ?>
        <?php if (!empty($lang['paradigm'])): ?>
            <span class="detail-meta-item"><strong>Парадигма:</strong> <?= htmlspecialchars($lang['paradigm']) ?></span>
        <?php endif; ?>
        <?php if (!empty($lang['typing'])): ?>
            <span class="detail-meta-item"><strong>Типизация:</strong> <?= htmlspecialchars($lang['typing']) ?></span>
        <?php endif; ?>
        <?php if (!empty($lang['popularity_rank'])): ?>
            <span class="detail-meta-item"><strong>Популярность:</strong> <?= (int)$lang['popularity_rank'] ?></span>
        <?php endif; ?>
        <?php if (!empty($lang['difficulty'])): ?>
            <span class="detail-meta-item"><strong>Сложность:</strong> 
                <?php 
                    $diff_text = ['easy' => 'Лёгкий', 'medium' => 'Средний', 'hard' => 'Сложный'];
                    echo $diff_text[$lang['difficulty']] ?? $lang['difficulty'];
                ?>
            </span>
        <?php endif; ?>
    </div>

    <!-- Краткое описание -->
    <?php if (!empty($lang['description'])): ?>
        <div class="detail-section">
            <h2><i class="fas fa-info-circle"></i> Краткое описание</h2>
            <p><?= nl2br(htmlspecialchars($lang['description'])) ?></p>
        </div>
    <?php endif; ?>

    <!-- Полное описание -->
    <?php if (!empty($lang['full_description'])): ?>
        <div class="detail-section">
            <h2><i class="fas fa-align-left"></i> Подробнее о языке</h2>
            <div class="full-description">
                <?= nl2br(htmlspecialchars($lang['full_description'])) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Преимущества -->
    <?php if (!empty($pros)): ?>
        <div class="detail-section">
            <h2><i class="fas fa-thumbs-up"></i> Преимущества</h2>
            <ul class="pros-list">
                <?php foreach ($pros as $pro): ?>
                    <?php $pro = trim($pro); if (!empty($pro)): ?>
                        <li><?= htmlspecialchars($pro) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Недостатки -->
    <?php if (!empty($cons)): ?>
        <div class="detail-section">
            <h2><i class="fas fa-thumbs-down"></i> Недостатки</h2>
            <ul class="cons-list">
                <?php foreach ($cons as $con): ?>
                    <?php $con = trim($con); if (!empty($con)): ?>
                        <li><?= htmlspecialchars($con) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="detail-actions">
        <a href="languages.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> К списку языков</a>
    </div>
</div>

<?php require_once 'footer.php'; ?>