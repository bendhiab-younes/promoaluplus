@extends('layouts.app')
@php
    use App\Models\SiteSetting;
@endphp
@section('title', __('messages.nav_home'))

@section('content')
    <!-- Hero Carousel Section -->
    <section class="relative pt-16 md:pt-20">
        <div class="carousel-container relative w-full h-[85vh] md:h-[90vh] overflow-hidden bg-gray-900">
            <!-- Carousel Slides -->
            <div class="carousel-slides relative h-full">
                <!-- Slide 1 - Modern Aluminum Windows -->
                <div class="carousel-slide active absolute inset-0 transition-opacity duration-1000 ease-in-out" data-slide="0" style="opacity: 1; z-index: 10;">
                    <div class="relative h-full">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                             alt="Modern Aluminum Windows" 
                             class="w-full h-full object-cover object-center"
                             loading="eager">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/30"></div>
                        <div class="absolute inset-0 flex items-center pb-24 md:pb-32">
                            <div class="container mx-auto px-6 md:px-8">
                                <div class="max-w-3xl text-white slide-content">
                                    <span class="inline-block px-5 py-2.5 bg-white/10 backdrop-blur-md rounded-full text-sm font-semibold text-blue-200 mb-6 border border-white/20 shadow-lg">
                                        ✨ {{ SiteSetting::getTranslated('hero_badge', __('messages.premium_quality')) }}
                                    </span>
                                    <h1 class="font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight drop-shadow-2xl">
                                        {{ SiteSetting::getTranslated('hero_title', __('messages.hero_title')) }}
                                        <span class="bg-gradient-to-r from-orange-400 via-orange-300 to-yellow-300 bg-clip-text text-transparent block mt-3">{{ SiteSetting::getTranslated('hero_subtitle', __('messages.hero_subtitle')) }}</span>
                                    </h1>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-8 text-gray-100 leading-relaxed max-w-2xl drop-shadow-lg">
                                        {{ SiteSetting::getTranslated('hero_description', __('messages.hero_description')) }}
                                    </p>
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <a href="{{ route('contact') }}" class="btn-primary text-center inline-flex items-center justify-center group shadow-2xl hover:shadow-orange-500/50">
                                            <i data-lucide="phone" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
                                            {{ __('messages.request_quote') }}
                                        </a>
                                        <button onclick="openWhatsApp()" class="btn-secondary inline-flex items-center justify-center group shadow-xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="mr-2 group-hover:scale-110 transition-transform"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
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
                    <div class="relative h-full">
                        <img src="https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                             alt="Modern Aluminum Doors" 
                             class="w-full h-full object-cover object-center"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/30"></div>
                        <div class="absolute inset-0 flex items-center pb-24 md:pb-32">
                            <div class="container mx-auto px-6 md:px-8">
                                <div class="max-w-3xl text-white slide-content">
                                    <span class="inline-block px-5 py-2.5 bg-orange-500/20 backdrop-blur-md rounded-full text-sm font-semibold text-orange-200 mb-6 border border-orange-300/30 shadow-lg">
                                        🚪 {{ __('messages.doors') }}
                                    </span>
                                    <h2 class="font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight drop-shadow-2xl">
                                        {{ __('messages.modern_design') }}
                                        <span class="bg-gradient-to-r from-orange-400 via-orange-300 to-yellow-300 bg-clip-text text-transparent block mt-3">{{ __('messages.enhanced_security') }}</span>
                                    </h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-8 text-gray-100 leading-relaxed max-w-2xl drop-shadow-lg">
                                        {{ __('messages.doors_desc') }}
                                    </p>
                                    <a href="{{ route('services') }}" class="btn-primary inline-flex items-center justify-center group shadow-2xl hover:shadow-orange-500/50">
                                        {{ __('messages.learn_more') }}
                                        <i data-lucide="arrow-right" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 - Glass Facades & Curtain Walls -->
                <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out" data-slide="2" style="opacity: 0; z-index: 5;">
                    <div class="relative h-full">
                        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                             alt="Modern Glass Facades" 
                             class="w-full h-full object-cover object-center"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/30"></div>
                        <div class="absolute inset-0 flex items-center pb-24 md:pb-32">
                            <div class="container mx-auto px-6 md:px-8">
                                <div class="max-w-3xl text-white slide-content">
                                    <span class="inline-block px-5 py-2.5 bg-blue-500/20 backdrop-blur-md rounded-full text-sm font-semibold text-blue-200 mb-6 border border-blue-300/30 shadow-lg">
                                        🏢 {{ __('messages.facades') }}
                                    </span>
                                    <h2 class="font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight drop-shadow-2xl">
                                        {{ __('messages.curtain_walls') }}
                                        <span class="bg-gradient-to-r from-blue-400 via-cyan-300 to-teal-300 bg-clip-text text-transparent block mt-3">{{ __('messages.modern_architecture') }}</span>
                                    </h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-8 text-gray-100 leading-relaxed max-w-2xl drop-shadow-lg">
                                        {{ __('messages.facades_desc') }}
                                    </p>
                                    <a href="{{ route('portfolio') }}" class="btn-primary inline-flex items-center justify-center group shadow-2xl hover:shadow-blue-500/50">
                                        {{ __('messages.view_our_work') }}
                                        <i data-lucide="arrow-right" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 - Company Experience -->
                <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out" data-slide="3" style="opacity: 0; z-index: 5;">
                    <div class="relative h-full">
                        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                             alt="Professional Construction Team" 
                             class="w-full h-full object-cover object-center"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/30"></div>
                        <div class="absolute inset-0 flex items-center pb-24 md:pb-32">
                            <div class="container mx-auto px-6 md:px-8">
                                <div class="max-w-3xl text-white slide-content">
                                    <span class="inline-block px-5 py-2.5 bg-green-500/20 backdrop-blur-md rounded-full text-sm font-semibold text-green-200 mb-6 border border-green-300/30 shadow-lg">
                                        ⭐ {{ __('messages.guaranteed_quality') }}
                                    </span>
                                    <h2 class="font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight drop-shadow-2xl">
                                        {{ SiteSetting::get('stats_years', '15') }}+ {{ __('messages.years_experience') }}
                                        <span class="bg-gradient-to-r from-green-400 via-emerald-300 to-teal-300 bg-clip-text text-transparent block mt-3">{{ SiteSetting::get('stats_projects', '500') }}+ {{ __('messages.projects_completed') }}</span>
                                    </h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-8 text-gray-100 leading-relaxed max-w-2xl drop-shadow-lg">
                                        {{ __('messages.european_standards') }}
                                    </p>
                                    <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center justify-center group shadow-2xl hover:shadow-green-500/50">
                                        {{ __('messages.start_your_project') }}
                                        <i data-lucide="arrow-right" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button onclick="prevSlide()" class="carousel-prev absolute left-3 md:left-8 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/25 backdrop-blur-md text-white p-3 md:p-4 rounded-full transition-all duration-300 z-20 group border border-white/20 shadow-xl hover:scale-110" aria-label="Previous slide">
                <i data-lucide="chevron-left" class="w-6 h-6 md:w-8 md:h-8"></i>
            </button>
            <button onclick="nextSlide()" class="carousel-next absolute right-3 md:right-8 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/25 backdrop-blur-md text-white p-3 md:p-4 rounded-full transition-all duration-300 z-20 group border border-white/20 shadow-xl hover:scale-110" aria-label="Next slide">
                <i data-lucide="chevron-right" class="w-6 h-6 md:w-8 md:h-8"></i>
            </button>

            <!-- Dots Indicators -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2.5 z-20">
                <button onclick="goToSlide(0)" class="carousel-dot w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-white shadow-lg transition-all duration-300 hover:scale-125" aria-label="Go to slide 1"></button>
                <button onclick="goToSlide(1)" class="carousel-dot w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-white/40 hover:bg-white/70 shadow-lg transition-all duration-300 hover:scale-125" aria-label="Go to slide 2"></button>
                <button onclick="goToSlide(2)" class="carousel-dot w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-white/40 hover:bg-white/70 shadow-lg transition-all duration-300 hover:scale-125" aria-label="Go to slide 3"></button>
                <button onclick="goToSlide(3)" class="carousel-dot w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-white/40 hover:bg-white/70 shadow-lg transition-all duration-300 hover:scale-125" aria-label="Go to slide 4"></button>
            </div>

            <!-- Trust indicators overlay (positioned at bottom of carousel) -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 via-black/50 to-transparent pt-20 pb-6 z-15">
                <div class="container mx-auto px-6">
                    <div class="grid grid-cols-3 gap-4 md:gap-8 max-w-4xl mx-auto">
                        <div class="text-center px-2">
                            <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-1 drop-shadow-lg">{{ SiteSetting::get('stats_projects', '500') }}+</div>
                            <div class="text-xs sm:text-sm md:text-base text-gray-200 font-medium">{{ __('messages.projects_completed') }}</div>
                        </div>
                        <div class="text-center px-2 border-x border-white/30">
                            <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-1 drop-shadow-lg">{{ SiteSetting::get('stats_years', '15') }}+</div>
                            <div class="text-xs sm:text-sm md:text-base text-gray-200 font-medium">{{ __('messages.years_experience') }}</div>
                        </div>
                        <div class="text-center px-2">
                            <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-1 drop-shadow-lg">{{ SiteSetting::get('stats_satisfaction', '98') }}%</div>
                            <div class="text-xs sm:text-sm md:text-base text-gray-200 font-medium">{{ __('messages.satisfied_clients') }}</div>
                        </div>
                    </div>
                </div>
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
            
            <!-- Horizontal Scrolling Service Cards -->
            <div class="relative">
                <!-- Scroll Container -->
                <div class="overflow-x-auto scrollbar-hide pb-6 -mx-6 px-6">
                    <div class="flex gap-6 min-w-max">
                        @php
                            $newServices = [
                                ['name' => 'doors', 'icon' => 'door-open', 'color' => 'orange', 'emoji' => '🚪'],
                                ['name' => 'windows', 'icon' => 'square', 'color' => 'blue', 'emoji' => '🪟'],
                                ['name' => 'sliding', 'icon' => 'move-horizontal', 'color' => 'cyan', 'emoji' => '↔️'],
                                ['name' => 'rolling_shutters', 'icon' => 'blinds', 'color' => 'purple', 'emoji' => '🎚️'],
                                ['name' => 'railings', 'icon' => 'fence', 'color' => 'green', 'emoji' => '🛡️'],
                                ['name' => 'pergola', 'icon' => 'tent', 'color' => 'amber', 'emoji' => '🏕️'],
                                ['name' => 'sun_breakers', 'icon' => 'sun', 'color' => 'yellow', 'emoji' => '☀️'],
                                ['name' => 'mosquito_nets', 'icon' => 'bug', 'color' => 'teal', 'emoji' => '🦟'],
                                ['name' => 'space_design', 'icon' => 'layout-grid', 'color' => 'indigo', 'emoji' => '✨'],
                            ];
                        @endphp

                        @foreach($newServices as $service)
                        <a href="{{ route('services') }}#{{ $service['name'] }}" 
                           class="service-item group relative flex flex-col items-center justify-center bg-white rounded-2xl shadow-lg p-8 transition-all duration-500 hover:shadow-2xl hover:scale-110 hover:-translate-y-2 cursor-pointer min-w-[200px] w-[200px] border-2 border-transparent hover:border-{{ $service['color'] }}-300">
                            <!-- Icon Container -->
                            <div class="w-20 h-20 mb-4 rounded-xl bg-gradient-to-br from-{{ $service['color'] }}-400 to-{{ $service['color'] }}-600 flex items-center justify-center transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 shadow-lg group-hover:shadow-{{ $service['color'] }}-500/50">
                                <span class="text-3xl">{{ $service['emoji'] }}</span>
                            </div>
                            
                            <!-- Service Name -->
                            <h3 class="text-lg font-bold text-gray-800 text-center mb-2 transition-colors group-hover:text-{{ $service['color'] }}-600">
                                {{ __('messages.' . $service['name']) }}
                            </h3>
                            
                            <!-- Short Description -->
                            <p class="text-sm text-gray-600 text-center leading-relaxed opacity-0 group-hover:opacity-100 transition-opacity duration-300 absolute -bottom-16 left-0 right-0 bg-white/95 backdrop-blur-sm p-3 rounded-lg shadow-lg">
                                {{ __('messages.' . $service['name'] . '_desc') }}
                            </p>
                            
                            <!-- Hover Arrow -->
                            <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-y-2">
                                <i data-lucide="chevron-down" class="w-6 h-6 text-{{ $service['color'] }}-600 animate-bounce"></i>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Scroll Indicators -->
                <div class="absolute left-0 top-0 bottom-0 w-20 bg-gradient-to-r from-gray-50 to-transparent pointer-events-none"></div>
                <div class="absolute right-0 top-0 bottom-0 w-20 bg-gradient-to-l from-white to-transparent pointer-events-none"></div>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12">
                <a href="{{ route('services') }}" class="btn-primary inline-flex items-center justify-center group">
                    {{ __('messages.view_all_services') }}
                    <i data-lucide="arrow-right" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"></i>
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
        }

        .service-item:hover {
            z-index: 10;
        }

        /* Smooth scroll behavior */
        .overflow-x-auto {
            scroll-behavior: smooth;
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
