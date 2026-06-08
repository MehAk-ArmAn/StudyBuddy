

<?php $__env->startSection('title', 'Home'); ?>
<?php $__env->startSection('body_class', 'page-shell page-home'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/home-cosmic.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/home-cosmic.js')); ?>" defer></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);

    $homeApps = [
        ['title' => 'Math Quest', 'img' => 'app-math-quest.png', 'url' => route('apps.math-quest')],
        ['title' => 'Spelling Sprint', 'img' => 'app-spelling-sprint.png', 'url' => route('apps.index')],
        ['title' => 'Reading Garden', 'img' => 'app-reading-garden.png', 'url' => route('apps.index')],
        ['title' => 'Focus Forest', 'img' => 'app-focus-forest.png', 'url' => route('apps.index')],
        ['title' => 'Planner City', 'img' => 'app-planner-city.png', 'url' => route('apps.index')],
        ['title' => 'Quiz Galaxy', 'img' => 'app-quiz-galaxy.png', 'url' => route('apps.index')],
        ['title' => 'Shapes Lab', 'img' => 'app-shapes-lab.png', 'url' => route('apps.index')],
        ['title' => 'Flashcard Castle', 'img' => 'app-flashcard-castle.png', 'url' => route('apps.index')],
    ];
?>

<div class="home-cosmic-page reveal-on-load" data-home-cosmic>
    <div class="home-bg" aria-hidden="true">
        <div class="home-bg-base"></div>
        <div class="home-bg-nebula home-bg-nebula-a"></div>
        <div class="home-bg-nebula home-bg-nebula-b"></div>
        <div class="home-bg-glow home-bg-glow-center"></div>
        <div class="home-starfield" data-home-stars></div>

        <span class="home-orb home-orb-1" data-parallax="0.035"></span>
        <span class="home-orb home-orb-2" data-parallax="0.05"></span>
        <span class="home-orb home-orb-3" data-parallax="0.028"></span>

        <div class="home-planet-wrap home-planet-wrap-ringed" data-parallax="0.012">
            <div class="home-planet-halo"></div>
            <img class="home-blend-img home-planet home-planet-ringed" src="<?php echo e($asset('planet-ringed-lg.png')); ?>" alt="">
        </div>
        <div class="home-planet-wrap home-planet-wrap-purple" data-parallax="0.015">
            <div class="home-planet-halo"></div>
            <img class="home-blend-img home-planet home-planet-purple" src="<?php echo e($asset('planet-purple-lg.png')); ?>" alt="">
        </div>

        <span class="home-comet home-comet-a" aria-hidden="true"><i></i></span>
        <span class="home-comet home-comet-b" aria-hidden="true"><i></i></span>
    </div>

    <div class="home-mouse-glow" data-mouse-glow aria-hidden="true"></div>

    <div class="home-shell">
        <header class="home-nav">
            <a class="home-brand" href="<?php echo e(route('home')); ?>" aria-label="StudyBuddy home">
                <span class="home-brand-icon">
                    <img class="home-blend-img" src="<?php echo e($asset('logo-icon.png')); ?>" alt="">
                </span>
                <span class="home-brand-text"><em>Study</em>Buddy</span>
            </a>
            <nav class="home-nav-links" aria-label="Main navigation">
                <a href="<?php echo e(route('home')); ?>" class="is-active">Home</a>
                <a href="<?php echo e(route('apps.index')); ?>">Apps</a>
                <a href="<?php echo e(route('for-parents')); ?>">For Parents</a>
                <a href="<?php echo e(route('for-teachers')); ?>">For Teachers</a>
                <a href="<?php echo e(route('pricing')); ?>">Pricing</a>
                <a href="<?php echo e(route('support')); ?>">Support</a>
            </nav>
            <a class="home-signup" href="<?php echo e(route('apps.index')); ?>">Sign Up</a>
        </header>

        <section class="home-hero">
            <div class="home-copy">
                <h1 class="home-headline">
                    <span class="home-headline-main">Learn. Play. Grow.</span>
                    <span class="home-gradient">Your Way.</span>
                </h1>
                <p>A fun and safe learning universe where students can practice, play, focus, and grow with their personal study buddy.</p>
                <div class="home-actions">
                    <a class="home-btn home-btn-primary" href="<?php echo e(route('apps.index')); ?>">
                        <span>Start Learning</span>
                    </a>
                    <a class="home-btn home-btn-ghost" href="<?php echo e(route('apps.index')); ?>">Explore Apps</a>
                </div>
            </div>

            <div class="home-visual" data-parallax="0.012">
                <div class="home-visual-stage">
                    <div class="home-visual-glow"></div>
                    <div class="home-visual-glow home-visual-glow-secondary"></div>
                    <span class="home-orbit-star home-orbit-star-a"></span>
                    <span class="home-orbit-star home-orbit-star-b"></span>
                    <span class="home-orbit-star home-orbit-star-c"></span>
                    <span class="home-orbit-ring" aria-hidden="true"></span>
                    <span class="home-speech" aria-hidden="true"><i></i><i></i><i></i></span>
                    <img
                        class="home-blend-img home-mascot"
                       
                        src="<?php echo e($asset('hero-dolphin-book.png')); ?>"
                        alt="StudyBuddy dolphin mascot jumping from a glowing book"
                    >
                </div>
            </div>
        </section>

        <section class="home-app-strip" aria-label="Featured mini apps">
            <div class="home-app-strip-inner">
                <?php $__currentLoopData = $homeApps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="home-app-card" href="<?php echo e($app['url']); ?>" data-tilt-card>
                        <span class="home-app-card-shine"></span>
                        <span class="home-app-icon-stage">
                            <span class="home-app-icon-halo"></span>
                            <img class="home-blend-img" src="<?php echo e($asset($app['img'])); ?>" alt="<?php echo e($app['title']); ?>">
                        </span>
                        <span class="home-app-label"><?php echo e($app['title']); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <section class="home-stats" aria-label="Platform statistics">
            <div class="home-stat">
                <span class="home-stat-icon home-stat-icon-apps" aria-hidden="true"></span>
                <div>
                    <strong>50+</strong>
                    <span>Mini Apps</span>
                </div>
            </div>
            <div class="home-stat">
                <span class="home-stat-icon home-stat-icon-students" aria-hidden="true"></span>
                <div>
                    <strong>10K+</strong>
                    <span>Students</span>
                </div>
            </div>
            <div class="home-stat">
                <span class="home-stat-icon home-stat-icon-lessons" aria-hidden="true"></span>
                <div>
                    <strong>100K+</strong>
                    <span>Lessons Completed</span>
                </div>
            </div>
            <div class="home-stat">
                <span class="home-stat-icon home-stat-icon-rating" aria-hidden="true"></span>
                <div>
                    <strong>4.9</strong>
                    <span>Parent Rating</span>
                    <em class="home-stars" aria-hidden="true">★★★★★</em>
                </div>
            </div>
        </section>

        <footer class="home-footer">
            <div class="home-footer-brand">
                <img class="home-blend-img" src="<?php echo e($asset('logo-icon.png')); ?>" alt="">
                <div>
                    <strong>StudyBuddy</strong>
                    <p>A safe and fun learning universe for every student.</p>
                </div>
            </div>
            <div class="home-footer-links">
                <a href="<?php echo e(route('home')); ?>">Home</a>
                <a href="<?php echo e(route('apps.index')); ?>">Apps</a>
                <a href="<?php echo e(route('for-parents')); ?>">For Parents</a>
                <a href="<?php echo e(route('for-teachers')); ?>">For Teachers</a>
                <a href="<?php echo e(route('pricing')); ?>">Pricing</a>
                <a href="<?php echo e(route('support')); ?>">Support</a>
            </div>
        </footer>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/pages/home.blade.php ENDPATH**/ ?>