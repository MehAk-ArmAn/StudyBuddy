

<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('content'); ?>
<section class="landing-stage reveal-on-load">
    <div class="landing-panel glass-panel">
        <div class="hero-copy">
            <div class="pill-row">
                <span class="cosmic-pill">✺ Interactive Animations</span>
                <span class="cosmic-pill">✦ Magical Experience</span>
                <span class="cosmic-pill">● Multi-Role System</span>
            </div>
            <h1>Learn. Play. Grow. <span>Your Way.</span></h1>
            <p>A fun and safe learning universe where students can practice, play, focus, and grow with their personal study buddy.</p>
            <div class="hero-actions">
                <a class="button" href="<?php echo e(route('apps.math-quest.play')); ?>">Start Learning</a>
                <a class="button button-ghost" href="<?php echo e(route('apps.index')); ?>">Explore Apps</a>
            </div>
        </div>

        <div class="hero-visual-wrap">
            <span class="hero-planet mini-planet-a"></span>
            <span class="hero-planet mini-planet-b"></span>
            <span class="hero-star hero-star-a">★</span>
            <span class="hero-star hero-star-b">✦</span>
            <?php echo $__env->make('partials.image-placeholder', ['label' => 'HERO_MASCOT_IMAGE', 'variant' => 'mascot', 'caption' => 'Dolphin + open book mascot art'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <div class="shortcut-strip">
            <?php $__currentLoopData = $featuredApps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="shortcut-card tilt-card" href="<?php echo e($app->launch_path ?? route('apps.index')); ?>">
                    <?php echo $__env->make('partials.image-placeholder', ['label' => $app->image_label, 'variant' => 'shortcut', 'caption' => $app->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <span><?php echo e($app->title); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a class="shortcut-card tilt-card" href="<?php echo e(route('apps.index')); ?>">
                <?php echo $__env->make('partials.image-placeholder', ['label' => 'APP_SHORTCUT_MORE_IMAGE', 'variant' => 'shortcut', 'caption' => 'More apps'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span>More Apps</span>
            </a>
        </div>
    </div>

    <div class="stats-row glass-panel">
        <div><strong>50+</strong><span>Mini Apps</span></div>
        <div><strong>10K+</strong><span>Students</span></div>
        <div><strong>100K+</strong><span>Lessons Completed</span></div>
        <div><strong>4.9</strong><span>Parent Rating ★★★★★</span></div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/pages/home.blade.php ENDPATH**/ ?>