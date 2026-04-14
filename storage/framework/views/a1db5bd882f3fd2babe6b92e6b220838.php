<nav class="navbar navbar-expand-lg app-navbar sticky-top shadow-sm reveal">
    <div class="container py-2">
        <a href="<?php echo e(route('home')); ?>" class="navbar-brand fw-bold d-flex align-items-center gap-2" aria-label="Ga naar de homepage">
            <img src="<?php echo e(asset('images/voedselbank-symbool.svg')); ?>" alt="Symbool van Voedselbank Samen" class="brand-symbol">
            <span>Voedselbank Samen</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Navigatie openen">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavigation">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 mt-3 mt-lg-0">
                <li class="nav-item">
                    <a href="<?php echo e(route('home')); ?>" class="nav-link fw-semibold <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">Home</a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('leverancier.index')); ?>" class="nav-link fw-semibold <?php echo e(request()->routeIs('leverancier.*') ? 'active' : ''); ?>">Leveranciers</a>
                </li>

                <?php if(auth()->guard()->check()): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('allergie.index')); ?>" class="nav-link fw-semibold <?php echo e(request()->routeIs('allergie.*') ? 'active' : ''); ?>">Allergieen</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('dashboard')); ?>" class="nav-link fw-semibold <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('voedselpakketten.index')); ?>" class="nav-link fw-semibold <?php echo e(request()->routeIs('voedselpakketten.*') ? 'active' : ''); ?>">Overzicht voedselpakketten</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('voorraad.index')); ?>" class="nav-link fw-semibold <?php echo e(request()->routeIs('voorraad.*') ? 'active' : ''); ?>">Overzicht Productvoorraden</a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-outline-secondary btn-sm px-3">Uitloggen</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('login')); ?>" class="nav-link fw-semibold <?php echo e(request()->routeIs('login') ? 'active' : ''); ?>">Inloggen</a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="<?php echo e(route('register')); ?>" class="btn btn-primary btn-sm px-3">Registreren</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH C:\Users\odaib\Herd\vodselbank-op-1-dag-\resources\views/components/navbar.blade.php ENDPATH**/ ?>