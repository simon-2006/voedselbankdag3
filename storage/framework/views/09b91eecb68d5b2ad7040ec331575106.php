<?php $__env->startSection('title', 'Overzicht gezinnen met allergieen'); ?>

<?php $__env->startSection('content'); ?>
    <section class="wireframe-card mb-4">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
            <h1 class="wireframe-title m-0">Overzicht gezinnen met allergieën</h1>

            <form method="GET" action="<?php echo e(route('allergie.index')); ?>" class="allergie-filter-form ms-auto">
                <select name="allergie_id" class="form-select wireframe-select">
                    <option value="0">Selecteer alle allergieen</option>
                    <?php $__currentLoopData = $allergieen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allergie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($allergie->Id); ?>" <?php if((int) $geselecteerdeAllergieId === (int) $allergie->Id): echo 'selected'; endif; ?>>
                            <?php echo e($allergie->Naam); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="btn wireframe-btn-secondary text-nowrap">Toon Gezinnen</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle wireframe-table">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Omschrijving</th>
                        <th>Volwassenen</th>
                        <th>Kinderen</th>
                        <th>Babys</th>
                        <th>Vertegenwoordiger</th>
                        <th class="text-center">Allergie Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($toonLegeMelding): ?>
                        <tr>
                            <td colspan="7">
                                <div class="alert alert-warning m-2 text-center">
                                    Er zijn geen gezinnen bekent die de geselecteerde allergie hebben
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $__empty_1 = true; $__currentLoopData = $gezinnen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gezin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($gezin->Naam); ?></td>
                                <td><?php echo e($gezin->Omschrijving); ?></td>
                                <td><?php echo e($gezin->AantalVolwassenen); ?></td>
                                <td><?php echo e($gezin->AantalKinderen); ?></td>
                                <td><?php echo e($gezin->AantalBabys); ?></td>
                                <td><?php echo e($gezin->Vertegenwoordiger); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('allergie.gezin', ['gezin' => $gezin->Id])); ?>" class="icon-link" title="Bekijk allergiedetails">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5H11Z"/>
                                            <path d="M4.5 9.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Geen gegevens beschikbaar</td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">Home</a>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/allergie/overzicht.blade.php ENDPATH**/ ?>