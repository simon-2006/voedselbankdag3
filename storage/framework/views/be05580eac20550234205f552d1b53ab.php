<?php $__env->startSection('title', 'Wijzig Product Details – ' . $product->Productnaam); ?>

<?php $__env->startSection('content'); ?>
    <section class="card border-0 shadow-sm align-items-center">
        <div class="card-body">

            <h1 class="h2 mb-4 text-success text-decoration-underline fw-bold">
                Wijzig Product Details – <?php echo e($product->Productnaam); ?>

            </h1>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <?php if($foutmelding ?? false): ?>
                <div class="alert alert-danger"><?php echo e($foutmelding); ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('voorraad.update', $product->ProductPerMagazijnId)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <table class="table w-auto mb-4">
                    <tbody>
                        <tr>
                            <th>
                                <label for="Productnaam" class="form-label mb-0">Productnaam</label>
                            </th>
                            <td>
                                <input type="text" id="Productnaam" name="Productnaam"
                                    class="form-control <?php $__errorArgs = ['Productnaam'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('Productnaam', $product->Productnaam)); ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="Houdbaarheidsdatum" class="form-label mb-0">Houdbaarheidsdatum</label>
                            </th>
                            <td>
                                <input type="date" id="Houdbaarheidsdatum" name="Houdbaarheidsdatum"
                                    class="form-control <?php $__errorArgs = ['Houdbaarheidsdatum'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('Houdbaarheidsdatum', \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('Y-m-d'))); ?>"
                                    required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="Barcode" class="form-label mb-0">Barcode</label>
                            </th>
                            <td>
                                <input type="text" id="Barcode" name="Barcode"
                                    class="form-control <?php $__errorArgs = ['Barcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('Barcode', $product->Barcode)); ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="MagazijnLocatie" class="form-label mb-0">Magazijnlocatie</label>
                            </th>
                            <td>
                                <select id="MagazijnLocatie" name="MagazijnLocatie"
                                    class="form-select <?php $__errorArgs = ['MagazijnLocatie'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php $__currentLoopData = $locaties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locatie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($locatie); ?>"
                                            <?php echo e(old('MagazijnLocatie', $product->MagazijnLocatie) === $locatie ? 'selected' : ''); ?>>
                                            <?php echo e($locatie); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="Ontvangstdatum" class="form-label mb-0">Ontvangstdatum</label>
                            </th>
                            <td>
                                <input type="date" id="Ontvangstdatum" name="Ontvangstdatum"
                                    class="form-control <?php $__errorArgs = ['Ontvangstdatum'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('Ontvangstdatum', \Carbon\Carbon::parse($product->Ontvangstdatum)->format('Y-m-d'))); ?>"
                                    required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="AantalUitgeleverd" class="form-label mb-0">Aantal uitgeleverde producten</label>
                            </th>
                            <td>
                                <input type="number" id="AantalUitgeleverd" name="AantalUitgeleverd"
                                    class="form-control <?php $__errorArgs = ['AantalUitgeleverd'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    min="0"
                                    value="<?php echo e(old('AantalUitgeleverd', 0)); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="Uitleveringsdatum" class="form-label mb-0">Uitleveringsdatum</label>
                            </th>
                            <td>
                                <input type="date" id="Uitleveringsdatum" name="Uitleveringsdatum"
                                    class="form-control <?php $__errorArgs = ['Uitleveringsdatum'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('Uitleveringsdatum', $product->Uitleveringsdatum ? \Carbon\Carbon::parse($product->Uitleveringsdatum)->format('Y-m-d') : '')); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="AantalOpVoorraad" class="form-label mb-0">Aantal op voorraad</label>
                            </th>
                            <td>
                                <input type="number" id="AantalOpVoorraad" name="AantalOpVoorraad"
                                    class="form-control <?php $__errorArgs = ['AantalOpVoorraad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    min="0"
                                    value="<?php echo e(old('AantalOpVoorraad', $product->AantalOpVoorraad)); ?>" required>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-info text-white">Wijzig Product Details</button>
                    <a href="<?php echo e(route('voorraad.show', $product->ProductPerMagazijnId)); ?>" class="btn btn-info text-white ms-auto">Annuleren</a>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-info text-white">Home</a>
                </div>
            </form>

        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/voorraad/edit.blade.php ENDPATH**/ ?>