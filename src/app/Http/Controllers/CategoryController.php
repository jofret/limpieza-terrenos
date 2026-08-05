<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Resuelve /{slug} como categoría y, si no existe, como tag.
     * Una categoría y un tag con el mismo slug no pueden convivir: la
     * categoría gana siempre (ver reject() de colisiones en el partial
     * includes.popular-searches y en SitemapController).
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($category) {
            return $this->showCategory($category);
        }

        $tag = Tag::where('slug', $slug)->first();

        if ($tag) {
            return app(TagController::class)->show($slug);
        }

        abort(404);
    }

    private function showCategory(Category $category)
    {
        $posts = Post::with('category', 'tags')
            ->where('category_id', $category->id)
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        // Meta tags para SEO
        $metaTitle = $category->meta_title;
        $metaDescription = $category->meta_description;

        return view('categories.show', compact('category', 'posts', 'metaTitle', 'metaDescription'));
    }
}