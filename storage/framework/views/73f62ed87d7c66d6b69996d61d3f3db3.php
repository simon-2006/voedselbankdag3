<?php $__env->startSection('title', 'Overzicht voedselpakketten'); ?>

<?php $__env->startSection('content'); ?>
    <section class="wireframe-card mb-4">
        <h1 class="wireframe-title mb-4">Overzicht Voedselpakketten</h1>

        <table class="table table-bordered summary-table mb-4">
            <tbody>
                <tr>
                    <th>Naam:</th>
                    <td><?php echo e($gezin->Gezinsnaam); ?></td>
                </tr>
                <tr>
                    <th>Omschrijving:</th>
                    <td><?php echo e($gezin->Omschrijving); ?></td>
                </tr>
                <tr>
                    <th>Totaal aantal Personen:</th>
                    <td><?php echo e($gezin->TotaalAantalPersonen); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="table-responsive">
            <table class="table table-bordered align-middle wireframe-table">
                <thead>
                    <tr>
                        <th>Pakketnummer</th>
                        <th>Datum samenstelling</th>
                        <th>Datum uitgifte</th>
                        <th>Status</th>
                        <th>Aantal producten</th>
                        <th class="text-center">Wijzig Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $voedselpakketten; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voedselpakket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($voedselpakket->PakketNummer); ?></td>
                            <td><?php echo e($voedselpakket->DatumSamenstelling); ?></td>
                            <td><?php echo e($voedselpakket->DatumUitgifteLabel); ?></td>
                            <td><?php echo e($voedselpakket->StatusLabel); ?></td>
                            <td><?php echo e($voedselpakket->AantalProducten); ?></td>
                            <td class="text-center">
                                <a
                                    href="<?php echo e(route('voedselpakketten.status.edit', ['voedselpakket' => $voedselpakket->VoedselpakketId])); ?>"
                                    class="icon-link"
                                    title="Wijzig status"
                                    aria-label="Wijzig status van voedselpakket <?php echo e($voedselpakket->PakketNummer); ?>"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708L5.207 14.5H2v-3.207L12.146.146zm.708.708L3 10.707V13h2.293l9.854-9.854-2.293-2.292z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Geen voedselpakketten gevonden voor dit gezin.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?php echo e(route('voedselpakketten.index')); ?>" class="btn btn-primary">terug</a>
            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">home</a>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/voedselpakketten/show.blade.php ENDPATH**/ ?>