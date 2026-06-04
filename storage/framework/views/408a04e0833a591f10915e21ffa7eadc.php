

<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('content'); ?>
<section class="hero-grid section-pad">
    <div class="hero-copy">
        <p class="eyebrow"><?php echo e($content->get('home.hero')?->metadata['eyebrow'] ?? 'StudyBuddy Galaxy OS'); ?></p>
        <h1><?php echo e($content->get('home.hero')?->title ?? 'A premium cosmic universe for confident learning'); ?></h1>
        <p class="lede"><?php echo e($content->get('home.hero')?->body ?? 'StudyBuddy turns learning into luminous mini missions.'); ?></p>
        <div class="hero-actions">
            <a class="button" href="<?php echo e(route('apps.index')); ?>">Explore apps</a>
            <a class="button ghost" href="<?php echo e(route('showcase')); ?>">View showcase</a>
        </div>
    </div>
    <div class="hero-orbit glass-card">
        <div class="orbit-ring"></div>
        <div class="buddy-orb">🐬📖</div>
        <div class="floating-chip chip-one">+240 XP</div>
        <div class="floating-chip chip-two">Math streak</div>
        <div class="floating-chip chip-three">Galaxy badge</div>
    </div>
</section>

<section class="section-pad split-section">
    <?php echo $__env->make('partials.mascot', ['title' => $content->get('home.mascot')?->title ?? 'Meet Buddy'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="glass-card feature-panel">
        <p class="eyebrow">Premium foundation</p>
        <h2>No Bootstrap look. No generic template.</h2>
        <p>This foundation uses custom Blade partials, handcrafted CSS, cosmic UI motion, and reusable cards ready for product-specific growth.</p>
    </div>
</section>

<section class="section-pad">
    <div class="section-heading">
        <p class="eyebrow">Launch-ready mini apps</p>
        <h2>Play Store style learning products</h2>
    </div>
    <div class="app-grid">
        <?php $__empty_1 = true; $__currentLoopData = $featuredApps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('partials.app-card', ['app' => $app], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="empty-state">Run <code>php artisan db:seed</code> to load the demo mini apps.</p>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/pages/home.blade.php ENDPATH**/ ?>