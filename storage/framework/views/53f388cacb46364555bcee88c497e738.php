<article class="store-app-card tilt-card tone-<?php echo e($app->card_tone ?? 'violet'); ?>">
    <?php echo $__env->make('partials.image-placeholder', ['label' => $app->image_label ?? 'APP_CARD_IMAGE', 'src' => $app->image_path ?? null, 'variant' => 'app', 'caption' => $app->title.' app artwork'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="store-app-copy">
        <h3><?php echo e($app->title); ?></h3>
        <p><?php echo e($app->description); ?></p>
        <div class="rating-line"><span>⭐ <?php echo e($app->hero_metric); ?></span><span><?php echo e($app->age_band); ?></span></div>
        <?php if($app->launch_path): ?>
            <a class="mini-button" href="<?php echo e($app->launch_path); ?>">Start</a>
        <?php else: ?>
            <button class="mini-button" type="button">Start</button>
        <?php endif; ?>
    </div>
</article>
<?php /**PATH D:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/partials/app-card.blade.php ENDPATH**/ ?>