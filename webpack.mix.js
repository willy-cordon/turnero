const mix = require('laravel-mix');

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

mix.webpackConfig({
    module: {
        rules: [
            {
                test: /datatables\.net-bs4./,
                loader: 'imports-loader?define=>false'
            },
        ]
    }
});


mix.js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/datatables.js', 'public/js')
      .copyDirectory('resources/assets/external_modules/scheduler', 'public/js/scheduler')
        .sass('resources/assets/sass/app.scss', 'public/css')
          .sass('resources/assets/sass/custom.scss', 'public/css')
            .sass('resources/assets/sass/datatables.scss', 'public/css')
              .copy('resources/assets/js/mapInput.js', 'public/js');






//O se ponene en el vendor o sino hay que hacer un copy a public e incluirlo desde el js/css