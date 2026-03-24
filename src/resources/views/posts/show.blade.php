@extends('layouts.app')

@section('meta_title', $post->meta_title ?? $post->title)
@section('meta_description', 'Limpieza y Desmalezado de terrenos WhatsApp ✅ 11 7178 9529 | ' . strip_tags($post->excerpt ?? Str::limit($post->content, 150)))

@section('og_image', $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-og.jpg'))

@section('content')
    {{-- Breadcrumbs --}}
    <div class="container mx-auto px-4 py-4">
        <nav class="text-sm text-gray-600">
            <a href="/" class="hover:text-green-700">Inicio</a> /
            <a href="/{{ $post->category->slug }}" class="hover:text-green-700">{{ $post->category->name }}</a> /
            <span class="text-gray-800">{{ $post->title }}</span>
        </nav>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-12">
            {{-- Contenido principal --}}
            <div class="lg:w-2/3">
                <article class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 md:p-8">
                        {{-- Imagen destacada con lightbox (unificada con galería) --}}
                        @if($post->featured_image)
                            <div class="mb-6 rounded-lg overflow-hidden">
                                <a href="{{ Storage::url($post->featured_image) }}" 
                                   data-lightbox="post-gallery" 
                                   data-title="{{ $post->title }} - Imagen destacada">
                                    <img src="{{ Storage::url($post->featured_image) }}" 
                                         alt="{{ $post->title }}" 
                                         class="w-full h-auto object-cover cursor-pointer hover:opacity-95 transition">
                                </a>
                            </div>
                        @endif

                        <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $post->title }}</h1>

                        @if($post->subtitle)
                            <p class="text-xl text-gray-600 mb-6">{{ $post->subtitle }}</p>
                        @endif

                        <div class="prose max-w-none">
                            {!! $post->content !!}
                        </div>

                        {{-- Galería con lightbox (mismo grupo) --}}
                        @if($post->gallery_images && count($post->gallery_images) > 0)
                            <div class="mt-12">
                                <h3 class="text-2xl font-bold mb-4">Galería de imágenes</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($post->gallery_images as $index => $image)
                                        <a href="{{ Storage::url($image) }}" 
                                           data-lightbox="post-gallery" 
                                           data-title="{{ $post->title }} - Imagen {{ $index+1 }}">
                                            <div class="rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                                <img src="{{ Storage::url($image) }}" 
                                                     alt="{{ $post->title }}" 
                                                     class="w-full h-48 object-cover hover:scale-105 transition duration-300">
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Etiquetas del post --}}
                        @if($post->tags->count() > 0)
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <h3 class="font-semibold mb-2">Etiquetas de este post:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($post->tags as $tag)
                                        <a href="/tag/{{ $tag->slug }}" class="bg-gray-200 px-3 py-1 rounded-full text-sm hover:bg-green-600 hover:text-white transition">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </article>
            </div>

            {{-- Sidebar --}}
            <aside class="lg:w-1/3 space-y-8">
                {{-- Contacto rápido --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-phone-alt text-green-600"></i>
                        Contacto
                    </h3>
                    <div class="space-y-3 text-gray-600">
                        <p class="flex items-center gap-2"><i class="fas fa-map-marker-alt w-5 text-green-600"></i> Buenos Aires, Argentina</p>
                        <p class="flex items-center gap-2"><i class="fab fa-whatsapp w-5 text-green-600"></i> <a href="https://wa.me/5491171789529" class="hover:text-green-700">11 7178-9529</a></p>
                        <p class="flex items-center gap-2"><i class="fas fa-envelope w-5 text-green-600"></i> <a href="mailto:info@serviciodejardineria.com.ar" class="hover:text-green-700">info@serviciodejardineria.com.ar</a></p>
                        <p class="flex items-center gap-2"><i class="fab fa-facebook-f w-5 text-green-600"></i> <a href="https://www.facebook.com/cortamospastoyjardines" target="_blank" class="hover:text-green-700">Síguenos</a></p>
                    </div>
                </div>

                {{-- Categorías --}}
                @php
                    $categories = App\Models\Category::withCount('posts')->orderBy('name')->get();
                @endphp
                @if($categories->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-xl font-bold mb-4">Categorías</h3>
                    <ul class="space-y-2">
                        @foreach($categories as $cat)
                            <li><a href="/{{ $cat->slug }}" class="text-gray-600 hover:text-green-700 transition flex justify-between"><span>{{ $cat->name }}</span><span class="text-sm text-gray-400">({{ $cat->posts_count }})</span></a></li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Etiquetas populares --}}
                @php
                    $popularTags = App\Models\Tag::withCount('posts')->orderBy('posts_count', 'desc')->limit(10)->get();
                @endphp
                @if($popularTags->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-xl font-bold mb-4">Etiquetas populares</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($popularTags as $tag)
                            <a href="/tag/{{ $tag->slug }}" class="bg-gray-100 hover:bg-green-700 hover:text-white px-3 py-1 rounded-full text-sm transition">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Últimas publicaciones --}}
                @php
                    $recentPosts = App\Models\Post::where('is_published', true)
                                    ->where('id', '!=', $post->id)
                                    ->latest('published_at')
                                    ->limit(5)
                                    ->get();
                @endphp
                @if($recentPosts->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-xl font-bold mb-4">Últimas publicaciones</h3>
                    <div class="space-y-3">
                        @foreach($recentPosts as $recent)
                            <div class="border-b border-gray-100 last:border-0 pb-3 last:pb-0">
                                <a href="{{ url($recent->category->slug . '/' . $recent->slug) }}" class="block hover:text-green-700 transition">
                                    <p class="font-medium">{{ $recent->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $recent->formatted_date }}</p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>

    {{-- FORMULARIO DE CONTACTO (igual al de home) --}}
    <div id="contacto-formulario" class="relative w-full py-16 my-8" 
         style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1589923188900-85dae523342b?w=1200') fixed center/cover;">
        
        <div class="container mx-auto px-4">
            <div class="flex justify-center">
                <div class="w-full md:w-2/3 lg:w-1/2">
                    <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 wow fadeIn" data-wow-delay="0.5s">
                        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Contáctenos</h2>
                        
                        @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                        @endif

                        <form id="contact-form" action="{{ route('contacto.enviar') }}" method="POST">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Zona *</label>
                                    <select id="zona_principal" name="zona_principal" 
                                            class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 @error('zona_principal') border-red-500 @enderror"
                                            required>
                                        <option value="">Seleccione zona...</option>
                                        <option value="CABA">CABA</option>
                                        <option value="Zona Norte">Zona Norte</option>
                                        <option value="Zona Oeste">Zona Oeste</option>
                                        <option value="Otra">Otra zona (especificar)</option>
                                    </select>
                                    @error('zona_principal')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Localidad / Partido *</label>
                                    <select id="partido" name="partido" 
                                            class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 @error('partido') border-red-500 @enderror"
                                            required>
                                        <option value="">Primero seleccione zona</option>
                                    </select>
                                    @error('partido')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div id="otra_zona_container" class="mb-4 hidden">
                                <label class="block text-gray-700 font-medium mb-1">Especificar otra zona/localidad *</label>
                                <input type="text" id="otra_zona" name="otra_zona"
                                       class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 @error('otra_zona') border-red-500 @enderror"
                                       placeholder="Ej: La Plata, Berisso, Mar del Plata, etc.">
                                @error('otra_zona')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Nombre *</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                           class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror"
                                           placeholder="Tu nombre" required>
                                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Email *</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                           class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 @error('email') border-red-500 @enderror"
                                           placeholder="tu@email.com" required>
                                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Teléfono *</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}"
                                           class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 @error('phone') border-red-500 @enderror"
                                           placeholder="11 7178-9529" required>
                                    @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Servicio *</label>
                                    <select name="service" class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500" required>
                                        <option value="">Seleccione...</option>
                                        <option value="desmalezado">Desmalezado</option>
                                        <option value="limpieza">Limpieza de Terrenos</option>
                                        <option value="roza">Roza</option>
                                        <option value="prevencion">Prevención de Incendios</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-1">Mensaje *</label>
                                <textarea name="message" rows="4"
                                          class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 @error('message') border-red-500 @enderror"
                                          placeholder="Escribí tu consulta..." required>{{ old('message') }}</textarea>
                                @error('message')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="text-center mt-6">
                                <x-honey recaptcha/>
                                <button type="submit" class="bg-green-700 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-green-800 transition transform hover:scale-105 shadow-lg inline-flex items-center">
                                    Enviar Ahora <i class="fas fa-paper-plane ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TESTIMONIOS (igual al de home) --}}
    <section id="testimonios" class="py-16 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">¿Qué dicen nuestros clientes?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">La plena satisfacción de nuestros clientes es nuestro principal objetivo.</p>
            </div>

            <div x-data="{
                testimonios: [
                    { id:1, nombre:'Carlos Rodríguez', ubicacion:'Pilar', tamaño:'5000m²', texto:'Excelente servicio! Tenía el terreno abandonado hace años y lo dejaron impecable.', imagen:'https://images.unsplash.com/photo-1589923188900-85dae523342b?w=600', tipo:'Antes', rating:5 },
                    { id:2, nombre:'Martina González', ubicacion:'Escobar', tamaño:'2000m²', texto:'Rápidos y eficientes. Me salvaron de una multa municipal.', imagen:'https://images.unsplash.com/photo-1589923188650-aa8f6d441a9b?w=600', tipo:'Después', rating:5 },
                    { id:3, nombre:'Juan Pérez', ubicacion:'Campana', tamaño:'5 hectáreas', texto:'Contraté el servicio para un campo de 5 hectáreas. Dejaron todo impecable.', imagen:'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=600', tipo:'Resultado', rating:5 }
                ],
                currentIndex: 0,
                autoplayInterval: null,
                getItemsPerSlide() { return window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1; },
                get totalSlides() { return Math.ceil(this.testimonios.length / this.getItemsPerSlide()); },
                next() { this.currentIndex = (this.currentIndex + 1) % this.totalSlides; },
                prev() { this.currentIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides; },
                startAutoplay() { this.autoplayInterval = setInterval(() => this.next(), 5000); },
                stopAutoplay() { clearInterval(this.autoplayInterval); },
                init() { this.startAutoplay(); }
            }"
            x-init="init()"
            @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()"
            class="relative max-w-7xl mx-auto">
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-in-out"
                         :style="'transform: translateX(-' + (currentIndex * 100 / getItemsPerSlide()) + '%)'">
                        <template x-for="testimonio in testimonios" :key="testimonio.id">
                            <div class="flex-shrink-0 px-3" :style="'width: ' + (100 / getItemsPerSlide()) + '%'">
                                <div class="bg-white rounded-xl shadow-lg overflow-hidden h-full hover:shadow-xl transition card-hover">
                                    <div class="relative h-48 overflow-hidden">
                                        <img :src="testimonio.imagen" :alt="'Terreno ' + testimonio.tipo" class="w-full h-full object-cover hover:scale-110 transition duration-500">
                                        <div class="absolute top-2 left-2 bg-green-600 text-white px-2 py-1 rounded-full text-xs font-bold" x-text="testimonio.tipo"></div>
                                    </div>
                                    <div class="p-6">
                                        <div class="text-yellow-400 flex mb-3">
                                            <template x-for="i in testimonio.rating"><i class="fas fa-star"></i></template>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-4 italic line-clamp-4" x-text="testimonio.texto"></p>
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-700 font-bold text-lg" x-text="testimonio.nombre.charAt(0)"></div>
                                            <div class="ml-3">
                                                <h4 class="font-bold text-gray-800" x-text="testimonio.nombre"></h4>
                                                <p class="text-xs text-gray-500"><span x-text="testimonio.ubicacion"></span> · <span x-text="testimonio.tamaño"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <button @click="prev()" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 bg-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center text-green-700 hover:bg-green-700 hover:text-white transition z-10"><i class="fas fa-chevron-left text-xl"></i></button>
                <button @click="next()" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 bg-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center text-green-700 hover:bg-green-700 hover:text-white transition z-10"><i class="fas fa-chevron-right text-xl"></i></button>
                <div class="flex justify-center mt-8 space-x-2">
                    <template x-for="(slide, index) in Array.from({ length: totalSlides })" :key="index">
                        <button @click="currentIndex = index" class="w-3 h-3 rounded-full transition-all duration-300" :class="currentIndex === index ? 'bg-green-700 w-6' : 'bg-gray-300 hover:bg-green-500'"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    {{-- Script para el formulario de contacto (igual al de home) --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const zonaPrincipal = document.getElementById('zona_principal');
        const partido = document.getElementById('partido');
        const otraZonaContainer = document.getElementById('otra_zona_container');
        const otraZona = document.getElementById('otra_zona');
        const form = document.getElementById('contact-form');

        const localidades = {
            'CABA': ['Palermo', 'Belgrano', 'Recoleta', 'Puerto Madero', 
                     'Caballito', 'Almagro', 'Villa Crespo', 'Colegiales',
                     'Nuñez', 'Saavedra', 'Villa Urquiza', 'Villa Devoto'],
            'Zona Norte': ['Pilar', 'Escobar', 'Tigre', 'San Isidro', 'Vicente López',
                           'San Fernando', 'San Martín', 'Malvinas Argentinas', 'José C. Paz'],
            'Zona Oeste': ['Moreno', 'Merlo', 'Morón', 'Ituzaingó', 'Hurlingham',
                           'La Matanza', 'Tres de Febrero', 'San Miguel']
        };

        if (zonaPrincipal) {
            zonaPrincipal.addEventListener('change', function() {
                const selected = this.value;
                partido.innerHTML = '<option value="">Seleccione localidad...</option>';
                partido.disabled = false;
                partido.required = true;
                otraZonaContainer.classList.add('hidden');
                otraZona.required = false;

                if (selected === 'Otra') {
                    partido.disabled = true;
                    partido.required = false;
                    otraZonaContainer.classList.remove('hidden');
                    otraZona.required = true;
                } else if (selected && localidades[selected]) {
                    localidades[selected].forEach(function(l) {
                        const option = document.createElement('option');
                        option.value = l;
                        option.textContent = l;
                        partido.appendChild(option);
                    });
                }
            });

            if (form) {
                form.addEventListener('submit', function() {
                    if (zonaPrincipal.value === 'Otra') {
                        partido.required = false;
                        otraZona.required = true;
                    } else {
                        partido.required = true;
                        otraZona.required = false;
                    }
                });
            }

            @if(old('zona_principal'))
                setTimeout(() => {
                    zonaPrincipal.value = "{{ old('zona_principal') }}";
                    zonaPrincipal.dispatchEvent(new Event('change'));
                    @if(old('partido'))
                        setTimeout(() => {
                            partido.value = "{{ old('partido') }}";
                        }, 100);
                    @endif
                }, 100);
            @endif
        }

        @if(session('success') || $errors->any())
            setTimeout(function() {
                const formulario = document.getElementById('contacto-formulario');
                if (formulario) {
                    formulario.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        @endif
    });
    </script>
@endsection

@php
    $featuredImageUrl = $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg');

    $blogPosting = [
        "@context" => "https://schema.org",
        "@type" => "BlogPosting",
        "headline" => $post->title,
        "description" => $post->excerpt ?? strip_tags(Str::limit($post->content, 150)),
        "image" => $featuredImageUrl,
        "datePublished" => $post->published_at->toIso8601String(),
        "dateModified" => $post->updated_at->toIso8601String(),
        "author" => [
            "@type" => "Organization",
            "name" => "Limpieza de Terrenos",
            "url" => url('/')
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => "Limpieza de Terrenos",
            "logo" => [
                "@type" => "ImageObject",
                "url" => asset('images/logo.jpg')
            ]
        ],
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => url()->current()
        ]
    ];

    $breadcrumbPost = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            [
                "@type" => "ListItem",
                "position" => 1,
                "name" => "Inicio",
                "item" => url('/')
            ],
            [
                "@type" => "ListItem",
                "position" => 2,
                "name" => $post->category->name,
                "item" => url('/' . $post->category->slug)
            ],
            [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $post->title,
                "item" => url()->current()
            ]
        ]
    ];
@endphp

@push('schema')
<script type="application/ld+json">
{!! json_encode($blogPosting, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbPost, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush