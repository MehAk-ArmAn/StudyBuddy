<?php
    $label = $label ?? 'IMAGE_PLACEHOLDER';
    $variant = $variant ?? 'orbital';
    $caption = $caption ?? 'Future image slot';
    $class = $class ?? '';
    $src = $src ?? null;
    $hasAsset = $src && file_exists(public_path($src));
?>

<div class="asset-frame frame-<?php echo e($variant); ?> <?php echo e($class); ?> <?php echo e($hasAsset ? 'has-real-asset' : 'is-fallback-art'); ?>" data-placeholder="<?php echo e($label); ?>">
    <?php if($hasAsset): ?>
        <img src="<?php echo e(asset($src)); ?>" alt="<?php echo e($caption); ?>" loading="lazy">
    <?php else: ?>
        <span class="frame-shine"></span>
        <span class="frame-orbit"></span>
        <span class="frame-spark spark-a"></span>
        <span class="frame-spark spark-b"></span>
        <strong><?php echo e($label); ?></strong>
        <small><?php echo e($caption); ?></small>
    <?php endif; ?>
</div>
<?php /**PATH D:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/partials/image-placeholder.blade.php ENDPATH**/ ?>