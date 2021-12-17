let mix = require('laravel-mix');

mix.js('resources/js/adminr-core.js', 'resources/assets/js')
    .vue()
    .sass('resources/sass/adminr-core.scss', 'resources/assets/css');
    // .sass('resources/sass/coreui.scss', 'resources/assets/coreui/css');
