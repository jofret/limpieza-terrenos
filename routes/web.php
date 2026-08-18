<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MovedLinkController;

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

/*
|--------------------------------------------------------------------------
| Links viejos (encuesta/conformidad/presupuesto) — sistema mudado al panel
| central de Altoparque
|--------------------------------------------------------------------------
| Estas rutas ya no tienen su controller/modelo real (ver borrado de
| Survey/WorkOrder/ServiceOrder): quedan enlaces sueltos de WhatsApp/email
| de antes de la migración. En vez de un 500, se muestra un aviso simple.
*/
Route::get('/encuesta/{token}', [MovedLinkController::class, 'show'])->name('survey.show');
Route::post('/encuesta/{token}', [MovedLinkController::class, 'show'])->name('survey.store');
Route::get('/conformidad/{token}', [MovedLinkController::class, 'show'])->name('conformity.show');
Route::post('/conformidad/{token}/confirmar', [MovedLinkController::class, 'show'])->name('conformity.confirm');
Route::get('/presupuesto/{token}', [MovedLinkController::class, 'show'])->name('budget.show');
Route::post('/presupuesto/{token}/aceptar', [MovedLinkController::class, 'show'])->name('budget.accept');

// Webhook de WhatsApp (Claudia)
Route::get('/webhook/whatsapp', [App\Http\Controllers\WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
Route::post('/webhook/whatsapp', [App\Http\Controllers\WhatsAppWebhookController::class, 'receive'])->name('whatsapp.webhook.receive');

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

