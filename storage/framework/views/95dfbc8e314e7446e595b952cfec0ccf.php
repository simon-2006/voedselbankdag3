<?php $__env->startSection('title', 'Wijzig voedselpakket status'); ?>

<?php $__env->startSection('content'); ?>
    <section class="wireframe-card mb-4">
        <h1 class="wireframe-title mb-4">Wijzig voedselpakket status</h1>

        <form
            method="POST"
            action="<?php echo e(route('voedselpakketten.status.update', ['voedselpakket' => $pakket->VoedselpakketId])); ?>"
            id="voedselpakketStatusForm"
            novalidate
        >
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <select
                name="status"
                id="status"
                class="form-select wireframe-select mb-3 <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                <?php if($isWijzigenGeblokkeerd): echo 'disabled'; endif; ?>
                required
            >
                <?php $__currentLoopData = $statusOpties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusWaarde => $statusLabelOptie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($statusWaarde); ?>" <?php if((string) old('status', $geselecteerdeStatus) === (string) $statusWaarde): echo 'selected'; endif; ?>>
                        <?php echo e($statusLabelOptie); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback d-block mb-3"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <?php if(session('voedselpakket_status_wijziging_mislukt')): ?>
                <div class="alert alert-danger mb-3"><?php echo e(session('voedselpakket_status_wijziging_mislukt')); ?></div>
            <?php elseif(session('voedselpakket_status_wijziging_gelukt')): ?>
                <div class="alert alert-success mb-3"><?php echo e(session('voedselpakket_status_wijziging_gelukt')); ?></div>
            <?php elseif(session('voedselpakket_status_ongewijzigd')): ?>
                <div class="alert alert-warning mb-3"><?php echo e(session('voedselpakket_status_ongewijzigd')); ?></div>
            <?php elseif($isWijzigenGeblokkeerd): ?>
                <div class="alert alert-danger mb-3">
                    Dit gezin is niet meer ingeschreven bij de voedselbank en daarom kan er geen voedselpakket worden uitgereikt
                </div>
            <?php endif; ?>

            <div class="d-flex flex-wrap justify-content-between gap-3">
                <button type="submit" class="btn wireframe-btn-secondary" <?php if($isWijzigenGeblokkeerd): echo 'disabled'; endif; ?>>
                    Wijzig status voedselpakket
                </button>

                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('voedselpakketten.gezin.show', ['gezin' => $pakket->GezinId])); ?>" class="btn btn-primary">terug</a>
                    <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">home</a>
                </div>
            </div>
        </form>
    </section>

    <?php if(session('voedselpakket_status_wijziging_gelukt')): ?>
        <script>
            setTimeout(() => {
                window.location.href = <?php echo json_encode(route('voedselpakketten.gezin.show', ['gezin' => $pakket->GezinId]), 512) ?>;
            }, 3000);
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('voedselpakketStatusForm');
            const select = document.getElementById('status');

            if (!form || !select || select.disabled) {
                return;
            }

            const toegestaneStatussen = new Set(['NietUitgereikt', 'Uitgereikt']);

            form.addEventListener('submit', (event) => {
                if (!toegestaneStatussen.has(select.value)) {
                    event.preventDefault();
                    select.classList.add('is-invalid');
                    return;
                }

                select.classList.remove('is-invalid');
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/voedselpakketten/edit-status.blade.php ENDPATH**/ ?>