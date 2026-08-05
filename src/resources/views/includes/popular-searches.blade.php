{{--
    Nube combinada de categorías + tags ("Búsquedas Populares").
    Self-contained: hace sus propias queries, no depende de que el controller
    le pase datos, así se puede incluir en cualquier vista sin tocar controllers.

    Si un tag tiene el mismo slug que una categoría activa, se excluye de esta
    nube: /{slug} siempre resuelve la categoría primero (ver CategoryController),
    así que ese tag no tiene URL propia alcanzable.
--}}
@php
    $popularSearchCategories = \App\Models\Category::where('is_active', true)
        ->whereHas('posts', fn ($q) => $q->where('is_published', true)->where('published_at', '<=', now()))
        ->get();

    $popularSearchCategorySlugs = $popularSearchCategories->pluck('slug');

    $popularSearchItems = $popularSearchCategories->map(fn ($category) => [
        'label' => $category->name,
        'url' => url('/' . $category->slug) . '#resultados',
    ])->concat(
        \App\Models\Tag::whereHas('posts', fn ($q) => $q->where('is_published', true)->where('published_at', '<=', now()))
            ->get()
            ->reject(fn ($tag) => $popularSearchCategorySlugs->contains($tag->slug))
            ->map(fn ($tag) => [
                'label' => $tag->name,
                'url' => url('/' . $tag->slug) . '#resultados',
            ])
    )->shuffle();
@endphp

@if($popularSearchItems->isNotEmpty())
<section class="py-16 bg-gray-50 rounded-xl shadow-sm mb-8">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">Búsquedas Populares</h2>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach($popularSearchItems as $item)
            <a href="{{ $item['url'] }}" class="bg-white px-4 py-2 rounded-full text-gray-700 hover:bg-green-700 hover:text-white transition shadow-sm" aria-label="Ver trabajos de {{ $item['label'] }}">
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
