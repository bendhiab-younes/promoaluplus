@extends('layouts.app')

@section('title', __('messages.nav_portfolio'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-32 pb-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">{{ __('messages.our_projects') }}</h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                {{ __('messages.portfolio_intro') }}
            </p>
        </div>
    </section>

    <!-- Portfolio Filter -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <!-- Filter Buttons -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <a href="{{ route('portfolio') }}" 
                   class="portfolio-filter px-6 py-2 rounded-full font-medium transition-all {{ $category === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ __('messages.all') }}
                </a>
                <a href="{{ route('portfolio', ['category' => 'windows']) }}" 
                   class="portfolio-filter px-6 py-2 rounded-full font-medium transition-all {{ $category === 'windows' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ __('messages.windows') }}
                </a>
                <a href="{{ route('portfolio', ['category' => 'doors']) }}" 
                   class="portfolio-filter px-6 py-2 rounded-full font-medium transition-all {{ $category === 'doors' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ __('messages.doors') }}
                </a>
                <a href="{{ route('portfolio', ['category' => 'facades']) }}" 
                   class="portfolio-filter px-6 py-2 rounded-full font-medium transition-all {{ $category === 'facades' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ __('messages.facades') }}
                </a>
            </div>

            <!-- Portfolio Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects as $project)
                <div class="portfolio-item group relative overflow-hidden rounded-xl shadow-lg">
                    <img src="{{ $project->image ? asset('storage/' . $project->image) : 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }}" 
                         alt="{{ $project->getTranslatedTitle() }}" 
                         class="w-full h-72 object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
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
                    <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Villa Moderne - La Marsa" 
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
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Résidence Carthage" 
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
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Immeuble Commercial" 
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
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-16">{{ __('messages.client_testimonials') }}</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                @forelse($testimonials as $testimonial)
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < $testimonial->rating; $i++)
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ $testimonial->getTranslatedContent() }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
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
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_1') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-blue-600 font-bold">M</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Mohamed B.</h4>
                            <p class="text-sm text-gray-500">Paris, France</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_2') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-green-600 font-bold">S</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Sonia K.</h4>
                            <p class="text-sm text-gray-500">Montréal, Canada</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_3') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
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
    <section class="py-20 bg-blue-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">{{ __('messages.start_your_project') }}</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">{{ __('messages.cta_description') }}</p>
            <a href="{{ route('contact') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors inline-flex items-center">
                <i data-lucide="mail" class="w-5 h-5 mr-2"></i>
                {{ __('messages.request_quote') }}
            </a>
        </div>
    </section>
@endsection
