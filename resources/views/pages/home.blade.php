@extends('layouts.app')
@php
    use App\Models\SiteSetting;
@endphp
@section('title', __('messages.nav_home'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-gradient pt-24 md:pt-28 min-h-screen flex items-center relative overflow-hidden">
        <div class="container mx-auto px-6 md:px-8 py-16 md:py-24 relative z-10">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div class="text-white scroll-fade-left">
                    <span class="inline-block px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-200 mb-4 md:mb-6 border border-white/20">
                        ✨ {{ SiteSetting::getTranslated('hero_badge', __('messages.premium_quality')) }}
                    </span>
                    <h1 class="font-display text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight">
                        {{ SiteSetting::getTranslated('hero_title', __('messages.hero_title')) }}
                        <span class="text-gradient bg-gradient-to-r from-orange-400 to-yellow-300 bg-clip-text text-transparent block mt-2">{{ SiteSetting::getTranslated('hero_subtitle', __('messages.hero_subtitle')) }}</span>
                    </h1>
                    <p class="text-base md:text-lg lg:text-xl mb-6 md:mb-8 text-blue-100/90 leading-relaxed max-w-lg">
                        {{ SiteSetting::getTranslated('hero_description', __('messages.hero_description')) }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('contact') }}" class="btn-primary text-center inline-flex items-center justify-center group">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 group-hover:scale-110 transition-transform"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="16" x2="16" y1="14" y2="18"/><path d="M16 10h.01"/><path d="M12 10h.01"/><path d="M8 10h.01"/><path d="M12 14h.01"/><path d="M8 14h.01"/><path d="M12 18h.01"/><path d="M8 18h.01"/></svg>
                            {{ __('messages.request_quote') }}
                        </a>
                        <button onclick="openWhatsApp()" class="btn-secondary inline-flex items-center justify-center group">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="mr-2 group-hover:scale-110 transition-transform"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </button>
                    </div>
                </div>
                <div class="scroll-fade-right hidden md:block">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-orange-500/20 to-blue-500/20 rounded-2xl blur-2xl"></div>
                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                             alt="{{ __('messages.modern_aluminum_windows') }}" 
                             class="relative rounded-2xl shadow-2xl border border-white/10">
                        <div class="absolute -bottom-4 -right-4 md:-bottom-6 md:-right-6 bg-white p-4 rounded-xl shadow-xl border border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="flex -space-x-1">
                                    <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-yellow-500"></i>
                                    <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-yellow-500"></i>
                                    <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-yellow-500"></i>
                                    <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-yellow-500"></i>
                                    <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-yellow-500"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800">4.9/5</span>
                                    <span class="text-gray-500 text-sm block">{{ __('messages.satisfied_clients') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-4 -left-4 bg-gradient-to-br from-orange-500 to-orange-600 text-white px-4 py-2 rounded-xl shadow-lg">
                            <span class="font-bold text-lg">15+</span>
                            <span class="text-sm block text-orange-100">{{ __('messages.years_experience') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trust indicators - Mobile & Desktop -->
        <div class="absolute bottom-8 md:bottom-12 left-1/2 transform -translate-x-1/2 w-full px-6">
            <div class="flex items-center justify-center gap-4 sm:gap-8 md:gap-12 text-white/90">
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold">{{ SiteSetting::get('stats_projects', '500') }}+</div>
                    <div class="text-xs md:text-sm text-blue-200">{{ __('messages.projects_completed') }}</div>
                </div>
                <div class="w-px h-10 bg-white/20 hidden sm:block"></div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold">{{ SiteSetting::get('stats_years', '15') }}+</div>
                    <div class="text-xs md:text-sm text-blue-200">{{ __('messages.years_experience') }}</div>
                </div>
                <div class="w-px h-10 bg-white/20 hidden sm:block"></div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold">{{ SiteSetting::get('stats_satisfaction', '98') }}%</div>
                    <div class="text-xs md:text-sm text-blue-200">{{ __('messages.satisfied_clients') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section Preview -->
    <section class="py-12 md:py-20 lg:py-24 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-6 md:px-8">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-3">
                    {{ __('messages.nav_services') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3 md:mb-4">{{ __('messages.our_services') }}</h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed px-4">
                    {{ __('messages.services_intro') }}
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mb-10 md:mb-12">
                @forelse($services as $index => $service)
                <div class="service-card bg-white p-6 md:p-8 rounded-xl shadow-lg fade-in {{ $index > 0 ? 'fade-in-delay-' . $index : '' }}">
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
                <div class="service-card bg-white p-6 md:p-8 rounded-xl shadow-lg fade-in">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-4 md:mb-6">
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
                
                <div class="service-card bg-white p-6 md:p-8 rounded-xl shadow-lg fade-in fade-in-delay-1">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-orange-100 rounded-lg flex items-center justify-center mb-4 md:mb-6">
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
                
                <div class="service-card bg-white p-6 md:p-8 rounded-xl shadow-lg fade-in fade-in-delay-2">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-green-100 rounded-lg flex items-center justify-center mb-4 md:mb-6">
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
    <section class="py-12 md:py-20 lg:py-24 bg-white relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute top-0 left-0 w-72 h-72 bg-blue-100 rounded-full -translate-x-1/2 -translate-y-1/2 opacity-50"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-100 rounded-full translate-x-1/2 translate-y-1/2 opacity-50"></div>
        
        <div class="container mx-auto px-6 md:px-8 relative z-10">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-3">
                    {{ __('messages.why_choose_us') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __('messages.advantages_that_matter') }}</h2>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <div class="scroll-fade stagger-1 group">
                    <div class="bg-gradient-to-br from-white to-blue-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-blue-200 transition-all duration-300 hover:shadow-xl text-center h-full">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="award" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ __('messages.guaranteed_quality') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base">{{ __('messages.european_standards') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-2 group">
                    <div class="bg-gradient-to-br from-white to-green-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-green-200 transition-all duration-300 hover:shadow-xl text-center h-full">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="users" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ __('messages.expat_service') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base">{{ __('messages.remote_follow_up') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-3 group">
                    <div class="bg-gradient-to-br from-white to-orange-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-orange-200 transition-all duration-300 hover:shadow-xl text-center h-full">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="clock" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ __('messages.deadlines_respected') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base">{{ __('messages.clear_planning') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-4 group">
                    <div class="bg-gradient-to-br from-white to-purple-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-purple-200 transition-all duration-300 hover:shadow-xl text-center h-full">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="shield-check" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ __('messages.transparent_pricing') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base">{{ __('messages.detailed_quotes') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 md:py-20 lg:py-24 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <div class="max-w-3xl mx-auto scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-4 md:mb-6 border border-white/20">
                    🚀 {{ __('messages.start_your_project') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-4 md:mb-6 leading-tight">
                    {{ SiteSetting::getTranslated('cta_title', __('messages.ready_to_start')) }}
                </h2>
                <p class="text-base md:text-lg lg:text-xl text-blue-200 mb-6 md:mb-8 leading-relaxed px-4">
                    {{ SiteSetting::getTranslated('cta_description', __('messages.cta_description')) }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 group-hover:scale-110 transition-transform"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        {{ __('messages.request_quote') }}
                    </a>
                    <a href="{{ route('portfolio') }}" class="group bg-white/10 backdrop-blur-sm hover:bg-white text-white hover:text-blue-900 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center border-2 border-white/30 hover:border-white hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 group-hover:scale-110 transition-transform"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{ __('messages.view_our_work') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
