<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/js/lang', function () {
    if (App::environment('local'))
        Cache::forget('lang.js');
    $strings = Cache::rememberForever('lang.js', function () {
        $lang = config('app.locale');
        $files = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];
        foreach ($files as $file) {
            $name = basename($file, '.php');
            $strings[$name] = require $file;
        }
        return $strings;
    });
    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode($strings) . ';');
    exit();
})->name('assets.lang');

Route::get('/{vue?}', function () {
    if(Crawler::isCrawler()) {
        $category = App\Category::where('slug', Request::segment(1))->first();
        if (empty($category)) return view('seo')->with('article', null);
        $article = App\Post::filterByCategoryAndSlug($category, Request::segment(2))->first();
        return view('seo')->with('article', $article);
    }
    return view('home');
})->where('vue', '[\/\w\.-]*')->name('home');
