const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/pages/auth.js', 'public/js')
    .js('resources/js/pages/registration.js', 'public/js')
    .js('resources/js/pages/home.js', 'public/js')
    .js('resources/js/pages/emi-calculator.js', 'public/js')
    .js('resources/js/pages/admin-loan-application.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps()
    .version();

