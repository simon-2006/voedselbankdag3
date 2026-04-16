

<?php $__env->startSection('title', 'Voedselbank Samen | Iedereen verdient een volle tafel'); ?>

<?php $__env->startSection('content'); ?>
    <section class="row g-4 align-items-stretch mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg hero-panel reveal h-100">
                <div class="card-body p-4 p-lg-5">
                    <p class="section-eyebrow mb-2">Samen tegen voedselarmoede</p>
                    <h1 class="display-5 fw-semibold mb-3">Een warme buurt begint met een volle tafel.</h1>
                    <p class="lead text-secondary mb-4">
                        Voedselbank Samen ondersteunt gezinnen met verse pakketten, persoonlijk contact en praktische hulp.
                        Registreer je om hulp aan te vragen of sluit je aan als vrijwilliger.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary btn-lg">Ga naar mijn dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('register')); ?>" class="btn btn-primary btn-lg">Vraag hulp aan</a>
                            <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-secondary btn-lg">Ik heb al een account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow h-100 reveal reveal-delay-1 hero-side-panel">
                <div class="card-body p-4">
                    <h2 class="h3 mb-3">Wat we deze week doen</h2>
                    <ul class="list-group list-group-flush stat-list">
                        <li class="list-group-item px-0">82 gezinnen ontvangen een pakket</li>
                        <li class="list-group-item px-0">31 vrijwilligers actief op locatie</li>
                        <li class="list-group-item px-0">14 lokale winkels doneren producten</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="row g-3">
        <article class="col-md-6 col-xl-4 reveal reveal-delay-1">
            <div class="card border-0 shadow-sm h-100 card-lift">
                <div class="card-body p-4">
                    <h3 class="h4 mb-2">Hulp aanvragen</h3>
                    <p class="text-secondary mb-0">Maak een account, vul je gegevens in en wij nemen snel contact met je op.</p>
                </div>
            </div>
        </article>

        <article class="col-md-6 col-xl-4 reveal reveal-delay-2">
            <div class="card border-0 shadow-sm h-100 card-lift">
                <div class="card-body p-4">
                    <h3 class="h4 mb-2">Donaties</h3>
                    <p class="text-secondary mb-0">Lever houdbare producten in of steun met een financiële bijdrage.</p>
                </div>
            </div>
        </article>

        <article class="col-md-6 col-xl-4 reveal reveal-delay-3">
            <div class="card border-0 shadow-sm h-100 card-lift">
                <div class="card-body p-4">
                    <h3 class="h4 mb-2">Vrijwilligers</h3>
                    <p class="text-secondary mb-0">Help mee met sorteren, uitdelen en buurtgerichte acties.</p>
                </div>
            </div>
        </article>
    </section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/home.blade.php ENDPATH**/ ?>