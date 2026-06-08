

<?php $__env->startSection('title', 'Apps'); ?>

<?php $__env->startSection('content'); ?>
<section class="playstore-page reveal-on-load">
    <aside class="left-rail glass-panel">
        <a class="rail-logo" href="<?php echo e(route('home')); ?>"><?php echo $__env->make('partials.image-placeholder', ['label' => 'LOGO_ICON', 'src' => 'assets/studybuddy/logo-icon.png', 'variant' => 'logo', 'caption' => 'Logo'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <b>StudyBuddy</b></a>
        <nav><a class="active">Apps</a><a>Popular</a><a>Primary</a><a>Secondary</a><a>New</a><a>Rewards</a></nav>
    </aside>
    <div class="playstore-panel glass-panel">
        <div class="store-topline"><div><p class="eyebrow">02 Apps Store (Playstore Style)</p><h1>StudyBuddy Apps</h1></div><label class="search-bar">⌕ <input type="search" placeholder="Search apps..." aria-label="Search apps"></label></div>
        <div class="filter-pills"><button class="active">All</button><button>Popular</button><button>Primary (6–10)</button><button>Secondary (7–11)</button><button>New</button></div>
        <div class="store-grid">
            <?php $__currentLoopData = $apps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('partials.app-card', ['app' => $app], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/apps/index.blade.php ENDPATH**/ ?>