<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="StudyBuddy is a premium cosmic learning universe for learners, parents, teachers, and admins.">
    <title><?php echo $__env->yieldContent('title', 'StudyBuddy'); ?> · Cosmic Learning Universe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/studybuddy.css')); ?>">
</head>
<body>
    <div class="cosmic-bg" aria-hidden="true">
        <span class="planet planet-one"></span>
        <span class="planet planet-two"></span>
        <span class="comet comet-one"></span>
        <span class="comet comet-two"></span>
        <span class="starfield"></span>
    </div>

    <?php echo $__env->make('partials.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="site-main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="<?php echo e(asset('assets/js/studybuddy.js')); ?>" defer></script>
</body>
</html>
<?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/layouts/app.blade.php ENDPATH**/ ?>