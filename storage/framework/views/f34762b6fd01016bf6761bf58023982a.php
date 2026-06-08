<?php ($logoExists = file_exists(public_path('assets/studybuddy/logo-icon.png'))); ?>
<header class="nav-shell reveal-on-load">
    <a class="brand" href="<?php echo e(route('home')); ?>" aria-label="StudyBuddy home">
        <span class="brand-mark">
            <?php if($logoExists): ?>
                <img src="<?php echo e(asset('assets/studybuddy/logo-icon.png')); ?>" alt="StudyBuddy logo">
            <?php else: ?>
                🐬
            <?php endif; ?>
        </span>
        <span class="brand-copy"><strong>StudyBuddy</strong></span>
    </a>
    <nav class="nav-links" aria-label="Main navigation">
        <a href="<?php echo e(route('home')); ?>">Home</a>
        <a href="<?php echo e(route('apps.index')); ?>">Apps</a>
        <a href="<?php echo e(route('demo.parent')); ?>">For Parents</a>
        <a href="<?php echo e(route('demo.teacher')); ?>">For Teachers</a>
        <a href="<?php echo e(route('rewards')); ?>">Pricing</a>
        <a href="<?php echo e(route('showcase')); ?>">Support</a>
    </nav>
    <div class="nav-actions">
        <a class="button button-compact" href="<?php echo e(route('apps.math-quest.play')); ?>">Sign Up</a>
    </div>
</header>
<?php /**PATH D:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/partials/navigation.blade.php ENDPATH**/ ?>