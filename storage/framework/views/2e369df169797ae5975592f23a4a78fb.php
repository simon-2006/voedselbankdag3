<?php $__env->startSection('title', 'Product Details – ' . $product->Productnaam); ?>

<?php $__env->startSection('content'); ?>
    <section class="card border-0 shadow-sm">
        <div class="card-body">

            <h1 class="h2 mb-4 text-success text-decoration-underline fw-bold">
                Product Details – <?php echo e($product->Productnaam); ?>

            </h1>

            <?php if($foutmelding): ?>
                <div class="alert alert-danger"><?php echo e($foutmelding); ?></div>
            <?php else: ?>
                <table class="table w-auto">
                    <tbody>
                        <tr>
                            <th>Productnaam</th>
                            <td><?php echo e($product->Productnaam); ?></td>
                        </tr>
                        <tr>
                            <th>Barcode</th>
                            <td><?php echo e($product->Barcode); ?></td>
                        </tr>
                        <tr>
                            <th>Houdbaarheidsdatum</th>
                            <td><?php echo e(\Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y')); ?></td>
                        </tr>
                        <tr>
                            <th>Magazijnlocatie</th>
                            <td><?php echo e($product->MagazijnLocatie); ?></td>
                        </tr>
                        <tr>
                            <th>Ontvangstdatum</th>
                            <td><?php echo e(\Carbon\Carbon::parse($product->Ontvangstdatum)->format('d-m-Y')); ?></td>
                        </tr>
                        <tr>
                            <th>Uitleveringsdatum</th>
                            <td>
                                <?php echo e($product->Uitleveringsdatum
                                    ? \Carbon\Carbon::parse($product->Uitleveringsdatum)->format('d-m-Y')
                                    : '–'); ?>

                            </td>
                        </tr>
                        <tr>
                            <th>Aantal op voorraad</th>
                            <td><?php echo e($product->AantalOpVoorraad); ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="d-flex gap-2 mt-3">
                <?php if(!$foutmelding): ?>
                    <a href="<?php echo e(route('voorraad.edit', $product->ProductPerMagazijnId)); ?>"
                       class="btn btn-info text-white">
                        Wijzig
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('voorraad.index')); ?>" class="btn btn-info text-white">Terug naar overzicht</a>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-info text-white">Home</a>
            </div>

        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/voorraad/show.blade.php ENDPATH**/ ?>