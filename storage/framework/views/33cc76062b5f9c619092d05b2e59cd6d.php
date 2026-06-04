<article class="app-card tone-<?php echo e($app->card_tone ?? 'violet'); ?>">
    <div class="app-icon">✦</div>
    <div class="app-copy">
        <p class="eyebrow"><?php echo e($app->subject); ?> · <?php echo e($app->age_band); ?></p>
        <h3><?php echo e($app->title); ?></h3>
        <p><?php echo e($app->description); ?></p>
        <div class="app-meta">
            <span><?php echo e($app->hero_metric); ?></span>
            <span class="status"><?php echo e(ucfirst($app->status)); ?></span>
        </div>
    </div>
    <?php if($app->launch_path): ?>
        <a class="button small" href="<?php echo e($app->launch_path); ?>">Launch</a>
    <?php else: ?>
        <span class="button small ghost">Coming soon</span>
    <?php endif; ?>
</article>
<?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/partials/app-card.blade.php ENDPATH**/ ?>