@extends('layouts.app')
@php
    use App\Models\SiteSetting;
@endphp
@section('title', __('messages.nav_about'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-32 pb-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">{{ __('messages.about_us') }}</h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                {{ __('messages.about_intro') }}
            </p>
        </div>
    </section>

    <!-- Company Story -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">{{ __('messages.our_story') }}</h2>
                    <p class="text-gray-600 mb-4 text-lg">{{ __('messages.story_p1') }}</p>
                    <p class="text-gray-600 mb-4 text-lg">{{ __('messages.story_p2') }}</p>
                    <p class="text-gray-600 text-lg">{{ __('messages.story_p3') }}</p>
                </div>
                <div>
                    <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="{{ __('messages.our_workshop') }}" 
                         class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-20 bg-blue-600">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <div class="text-5xl font-bold mb-2">{{ SiteSetting::get('stats_years', '15') }}+</div>
                    <div class="text-blue-100">{{ __('messages.years_experience') }}</div>
                </div>
                <div>
                    <div class="text-5xl font-bold mb-2">{{ SiteSetting::get('stats_projects', '500') }}+</div>
                    <div class="text-blue-100">{{ __('messages.projects_completed') }}</div>
                </div>
                <div>
                    <div class="text-5xl font-bold mb-2">{{ SiteSetting::get('stats_satisfaction', '98') }}%</div>
                    <div class="text-blue-100">{{ __('messages.satisfied_clients') }}</div>
                </div>
                <div>
                    <div class="text-5xl font-bold mb-2">{{ SiteSetting::get('stats_team', '12') }}+</div>
                    <div class="text-blue-100">{{ __('messages.team_members') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Values -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">{{ __('messages.mission_values') }}</h2>
                <p class="text-xl text-gray-600">{{ __('messages.what_drives_us') }}</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="target" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ __('messages.our_mission') }}</h3>
                    <p class="text-gray-600">{{ __('messages.mission_desc') }}</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="eye" class="w-8 h-8 text-orange-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ __('messages.our_vision') }}</h3>
                    <p class="text-gray-600">{{ __('messages.vision_desc') }}</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="heart" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ __('messages.our_values') }}</h3>
                    <p class="text-gray-600">{{ __('messages.values_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Expats Choose Us -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">{{ __('messages.why_expats_choose_us') }}</h2>
                <p class="text-xl text-gray-600">{{ __('messages.expats_intro') }}</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="globe" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ __('messages.multilingual_team') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('messages.multilingual_desc') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="video" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ __('messages.remote_follow_up') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('messages.remote_desc') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="shield-check" class="w-8 h-8 text-orange-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ __('messages.european_standards') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('messages.standards_desc') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="credit-card" class="w-8 h-8 text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ __('messages.flexible_payment') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('messages.payment_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Certifications -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12">{{ __('messages.certifications') }}</h2>
            <div class="flex flex-wrap justify-center gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <i data-lucide="award" class="w-12 h-12 text-blue-600 mx-auto mb-2"></i>
                    <p class="font-medium text-gray-800">ISO 9001</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <i data-lucide="shield" class="w-12 h-12 text-green-600 mx-auto mb-2"></i>
                    <p class="font-medium text-gray-800">CE {{ __('messages.marking') }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <i data-lucide="check-circle" class="w-12 h-12 text-orange-600 mx-auto mb-2"></i>
                    <p class="font-medium text-gray-800">{{ __('messages.guarantee_10_years') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-blue-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">{{ __('messages.work_with_us') }}</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">{{ __('messages.cta_description') }}</p>
            <a href="{{ route('contact') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors inline-flex items-center">
                <i data-lucide="mail" class="w-5 h-5 mr-2"></i>
                {{ __('messages.contact_us') }}
            </a>
        </div>
    </section>
@endsection
