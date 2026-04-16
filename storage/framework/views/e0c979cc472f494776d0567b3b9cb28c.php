<?php $__env->startSection('title', 'Overzicht Leveranciers'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $feedbackType = $feedbackType ?? session('leverancier_feedback_type');
        $feedbackMessage = $feedbackMessage ?? session('leverancier_feedback_message');
        $overzichtBeschikbaar = $overzichtBeschikbaar ?? true;
    ?>

    <section class="wireframe-card mb-4">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
            <h1 class="wireframe-title m-0">Overzicht Leveranciers</h1>

            
            <form method="GET" action="<?php echo e(route('leverancier.index')); ?>" class="leverancier-filter-form ms-auto">
                <select name="leverancier_type" class="form-select wireframe-select">
                    <option value="">Selecteer Leveranciertype</option>
                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type); ?>" <?php if($geselecteerdeType === $type): echo 'selected'; endif; ?>>
                            <?php echo e($type); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="btn wireframe-btn-secondary text-nowrap">Toon Leveranciers</button>
            </form>
        </div>

        <?php if($feedbackMessage): ?>
            <div class="alert alert-<?php echo e($feedbackType ?? 'danger'); ?> shadow-sm mb-3" role="alert">
                <?php echo e($feedbackMessage); ?>

            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered align-middle wireframe-table">
                <thead>
                    <tr>
                        <th>Naam</th> 
                        <th>Contactpersoon</th> 
                        <th>Email</th> 
                        <th>Mobiel</th> 
                        <th>Leveranciernummer</th> 
                        <th>LeverancierType</th> 
                        <th class="text-center">Product Details</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if(!$overzichtBeschikbaar): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Het leveranciersoverzicht kan nu niet worden geladen.</td>
                        </tr>
                    <?php elseif($toonLegeMelding): ?>
                        
                        <tr>
                            <td colspan="7">
                                <div class="alert alert-warning m-2 text-center">
                                    Er zijn geen leveranciers bekend van het geselecteerde leverancierstype
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $__empty_1 = true; $__currentLoopData = $leveranciers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leverancier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($leverancier->Naam); ?></td>
                                <td><?php echo e($leverancier->ContactPersoon); ?></td>
                                <td><?php echo e($leverancier->Email); ?></td>
                                <td><?php echo e($leverancier->Mobiel); ?></td>
                                <td><?php echo e($leverancier->LeverancierNummer); ?></td>
                                <td><?php echo e($leverancier->LeverancierType); ?></td>
                                <td class="text-center">
                                    <?php if($leverancier->LeverancierType === 'Donor'): ?>
                                        <span class="text-muted">-</span>
                                    <?php else: ?>
                                        
                                        <a href="<?php echo e(route('leverancier.producten', $leverancier->Id)); ?>" class="icon-link" title="Bekijk product details">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5H11Z"/>
                                                <path d="M4.5 9.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5Z"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Geen leveranciers gevonden</td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <?php if(!$overzichtBeschikbaar): ?>
                <a href="<?php echo e(route('leverancier.index')); ?>" class="btn btn-primary">Opnieuw proberen</a>
            <?php elseif($toonLegeMelding): ?>
                <a href="<?php echo e(route('leverancier.index')); ?>" class="btn btn-primary">Terug</a> 
            <?php endif; ?>
            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">Home</a> 
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/leverancier/index.blade.php ENDPATH**/ ?>