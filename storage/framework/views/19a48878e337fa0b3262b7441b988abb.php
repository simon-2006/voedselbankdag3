<?php $__env->startSection('title', 'Overzicht Productvoorraden'); ?>

<?php $__env->startSection('content'); ?>
    <section class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <h1 class="h2 mb-0 text-success text-decoration-underline fw-bold">
                    Overzicht Productvoorraden
                </h1>

                <form method="GET" action="<?php echo e(route('voorraad.index')); ?>" class="d-flex flex-wrap gap-2">
                    <select name="categorie" class="form-select" style="min-width: 230px;">
                        <option value="">Selecteer Categorie</option>
                        <?php $__currentLoopData = $categorieen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categorie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($categorie); ?>" <?php echo e($gekozenCategorie === $categorie ? 'selected' : ''); ?>>
                                <?php echo e($categorie); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="btn btn-secondary">Toon Voorraad</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Productnaam</th>
                            <th>Categorie</th>
                            <th>Eenheid</th>
                            <th>Aantal</th>
                            <th>Houdbaarheidsdatum</th>
                            <th>Magazijn</th>
                            <th class="text-center">Voorraad Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($foutmelding): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="alert alert-danger mb-0"><?php echo e($foutmelding); ?></div>
                                </td>
                            </tr>
                        <?php elseif($voorraadProducten->isEmpty()): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="alert alert-warning mb-0 text-center">
                                        Er zijn geen producten bekend die behoren bij de geselecteerde productcategorie.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $__currentLoopData = $voorraadProducten; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($product->Productnaam); ?></td>
                                    <td><?php echo e($product->Categorie); ?></td>
                                    <td><?php echo e($product->Eenheid); ?></td>
                                    <td><?php echo e($product->Aantal); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y')); ?></td>
                                    <td><?php echo e($product->Magazijn); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('voorraad.show', $product->ProductPerMagazijnId)); ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            📘 Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary text-white">Home</a>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/voorraad/index.blade.php ENDPATH**/ ?>