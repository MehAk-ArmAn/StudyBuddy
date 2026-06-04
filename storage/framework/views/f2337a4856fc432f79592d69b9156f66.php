<?php
    $label = $label ?? 'IMAGE_PLACEHOLDER';
    $variant = $variant ?? 'orbital';
    $caption = $caption ?? 'Future image slot';
    $class = $class ?? '';
?>

<div class="image-placeholder placeholder-<?php echo e($variant); ?> <?php echo e($class); ?>" data-placeholder="<?php echo e($label); ?>">
    <span class="placeholder-shine"></span>
    <span class="placeholder-orbit"></span>
    <span class="placeholder-spark spark-a"></span>
    <span class="placeholder-spark spark-b"></span>
    <strong><?php echo e($label); ?></strong>
    <small><?php echo e($caption); ?></small>
</div>
<?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/partials/image-placeholder.blade.php ENDPATH**/ ?>