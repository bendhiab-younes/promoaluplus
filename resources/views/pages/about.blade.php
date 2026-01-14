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
    <section class="py-12 md:py-20 lg:py-24 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <div class="max-w-3xl mx-auto">
                <span class="inline-block px-5 py-2 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-6 border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ __('messages.start_your_project') }}
                </span>
                <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">{{ __('messages.work_with_us') }}</h2>
                <p class="text-lg md:text-xl text-blue-200 mb-8 leading-relaxed max-w-2xl mx-auto">{{ __('messages.cta_description') }}</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center shadow-2xl hover:shadow-orange-500/40 hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        {{ __('messages.contact_us') }}
                    </a>
                    <a href="{{ route('portfolio') }}" class="group bg-white/10 backdrop-blur-sm hover:bg-white text-white hover:text-blue-900 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center border-2 border-white/30 hover:border-white hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{ __('messages.view_our_work') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
