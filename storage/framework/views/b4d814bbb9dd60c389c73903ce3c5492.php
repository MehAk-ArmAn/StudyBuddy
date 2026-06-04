<footer class="cosmic-footer reveal-on-load">
    <div class="footer-brand">
        <span class="brand-mark footer-mark">🐬</span>
        <div>
            <h2>StudyBuddy</h2>
            <p>A safe and fun learning universe for every student.</p>
        </div>
    </div>
    <div class="footer-columns">
        <div><h3>Explore</h3><a href="<?php echo e(route('home')); ?>">Home</a><a href="<?php echo e(route('apps.index')); ?>">Apps</a><a href="<?php echo e(route('rewards')); ?>">Rewards</a></div>
        <div><h3>Dashboards</h3><a href="<?php echo e(route('demo.primary')); ?>">Primary</a><a href="<?php echo e(route('demo.secondary')); ?>">Secondary</a><a href="<?php echo e(route('demo.parent')); ?>">Parent</a><a href="<?php echo e(route('demo.teacher')); ?>">Teacher</a></div>
        <div><h3>Preview</h3><a href="<?php echo e(route('showcase')); ?>">Showcase</a><a href="<?php echo e(route('demo.admin')); ?>">Admin</a><a href="<?php echo e(route('apps.math-quest')); ?>">Math Quest</a></div>
    </div>
    <div class="footer-apps">
        <p class="eyebrow">Get StudyBuddy Apps</p>
        <div class="store-badge">▶ Google Play</div>
        <div class="store-badge"> App Store</div>
        <?php echo $__env->make('partials.image-placeholder', ['label' => 'FOOTER_QR_IMAGE', 'variant' => 'qr', 'caption' => 'Footer app QR'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</footer>
<?php /**PATH E:\My real data\Biz\PixelCraftsLab\Projects\Study Buddy\website\StudyBuddy\resources\views/partials/footer.blade.php ENDPATH**/ ?>