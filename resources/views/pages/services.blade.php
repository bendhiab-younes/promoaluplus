@extends('layouts.app')

@section('title', __('messages.nav_services'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-32 pb-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">{{ __('messages.our_services') }}</h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                {{ __('messages.services_page_intro') }}
            </p>
        </div>
    </section>

    <!-- Windows Section -->
    <section id="windows" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <i data-lucide="home" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">{{ __('messages.windows') }}</h2>
                    <p class="text-gray-600 mb-6 text-lg">{{ __('messages.windows_full_desc') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.double_glazing') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.thermal_insulation') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.acoustic_insulation') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.custom_made') }}
                        </li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center">
                        {{ __('messages.request_quote') }}
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
                <div>
                    <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="{{ __('messages.windows') }}" 
                         class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Doors Section -->
    <section id="doors" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="{{ __('messages.doors') }}" 
                         class="rounded-lg shadow-xl">
                </div>
                <div class="order-1 md:order-2">
                    <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
                        <i data-lucide="door-open" class="w-8 h-8 text-orange-600"></i>
                    </div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">{{ __('messages.doors') }}</h2>
                    <p class="text-gray-600 mb-6 text-lg">{{ __('messages.doors_full_desc') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.enhanced_security') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.modern_design') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.perfect_sealing') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.sliding_doors') }}
                        </li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center">
                        {{ __('messages.request_quote') }}
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Facades Section -->
    <section id="facades" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <i data-lucide="building" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">{{ __('messages.facades') }}</h2>
                    <p class="text-gray-600 mb-6 text-lg">{{ __('messages.facades_full_desc') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.curtain_walls') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.custom_verandas') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.ventilated_facades') }}
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                            {{ __('messages.modern_architecture') }}
                        </li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center">
                        {{ __('messages.request_quote') }}
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
                <div>
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="{{ __('messages.facades') }}" 
                         class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-20 bg-blue-600">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-white text-center mb-16">{{ __('messages.our_process') }}</h2>
            
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center text-white">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ __('messages.step_contact') }}</h3>
                    <p class="text-blue-100">{{ __('messages.step_contact_desc') }}</p>
                </div>
                <div class="text-center text-white">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ __('messages.step_quote') }}</h3>
                    <p class="text-blue-100">{{ __('messages.step_quote_desc') }}</p>
                </div>
                <div class="text-center text-white">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ __('messages.step_production') }}</h3>
                    <p class="text-blue-100">{{ __('messages.step_production_desc') }}</p>
                </div>
                <div class="text-center text-white">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold">4</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ __('messages.step_installation') }}</h3>
                    <p class="text-blue-100">{{ __('messages.step_installation_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-6">{{ __('messages.ready_to_start') }}</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">{{ __('messages.cta_description') }}</p>
            <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center text-lg px-8 py-4">
                <i data-lucide="mail" class="w-5 h-5 mr-2"></i>
                {{ __('messages.request_quote') }}
            </a>
        </div>
    </section>
@endsection
