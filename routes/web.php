<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CurriculoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\TermController;

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


Route::get('/empregos/aberto-o-concurso-publico-da-policia-nacional-de-angola', function () {
    return redirect('/articles/policia-nacional-de-angola-desmente-boatos-de-concurso-publico');
});

Route::get('/articles/novo-portal-de-recrutamento-da-policia-nacional-de-angola-revela-um-possivel-recrutamento', function () {
    return redirect('/articles/policia-nacional-de-angola-desmente-boatos-de-concurso-publico');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/terms', [TermController::class, 'index'])->name('terms');

Route::get('/categories/{id}', [JobController::class, 'getByCategoryId'])
    ->where('id', '[0-9]+');

Route::get('/empregos', [JobController::class, 'index']);
Route::get('/empregos/{slug}', [JobController::class, 'getBySlug']);


Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'getById'])
    ->where('id', '[0-9]+');

Route::get('/pesquisar', [JobController::class, 'search'])->name('search');

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'getById'])
    ->where('id', '[0-9]+');
Route::get('/articles/{slug}', [ArticleController::class, 'getBySlug']);
//Route::get('/articles/{slug}/amp', [ArticleController::class, 'getBySlugAMP']);

Route::get('/modelos-de-curriculos', [CurriculoController::class, 'index']);
Route::get('/modelos-de-curriculos/{slug}', [CurriculoController::class, 'getBySlug']);

Route::get('/onlineocr', [OcrController::class, 'index'])->name('ocr');
Route::get('/quiz', [OcrController::class, 'indexQuizPt'])->name('quiz');
Route::get('/en/onlineocr', [OcrController::class, 'indexEn'])->name('ocren');
Route::get('/en/dashboard', [OcrController::class, 'indexDashboardEn'])->name('dashboardEn');
Route::get('/dashboard', [OcrController::class, 'indexDashboardPt'])->name('dashboardPt');

Route::get('/sitemap.xml', [JobController::class, 'siteMapGenerator'])->name('sitemap');
Route::get('/feed', [JobController::class, 'feedGenerator'])->name('feed');



Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});
