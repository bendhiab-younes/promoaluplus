@extends('layouts.app')
@php
    use App\Models\SiteSetting;
@endphp
@section('title', __('messages.nav_about'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-28 md:pt-32 pb-16 md:pb-20 relative">
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-4 border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                {{ __('messages.about_us') }}
            </span>
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 md:mb-6">{{ __('messages.about_us') }}</h1>
            <p class="text-lg md:text-xl text-blue-100/90 max-w-2xl mx-auto leading-relaxed">
                {{ __('messages.about_intro') }}
            </p>
        </div>
    </section>

    <!-- Company Story -->
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 md:px-8">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div class="scroll-fade">
                    <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ __('messages.our_story') }}
                    </span>
                    <h2 class="font-display text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ __('messages.our_story') }}</h2>
                    <p class="text-gray-600 mb-4 text-base md:text-lg leading-relaxed">{{ __('messages.story_p1') }}</p>
                    <p class="text-gray-600 mb-4 text-base md:text-lg leading-relaxed">{{ __('messages.story_p2') }}</p>
                    <p class="text-gray-600 text-base md:text-lg leading-relaxed">{{ __('messages.story_p3') }}</p>
                </div>
                <div class="scroll-fade stagger-1">
                    <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="{{ __('messages.our_workshop') }}" 
                         class="rounded-2xl shadow-2xl hover:scale-105 transition-transform duration-500">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Values -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-6 md:px-8">
            <div class="text-center mb-10 md:mb-16 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    {{ __('messages.mission_values') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __('messages.mission_values') }}</h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">{{ __('messages.what_drives_us') }}</p>
            </div>
            
            <!-- Mission & Vision in Two Columns -->
            <div class="grid md:grid-cols-2 gap-8 md:gap-10 mb-8 md:mb-10">
                <div class="scroll-fade stagger-1">
                    <div class="bg-white p-8 md:p-10 rounded-3xl border-2 border-blue-200 shadow-xl hover:shadow-2xl transition-all duration-300 h-full">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-500/30">
                                <i data-lucide="target" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">{{ __('messages.our_mission') }}</h3>
                            </div>
                        </div>
                        <p class="text-gray-700 text-base md:text-lg leading-relaxed">{{ __('messages.mission_desc') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-2">
                    <div class="bg-white p-8 md:p-10 rounded-3xl border-2 border-orange-200 shadow-xl hover:shadow-2xl transition-all duration-300 h-full">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-orange-500/30">
                                <i data-lucide="eye" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">{{ __('messages.our_vision') }}</h3>
                            </div>
                        </div>
                        <p class="text-gray-700 text-base md:text-lg leading-relaxed">{{ __('messages.vision_desc') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Values in Full Width -->
            <div class="scroll-fade stagger-3">
                <div class="bg-white p-8 md:p-12 rounded-3xl border-2 border-green-200 shadow-xl hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-center justify-center gap-4 mb-8">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-green-500/30">
                            <i data-lucide="heart" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900">{{ __('messages.our_values') }}</h3>
                    </div>
                    <div class="max-w-5xl mx-auto">
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                            @php
                                $values = [
                                    ['title' => __('messages.value_quality_title'), 'desc' => __('messages.value_quality_desc'), 'icon' => 'shield-check', 'color' => 'blue'],
                                    ['title' => __('messages.value_proximity_title'), 'desc' => __('messages.value_proximity_desc'), 'icon' => 'users', 'color' => 'green'],
                                    ['title' => __('messages.value_sustainability_title'), 'desc' => __('messages.value_sustainability_desc'), 'icon' => 'leaf', 'color' => 'emerald'],
                                    ['title' => __('messages.value_innovation_title'), 'desc' => __('messages.value_innovation_desc'), 'icon' => 'lightbulb', 'color' => 'orange'],
                                    ['title' => __('messages.value_integrity_title'), 'desc' => __('messages.value_integrity_desc'), 'icon' => 'award', 'color' => 'purple'],
                                    ['title' => __('messages.value_timely_title'), 'desc' => __('messages.value_timely_desc'), 'icon' => 'clock', 'color' => 'red'],
                                ];
                            @endphp
                            @foreach($values as $value)
                            <div class="text-center">
                                <div class="w-12 h-12 bg-{{ $value['color'] }}-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="{{ $value['icon'] }}" class="w-6 h-6 text-{{ $value['color'] }}-600"></i>
                                </div>
                                <h4 class="font-bold text-gray-900 mb-2 text-sm md:text-base">{{ $value['title'] }}</h4>
                                <p class="text-gray-600 text-xs md:text-sm leading-relaxed">{{ $value['desc'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Expats Choose Us -->
    <!-- <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 md:px-8">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    {{ __('messages.why_expats_choose_us') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __('messages.why_expats_choose_us') }}</h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">{{ __('messages.expats_intro') }}</p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                <div class="scroll-fade stagger-1 group">
                    <div class="bg-gradient-to-br from-white to-blue-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-blue-200 transition-all duration-300 hover:shadow-xl text-center h-full">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="globe" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ __('messages.multilingual_team') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base">{{ __('messages.multilingual_desc') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-3 group">
                    <div class="bg-gradient-to-br from-white to-orange-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-orange-200 transition-all duration-300 hover:shadow-xl text-center h-full">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="shield-check" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ __('messages.european_standards') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base">{{ __('messages.standards_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    {{-- Certifications - Hidden for now --}}
    {{-- <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-6 md:px-8 text-center">
            <div class="scroll-fade mb-10 md:mb-14">
                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                    {{ __('messages.certifications') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __('messages.certifications') }}</h2>
            </div>
            <div class="flex flex-wrap justify-center gap-6 md:gap-8">
                <div class="scroll-fade stagger-1 group">
                    <div class="bg-gradient-to-br from-white to-blue-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-blue-200 transition-all duration-300 hover:shadow-xl min-w-[140px]">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="award" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <p class="font-bold text-gray-900">ISO 9001</p>
                    </div>
                </div>
                <div class="scroll-fade stagger-2 group">
                    <div class="bg-gradient-to-br from-white to-green-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-green-200 transition-all duration-300 hover:shadow-xl min-w-[140px]">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="shield" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <p class="font-bold text-gray-900">CE {{ __('messages.marking') }}</p>
                    </div>
                </div>
                <div class="scroll-fade stagger-3 group">
                    <div class="bg-gradient-to-br from-white to-orange-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-orange-200 transition-all duration-300 hover:shadow-xl min-w-[140px]">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="check-circle" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <p class="font-bold text-gray-900">{{ __('messages.guarantee_10_years') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

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
