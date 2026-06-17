<nav class="nav">
    <a class="brand" href="<?php echo e(route('home')); ?>">
        <?php if(!empty($settings['logo_path'])): ?>
            <img src="<?php echo e(asset($settings['logo_path'])); ?>" alt="<?php echo e($settings['brand_name'] ?? 'StudyBuddy'); ?>">
        <?php endif; ?>

        <span><?php echo e($settings['brand_name'] ?? 'StudyBuddy'); ?></span>
    </a>

    <button class="nav-toggle" type="button" data-nav-toggle>
        Menu
    </button>

    <div class="nav-links" data-nav-links>
        <?php $__currentLoopData = $navigationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($item->opens_new_tab): ?>
                <a href="<?php echo e($item->url); ?>" target="_blank" rel="noopener">
                    <?php echo e($item->label); ?>

                </a>
            <?php else: ?>
                <a href="<?php echo e($item->url); ?>">
                    <?php echo e($item->label); ?>

                </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if(!empty($settings['global_cta_label'])): ?>
            <a class="nav-cta" href="<?php echo e($settings['global_cta_url'] ?? '#top'); ?>">
                <?php echo e($settings['global_cta_label']); ?>

            </a>
        <?php endif; ?>
    </div>
</nav>
<?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/partials/navbar.blade.php ENDPATH**/ ?>