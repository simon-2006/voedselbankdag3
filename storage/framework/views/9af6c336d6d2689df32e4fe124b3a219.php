<?php $__env->startSection('title', 'Productgegevens gewijzigd'); ?>

<?php $__env->startSection('content'); ?>
    <section class="card border-0 shadow-sm">
        <div class="card-body">

            <h1 class="h2 mb-4 text-success text-decoration-underline fw-bold">
                Productgegevens gewijzigd
            </h1>

            
            <div class="alert alert-success">
                De productgegevens zijn gewijzigd.
            </div>

            <p class="text-muted">
                U wordt over <span id="countdown">3</span> seconden automatisch teruggestuurd naar de productdetails.
            </p>

            <div class="d-flex gap-2 mt-3">
                <a href="<?php echo e(route('voorraad.show', $productPerMagazijnId)); ?>" class="btn btn-secondary">
                    Terug naar Product Details
                </a>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary text-white">Home</a>
            </div>

        </div>
    </section>

    <script>
        let seconds = 3;
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = "<?php echo e(route('voorraad.show', $productPerMagazijnId)); ?>";
            }
        }, 1000);
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/voorraad/gewijzigd.blade.php ENDPATH**/ ?>