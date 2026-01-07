@extends('layouts.app')

@section('title', __('messages.nav_services'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-32 pb-20">
        <div class="container mx-auto px-6 md:px-8 text-center">
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">{{ __('messages.our_services') }}</h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-3xl mx-auto leading-relaxed">
                {{ __('messages.services_page_intro') }}
            </p>
        </div>
    </section>

    @php
        $services = [
            ['id' => 'doors', 'emoji' => '🚪', 'color' => 'orange', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['enhanced_security', 'modern_design', 'perfect_sealing', 'custom_made']],
            ['id' => 'windows', 'emoji' => '🪟', 'color' => 'blue', 'image' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['double_glazing', 'thermal_insulation', 'acoustic_insulation', 'custom_made']],
            ['id' => 'sliding', 'emoji' => '↔️', 'color' => 'cyan', 'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['modern_design', 'easy_maintenance', 'perfect_sealing', 'durable']],
            ['id' => 'rolling_shutters', 'emoji' => '🎚️', 'color' => 'purple', 'image' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['motorized', 'enhanced_security', 'thermal_insulation', 'weather_resistant']],
            ['id' => 'railings', 'emoji' => '🛡️', 'color' => 'green', 'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['enhanced_security', 'modern_design', 'corrosion_resistant', 'easy_maintenance']],
            ['id' => 'pergola', 'emoji' => '🏕️', 'color' => 'amber', 'image' => 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['weather_resistant', 'modern_architecture', 'durable', 'custom_made']],
            ['id' => 'sun_breakers', 'emoji' => '☀️', 'color' => 'yellow', 'image' => 'https://images.unsplash.com/photo-1545259742-14b90aaa3a60?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['modern_architecture', 'thermal_insulation', 'weather_resistant', 'custom_made']],
            ['id' => 'mosquito_nets', 'emoji' => '🦟', 'color' => 'teal', 'image' => 'https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['easy_maintenance', 'custom_made', 'durable', 'modern_design']],
            ['id' => 'space_design', 'emoji' => '✨', 'color' => 'indigo', 'image' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'features' => ['modern_architecture', 'custom_made', 'modern_design', 'durable']],
        ];
    @endphp

    @foreach($services as $index => $service)
    <!-- {{ ucfirst($service['id']) }} Section -->
    <section id="{{ $service['id'] }}" class="py-16 md:py-24 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} scroll-fade">
        <div class="container mx-auto px-6 md:px-8">
            <div class="grid md:grid-cols-2 gap-8 md:gap-16 items-center">
                <div class="{{ $index % 2 == 1 ? 'order-2 md:order-1' : '' }}">
                    <img src="{{ $service['image'] }}" 
                         alt="{{ __('messages.' . $service['id']) }}" 
                         class="rounded-2xl shadow-2xl w-full h-[400px] object-cover hover:scale-105 transition-transform duration-500">
                </div>
                <div class="{{ $index % 2 == 1 ? 'order-1 md:order-2' : '' }}">
                    <!-- Service Icon -->
                    <div class="inline-flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-{{ $service['color'] }}-400 to-{{ $service['color'] }}-600 rounded-2xl flex items-center justify-center shadow-lg transform hover:rotate-6 transition-transform duration-300">
                            <span class="text-3xl md:text-4xl">{{ $service['emoji'] }}</span>
                        </div>
                        <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900">
                            {{ __('messages.' . $service['id']) }}
                        </h2>
                    </div>

                    <!-- Description -->
                    <p class="text-base md:text-lg text-gray-600 mb-8 leading-relaxed">
                        {{ __('messages.' . $service['id'] . '_desc') }}
                    </p>

                    <!-- Features List -->
                    <ul class="space-y-4 mb-8">
                        @foreach($service['features'] as $feature)
                        <li class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                                <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <span class="text-gray-700 text-base">{{ __('messages.' . $feature) }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <!-- CTA Button -->
                    <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center justify-center group shadow-xl hover:shadow-2xl">
                        <i data-lucide="phone" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
                        {{ __('messages.request_quote') }}
                        <i data-lucide="arrow-right" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endforeach

    <!-- CTA Section -->
    <section class="py-16 md:py-24 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <div class="max-w-3xl mx-auto">
                <span class="inline-block px-5 py-2 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-6 border border-white/20">
                    🚀 {{ __('messages.start_your_project') }}
                </span>
                <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
                    {{ __('messages.ready_to_start') }}
                </h2>
                <p class="text-lg md:text-xl text-blue-200 mb-8 leading-relaxed">
                    {{ __('messages.cta_description') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center shadow-2xl hover:shadow-orange-500/40 hover:-translate-y-1">
                        <i data-lucide="mail" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
                        {{ __('messages.request_quote') }}
                    </a>
                    <a href="{{ route('portfolio') }}" class="group bg-white/10 backdrop-blur-sm hover:bg-white text-white hover:text-blue-900 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center border-2 border-white/30 hover:border-white hover:-translate-y-1">
                        <i data-lucide="eye" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
                        {{ __('messages.view_our_work') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
