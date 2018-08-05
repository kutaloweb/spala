let mix = require('laravel-mix');                           // If you are new to this then please visit https://laravel.com/docs/5.5/mix
const webpack = require('webpack');

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

let plugin = 'resources/assets/plugins/';
const CompressionPlugin = require('compression-webpack-plugin');

mix.js('resources/assets/js/app.js', 'public/js/app.js')
    .combine([
        plugin + 'jquery/jquery.min.js',
        plugin + 'popper/popper.min.js',
        plugin + 'bootstrap/bootstrap.min.js',
        plugin + 'sidebarmenu.js',
        plugin + 'moment/moment-with-locales.min.js',
        plugin + 'toastr/toastr.min.js',
        plugin + 'slimscroll/jquery.slimscroll.js',
        plugin + 'waves/waves.js',
        plugin + 'summernote/summernote-bs4.js',
        'resources/assets/js/custom.js'
    ], 'public/js/plugin.js')
    .sass('resources/assets/sass/style.scss', 'public/css')
    .sass('resources/assets/sass/colors/blue.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/blue-dark.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/default.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/default-dark.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/green.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/green-dark.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/megna.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/megna-dark.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/purple.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/purple-dark.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/red.scss', 'public/css/colors')
    .sass('resources/assets/sass/colors/red-dark.scss', 'public/css/colors')
    .webpackConfig({
        plugins: [
            new CompressionPlugin({
                asset: "[path].gz[query]",
                algorithm: "gzip",
                test: /\.js$|\.css$|\.html$|\.svg$/,
                threshold: 10240,
                minRatio: 0.8
            }),
            new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
        ]
    });

if (mix.inProduction()) {
    mix.version();
}
