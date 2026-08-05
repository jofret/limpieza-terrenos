<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SurveyController;

/*
|--------------------------------------------------------------------------
| Rutas principales
|--------------------------------------------------------------------------
*/

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Tags (etiquetas)
|--------------------------------------------------------------------------
| Las URLs de tag se fusionaron con las de categoría en /{slug} (más abajo).
| Este redirect 301 es compatibilidad por si /tag/{slug} ya estaba indexado.
*/
Route::redirect('/tag/{slug}', '/{slug}', 301);

// Debe ir antes de las rutas dinámicas de abajo
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

/*
|--------------------------------------------------------------------------
| Contacto (envío del formulario embebido en home/posts vía #contacto-formulario)
|--------------------------------------------------------------------------
*/
Route::post('/contacto/enviar', [ContactController::class, 'send'])
    ->middleware(['honey', 'honey-recaptcha'])
    ->name('contacto.enviar');

// Encuestas públicas
Route::get('/encuesta/{token}', [App\Http\Controllers\SurveyController::class, 'show'])->name('survey.show');
Route::post('/encuesta/{token}', [App\Http\Controllers\SurveyController::class, 'store'])->name('survey.store');

/*
|--------------------------------------------------------------------------
| sitemap
|--------------------------------------------------------------------------
*/


// Sitemap
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Rutas semánticas (sin /categoria) - URLs limpias
|--------------------------------------------------------------------------
| /{slug} sirve tanto categoría como tag (CategoryController resuelve cuál es).
| Estructura: /desmalezado
|           /desmalezado/terreno-en-pilar
*/
Route::get('/{slug}', [CategoryController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('category.show');
Route::get('/{category:slug}/{post:slug}', [PostController::class, 'show'])->name('post.show');



/*
|--------------------------------------------------------------------------
| Fallback (página 404 personalizada)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return view('errors.404');
});

