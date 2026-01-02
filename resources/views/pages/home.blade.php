@extends('layouts.app')

@section('title', __('messages.nav_home'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-gradient pt-20 min-h-screen flex items-center relative overflow-hidden">
        <div class="container mx-auto px-4 py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white fade-in">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        {{ __('messages.hero_title') }}
                        <span class="text-orange-400 block mt-2">{{ __('messages.hero_subtitle') }}</span>
                    </h1>
                    <p class="text-xl mb-8 text-blue-100">
                        {{ __('messages.hero_description') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('contact') }}" class="btn-primary text-center inline-flex items-center justify-center">
                            <i data-lucide="calculator" class="w-5 h-5 mr-2"></i>
                            {{ __('messages.request_quote') }}
                        </a>
                        <button onclick="openWhatsApp()" class="btn-secondary inline-flex items-center justify-center">
                            <i data-lucide="phone" class="w-5 h-5 mr-2"></i>
                            WhatsApp
                        </button>
                    </div>
                </div>
                <div class="fade-in fade-in-delay-1">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                             alt="{{ __('messages.modern_aluminum_windows') }}" 
                             class="rounded-lg shadow-2xl">
                        <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-lg shadow-lg">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                                <span class="font-bold text-gray-800">4.9/5</span>
                                <span class="text-gray-600 text-sm">{{ __('messages.satisfied_clients') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trust indicators -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 hidden md:block">
            <div class="flex items-center space-x-8 text-white/80">
                <div class="text-center">
                    <div class="text-3xl font-bold">500+</div>
                    <div class="text-sm">{{ __('messages.projects_completed') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">15+</div>
                    <div class="text-sm">{{ __('messages.years_experience') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">98%</div>
                    <div class="text-sm">{{ __('messages.satisfied_clients') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section Preview -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">{{ __('messages.our_services') }}</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ __('messages.services_intro') }}
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @forelse($services as $index => $service)
                <div class="service-card bg-white p-8 rounded-xl shadow-lg fade-in {{ $index > 0 ? 'fade-in-delay-' . $index : '' }}">
                    <div class="w-16 h-16 bg-{{ $service->color }}-100 rounded-lg flex items-center justify-center mb-6">
                        <i data-lucide="{{ $service->icon ?? 'home' }}" class="w-8 h-8 text-{{ $service->color }}-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ $service->getTranslatedTitle() }}</h3>
                    <p class="text-gray-600 mb-6">
                        {{ $service->getTranslatedShortDescription() }}
                    </p>
                    @if($service->features)
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        @foreach(array_slice($service->features, 0, 3) as $feature)
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    <a href="{{ route('services') }}#{{ $service->slug }}" class="text-{{ $service->color }}-600 hover:text-{{ $service->color }}-800 font-semibold inline-flex items-center">
                        {{ __('messages.learn_more') }}
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
                @empty
                <!-- Default services if none in database -->
                <div class="service-card bg-white p-8 rounded-xl shadow-lg fade-in">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <i data-lucide="home" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ __('messages.windows') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('messages.windows_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.double_glazing') }}</li>
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.thermal_insulation') }}</li>
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.custom_made') }}</li>
                    </ul>
                    <a href="{{ route('services') }}#windows" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center">
                        {{ __('messages.learn_more') }}
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
                
                <div class="service-card bg-white p-8 rounded-xl shadow-lg fade-in fade-in-delay-1">
                    <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
                        <i data-lucide="door-open" class="w-8 h-8 text-orange-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ __('messages.doors') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('messages.doors_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.enhanced_security') }}</li>
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.modern_design') }}</li>
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.perfect_sealing') }}</li>
                    </ul>
                    <a href="{{ route('services') }}#doors" class="text-orange-600 hover:text-orange-800 font-semibold inline-flex items-center">
                        {{ __('messages.learn_more') }}
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
                
                <div class="service-card bg-white p-8 rounded-xl shadow-lg fade-in fade-in-delay-2">
                    <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <i data-lucide="building" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ __('messages.facades') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('messages.facades_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.curtain_walls') }}</li>
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.custom_verandas') }}</li>
                        <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>{{ __('messages.modern_architecture') }}</li>
                    </ul>
                    <a href="{{ route('services') }}#facades" class="text-green-600 hover:text-green-800 font-semibold inline-flex items-center">
                        {{ __('messages.learn_more') }}
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
                @endforelse
            </div>
            
            <div class="text-center">
                <a href="{{ route('services') }}" class="btn-primary inline-block">
                    {{ __('messages.view_all_services') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">{{ __('messages.why_choose_us') }}</h2>
                <p class="text-xl text-gray-600">{{ __('messages.advantages_that_matter') }}</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center fade-in">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="award" class="w-10 h-10 text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('messages.guaranteed_quality') }}</h3>
                    <p class="text-gray-600">{{ __('messages.european_standards') }}</p>
                </div>
                
                <div class="text-center fade-in fade-in-delay-1">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="users" class="w-10 h-10 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('messages.expat_service') }}</h3>
                    <p class="text-gray-600">{{ __('messages.remote_follow_up') }}</p>
                </div>
                
                <div class="text-center fade-in fade-in-delay-2">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="clock" class="w-10 h-10 text-orange-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('messages.deadlines_respected') }}</h3>
                    <p class="text-gray-600">{{ __('messages.clear_planning') }}</p>
                </div>
                
                <div class="text-center fade-in fade-in-delay-3">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="shield-check" class="w-10 h-10 text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('messages.transparent_pricing') }}</h3>
                    <p class="text-gray-600">{{ __('messages.detailed_quotes') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-blue-600">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    {{ __('messages.ready_to_start') }}
                </h2>
                <p class="text-xl text-blue-100 mb-8">
                    {{ __('messages.cta_description') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors inline-flex items-center justify-center">
                        <i data-lucide="mail" class="w-5 h-5 mr-2"></i>
                        {{ __('messages.request_quote') }}
                    </a>
                    <a href="{{ route('portfolio') }}" class="bg-white hover:bg-gray-100 text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold transition-colors inline-flex items-center justify-center">
                        <i data-lucide="eye" class="w-5 h-5 mr-2"></i>
                        {{ __('messages.view_our_work') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
