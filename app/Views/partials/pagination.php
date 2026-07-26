<?php
/**
 * Partial de pagination. Variables attendues :
 * @var int $page  Page courante
 * @var int $pages Nombre total de pages
 * @var string $base Chemin de base (ex: 'employes')
 * @var array $params Parametres de requete additionnels a conserver (ex: ['q' => 'terme'])
 */
$params = $params ?? [];
?>
<?php if ($pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <?php $query = array_merge($params, ['page' => $i]); ?>
            <?php if ($i === $page): ?>
                <span class="actif"><?= $i ?></span>
            <?php else: ?>
                <a href="<?= e(url($base . '?' . http_build_query($query))) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
<?php endif; ?>
