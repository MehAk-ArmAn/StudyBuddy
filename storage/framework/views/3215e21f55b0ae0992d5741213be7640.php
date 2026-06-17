<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo e($settings['seo_title'] ?? $settings['site_name'] ?? config('app.name', 'StudyBuddy')); ?></title>

    <?php if(!empty($settings['seo_description'])): ?>
        <meta name="description" content="<?php echo e($settings['seo_description']); ?>">
    <?php endif; ?>

    <?php if(!empty($settings['seo_keywords'])): ?>
        <meta name="keywords" content="<?php echo e($settings['seo_keywords']); ?>">
    <?php endif; ?>

    <?php if(!empty($settings['favicon_path'])): ?>
        <link rel="icon" href="<?php echo e(asset($settings['favicon_path'])); ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/site.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body id="top" class="studybuddy-site">
    <?php echo $__env->make('partials.navbar', [
        'settings' => $settings ?? [],
        'navigationItems' => $navigationItems ?? collect(),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.footer', [
        'settings' => $settings ?? [],
        'footerGroups' => $footerGroups ?? collect(),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="<?php echo e(asset('assets/js/site.js')); ?>" defer></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/layouts/app.blade.php ENDPATH**/ ?>