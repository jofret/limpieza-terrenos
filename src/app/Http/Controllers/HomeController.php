<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Services\Altoparque\AltoparqueApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class HomeController extends Controller
{
    /**
     * Página de inicio
     */
    public function index(AltoparqueApiClient $altoparque)
    {
        // Últimos 6 posts destacados
        $featuredPosts = Post::with('category')
            ->where('is_published', true)
            ->where('is_featured', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // Últimos 9 posts en general
        $latestPosts = Post::with('category')
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(9)
            ->get();

        // Categorías activas
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Testimonios reales: ahora viven en altoparque.com (Survey ya no se
        // crea localmente) — se piden por API y se arma un objeto con la
        // misma forma que esperaba partials/testimonios.blade.php cuando
        // leía Eloquent local, para no tener que tocar esa vista.
        $testimonials = $this->testimonialsFromAltoparque($altoparque);

        return view('home', compact(
            'featuredPosts',
            'latestPosts',
            'categories',
            'testimonials'
        ));
    }

    /**
     * Si la API central no responde, la home no debe caerse entera por
     * eso — se loguea y se muestra la home sin la sección de testimonios
     * (mismo criterio que WhatsappConversationResource::pendingAttentionCount()).
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function testimonialsFromAltoparque(AltoparqueApiClient $altoparque)
    {
        try {
            $testimonials = collect($altoparque->testimonials());
        } catch (Throwable $e) {
            Log::warning('No se pudieron obtener los testimonios desde Altoparque.', [
                'error' => $e->getMessage(),
            ]);

            return collect();
        }

        $posts = Post::with('category')
            ->whereIn('id', $testimonials->pluck('post_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        return $testimonials
            ->filter(fn (array $t) => filled($t['comment']) && filled($t['customer_name']))
            ->map(fn (array $t) => (object) [
                'id' => $t['id'],
                'comment' => $t['comment'],
                'customer' => (object) [
                    'name' => $t['customer_name'],
                    'zone' => $t['customer_zone'],
                    'zona_principal' => null,
                ],
                'post' => $t['post_id'] ? $posts->get($t['post_id']) : null,
            ])
            ->take(9)
            ->values();
    }
}