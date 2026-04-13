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

    @foreach($services as $index => $service)
    @php
        $gallery = $service->getGalleryImages();
        $features = $service->getTranslatedFeatures();
        $materials = $service->getTranslatedMaterials();
        $specs = $service->specs ?? [];
    @endphp
    <!-- {{ $service->getTranslatedTitle() }} Section -->
    <section id="{{ $service->slug }}" class="py-16 md:py-24 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} scroll-fade scroll-mt-24">
        <div class="container mx-auto px-6 md:px-8">
            <div class="grid md:grid-cols-2 gap-8 md:gap-16 items-center">
                <div class="{{ $index % 2 == 1 ? 'order-2 md:order-1' : '' }}">
                    <!-- Interactive Gallery -->
                    <div class="space-y-3">
                        <!-- Main Image -->
                        <div class="relative group">
                               <img id="main-image-{{ $service->slug }}" 
                                   src="{{ $gallery[0] ?? asset('images/placeholder.jpg') }}" 
                                   alt="{{ $service->getTranslatedTitle() }}" 
                                   loading="lazy"
                                   decoding="async"
                                   class="rounded-2xl shadow-2xl w-full h-[400px] object-cover transition-all duration-500">
                        </div>
                        <!-- Thumbnail Navigation -->
                        @if(count($gallery) > 1)
                        <div class="flex gap-2 justify-center">
                            @foreach($gallery as $thumbIndex => $thumb)
                            <button type="button" onclick="event.stopPropagation(); changeServiceImage('{{ $service->slug }}', {{ $thumbIndex }}, '{{ $thumb }}')" 
                                    id="thumb-{{ $service->slug }}-{{ $thumbIndex }}"
                                    class="w-16 h-16 rounded-xl overflow-hidden border-3 {{ $thumbIndex === 0 ? 'border-blue-500 ring-2 ring-blue-500/30' : 'border-gray-200 hover:border-blue-300' }} shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                <img src="{{ $thumb }}" alt="" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                <div class="{{ $index % 2 == 1 ? 'order-1 md:order-2' : '' }}">
                    <!-- Service Icon -->
                    <div class="inline-flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-{{ $service->color }}-400 to-{{ $service->color }}-600 rounded-2xl flex items-center justify-center shadow-lg transform hover:rotate-6 transition-transform duration-300">
                            @if($service->svg_icon)
                                {!! $service->svg_icon !!}
                            @elseif($service->icon)
                                <i data-lucide="{{ $service->icon }}" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            @endif
                        </div>
                        <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900">
                            {{ $service->getTranslatedTitle() }}
                        </h2>
                    </div>

                    <!-- Description -->
                    <div class="text-base md:text-lg text-gray-600 mb-8 leading-relaxed prose prose-gray max-w-none">
                        @if($service->getTranslatedDescription())
                            {!! $service->getTranslatedDescription() !!}
                        @else
                            {{ $service->getTranslatedShortDescription() }}
                        @endif
                    </div>

                    <!-- Features List -->
                    @if(count($features) > 0)
                    <ul class="space-y-4 mb-8">
                        @foreach($features as $feature)
                        <li class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                                <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <span class="text-gray-700 text-base">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center justify-center group shadow-xl hover:shadow-2xl">
                            <i data-lucide="phone" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
                            {{ __('messages.request_quote') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
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

    <script>
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

            // Update thumbnail active states - find all thumbnails for this service
            const thumbs = document.querySelectorAll(`[id^="thumb-${serviceId}-"]`);
            thumbs.forEach((thumb, thumbIndex) => {
                if (thumbIndex === index) {
                    thumb.classList.remove('border-gray-200', 'hover:border-blue-300');
                    thumb.classList.add('border-blue-500', 'ring-2', 'ring-blue-500/30');
                } else {
                    thumb.classList.remove('border-blue-500', 'ring-2', 'ring-blue-500/30');
                    thumb.classList.add('border-gray-200', 'hover:border-blue-300');
                }
            });
        }
    </script>

    <style>
        /* Smooth image transitions for service cards */
        [id^="main-image-"] {
            transition: opacity 0.15s ease-in-out;
        }
        /* Thumbnail border styling */
        .border-3 {
            border-width: 3px;
        }
    </style>
@endsection
