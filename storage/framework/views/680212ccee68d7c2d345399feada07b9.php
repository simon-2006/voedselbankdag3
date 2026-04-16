<?php $__env->startSection('title', 'Wijzig allergie'); ?>

<?php $__env->startSection('content'); ?>
    <section class="wireframe-card mb-4">
        <h1 class="wireframe-title mb-4">Wijzig allergie</h1>

        <form method="POST" action="<?php echo e(route('allergie.update', ['gezin' => $gezinId, 'persoon' => $persoon->PersoonId])); ?>">
            <?php echo csrf_field(); ?>

            <select name="allergie_id" class="form-select wireframe-select mb-3 <?php $__errorArgs = ['allergie_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">Selecteer Allergie</option>
                <?php $__currentLoopData = $allergieen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allergie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($allergie->Id); ?>" <?php if((int) old('allergie_id', (int) ($persoon->AllergieId ?? 0)) === (int) $allergie->Id): echo 'selected'; endif; ?>>
                        <?php echo e($allergie->Naam); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <?php $__errorArgs = ['allergie_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback d-block mb-3"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <?php if(session('wijziging_niet_doorgvoerd')): ?>
                <div class="alert alert-danger mb-3"><?php echo e(session('wijziging_niet_doorgvoerd')); ?></div>
            <?php elseif(session('wijziging_doorgvoerd')): ?>
                <div class="alert alert-success mb-3"><?php echo e(session('wijziging_doorgvoerd')); ?></div>
            <?php elseif($heeftHoogRisico): ?>
                <div class="alert alert-danger mb-3">
                    Voor het wijzigen van deze allergie wordt geadviseerd eerst een arts te raadplegen vanwege een hoog risico op een anafylactisch shock
                </div>
            <?php endif; ?>

            <div class="d-flex flex-wrap justify-content-between gap-3">
                <button type="submit" class="btn wireframe-btn-secondary">Wijzig Allergie</button>

                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('allergie.gezin', ['gezin' => $gezinId])); ?>" class="btn btn-primary">Terug</a>
                    <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">Home</a>
                </div>
            </div>
        </form>
    </section>

    <?php if(session('wijziging_doorgvoerd')): ?>
        <script>
            // Na succesvolle wijziging automatisch terug naar de gezinsdetails.
            setTimeout(() => {
                window.location.href = <?php echo json_encode(route('allergie.gezin', ['gezin' => $gezinId]), 512) ?>;
            }, 3000);
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/allergie/wijzig.blade.php ENDPATH**/ ?>