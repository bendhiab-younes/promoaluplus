@extends('layouts.app')

@section('title', __('messages.seo_title_portfolio'))
@section('meta_description', __('messages.seo_desc_portfolio'))
@section('og_title', __('messages.seo_title_portfolio'))
@section('og_description', __('messages.seo_desc_portfolio'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-28 md:pt-32 pb-16 md:pb-20 relative">
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-4 border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                {{ __('messages.nav_portfolio') }}
            </span>
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 md:mb-6">{{ __('messages.our_projects') }}</h1>
            <p class="text-lg md:text-xl text-blue-100/90 max-w-2xl mx-auto leading-relaxed">
                {{ __('messages.portfolio_intro') }}
            </p>
        </div>
    </section>

    <!-- Portfolio Filter -->
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 md:px-8">
            <!-- Filter Buttons -->
            <div class="flex flex-wrap justify-center gap-3 md:gap-4 mb-10 md:mb-14 scroll-fade">
                <a href="{{ route('portfolio') }}" 
                   class="portfolio-filter px-5 md:px-6 py-2.5 rounded-full font-semibold transition-all duration-300 {{ $category === 'all' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('messages.all') }}
                </a>
                <a href="{{ route('portfolio', ['category' => 'windows']) }}" 
                   class="portfolio-filter px-5 md:px-6 py-2.5 rounded-full font-semibold transition-all duration-300 {{ $category === 'windows' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('messages.windows') }}
                </a>
                <a href="{{ route('portfolio', ['category' => 'doors']) }}" 
                   class="portfolio-filter px-5 md:px-6 py-2.5 rounded-full font-semibold transition-all duration-300 {{ $category === 'doors' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('messages.doors') }}
                </a>
                <a href="{{ route('portfolio', ['category' => 'facades']) }}" 
                   class="portfolio-filter px-5 md:px-6 py-2.5 rounded-full font-semibold transition-all duration-300 {{ $category === 'facades' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('messages.facades') }}
                </a>
            </div>

            <!-- Portfolio Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @forelse($projects as $project)
                <div class="portfolio-item group relative overflow-hidden rounded-2xl shadow-lg scroll-fade">
                    <img src="{{ $project->image ? asset('storage/' . $project->image) : 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }}" 
                         alt="{{ $project->getTranslatedTitle() }}" 
                         loading="lazy"
                         decoding="async"
                         class="w-full h-72 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <h3 class="text-xl font-bold mb-2">{{ $project->getTranslatedTitle() }}</h3>
                            <p class="text-sm text-gray-200 mb-2">{{ $project->location }}</p>
                            <span class="inline-block px-3 py-1 bg-blue-600 rounded-full text-xs">{{ __('messages.' . $project->category) }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Default projects if none in database -->
                <div class="portfolio-item group relative overflow-hidden rounded-xl shadow-lg">
                    <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=600&q=80" 
                         alt="Villa Moderne - La Marsa" 
                         loading="lazy"
                         decoding="async"
                         class="w-full h-72 object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <h3 class="text-xl font-bold mb-2">Villa Moderne - La Marsa</h3>
                            <p class="text-sm text-gray-200 mb-2">La Marsa, Tunis</p>
                            <span class="inline-block px-3 py-1 bg-blue-600 rounded-full text-xs">{{ __('messages.windows') }}</span>
                        </div>
                    </div>
                </div>
                <div class="portfolio-item group relative overflow-hidden rounded-xl shadow-lg">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&q=80" 
                         alt="Résidence Carthage" 
                         loading="lazy"
                         decoding="async"
                         class="w-full h-72 object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <h3 class="text-xl font-bold mb-2">Résidence Carthage</h3>
                            <p class="text-sm text-gray-200 mb-2">Carthage, Tunis</p>
                            <span class="inline-block px-3 py-1 bg-orange-600 rounded-full text-xs">{{ __('messages.doors') }}</span>
                        </div>
                    </div>
                </div>
                <div class="portfolio-item group relative overflow-hidden rounded-xl shadow-lg">
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&q=80" 
                         alt="Immeuble Commercial" 
                         loading="lazy"
                         decoding="async"
                         class="w-full h-72 object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <h3 class="text-xl font-bold mb-2">Immeuble Commercial</h3>
                            <p class="text-sm text-gray-200 mb-2">Centre Urbain Nord</p>
                            <span class="inline-block px-3 py-1 bg-green-600 rounded-full text-xs">{{ __('messages.facades') }}</span>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    {{ __('messages.testimonials_badge') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __('messages.client_testimonials') }}</h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">{{ __('messages.testimonials_subtitle') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @forelse($testimonials as $testimonial)
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-transparent hover:border-blue-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < $testimonial->rating; $i++)
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ $testimonial->getTranslatedContent() }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center me-4">
                            <span class="text-blue-600 font-bold">{{ substr($testimonial->client_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $testimonial->client_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $testimonial->client_location }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Default testimonials -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-transparent hover:border-blue-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_1') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center me-4">
                            <span class="text-blue-600 font-bold">M</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Mohamed B.</h4>
                            <p class="text-sm text-gray-500">Paris, France</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-transparent hover:border-blue-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_2') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center me-4">
                            <span class="text-green-600 font-bold">S</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Sonia K.</h4>
                            <p class="text-sm text-gray-500">Montréal, Canada</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-transparent hover:border-blue-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_3') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center me-4">
                            <span class="text-orange-600 font-bold">A</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Ahmed T.</h4>
                            <p class="text-sm text-gray-500">Berlin, Allemagne</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-12 md:py-20 lg:py-24 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <div class="max-w-3xl mx-auto">
                <span class="inline-block px-5 py-2 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-6 border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ __('messages.start_your_project') }}
                </span>
                <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">{{ __('messages.start_your_project') }}</h2>
                <p class="text-lg md:text-xl text-blue-200 mb-8 leading-relaxed max-w-2xl mx-auto">{{ __('messages.cta_description') }}</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center shadow-2xl hover:shadow-orange-500/40 hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        {{ __('messages.request_quote') }}
                    </a>
                    <a href="{{ route('services') }}" class="group bg-white/10 backdrop-blur-sm hover:bg-white text-white hover:text-blue-900 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center border-2 border-white/30 hover:border-white hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        {{ __('messages.view_all_services') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
