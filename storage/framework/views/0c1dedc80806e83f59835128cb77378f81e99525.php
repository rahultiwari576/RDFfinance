<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(config('app.name')); ?> - <?php echo $__env->yieldContent('title', 'Finance Portal'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo e(mix('css/app.css')); ?>">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(url('/')); ?>"><?php echo e(config('app.name')); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(auth()->guard()->check()): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('home')); ?>" class="nav-link">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('emi.guest')); ?>" class="nav-link">Smart EMI Calculator</a>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-sm btn-outline-light ms-3" id="logoutButton" data-url="<?php echo e(route('logout')); ?>">Logout</button>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('login')); ?>" class="nav-link">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('register')); ?>" class="nav-link">Register</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('emi.guest')); ?>" class="nav-link">Smart EMI Calculator</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.DEFAULT_PENALTY = <?php echo e(config('loan.default_penalty', 100)); ?>;
    </script>
    <script src="<?php echo e(mix('js/app.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH D:\ARTtoframe_rahul\finance\resources\views/layouts/app.blade.php ENDPATH**/ ?>