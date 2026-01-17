@extends('layouts.app')

@section('title', __('messages.nav_services'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-28 md:pt-32 pb-16 md:pb-20 relative">
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-4 border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                {{ __('messages.nav_services') }}
            </span>
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 md:mb-6">{{ __('messages.our_services') }}</h1>
            <p class="text-lg md:text-xl text-blue-100/90 max-w-2xl mx-auto leading-relaxed">
                {{ __('messages.services_page_intro') }}
            </p>
        </div>
    </section>

    @php
        $services = [
            [
                'id' => 'kitchen', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="M3 9V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4"/><path d="M12 3v6"/><path d="M8 21v-4"/><path d="M16 21v-4"/></svg>', 
                'color' => 'rose', 
                'image' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['kitchen_robustness', 'kitchen_modern_design', 'kitchen_easy_maintenance', 'kitchen_customization', 'kitchen_comfort'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585152220-90363fe7e115?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1565538810643-b5bdb714032a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566752355-35792bedcfea?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_profiles', 'tempered_glass', 'stainless_hardware', 'powder_coating'],
                'specs' => ['thickness_range' => '1.2 - 2.0 mm', 'finish_options' => 'Mat / Brillant', 'colors' => '200+']
            ],
            [
                'id' => 'doors', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>', 
                'color' => 'orange', 
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['doors_power_strength', 'doors_sealing_efficiency', 'doors_style_elegance', 'doors_free_design'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_profiles', 'tempered_glass', 'stainless_hardware', 'thermal_break'],
                'specs' => ['thickness_range' => '1.4 - 2.0 mm', 'glass_options' => '6 - 24 mm', 'colors' => '200+']
            ],
            [
                'id' => 'windows', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="3" x2="12" y2="21"></line><line x1="3" y1="12" x2="21" y2="12"></line></svg>', 
                'color' => 'blue', 
                'image' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['windows_security_durability', 'windows_performance_efficiency', 'windows_free_design_aesthetic'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585154526-990dced4db0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_profiles', 'double_glass', 'epdm_seals', 'thermal_break'],
                'specs' => ['thickness_range' => '1.4 - 1.8 mm', 'glass_options' => '4+12+4 mm', 'colors' => '200+']
            ],
            [
                'id' => 'rolling_shutters', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="7" x2="21" y2="7"/><line x1="3" y1="11" x2="21" y2="11"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="3" y1="19" x2="21" y2="19"/></svg>', 
                'color' => 'purple', 
                'image' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['shutters_enhanced_security', 'shutters_thermal_comfort', 'shutters_weather_protection', 'shutters_aesthetic', 'shutters_peace_of_mind'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585154526-990dced4db0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600210491892-03d54c0aaf87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_slats', 'foam_insulation', 'motor_system', 'guide_rails'],
                'specs' => ['thickness_range' => '0.5 - 0.8 mm', 'slat_width' => '37 - 55 mm', 'colors' => '50+']
            ],
            [
                'id' => 'railings', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22V8"/><path d="M5 12H2a10 10 0 0 0 20 0h-3"/><path d="M22 8l-2 2-2-2"/><path d="M6 8l-2 2-2-2"/><path d="M6 22v-4"/><path d="M18 22v-4"/></svg>', 
                'color' => 'green', 
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['railings_guaranteed_strength', 'railings_modern_elegance', 'railings_easy_installation', 'railings_optimal_security', 'railings_versatility', 'railings_harmonious_integration'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600210491892-03d54c0aaf87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_profiles', 'tempered_glass', 'stainless_fittings', 'powder_coating'],
                'specs' => ['thickness_range' => '1.5 - 2.5 mm', 'glass_options' => '8 - 12 mm', 'colors' => '200+']
            ],
            [
                'id' => 'pergola', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22V6"/><path d="M20 22V6"/><path d="M2 6h20"/><path d="M2 10h20"/><path d="M12 6v16"/></svg>', 
                'color' => 'amber', 
                'image' => 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['pergola_robustness_durability', 'pergola_refined_aesthetic', 'pergola_no_maintenance', 'pergola_layout_ally', 'pergola_peace_of_mind'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600210491892-03d54c0aaf87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600607687644-c7171b42498f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_beams', 'polycarbonate_roof', 'drainage_system', 'led_lighting'],
                'specs' => ['thickness_range' => '2.0 - 3.0 mm', 'span_max' => '6 m', 'colors' => '100+']
            ],
            [
                'id' => 'sun_breakers', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>', 
                'color' => 'yellow', 
                'image' => 'https://images.unsplash.com/photo-1545259742-14b90aaa3a60?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['sunbreaker_thermal_comfort', 'sunbreaker_robustness', 'sunbreaker_modern_design', 'sunbreaker_no_maintenance', 'sunbreaker_harmonious_integration', 'sunbreaker_energy_savings'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1545259742-14b90aaa3a60?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600210491892-03d54c0aaf87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_blades', 'pivot_system', 'control_mechanism', 'powder_coating'],
                'specs' => ['blade_width' => '100 - 300 mm', 'angle_range' => '0 - 90°', 'colors' => '200+']
            ],
            [
                'id' => 'mosquito_nets', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>', 
                'color' => 'teal', 
                'image' => 'https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['mosquito_effective_protection', 'mosquito_robustness', 'mosquito_easy_installation', 'mosquito_optimal_comfort', 'mosquito_discreet_design', 'mosquito_peace_of_mind'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_frame', 'fiberglass_mesh', 'brush_seals', 'roller_mechanism'],
                'specs' => ['mesh_density' => '18x16', 'frame_profile' => '25 - 45 mm', 'colors' => '30+']
            ],
            [
                'id' => 'space_design', 
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>', 
                'color' => 'indigo', 
                'image' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 
                'features' => ['partition_robustness', 'partition_glass_elegance', 'partition_preserved_brightness', 'partition_versatility', 'partition_easy_installation', 'partition_no_maintenance', 'partition_contemporary_design'],
                'gallery' => [
                    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585154526-990dced4db0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'materials' => ['aluminum_partitions', 'glass_panels', 'door_systems', 'acoustic_seals'],
                'specs' => ['glass_options' => '6 - 12 mm', 'height_max' => '3.5 m', 'colors' => '200+']
            ],
        ];
    @endphp

    @foreach($services as $index => $service)
    <!-- {{ ucfirst($service['id']) }} Section -->
    <section id="{{ $service['id'] }}" class="py-16 md:py-24 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} scroll-fade scroll-mt-24">
        <div class="container mx-auto px-6 md:px-8">
            <div class="grid md:grid-cols-2 gap-8 md:gap-16 items-center">
                <div class="{{ $index % 2 == 1 ? 'order-2 md:order-1' : '' }}">
                    <!-- Interactive Gallery -->
                    <div class="space-y-3">
                        <!-- Main Image - Click to open modal -->
                        <div class="relative group cursor-pointer" onclick="openServiceModal('{{ $service['id'] }}')">
                            <img id="main-image-{{ $service['id'] }}" 
                                 src="{{ $service['gallery'][0] }}" 
                                 alt="{{ __('messages.' . $service['id']) }}" 
                                 class="rounded-2xl shadow-2xl w-full h-[400px] object-cover transition-all duration-500 group-hover:scale-[1.02] group-hover:shadow-3xl">
                            <!-- Overlay with View Gallery indicator -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between">
                                    <span class="text-white font-semibold text-lg drop-shadow-lg">{{ __('messages.view_gallery') }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-white/80 text-sm">{{ count($service['gallery']) }} {{ __('messages.photos') }}</span>
                                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-white/30">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Thumbnail Navigation -->
                        <div class="flex gap-2 justify-center">
                            @foreach($service['gallery'] as $thumbIndex => $thumb)
                            <button onclick="event.stopPropagation(); changeServiceImage('{{ $service['id'] }}', {{ $thumbIndex }}, '{{ $thumb }}')" 
                                    id="thumb-{{ $service['id'] }}-{{ $thumbIndex }}"
                                    class="w-16 h-16 rounded-xl overflow-hidden border-3 {{ $thumbIndex === 0 ? 'border-blue-500 ring-2 ring-blue-500/30' : 'border-gray-200 hover:border-blue-300' }} shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                <img src="{{ $thumb }}" alt="" class="w-full h-full object-cover">
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="{{ $index % 2 == 1 ? 'order-1 md:order-2' : '' }}">
                    <!-- Service Icon -->
                    <div class="inline-flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-{{ $service['color'] }}-400 to-{{ $service['color'] }}-600 rounded-2xl flex items-center justify-center shadow-lg transform hover:rotate-6 transition-transform duration-300">
                            {!! $service['icon'] !!}
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

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <button onclick="openServiceModal('{{ $service['id'] }}')" class="inline-flex items-center justify-center px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-300 group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            {{ __('messages.view_details') }}
                        </button>
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center justify-center group shadow-xl hover:shadow-2xl">
                            <i data-lucide="phone" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
                            {{ __('messages.request_quote') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Hidden data for modal -->
    <div id="service-data-{{ $service['id'] }}" class="hidden" 
         data-service='{!! json_encode([
            "id" => $service["id"],
            "title" => __("messages." . $service["id"]),
            "description" => __("messages." . $service["id"] . "_desc"),
            "color" => $service["color"],
            "gallery" => $service["gallery"],
            "materials" => array_map(fn($m) => __("messages." . $m), $service["materials"]),
            "specs" => $service["specs"],
            "features" => array_map(fn($f) => __("messages." . $f), $service["features"])
         ], JSON_HEX_APOS | JSON_HEX_QUOT) !!}'>
    </div>
    @endforeach

    <!-- CTA Section -->
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
                <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
                    {{ __('messages.ready_to_start') }}
                </h2>
                <p class="text-lg md:text-xl text-blue-200 mb-8 leading-relaxed">
                    {{ __('messages.cta_description') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center shadow-2xl hover:shadow-orange-500/40 hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        {{ __('messages.request_quote') }}
                    </a>
                    <a href="{{ route('portfolio') }}" class="group bg-white/10 backdrop-blur-sm hover:bg-white text-white hover:text-blue-900 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center border-2 border-white/30 hover:border-white hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{ __('messages.view_our_work') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Details Modal -->
    <div id="serviceModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="closeServiceModal()"></div>
        
        <!-- Modal Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Modal Content -->
                <div class="relative w-full max-w-5xl transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all" id="modalContent">
                    <!-- Close Button -->
                    <button onclick="closeServiceModal()" class="absolute top-4 right-4 z-20 w-12 h-12 flex items-center justify-center rounded-full bg-white/90 hover:bg-white text-gray-600 hover:text-gray-900 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    
                    <div class="grid md:grid-cols-2">
                        <!-- Gallery Section - Fixed Height -->
                        <div class="relative bg-gradient-to-br from-gray-100 to-gray-200 h-[300px] md:h-[600px]">
                            <!-- Main Image - Object Fit Cover -->
                            <div class="relative w-full h-full overflow-hidden">
                                <img id="modalMainImage" src="" alt="" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent pointer-events-none"></div>
                                
                                <!-- Image Navigation Buttons -->
                                <button onclick="prevModalImage()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-white/90 hover:bg-white text-gray-700 shadow-lg transition-all duration-200 hover:scale-110 hover:shadow-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                </button>
                                <button onclick="nextModalImage()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-white/90 hover:bg-white text-gray-700 shadow-lg transition-all duration-200 hover:scale-110 hover:shadow-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                                
                                <!-- Image Counter Badge -->
                                <div class="absolute top-4 left-4 px-4 py-2 bg-black/50 backdrop-blur-md rounded-full text-white text-sm font-medium">
                                    <span id="currentImageIndex">1</span> / <span id="totalImages">4</span>
                                </div>
                            </div>
                            
                            <!-- Thumbnail Strip - Fixed at Bottom -->
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
                                <div id="modalThumbnails" class="flex gap-2 justify-center">
                                    <!-- Thumbnails will be inserted here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Details Section -->
                        <div class="p-6 md:p-8 h-[300px] md:h-[600px] overflow-y-auto bg-white">
                            <!-- Service Badge -->
                            <div id="modalBadge" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-sm font-semibold mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                                {{ __('messages.nav_services') }}
                            </div>
                            
                            <!-- Title -->
                            <h3 id="modalTitle" class="font-display text-2xl md:text-3xl font-bold text-gray-900 mb-4"></h3>
                            
                            <!-- Description -->
                            <p id="modalDescription" class="text-gray-600 mb-6 leading-relaxed text-base"></p>
                            
                            <!-- Features Section -->
                            <div class="mb-6 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl border border-green-100">
                                <h4 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
                                    <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    </div>
                                    {{ __('messages.key_features') }}
                                </h4>
                                <ul id="modalFeatures" class="grid gap-2">
                                    <!-- Features will be inserted here -->
                                </ul>
                            </div>
                            
                            <!-- Materials Section -->
                            <div class="mb-6 p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                                <h4 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                    </div>
                                    {{ __('messages.materials_used') }}
                                </h4>
                                <div id="modalMaterials" class="flex flex-wrap gap-2">
                                    <!-- Materials tags will be inserted here -->
                                </div>
                            </div>
                            
                            <!-- Specifications Section -->
                            <div class="mb-6 p-4 bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl border border-orange-100">
                                <h4 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
                                    <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                    </div>
                                    {{ __('messages.specifications') }}
                                </h4>
                                <div id="modalSpecs" class="space-y-2">
                                    <!-- Specs will be inserted here -->
                                </div>
                            </div>
                            
                            <!-- CTA Button -->
                            <a href="{{ route('contact') }}" class="btn-primary w-full text-center inline-flex items-center justify-center group shadow-xl hover:shadow-2xl text-lg py-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                {{ __('messages.request_quote') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentServiceData = null;
        let currentImageIndex = 0;

        // Function to change the main image in a service card when clicking thumbnails
        function changeServiceImage(serviceId, index, imageSrc) {
            // Update main image
            const mainImage = document.getElementById(`main-image-${serviceId}`);
            if (mainImage) {
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    mainImage.src = imageSrc;
                    mainImage.style.opacity = '1';
                }, 150);
            }

            // Update thumbnail active states
            const dataElement = document.getElementById(`service-data-${serviceId}`);
            if (dataElement) {
                const serviceData = JSON.parse(dataElement.dataset.service);
                serviceData.gallery.forEach((_, thumbIndex) => {
                    const thumb = document.getElementById(`thumb-${serviceId}-${thumbIndex}`);
                    if (thumb) {
                        if (thumbIndex === index) {
                            thumb.classList.remove('border-gray-200', 'hover:border-blue-300');
                            thumb.classList.add('border-blue-500', 'ring-2', 'ring-blue-500/30');
                        } else {
                            thumb.classList.remove('border-blue-500', 'ring-2', 'ring-blue-500/30');
                            thumb.classList.add('border-gray-200', 'hover:border-blue-300');
                        }
                    }
                });
            }
        }

        function openServiceModal(serviceId) {
            const dataElement = document.getElementById(`service-data-${serviceId}`);
            if (!dataElement) {
                console.error('Service data not found for:', serviceId);
                return;
            }

            try {
                currentServiceData = JSON.parse(dataElement.dataset.service);
            } catch (e) {
                console.error('Error parsing service data:', e);
                return;
            }
            
            currentImageIndex = 0;

            // Populate modal
            document.getElementById('modalTitle').textContent = currentServiceData.title;
            document.getElementById('modalDescription').textContent = currentServiceData.description;
            
            // Set main image
            updateMainImage();
            
            // Generate thumbnails with improved styling
            const thumbnailsContainer = document.getElementById('modalThumbnails');
            thumbnailsContainer.innerHTML = currentServiceData.gallery.map((img, index) => `
                <button onclick="goToImage(${index})" class="w-14 h-14 md:w-16 md:h-16 rounded-xl overflow-hidden border-2 ${index === 0 ? 'border-white ring-2 ring-white/50' : 'border-white/30'} hover:border-white transition-all duration-200 shadow-lg hover:scale-105 transform">
                    <img src="${img}" alt="" class="w-full h-full object-cover">
                </button>
            `).join('');
            
            // Generate materials with improved styling
            const materialsContainer = document.getElementById('modalMaterials');
            materialsContainer.innerHTML = currentServiceData.materials.map(material => `
                <span class="inline-flex items-center px-3 py-2 bg-white text-blue-700 rounded-xl text-sm font-medium shadow-sm border border-blue-100 hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    ${material}
                </span>
            `).join('');
            
            // Generate specs with improved styling
            const specsContainer = document.getElementById('modalSpecs');
            const specLabels = {
                'thickness_range': '{{ __("messages.thickness_range") }}',
                'glass_options': '{{ __("messages.glass_options") }}',
                'colors': '{{ __("messages.available_colors") }}',
                'slat_width': '{{ __("messages.slat_width") }}',
                'blade_width': '{{ __("messages.blade_width") }}',
                'angle_range': '{{ __("messages.angle_range") }}',
                'span_max': '{{ __("messages.max_span") }}',
                'mesh_density': '{{ __("messages.mesh_density") }}',
                'frame_profile': '{{ __("messages.frame_profile") }}',
                'height_max': '{{ __("messages.max_height") }}',
                'finish_options': '{{ __("messages.finish_options") }}'
            };
            specsContainer.innerHTML = Object.entries(currentServiceData.specs).map(([key, value]) => `
                <div class="flex items-center justify-between py-3 px-4 bg-white rounded-xl shadow-sm border border-orange-100 hover:shadow-md transition-shadow">
                    <span class="text-gray-600 text-sm font-medium">${specLabels[key] || key}</span>
                    <span class="font-bold text-gray-900 bg-orange-100 px-3 py-1 rounded-lg text-sm">${value}</span>
                </div>
            `).join('');
            
            // Generate features with improved styling
            const featuresContainer = document.getElementById('modalFeatures');
            featuresContainer.innerHTML = currentServiceData.features.map(feature => `
                <li class="flex items-start gap-3 p-2 rounded-lg hover:bg-white/50 transition-colors">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-500 flex items-center justify-center mt-0.5 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span class="text-gray-700 font-medium">${feature}</span>
                </li>
            `).join('');

            // Update total images
            document.getElementById('totalImages').textContent = currentServiceData.gallery.length;

            // Show modal
            const modal = document.getElementById('serviceModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Animate in
            setTimeout(() => {
                document.getElementById('modalContent').classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeServiceModal() {
            const modal = document.getElementById('serviceModal');
            document.getElementById('modalContent').classList.remove('scale-100', 'opacity-100');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 200);
        }

        function updateMainImage() {
            const mainImage = document.getElementById('modalMainImage');
            mainImage.src = currentServiceData.gallery[currentImageIndex];
            document.getElementById('currentImageIndex').textContent = currentImageIndex + 1;
            
            // Update thumbnail active state
            const thumbnails = document.querySelectorAll('#modalThumbnails button');
            thumbnails.forEach((thumb, index) => {
                if (index === currentImageIndex) {
                    thumb.classList.remove('border-white/30');
                    thumb.classList.add('border-white', 'ring-2', 'ring-white/50');
                } else {
                    thumb.classList.remove('border-white', 'ring-2', 'ring-white/50');
                    thumb.classList.add('border-white/30');
                }
            });
        }

        function nextModalImage() {
            if (!currentServiceData) return;
            currentImageIndex = (currentImageIndex + 1) % currentServiceData.gallery.length;
            updateMainImage();
        }

        function prevModalImage() {
            if (!currentServiceData) return;
            currentImageIndex = (currentImageIndex - 1 + currentServiceData.gallery.length) % currentServiceData.gallery.length;
            updateMainImage();
        }

        function goToImage(index) {
            currentImageIndex = index;
            updateMainImage();
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('serviceModal');
            if (modal.classList.contains('hidden')) return;
            
            if (e.key === 'Escape') closeServiceModal();
            if (e.key === 'ArrowLeft') prevModalImage();
            if (e.key === 'ArrowRight') nextModalImage();
        });
    </script>

    <style>
        #modalContent {
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.2s ease-out;
        }
        #modalContent.scale-100 {
            transform: scale(1);
        }
        #modalContent.opacity-100 {
            opacity: 1;
        }
        /* Smooth image transitions for service cards */
        [id^="main-image-"] {
            transition: opacity 0.15s ease-in-out;
        }
        /* Thumbnail border styling */
        .border-3 {
            border-width: 3px;
        }
        /* Custom scrollbar for modal content */
        #serviceModal .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        #serviceModal .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        #serviceModal .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        #serviceModal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
@endsection
