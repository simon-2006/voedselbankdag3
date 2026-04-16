<?php $__env->startSection('title', 'Overzicht voedselpakketten | Voedselbank Maaskantje'); ?>

<?php $__env->startSection('content'); ?>
    <section class="card border-0 shadow-lg p-4 p-lg-5">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <h1 class="h2 mb-0 text-success">
                Overzicht gezinnen met voedselpakketten
            </h1>

            <form
                method="GET"
                action="<?php echo e(route('voedselpakketten.index')); ?>"
                class="d-flex flex-column flex-sm-row gap-2"
                id="overzichtFilterForm"
                novalidate
            >
                <label for="eetwens_id" class="visually-hidden">Selecteer Eetwens</label>

                <select
                    name="eetwens_id"
                    id="eetwens_id"
                    class="form-select <?php $__errorArgs = ['eetwens_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    aria-label="Selecteer Eetwens"
                >
                    <option value="">Selecteer Eetwens</option>

                    <?php $__currentLoopData = $eetwensen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eetwens): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option
                            value="<?php echo e($eetwens->Id); ?>"
                            <?php if((string) $selectedEetwensId === (string) $eetwens->Id): echo 'selected'; endif; ?>
                        >
                            <?php echo e($eetwens->Naam); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <button type="submit" class="btn btn-secondary fw-semibold px-3">
                    Toon Gezinnen
                </button>
            </form>
        </div>

        <?php $__errorArgs = ['eetwens_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="alert alert-danger mb-4" role="alert">
                <?php echo e($message); ?>

            </div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <?php if(! empty($feedbackMessage) && ! empty($feedbackType)): ?>
            <div class="alert alert-<?php echo e($feedbackType); ?> shadow-sm mb-4" role="alert">
                <?php echo e($feedbackMessage); ?>

            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Naam</th>
                        <th scope="col">Omschrijving</th>
                        <th scope="col">Volwassenen</th>
                        <th scope="col">Kinderen</th>
                        <th scope="col">Babys</th>
                        <th scope="col">Vertegenwoordiger</th>
                        <th scope="col" class="text-center">Voedselpakket Details</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if($gezinnen->isNotEmpty()): ?>
                        <?php $__currentLoopData = $gezinnen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gezin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($gezin->Gezinsnaam); ?></td>
                                <td><?php echo e($gezin->Omschrijving); ?></td>
                                <td><?php echo e($gezin->AantalVolwassenen); ?></td>
                                <td><?php echo e($gezin->AantalKinderen); ?></td>
                                <td><?php echo e($gezin->AantalBabys); ?></td>
                                <td>
                                    <?php echo e(trim($gezin->Vertegenwoordiger) !== '' ? $gezin->Vertegenwoordiger : 'Onbekend'); ?>

                                </td>
                                <td class="text-center">
                                    <a
                                        href="<?php echo e(route('voedselpakketten.gezin.show', ['gezin' => $gezin->GezinId])); ?>"
                                        class="icon-link"
                                        title="<?php echo e($gezin->AantalPakketten); ?> pakket(ten), <?php echo e($gezin->TotaalProductEenheden); ?> producteenheden"
                                        aria-label="Bekijk voedselpakket details voor <?php echo e($gezin->Gezinsnaam); ?>"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M8.5 1.25a.75.75 0 0 0-1 0l-5.5 4.4A.75.75 0 0 0 1.75 6.9v6.35c0 .83.67 1.5 1.5 1.5h9.5c.83 0 1.5-.67 1.5-1.5V6.9a.75.75 0 0 0-.25-.57l-5.5-5.08Zm-5.25 5.9L8 2.84l4.75 4.3v6.1h-9.5v-6.1Z"/>
                                            <path d="M5 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary btn-sm px-3">
                home
            </a>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('overzichtFilterForm');
            const select = document.getElementById('eetwens_id');

            if (!form || !select) {
                return;
            }

            const allowedValues = new Set(
                Array.from(select.options).map(function (option) {
                    return option.value;
                })
            );

            form.addEventListener('submit', function (event) {
                if (!allowedValues.has(select.value)) {
                    event.preventDefault();
                    select.classList.add('is-invalid');
                    return;
                }

                select.classList.remove('is-invalid');
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\vodselbank-op-1-dag-\resources\views/voedselpakketten/index.blade.php ENDPATH**/ ?>