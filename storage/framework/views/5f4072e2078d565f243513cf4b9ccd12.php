<?php $__env->startSection('title', 'Dashboard | Voedselbank Samen'); ?>

<?php $__env->startSection('content'); ?>
    <section class="card border-0 shadow-lg p-4 p-lg-5 reveal mb-4">
        <p class="section-eyebrow mb-2">Mijn omgeving</p>
        <h1 class="display-6 fw-semibold mb-3">Welkom, <?php echo e(auth()->user()->name); ?></h1>
        <p class="text-secondary mb-0">Je bent ingelogd. Vanaf hier kun je straks je aanvraagstatus, documenten en afspraken beheren.</p>
    </section>

    <section class="row g-3">
        <article class="col-md-6 reveal reveal-delay-1">
            <div class="card border-0 shadow-sm h-100 card-lift">
                <div class="card-body p-4">
                    <h3 class="h4 mb-2">Status aanvraag</h3>
                    <p class="text-secondary mb-0">Geen actieve aanvraag gevonden. Je kunt een nieuwe aanvraag starten via het team.</p>
                </div>
            </div>
        </article>

        <article class="col-md-6 reveal reveal-delay-2">
            <div class="card border-0 shadow-sm h-100 card-lift">
                <div class="card-body p-4">
                    <h3 class="h4 mb-2">Contactmoment</h3>
                    <p class="text-secondary mb-0">Heb je vragen? Gebruik het contactpunt in je buurtlocatie of bel tijdens openingstijden.</p>
                </div>
            </div>
        </article>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\odaib\Herd\vodselbank-op-1-dag-\resources\views/dashboard.blade.php ENDPATH**/ ?>