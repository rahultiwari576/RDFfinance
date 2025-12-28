

<?php $__env->startSection('title', 'Welcome'); ?>

<?php $__env->startSection('content'); ?>
<div class="text-center py-5">
    <h1 class="display-5">Welcome to <?php echo e(config('app.name')); ?></h1>
    <p class="lead">Secure authentication, smart loan management, and powerful EMI tools in one place.</p>
    <div class="mt-4">
        <a href="<?php echo e(route('login')); ?>" class="btn btn-primary btn-lg me-2">Login</a>
        <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-primary btn-lg me-2">Register</a>
        <a href="<?php echo e(route('emi.guest')); ?>" class="btn btn-success btn-lg">Try Smart EMI Calculator</a>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\ARTtoframe_rahul\finance\resources\views/welcome.blade.php ENDPATH**/ ?>