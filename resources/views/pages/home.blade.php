@extends('layouts.app')
@php
    use App\Models\SiteSetting;
@endphp
@section('title', __('messages.nav_home'))

@section('content')
    <!-- Hero Carousel Section -->
    <section class="relative">
        <div class="carousel-container relative w-full min-h-[100svh] overflow-hidden bg-gray-900">
            <!-- Carousel Slides -->
            <div class="carousel-slides relative h-full min-h-[100svh]">
                <!-- Slide 1 - Modern Aluminum Windows -->
                <div class="carousel-slide active absolute inset-0 transition-opacity duration-1000 ease-in-out" data-slide="0" style="opacity: 1; z-index: 10;">
                    <div class="relative h-full min-h-[100svh]">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                             alt="Modern Aluminum Windows" 
                             class="w-full h-full min-h-[100svh] object-cover object-center"
                             loading="eager">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center">
                            <div class="container mx-auto px-6 md:px-8 lg:px-12">
                                <div class="max-w-3xl text-white slide-content">
                                    <span class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-sm font-semibold text-blue-200 mb-5 border border-white/20 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2z"/></svg>
                                        {{ SiteSetting::getTranslated('hero_badge', __('messages.premium_quality')) }}
                                    </span>
                                    <h1 class="font-display text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-5 leading-[1.1] drop-shadow-2xl">
                                        {{ SiteSetting::getTranslated('hero_title', __('messages.hero_title')) }}
                                        <span class="bg-gradient-to-r from-orange-400 via-orange-300 to-yellow-300 bg-clip-text text-transparent block mt-2">{{ SiteSetting::getTranslated('hero_subtitle', __('messages.hero_subtitle')) }}</span>
                                    </h1>
                                    <p class="text-base sm:text-lg md:text-xl mb-7 text-gray-200 leading-relaxed max-w-xl drop-shadow-lg">
                                        {{ SiteSetting::getTranslated('hero_description', __('messages.hero_description')) }}
                                    </p>
                                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                                        <a href="{{ route('contact') }}" class="btn-primary text-center inline-flex items-center justify-center group shadow-2xl hover:shadow-orange-500/40">
                                            <i data-lucide="phone" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform"></i>
                                            {{ __('messages.request_quote') }}
                                        </a>
                                        <button onclick="openWhatsApp()" class="btn-secondary inline-flex items-center justify-center group shadow-xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="me-2 group-hover:scale-110 transition-transform flex-shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            WhatsApp
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 - Aluminum Doors -->
                <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out" data-slide="1" style="opacity: 0; z-index: 5;">
                    <div class="relative h-full min-h-[100svh]">
                        <img src="https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                             alt="Modern Aluminum Doors" 
                             class="w-full h-full min-h-[100svh] object-cover object-center"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center">
                            <div class="container mx-auto px-6 md:px-8 lg:px-12">
                                <div class="max-w-3xl text-white slide-content">
                                    <span class="inline-flex items-center px-4 py-2 bg-orange-500/20 backdrop-blur-md rounded-full text-sm font-semibold text-orange-200 mb-5 border border-orange-300/30 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        {{ __('messages.doors') }}
                                    </span>
                                    <h2 class="font-display text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-5 leading-[1.1] drop-shadow-2xl">
                                        {{ __('messages.modern_design') }}
                                        <span class="bg-gradient-to-r from-orange-400 via-orange-300 to-yellow-300 bg-clip-text text-transparent block mt-2">{{ __('messages.enhanced_security') }}</span>
                                    </h2>
                                    <p class="text-base sm:text-lg md:text-xl mb-7 text-gray-200 leading-relaxed max-w-xl drop-shadow-lg">
                                        {{ __('messages.doors_desc') }}
                                    </p>
                                    <a href="{{ route('services') }}" class="btn-primary inline-flex items-center justify-center group shadow-2xl hover:shadow-orange-500/40">
                                        {{ __('messages.learn_more') }}
                                        <i data-lucide="arrow-right" class="w-5 h-5 ms-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 - Glass Facades & Curtain Walls -->
                <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out" data-slide="2" style="opacity: 0; z-index: 5;">
                    <div class="relative h-full min-h-[100svh]">
                        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                             alt="Modern Glass Facades" 
                             class="w-full h-full min-h-[100svh] object-cover object-center"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center">
                            <div class="container mx-auto px-6 md:px-8 lg:px-12">
                                <div class="max-w-3xl text-white slide-content">
                                    <span class="inline-flex items-center px-4 py-2 bg-blue-500/20 backdrop-blur-md rounded-full text-sm font-semibold text-blue-200 mb-5 border border-blue-300/30 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                                        {{ __('messages.facades') }}
                                    </span>
                                    <h2 class="font-display text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-5 leading-[1.1] drop-shadow-2xl">
                                        {{ __('messages.curtain_walls') }}
                                        <span class="bg-gradient-to-r from-blue-400 via-cyan-300 to-teal-300 bg-clip-text text-transparent block mt-2">{{ __('messages.modern_architecture') }}</span>
                                    </h2>
                                    <p class="text-base sm:text-lg md:text-xl mb-7 text-gray-200 leading-relaxed max-w-xl drop-shadow-lg">
                                        {{ __('messages.facades_desc') }}
                                    </p>
                                    <a href="{{ route('portfolio') }}" class="btn-primary inline-flex items-center justify-center group shadow-2xl hover:shadow-blue-500/40">
                                        {{ __('messages.view_our_work') }}
                                        <i data-lucide="arrow-right" class="w-5 h-5 ms-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 - Company Experience -->
                <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out" data-slide="3" style="opacity: 0; z-index: 5;">
                    <div class="relative h-full min-h-[100svh]">
                        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                             alt="Professional Construction Team" 
                             class="w-full h-full min-h-[100svh] object-cover object-center"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center">
                            <div class="container mx-auto px-6 md:px-8 lg:px-12">
                                <div class="max-w-3xl text-white slide-content">
                                    <span class="inline-flex items-center px-4 py-2 bg-green-500/20 backdrop-blur-md rounded-full text-sm font-semibold text-green-200 mb-5 border border-green-300/30 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        {{ __('messages.guaranteed_quality') }}
                                    </span>
                                    <h2 class="font-display text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-5 leading-[1.1] drop-shadow-2xl">
                                        {{ SiteSetting::get('stats_years', '15') }}+ {{ __('messages.years_experience') }}
                                        <span class="bg-gradient-to-r from-green-400 via-emerald-300 to-teal-300 bg-clip-text text-transparent block mt-2">{{ SiteSetting::get('stats_projects', '500') }}+ {{ __('messages.projects_completed') }}</span>
                                    </h2>
                                    <p class="text-base sm:text-lg md:text-xl mb-7 text-gray-200 leading-relaxed max-w-xl drop-shadow-lg">
                                        {{ __('messages.european_standards') }}
                                    </p>
                                    <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center justify-center group shadow-2xl hover:shadow-green-500/40">
                                        {{ __('messages.start_your_project') }}
                                        <i data-lucide="arrow-right" class="w-5 h-5 ms-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button onclick="prevSlide()" class="carousel-prev absolute left-4 md:left-8 lg:left-12 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white p-3 md:p-4 rounded-full transition-all duration-300 z-20 group border border-white/20 shadow-lg hover:scale-105" aria-label="Previous slide">
                <i data-lucide="chevron-left" class="w-5 h-5 md:w-6 md:h-6"></i>
            </button>
            <button onclick="nextSlide()" class="carousel-next absolute right-4 md:right-8 lg:right-12 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white p-3 md:p-4 rounded-full transition-all duration-300 z-20 group border border-white/20 shadow-lg hover:scale-105" aria-label="Next slide">
                <i data-lucide="chevron-right" class="w-5 h-5 md:w-6 md:h-6"></i>
            </button>

            <!-- Dots Indicators -->
            <div class="absolute bottom-8 md:bottom-10 left-1/2 -translate-x-1/2 flex gap-2 z-20 p-2 bg-black/20 backdrop-blur-sm rounded-full">
                <button onclick="goToSlide(0)" class="carousel-dot w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-white shadow-sm transition-all duration-300 hover:scale-110" aria-label="Go to slide 1"></button>
                <button onclick="goToSlide(1)" class="carousel-dot w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-white/40 hover:bg-white/60 shadow-sm transition-all duration-300 hover:scale-110" aria-label="Go to slide 2"></button>
                <button onclick="goToSlide(2)" class="carousel-dot w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-white/40 hover:bg-white/60 shadow-sm transition-all duration-300 hover:scale-110" aria-label="Go to slide 3"></button>
                <button onclick="goToSlide(3)" class="carousel-dot w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-white/40 hover:bg-white/60 shadow-sm transition-all duration-300 hover:scale-110" aria-label="Go to slide 4"></button>
            </div>
        </div>
    </section>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        let autoplayInterval;
        let isTransitioning = false;

        function showSlide(index) {
            if (isTransitioning) return;
            isTransitioning = true;

            const oldSlide = currentSlide;
            
            // Immediately set z-index for proper layering
            slides[oldSlide].style.zIndex = '5';
            slides[index].style.zIndex = '10';
            
            // Start fade out of old slide and fade in of new slide
            slides[oldSlide].style.opacity = '0';
            slides[oldSlide].classList.remove('active');
            
            slides[index].style.opacity = '1';
            slides[index].classList.add('active');
            
            // Update dots
            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.remove('bg-white/40', 'hover:bg-white/70');
                    dot.classList.add('bg-white', 'scale-125');
                } else {
                    dot.classList.remove('bg-white', 'scale-125');
                    dot.classList.add('bg-white/40', 'hover:bg-white/70');
                }
            });

            // Re-initialize Lucide icons for the new slide
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);

            // Allow next transition after animation completes
            setTimeout(() => {
                isTransitioning = false;
            }, 1000);
        }

        function nextSlide() {
            const newSlide = (currentSlide + 1) % slides.length;
            showSlide(newSlide);
            currentSlide = newSlide;
            resetAutoplay();
        }

        function prevSlide() {
            const newSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(newSlide);
            currentSlide = newSlide;
            resetAutoplay();
        }

        function goToSlide(index) {
            if (currentSlide !== index && !isTransitioning) {
                showSlide(index);
                currentSlide = index;
                resetAutoplay();
            }
        }

        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 6000); // Change slide every 6 seconds
        }

        function resetAutoplay() {
            clearInterval(autoplayInterval);
            startAutoplay();
        }

        // Initialize carousel on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial state
            slides.forEach((slide, i) => {
                if (i === 0) {
                    slide.style.opacity = '1';
                    slide.style.zIndex = '10';
                    slide.classList.add('active');
                } else {
                    slide.style.opacity = '0';
                    slide.style.zIndex = '5';
                    slide.classList.remove('active');
                }
            });

            // Initialize dots
            dots.forEach((dot, i) => {
                if (i === 0) {
                    dot.classList.add('bg-white', 'scale-125');
                    dot.classList.remove('bg-white/40', 'hover:bg-white/70');
                } else {
                    dot.classList.add('bg-white/40', 'hover:bg-white/70');
                    dot.classList.remove('bg-white', 'scale-125');
                }
            });

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            startAutoplay();

            // Pause autoplay on hover
            const carouselContainer = document.querySelector('.carousel-container');
            if (carouselContainer) {
                carouselContainer.addEventListener('mouseenter', () => {
                    clearInterval(autoplayInterval);
                });

                carouselContainer.addEventListener('mouseleave', () => {
                    startAutoplay();
                });

                // Touch swipe support
                let touchStartX = 0;
                let touchEndX = 0;

                carouselContainer.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });

                carouselContainer.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                }, { passive: true });

                function handleSwipe() {
                    const swipeThreshold = 50;
                    if (touchEndX < touchStartX - swipeThreshold) {
                        nextSlide();
                    } else if (touchEndX > touchStartX + swipeThreshold) {
                        prevSlide();
                    }
                }
            }

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') prevSlide();
                if (e.key === 'ArrowRight') nextSlide();
            });
        });
    </script>

    <!-- Services Section Preview -->
    <section class="py-12 md:py-20 lg:py-24 bg-gradient-to-b from-gray-50 to-white">
        <!-- Header with container -->
        <div class="container mx-auto px-6 md:px-8">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    {{ __('messages.nav_services') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3 md:mb-4">{{ __('messages.our_services') }}</h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed px-4">
                    {{ __('messages.services_intro') }}
                </p>
            </div>
        </div>
        
        <!-- Full Width Horizontal Scrolling Service Cards -->
        <div class="relative w-full">
            <!-- Scroll Container - Full Width -->
            <div class="overflow-x-auto overflow-y-visible scrollbar-hide pb-8 pt-4 w-full">
                    <div class="flex gap-6 min-w-max py-4 px-6 md:px-8">
                        @php
                            $newServices = [
                                ['name' => 'kitchen', 'color' => 'rose', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="M3 9V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4"/><path d="M12 3v6"/><path d="M8 21v-4"/><path d="M16 21v-4"/></svg>'],
                                ['name' => 'doors', 'color' => 'orange', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'],
                                ['name' => 'windows', 'color' => 'blue', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="3" x2="12" y2="21"></line><line x1="3" y1="12" x2="21" y2="12"></line></svg>'],
                                ['name' => 'rolling_shutters', 'color' => 'purple', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="7" x2="21" y2="7"/><line x1="3" y1="11" x2="21" y2="11"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="3" y1="19" x2="21" y2="19"/></svg>'],
                                ['name' => 'railings', 'color' => 'green', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22V8"/><path d="M5 12H2a10 10 0 0 0 20 0h-3"/><path d="M22 8l-2 2-2-2"/><path d="M6 8l-2 2-2-2"/><path d="M6 22v-4"/><path d="M18 22v-4"/></svg>'],
                                ['name' => 'pergola', 'color' => 'amber', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22V6"/><path d="M20 22V6"/><path d="M2 6h20"/><path d="M2 10h20"/><path d="M12 6v16"/></svg>'],
                                ['name' => 'sun_breakers', 'color' => 'yellow', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'],
                                ['name' => 'mosquito_nets', 'color' => 'teal', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>'],
                                ['name' => 'space_design', 'color' => 'indigo', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
                            ];
                        @endphp

                        @foreach($newServices as $service)
                        <a href="{{ route('services') }}#{{ $service['name'] }}" 
                           class="service-item group relative flex flex-col items-center justify-center bg-white rounded-2xl shadow-lg p-6 md:p-8 transition-all duration-300 hover:shadow-2xl hover:scale-110 cursor-pointer min-w-[180px] w-[180px] md:min-w-[200px] md:w-[200px] border-2 border-transparent hover:border-{{ $service['color'] }}-300">
                            <!-- Icon Container -->
                            <div class="w-16 h-16 md:w-20 md:h-20 mb-4 rounded-xl bg-gradient-to-br from-{{ $service['color'] }}-400 to-{{ $service['color'] }}-600 flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 shadow-lg group-hover:shadow-{{ $service['color'] }}-500/50">
                                {!! $service['icon'] !!}
                            </div>
                            
                            <!-- Service Name -->
                            <h3 class="text-base md:text-lg font-bold text-gray-800 text-center transition-colors group-hover:text-{{ $service['color'] }}-600">
                                {{ __('messages.' . $service['name']) }}
                            </h3>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- View All Button - Inside Container -->
        <div class="container mx-auto px-6 md:px-8 text-center mt-12">
                <a href="{{ route('services') }}" class="btn-primary inline-flex items-center justify-center group">
                    {{ __('messages.view_all_services') }}
                    <i data-lucide="arrow-right" class="w-5 h-5 ms-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </section>

    <style>
        /* Hide scrollbar but keep functionality */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Service item animation improvements */
        .service-item {
            flex-shrink: 0;
            will-change: transform;
        }

        .service-item:hover {
            z-index: 20;
        }

        /* Smooth scroll behavior */
        .overflow-x-auto {
            scroll-behavior: smooth;
            /* Allow vertical overflow to prevent clipping on scale */
            overflow-y: visible !important;
        }

        /* Prevent parent from clipping transformed children */
        .overflow-x-auto > div {
            transform-style: preserve-3d;
        }
    </style>

    <!-- Why Choose Us Section -->
    <section class="py-12 md:py-20 lg:py-24 bg-white relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute top-0 left-0 w-72 h-72 bg-blue-100 rounded-full -translate-x-1/2 -translate-y-1/2 opacity-50"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-100 rounded-full translate-x-1/2 translate-y-1/2 opacity-50"></div>
        
        <div class="container mx-auto px-6 md:px-8 relative z-10">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ __('messages.start_your_project') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-4 md:mb-6 leading-tight">
                    {{ SiteSetting::getTranslated('cta_title', __('messages.ready_to_start')) }}
                </h2>
                <p class="text-base md:text-lg lg:text-xl text-blue-200 mb-6 md:mb-8 leading-relaxed px-4">
                    {{ SiteSetting::getTranslated('cta_description', __('messages.cta_description')) }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 group-hover:scale-110 transition-transform flex-shrink-0"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        {{ __('messages.request_quote') }}
                    </a>
                    <a href="{{ route('portfolio') }}" class="group bg-white/10 backdrop-blur-sm hover:bg-white text-white hover:text-blue-900 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center border-2 border-white/30 hover:border-white hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 group-hover:scale-110 transition-transform flex-shrink-0"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{ __('messages.view_our_work') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
