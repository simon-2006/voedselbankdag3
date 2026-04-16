<?php $__env->startSection('title', 'Inloggen | Voedselbank Samen'); ?>

<?php $__env->startSection('content'); ?>
    <section class="row justify-content-center reveal">
        <div class="col-xl-10">
            <div class="card border-0 shadow-lg auth-panel overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-5 p-4 p-lg-5 bg-light border-end">
                        <p class="section-eyebrow mb-2">Welkom terug</p>
                        <h1 class="display-6 fw-semibold mb-3">Log in op je account</h1>
                        <p class="text-secondary mb-0">Log in met het e-mailadres uit de tabel Gebruiker.</p>
                    </div>

                    <div class="col-lg-7 p-4 p-lg-5">
                        <form method="POST" action="<?php echo e(route('login')); ?>" class="vstack gap-3">
                            <?php echo csrf_field(); ?>

                            <div>
                                <label for="email" class="form-label fw-semibold">E-mail</label>
                                <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" class="form-control form-control-lg <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required autocomplete="email">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="password" class="form-label fw-semibold">Wachtwoord</label>
                                <input id="password" name="password" type="password" class="form-control form-control-lg <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required autocomplete="current-password">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">Inloggen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/auth/login.blade.php ENDPATH**/ ?>