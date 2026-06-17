<footer id="footer" class="site-footer">
    <div class="footer-brand">
        <a class="brand" href="<?php echo e(route('home')); ?>">
            <?php if(!empty($settings['logo_path'])): ?>
                <img src="<?php echo e(asset($settings['logo_path'])); ?>" alt="<?php echo e($settings['brand_name'] ?? 'StudyBuddy'); ?>">
            <?php endif; ?>

            <span><?php echo e($settings['footer_brand_text'] ?? $settings['brand_name'] ?? 'StudyBuddy'); ?></span>
        </a>

        <?php if(!empty($settings['footer_description'])): ?>
            <p><?php echo e($settings['footer_description']); ?></p>
        <?php endif; ?>
    </div>

    <?php $__currentLoopData = $footerGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="footer-group">
            <h3><?php echo e(str($groupName)->replace('-', ' ')->replace('_', ' ')->title()); ?></h3>

            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a
                    href="<?php echo e($item->url); ?>"
                    <?php if($item->opens_new_tab): ?>
                        target="_blank"
                        rel="noopener"
                    <?php endif; ?>
                >
                    <?php echo e($item->label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if(!empty($settings['footer_legal_text'])): ?>
        <div class="footer-bottom">
            <p><?php echo e($settings['footer_legal_text']); ?></p>
        </div>
    <?php endif; ?>
</footer><?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/partials/footer.blade.php ENDPATH**/ ?>