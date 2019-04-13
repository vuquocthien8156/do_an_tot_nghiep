let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.browserSync(process.env.APP_URL);

mix

	.js('resources/assets/js/common.js', 'public/js')
    
    .js('resources/assets/js/logintest/login/login.js', 'public/js/logintest/login')

    .js('resources/assets/js/vehicle/manage-vehicle/manage-vehicle.js', 'public/js/vehicle/manage-vehicle')
    
    

    .js('resources/assets/js/notification/manage-notification/manage-notification.js', 'public/js/notification/manage-notification')



    // add new JS config above this line
    .js('resources/assets/js/app.js', 'public/js')
    .extract(['vue'])

    .sass('resources/assets/scss/app.scss', 'public/css')
    

    .scripts([
        'resources/assets/js/libs/modernizr/modernizr-custom.js',
    ], 'public/js/ui.js')
    .scripts([
        'resources/assets/js/libs/prefixfree/prefixfree.min.js',
        'resources/assets/js/libs/prefixfree/prefixfree.viewport-units.js',
    ], 'public/js/lib/prefixfree.min.js')
    .scripts([
		'resources/assets/js/libs/nicecountryinput/niceCountryInput.js'
    ], 'public/js/lib/niceCountryInput.js')
    .scripts([
		'resources/assets/js/libs/notify/notify.min.js'
    ], 'public/js/lib/notify.min.js')
;


if (mix.inProduction()) {
    mix.version();
}