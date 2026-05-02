<?php
// professions.php – каталог профессий (пользовательская страница)
require_once 'header.php';

// Получаем все профессии из базы
$result = $db->query("SELECT * FROM professions ORDER BY title");
?>

<div class="app-shell app-shell--lift">
    <div class="professions-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($prof = $result->fetch_assoc()): ?>
                <?php 
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
                <div class="profession-card">
                    <?php if (!empty($image_url)): ?>
                        <div class="card-image">
                            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($prof['title']) ?>" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\'fas fa-briefcase\'></i>'; this.parentNode.classList.add('no-image');">
                        </div>
                    <?php else: ?>
                        <div class="card-image no-image">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-content">
                        <h3 class="card-title">
                            <a href="profession.php?id=<?= $prof['id'] ?>"><?= htmlspecialchars($prof['title']) ?></a>
                        </h3>
                        
                        <?php if (!empty($prof['category'])): ?>
                            <div class="card-category"><?= htmlspecialchars($prof['category']) ?></div>
                        <?php endif; ?>
                        
                        <p class="card-description">
                            <?= htmlspecialchars(mb_substr($prof['short_description'] ?? '', 0, 120)) ?>...
                        </p>
                        
                        <?php if ($prof['salary_min'] || $prof['salary_max']): ?>
                            <div class="card-salary">
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
                        
                        <a href="profession.php?id=<?= $prof['id'] ?>" class="btn btn-primary">Подробнее</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="app-empty">Профессии пока не добавлены.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>