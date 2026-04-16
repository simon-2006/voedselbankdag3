<footer class="site-footer mt-auto">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-5">
                <a href="<?php echo e(route('home')); ?>" class="footer-brand d-inline-flex align-items-center gap-2 text-decoration-none">
                    <img src="<?php echo e(asset('images/voedselbank-symbool.svg')); ?>" alt="Symbool van Voedselbank Samen" class="brand-symbol">
                    <span class="fw-bold">Voedselbank Samen</span>
                </a>
                <p class="footer-copy mt-3 mb-0">
                    Samen zorgen we dat gezinnen in Maaskantje wekelijks toegang hebben tot voedzame producten en persoonlijke ondersteuning.
                </p>
            </div>

            <div class="col-sm-6 col-lg-3">
                <h2 class="h5 mb-3 footer-title">Snel naar</h2>
                <ul class="list-unstyled mb-0 footer-links">
                    <li><a href="<?php echo e(route('home')); ?>">Homepage</a></li>
                    <?php if(auth()->guard()->check()): ?>
                        <li><a href="<?php echo e(route('dashboard')); ?>">Mijn dashboard</a></li>
                        <li><a href="<?php echo e(route('voedselpakketten.index')); ?>">Overzicht voedselpakketten</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo e(route('login')); ?>">Inloggen</a></li>
                        <li><a href="<?php echo e(route('register')); ?>">Registreren</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="col-sm-6 col-lg-4">
                <h2 class="h5 mb-3 footer-title">Contact en openingstijden</h2>
                <ul class="list-unstyled mb-0 footer-links">
                    <li>Prinses Irenestraat 12A, Maaskantje</li>
                    <li><a href="tel:+31623456123">+31 6 2345 6123</a></li>
                    <li><a href="mailto:info@voedselbanksamen.nl">info@voedselbanksamen.nl</a></li>
                    <li class="pt-2">Ma: 10:00 - 16:00 | Wo: 10:00 - 16:00 | Vr: 09:00 - 14:00</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom mt-4 pt-3 d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>&copy; <?php echo e(date('Y')); ?> Voedselbank Samen</span>
            <span>Gemaakt voor de regio Maaskantje.</span>
        </div>
    </div>
</footer>
<?php /**PATH C:\Users\odaib\Herd\vodselbank-op-1-dag-\resources\views/components/footer.blade.php ENDPATH**/ ?>