@extends('layouts.app')
@php
    use App\Models\SiteSetting;
@endphp
@section('title', __('messages.seo_title_home'))
@section('meta_description', __('messages.seo_desc_home'))
@section('og_title', __('messages.seo_title_home'))
@section('og_description', __('messages.seo_desc_home'))

@push('styles')
    {{-- Preload the first hero slide (LCP element) so the browser fetches it before layout --}}
    @if($heroSlides->isNotEmpty() && $heroSlides->first()->imageSrc())
        <link rel="preload" as="image" fetchpriority="high" href="{{ $heroSlides->first()->imageSrc() }}">
    @endif
@endpush

@section('content')
    <!-- Hero Carousel Section -->
    @if($heroSlides->isNotEmpty())
    <section class="relative overflow-hidden home-hero-section bg-gray-900" style="height: 100svh; padding-top: var(--site-header-height, 96px); box-sizing: border-box;">
        <div class="carousel-container relative w-full h-full overflow-hidden bg-gray-900">
            <!-- Carousel Slides -->
            @php
                $heroAccents = [
                    'orange' => ['badge' => 'bg-orange-500/20 text-orange-200 border-orange-300/30', 'highlight' => 'text-orange-300', 'shadow' => 'hover:shadow-orange-500/40'],
                    'blue' => ['badge' => 'bg-blue-500/20 text-blue-200 border-blue-300/30', 'highlight' => 'text-blue-300', 'shadow' => 'hover:shadow-blue-500/40'],
                    'cyan' => ['badge' => 'bg-blue-500/20 text-blue-200 border-blue-300/30', 'highlight' => 'text-cyan-200', 'shadow' => 'hover:shadow-blue-500/40'],
                    'emerald' => ['badge' => 'bg-green-500/20 text-green-200 border-green-300/30', 'highlight' => 'text-emerald-200', 'shadow' => 'hover:shadow-green-500/40'],
                ];
            @endphp
            <div class="carousel-slides relative h-full">
                @foreach($heroSlides as $slide)
                    @php
                        $accent = $heroAccents[$slide->accent_color] ?? $heroAccents['orange'];
                        $slideImage = $slide->imageSrc();
                        $ctaUrl = $slide->ctaUrl();
                        $ctaLabel = $slide->getTranslatedCtaLabel();
                    @endphp
                    <div class="carousel-slide {{ $loop->first ? 'active' : '' }} absolute inset-0 transition-opacity duration-1000 ease-in-out"
                         data-slide="{{ $loop->index }}"
                         data-order="{{ $loop->index }}"
                         style="opacity: {{ $loop->first ? '1' : '0' }}; z-index: {{ $loop->first ? '10' : '5' }};">
                        <div class="relative h-full overflow-hidden">
                            @if($slideImage)
                                @if($slide->image_fit === 'contain')
                                    {{-- Blurred backdrop fills the frame while the foreground shows the whole image.
                                         Slides 2+ carry data-src, not src: they are all stacked inside the viewport,
                                         so loading="lazy" would not stop the browser fetching every hero image up
                                         front. The carousel script swaps data-src in just before a slide is shown. --}}
                                    <img @if($loop->first) src="{{ $slideImage }}" loading="eager" @else data-src="{{ $slideImage }}" @endif
                                         alt="" aria-hidden="true"
                                         class="absolute inset-0 w-full h-full object-cover blur-2xl scale-110"
                                         decoding="async">
                                @endif
                                <img @if($loop->first) src="{{ $slideImage }}" loading="eager" fetchpriority="high" @else data-src="{{ $slideImage }}" @endif
                                     alt="{{ $slide->getTranslatedAltText() }}"
                                     class="absolute inset-0 w-full h-full {{ $slide->image_fit === 'contain' ? 'object-contain' : 'object-cover' }}"
                                     style="{{ $slide->imageStyle() }}"
                                     decoding="async">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                            <div class="absolute inset-0 flex items-center">
                                <div class="container mx-auto px-6 md:px-8 lg:px-12">
                                    <div class="max-w-3xl text-white slide-content pt-6 pb-24 md:pt-8 md:pb-28 lg:pt-10 lg:pb-32">
                                        @if($slide->getTranslatedBadge() !== '')
                                            <span class="inline-flex items-center px-4 py-2 backdrop-blur-md rounded-full text-sm font-semibold mb-6 md:mb-8 border shadow-lg {{ $accent['badge'] }}">
                                                <i data-lucide="{{ $slide->badge_icon ?: 'star' }}" class="w-4 h-4 me-2 flex-shrink-0"></i>
                                                {{ $slide->getTranslatedBadge() }}
                                            </span>
                                        @endif
                                        <{{ $loop->first ? 'h1' : 'h2' }} class="font-display text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-6 md:mb-8 lg:mb-10 leading-[1.15] drop-shadow-2xl">
                                            {{ $slide->getTranslatedTitle() }}
                                            @if($slide->getTranslatedHighlight() !== '')
                                                <span class="{{ $accent['highlight'] }} block mt-3 md:mt-4 lg:mt-5">{{ $slide->getTranslatedHighlight() }}</span>
                                            @endif
                                        </{{ $loop->first ? 'h1' : 'h2' }}>
                                        @if($slide->getTranslatedDescription() !== '')
                                            <p class="text-base sm:text-lg md:text-xl mb-8 md:mb-10 lg:mb-12 text-gray-200 leading-relaxed max-w-2xl drop-shadow-lg">
                                                {{ $slide->getTranslatedDescription() }}
                                            </p>
                                        @endif
                                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 relative z-30">
                                            @if($ctaUrl)
                                                <a href="{{ $ctaUrl }}" class="btn-primary text-center inline-flex items-center justify-center group shadow-2xl {{ $accent['shadow'] }}">
                                                    {{ $ctaLabel !== '' ? $ctaLabel : __('messages.learn_more') }}
                                                    <i data-lucide="arrow-right" class="w-5 h-5 ms-2 flex-shrink-0 group-hover:translate-x-1 transition-transform rtl:rotate-180"></i>
                                                </a>
                                            @endif
                                            @if($slide->show_whatsapp)
                                                <button onclick="openWhatsApp()" class="btn-secondary inline-flex items-center justify-center group shadow-xl">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="me-2 group-hover:scale-110 transition-transform flex-shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                    WhatsApp
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($heroSlides->count() > 1)
                <!-- Navigation Arrows -->
                <button onclick="prevSlide()" class="carousel-prev absolute left-4 md:left-8 lg:left-12 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white p-3 md:p-4 rounded-full transition-all duration-300 z-20 group border border-white/20 shadow-lg hover:scale-105" aria-label="Previous slide">
                    <i data-lucide="chevron-left" class="w-5 h-5 md:w-6 md:h-6 rtl:rotate-180"></i>
                </button>
                <button onclick="nextSlide()" class="carousel-next absolute right-4 md:right-8 lg:right-12 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white p-3 md:p-4 rounded-full transition-all duration-300 z-20 group border border-white/20 shadow-lg hover:scale-105" aria-label="Next slide">
                    <i data-lucide="chevron-right" class="w-5 h-5 md:w-6 md:h-6 rtl:rotate-180"></i>
                </button>

                <!-- Dots Indicators -->
                <div class="carousel-dots absolute bottom-5 md:bottom-7 left-1/2 -translate-x-1/2 flex items-center gap-2.5 z-10 px-3 py-2 bg-black/30 backdrop-blur-sm rounded-full border border-white/20">
                    @foreach($heroSlides as $slide)
                        <button onclick="goToSlide({{ $loop->index }})"
                                class="carousel-dot w-2 h-2 md:w-2.5 md:h-2.5 rounded-full {{ $loop->first ? 'bg-white' : 'bg-white/40 hover:bg-white/60' }} shadow-sm transition-all duration-300 hover:scale-110"
                                aria-label="{{ __('messages.go_to_slide', ['number' => $loop->iteration]) }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    <script>
        (() => {
            const siteHeader = document.querySelector('header');
            let headerSyncFrame = null;

            const syncHeroHeightToHeader = () => {
                const headerHeight = siteHeader ? siteHeader.offsetHeight : 96;
                document.documentElement.style.setProperty('--site-header-height', `${headerHeight}px`);
            };

            // Batch the read/write into a single frame so resize can't thrash layout.
            const scheduleHeroHeightSync = () => {
                if (headerSyncFrame !== null) {
                    return;
                }

                headerSyncFrame = window.requestAnimationFrame(() => {
                    headerSyncFrame = null;
                    syncHeroHeightToHeader();
                });
            };

            syncHeroHeightToHeader();
            window.addEventListener('resize', scheduleHeroHeightSync, { passive: true });

            const slides = Array.from(document.querySelectorAll('.carousel-slide'))
                .sort((leftSlide, rightSlide) => {
                    const leftOrder = Number(leftSlide.dataset.order ?? leftSlide.dataset.slide ?? 0);
                    const rightOrder = Number(rightSlide.dataset.order ?? rightSlide.dataset.slide ?? 0);

                    return leftOrder - rightOrder;
                });
            const dots = Array.from(document.querySelectorAll('.carousel-dot'));
            const carouselContainer = document.querySelector('.carousel-container');

            if (!slides.length || !dots.length || !carouselContainer) {
                return;
            }

            // Slides 2+ ship with data-src instead of src: stacking them all inside the
            // viewport defeats loading="lazy", so the browser would fetch every hero image
            // during the initial load. Swap in the real source only when a slide is about
            // to be shown, keeping one slide ahead warm so the fade never lands on a blank.
            const loadSlideImage = (index) => {
                const slide = slides[index];

                if (!slide) {
                    return;
                }

                slide.querySelectorAll('img[data-src]').forEach((image) => {
                    image.src = image.dataset.src;
                    delete image.dataset.src;
                });
            };

            const preloadAround = (index) => {
                loadSlideImage(index);
                loadSlideImage((index + 1) % slides.length);
            };

            const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            const autoplayDelay = 6000;
            const swipeThreshold = 50;
            const transitionDuration = 1000;

            let currentSlide = 0;
            let autoplayInterval = null;
            let isTransitioning = false;
            let isHovered = false;
            let isInViewport = true;
            let touchStartX = 0;

            const setSlideState = (slide, isActive) => {
                slide.style.opacity = isActive ? '1' : '0';
                slide.style.zIndex = isActive ? '10' : '5';
                slide.classList.toggle('active', isActive);
            };

            const updateDots = (activeIndex) => {
                dots.forEach((dot, index) => {
                    if (index === activeIndex) {
                        dot.classList.remove('bg-white/40', 'hover:bg-white/60');
                        dot.classList.add('bg-white', 'scale-110');
                    } else {
                        dot.classList.remove('bg-white', 'scale-110');
                        dot.classList.add('bg-white/40', 'hover:bg-white/60');
                    }
                });
            };

            const canAutoplay = () => !reduceMotionQuery.matches && !document.hidden && !isHovered && isInViewport;

            const stopAutoplay = () => {
                if (autoplayInterval !== null) {
                    window.clearInterval(autoplayInterval);
                    autoplayInterval = null;
                }
            };

            const startAutoplay = () => {
                if (!canAutoplay() || autoplayInterval !== null) {
                    return;
                }

                autoplayInterval = window.setInterval(() => {
                    const nextIndex = (currentSlide + 1) % slides.length;
                    showSlide(nextIndex);
                }, autoplayDelay);
            };

            const showSlide = (nextIndex) => {
                if (isTransitioning || nextIndex === currentSlide || nextIndex < 0 || nextIndex >= slides.length) {
                    return;
                }

                preloadAround(nextIndex);
                isTransitioning = true;
                const previousIndex = currentSlide;

                window.requestAnimationFrame(() => {
                    setSlideState(slides[previousIndex], false);
                    setSlideState(slides[nextIndex], true);
                    updateDots(nextIndex);
                    currentSlide = nextIndex;
                });

                window.setTimeout(() => {
                    isTransitioning = false;
                }, reduceMotionQuery.matches ? 0 : transitionDuration);
            };

            const goToSlideInternal = (targetIndex) => {
                showSlide(targetIndex);
                stopAutoplay();
                startAutoplay();
            };

            window.nextSlide = () => {
                goToSlideInternal((currentSlide + 1) % slides.length);
            };

            window.prevSlide = () => {
                goToSlideInternal((currentSlide - 1 + slides.length) % slides.length);
            };

            window.goToSlide = (index) => {
                if (typeof index !== 'number') {
                    return;
                }
                goToSlideInternal(index);
            };

            slides.forEach((slide, index) => {
                setSlideState(slide, index === 0);
            });
            updateDots(0);

            // Warm the second slide once the page has settled, so the first autoplay fade
            // has an image ready without competing with the LCP request.
            const warmNextSlide = () => loadSlideImage(1);

            if (document.readyState === 'complete') {
                warmNextSlide();
            } else {
                window.addEventListener('load', warmNextSlide, { once: true });
            }

            carouselContainer.addEventListener('mouseenter', () => {
                isHovered = true;
                stopAutoplay();
            });

            carouselContainer.addEventListener('mouseleave', () => {
                isHovered = false;
                startAutoplay();
            });

            carouselContainer.addEventListener('touchstart', (event) => {
                touchStartX = event.changedTouches[0].screenX;
            }, { passive: true });

            carouselContainer.addEventListener('touchend', (event) => {
                const touchEndX = event.changedTouches[0].screenX;
                const deltaX = touchEndX - touchStartX;

                if (Math.abs(deltaX) < swipeThreshold) {
                    return;
                }

                if (deltaX < 0) {
                    window.nextSlide();
                } else {
                    window.prevSlide();
                }
            }, { passive: true });

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    stopAutoplay();
                } else {
                    startAutoplay();
                }
            });

            if (typeof reduceMotionQuery.addEventListener === 'function') {
                reduceMotionQuery.addEventListener('change', () => {
                    stopAutoplay();
                    startAutoplay();
                });
            }

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    const [entry] = entries;
                    isInViewport = Boolean(entry?.isIntersecting);

                    if (!isInViewport) {
                        stopAutoplay();
                    } else {
                        startAutoplay();
                    }
                }, {
                    threshold: 0.25,
                });

                observer.observe(carouselContainer);
            }

            document.addEventListener('keydown', (event) => {
                const tagName = event.target?.tagName;
                if (tagName === 'INPUT' || tagName === 'TEXTAREA' || tagName === 'SELECT') {
                    return;
                }

                if (event.key === 'ArrowLeft') {
                    window.prevSlide();
                }

                if (event.key === 'ArrowRight') {
                    window.nextSlide();
                }
            });

            startAutoplay();
        })();
    </script>

    <!-- Services Section Preview -->
    <section class="py-12 md:py-20 lg:py-24 bg-gradient-to-b from-gray-50 to-white">
        <!-- Header with container -->
        <div class="container mx-auto px-6 md:px-8">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    {{ __('messages.nav_services') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3 md:mb-4">{{ __('messages.our_services') }}</h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed px-4">
                    {{ __('messages.services_intro') }}
                </p>
            </div>
        </div>
        
        <!-- Service Cards Grid -->
        @php
            $serviceAccent = [
                'rose' => '#e11d48', 'orange' => '#ea580c', 'blue' => '#2563eb',
                'violet' => '#7c3aed', 'emerald' => '#059669', 'amber' => '#d97706',
                'yellow' => '#ca8a04', 'teal' => '#0d9488', 'indigo' => '#4f46e5',
            ];
        @endphp
        <div class="container mx-auto px-6 md:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @forelse($services as $service)
                    @php
                        $cardColor = $service->getDisplayColor();
                        $cardHex = $serviceAccent[$cardColor] ?? '#2563eb';
                        $cardIcon = $service->getDisplayIcon();
                        $cardImage = $service->getFeaturedImage();
                        $cardSummary = trim(strip_tags($service->getTranslatedShortDescription()));
                    @endphp
                    <a href="{{ route('services') }}#{{ $service->slug }}"
                       aria-label="{{ __('messages.view_details') }} - {{ $service->getTranslatedTitle() }}"
                       style="--accent: {{ $cardHex }};"
                       class="service-card group relative flex flex-col overflow-hidden bg-white rounded-2xl shadow-lg ring-1 ring-gray-100 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1.5 scroll-fade">
                        <!-- Media -->
                        <div class="relative h-52 md:h-56 overflow-hidden bg-gray-100">
                            @if($cardImage)
                                <img src="{{ $cardImage }}"
                                     alt="{{ $service->getTranslatedTitle() }}"
                                     loading="lazy" decoding="async"
                                     class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110">
                            @else
                                <div class="w-full h-full" style="background: linear-gradient(135deg, {{ $cardHex }}26, {{ $cardHex }}80);"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>
                            <span class="absolute inset-x-0 top-0 h-1 origin-left scale-x-0 transition-transform duration-500 group-hover:scale-x-100" style="background: var(--accent);"></span>
                            <div class="absolute bottom-3 start-3 w-12 h-12 rounded-xl bg-white/95 backdrop-blur-sm flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:-translate-y-0.5" style="color: var(--accent);">
                                @if($service->svg_icon)
                                    {!! $service->svg_icon !!}
                                @elseif($cardIcon)
                                    <i data-lucide="{{ $cardIcon }}" class="w-6 h-6"></i>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                @endif
                            </div>
                        </div>
                        <!-- Body -->
                        <div class="flex flex-1 flex-col p-5 md:p-6">
                            <h3 class="service-card-title text-lg font-bold text-gray-900 leading-snug transition-colors duration-300">
                                {{ $service->getTranslatedTitle() }}
                            </h3>
                            @if($cardSummary !== '')
                                <p class="mt-2 text-sm text-gray-500 leading-relaxed line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($cardSummary, 110) }}
                                </p>
                            @endif
                            <span class="mt-4 inline-flex items-center text-sm font-semibold" style="color: var(--accent);">
                                {{ __('messages.view_details') }}
                                <i data-lucide="arrow-right" class="w-4 h-4 ms-1 transition-transform group-hover:translate-x-1"></i>
                            </span>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 text-center col-span-full">{{ __('messages.services_page_intro') }}</p>
                @endforelse
            </div>
        </div>

        <!-- View All Button -->
        <div class="container mx-auto px-6 md:px-8 text-center mt-12">
            <a href="{{ route('services') }}" class="btn-primary inline-flex items-center justify-center group">
                {{ __('messages.view_all_services') }}
                <i data-lucide="arrow-right" class="w-5 h-5 ms-2 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </section>

    <style>
        /* Reveal the per-service accent on the title when the card is hovered */
        .service-card:hover .service-card-title {
            color: var(--accent);
        }
    </style>

    {{-- TOP Produits Aluminium Section — hidden 2026-07-15, replaced by "They Trusted Us" testimonials below. Kept for possible future reuse.
    <section class="py-14 md:py-20 lg:py-24 bg-gray-50 overflow-hidden">
        <div class="container mx-auto px-6 md:px-8 mb-8 md:mb-12">
            <div class="text-center scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    {{ __('messages.top_products_badge') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __('messages.top_products_title') }}</h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">{{ __('messages.top_products_subtitle') }}</p>
            </div>
        </div>

        <!-- Product Carousel -->
        <div class="relative w-full">
            <button onclick="scrollProducts('left')" class="products-prev absolute start-2 md:start-6 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 rounded-full transition-all duration-300 z-20 shadow-lg hover:shadow-xl hover:scale-110 border border-gray-200" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <button onclick="scrollProducts('right')" class="products-next absolute end-2 md:end-6 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 rounded-full transition-all duration-300 z-20 shadow-lg hover:shadow-xl hover:scale-110 border border-gray-200" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>

            <div id="productsScroller" class="flex gap-5 md:gap-6 overflow-x-auto scrollbar-hide snap-x snap-mandatory px-6 md:px-16 lg:px-20 pb-4 pt-2">
                @foreach($services as $service)
                @php
                    $productImage = $service->getFeaturedImage();
                    $productIcon = $service->getDisplayIcon();
                    $productSummary = trim(strip_tags($service->getTranslatedShortDescription()));
                @endphp
                <a href="{{ route('services') }}#{{ $service->slug }}"
                   aria-label="{{ __('messages.view_details') }} - {{ $service->getTranslatedTitle() }}"
                   class="product-card group relative flex-shrink-0 snap-start w-[80vw] sm:w-[330px] md:w-[360px] h-[400px] md:h-[440px] rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500">
                    <img src="{{ $productImage ?? asset('images/placeholder.jpg') }}"
                         alt="{{ $service->getTranslatedTitle() }}"
                         loading="lazy" decoding="async"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
                    <span class="absolute top-4 start-4 inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white/15 backdrop-blur-md border border-white/25 text-white shadow-lg">
                        <i data-lucide="{{ $productIcon }}" class="w-5 h-5"></i>
                    </span>
                    <div class="absolute inset-x-0 bottom-0 p-5 md:p-6 text-white">
                        <h3 class="font-display text-xl md:text-2xl font-bold mb-2 drop-shadow-lg">{{ $service->getTranslatedTitle() }}</h3>
                        @if($productSummary !== '')
                        <p class="text-sm text-gray-200 leading-relaxed mb-3">{{ \Illuminate\Support\Str::limit($productSummary, 88) }}</p>
                        @endif
                        <span class="inline-flex items-center text-sm font-semibold text-orange-300">
                            {{ __('messages.view_details') }}
                            <i data-lucide="arrow-right" class="w-4 h-4 ms-1 rtl:rotate-180 transition-transform group-hover:translate-x-1"></i>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <style>
        #productsScroller {
            scroll-behavior: smooth;
        }
    </style>

    <script>
        function scrollProducts(direction) {
            const scroller = document.getElementById('productsScroller');
            if (!scroller) return;
            const amount = Math.max(300, Math.round(scroller.clientWidth * 0.85));
            const rtl = document.documentElement.dir === 'rtl';
            const delta = (direction === 'left' ? -amount : amount) * (rtl ? -1 : 1);
            scroller.scrollBy({ left: delta, behavior: 'smooth' });
        }
    </script>
    --}}

    <!-- Testimonials ("They Trusted Us") — moved here from portfolio page 2026-07-15 -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    {{ __('messages.testimonials_badge') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __('messages.client_testimonials') }}</h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">{{ __('messages.testimonials_subtitle') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @forelse($testimonials as $testimonial)
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-transparent hover:border-blue-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < $testimonial->rating; $i++)
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ $testimonial->getTranslatedContent() }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center me-4">
                            <span class="text-blue-600 font-bold">{{ substr($testimonial->client_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $testimonial->client_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $testimonial->client_location }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Default testimonials -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-transparent hover:border-blue-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_1') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center me-4">
                            <span class="text-blue-600 font-bold">M</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Mohamed B.</h4>
                            <p class="text-sm text-gray-500">Paris, France</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-transparent hover:border-blue-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_2') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center me-4">
                            <span class="text-green-600 font-bold">S</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Sonia K.</h4>
                            <p class="text-sm text-gray-500">Montréal, Canada</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-transparent hover:border-blue-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-current"></i>
                    </div>
                    <p class="text-gray-600 mb-6 italic">"{{ __('messages.testimonial_3') }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center me-4">
                            <span class="text-orange-600 font-bold">A</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Ahmed T.</h4>
                            <p class="text-sm text-gray-500">Berlin, Allemagne</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section - COMMENTED OUT -->
    {{--
    <section class="py-12 md:py-20 lg:py-24 bg-white relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute top-0 left-0 w-72 h-72 bg-blue-100 rounded-full -translate-x-1/2 -translate-y-1/2 opacity-50"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-100 rounded-full translate-x-1/2 translate-y-1/2 opacity-50"></div>
        
        <div class="container mx-auto px-6 md:px-8 relative z-10">
            <div class="text-center mb-10 md:mb-14 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    {{ __('messages.why_choose_us') }}
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __('messages.advantages_that_matter') }}</h2>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <div class="scroll-fade stagger-1 group">
                    <div class="bg-gradient-to-br from-white to-blue-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-blue-200 transition-all duration-300 hover:shadow-xl text-center h-full flex flex-col">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                            <i data-lucide="award" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3">{{ __('messages.guaranteed_quality') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">{{ __('messages.european_standards') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-3 group">
                    <div class="bg-gradient-to-br from-white to-orange-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-orange-200 transition-all duration-300 hover:shadow-xl text-center h-full flex flex-col">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                            <i data-lucide="clock" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3">{{ __('messages.deadlines_respected') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">{{ __('messages.clear_planning') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-4 group">
                    <div class="bg-gradient-to-br from-white to-sky-50 p-6 md:p-8 rounded-2xl border border-gray-100 hover:border-sky-200 transition-all duration-300 hover:shadow-xl text-center h-full flex flex-col">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-lg shadow-sky-500/30 group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                            <i data-lucide="shield-check" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3">{{ __('messages.transparent_pricing') }}</h3>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">{{ __('messages.detailed_quotes') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    --}}

    <!-- CTA Section -->
    <section class="py-12 md:py-20 lg:py-24 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg width="127" height="32" viewBox="0 0 127 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M99.9631 11.638V11.6312C99.965 11.3704 99.925 10.4651 99.2914 9.81971C98.8494 9.36924 98.222 9.14015 97.422 9.14015H94.9525C93.2729 9.14015 93.2729 8.75658 93.2729 7.72857C93.2729 7.70691 93.3272 5.62636 94.9777 5.03102C95.5031 4.84092 96.0866 4.74562 96.7112 4.74466H96.7388H99.648H99.6918C100.722 4.74466 101.649 4.04922 101.943 3.0525C102.008 2.83159 102.043 2.60346 102.043 2.37149C102.043 1.06338 100.987 0.000244141 99.6918 0.000244141C99.6423 0.000244141 99.5931 0.00120669 99.5326 0.00601945L99.5417 0.00457562H97.0487H96.8475C96.4561 0.00842583 96.0352 0.0344147 95.5942 0.0810985C94.2717 0.22115 93.0793 0.527241 92.0467 0.989266C87.6901 2.93988 87.4932 6.91425 87.4903 6.95468L87.4794 7.01724C87.2691 8.39466 86.9101 9.14112 85.6172 9.14112C84.976 9.14112 84.4596 9.34855 84.0825 9.75859C83.4141 10.4834 83.437 11.5923 83.4384 11.6028V11.5923C83.4384 13.8441 85.559 14.2677 85.5867 14.2715C86.5836 14.3577 87.3349 15.1739 87.3363 16.1644V16.1634C87.3363 16.2164 87.3225 21.2996 87.3225 23.9668C87.3225 24.9111 87.5733 25.6431 88.0682 26.1388C88.7137 26.7881 89.5347 26.7909 89.5704 26.7909H90.706C90.7747 26.7909 91.3916 26.7808 91.9775 26.3766C92.4557 26.0459 93.0259 25.3644 93.0259 23.9909V16.1081C93.0569 15.0752 93.8988 14.2677 94.9429 14.2677H94.9372H94.9405H94.9453H94.9572L94.9691 14.2653L94.9453 14.2677L94.9334 14.2662C94.942 14.2677 94.9572 14.2686 97.3677 14.2677C98.1557 14.2677 98.7836 14.0347 99.2332 13.577C99.9364 12.8614 99.9641 11.8228 99.9631 11.6211C99.9631 11.6187 99.9631 11.6216 99.9617 11.6264M120.801 22.5539H120.711V23.242H120.79C121.058 23.242 121.28 23.1342 121.28 22.8674C121.28 22.6215 121.109 22.5539 120.801 22.5539ZM121.425 24.7103C121.068 24.0803 120.869 23.6859 120.77 23.6859H120.71V24.7103H120.13V22.1109H121C121.577 22.1109 121.886 22.4155 121.886 22.8199C121.886 23.2422 121.526 23.4503 121.296 23.5298V23.5369C121.425 23.5369 121.786 24.1404 122.095 24.7103H121.425ZM120.998 21.4414C119.889 21.4414 119.091 22.3164 119.091 23.4426C119.091 24.5706 119.889 25.4456 120.998 25.4456C122.104 25.4456 122.902 24.5706 122.902 23.4426C122.902 22.3164 122.104 21.4414 120.998 21.4414ZM120.998 26.0468C119.457 26.0468 118.414 24.9334 118.414 23.4426C118.414 21.9541 119.467 20.8375 121.008 20.8375C122.504 20.8375 123.624 21.9541 123.624 23.4426C123.624 24.9334 122.482 26.0468 120.998 26.0468ZM5.4748 18.279C5.50446 18.2891 6.53776 18.7109 7.63229 19.1553L10.9814 20.5187C11.7722 20.8351 12.1864 21.6774 11.965 22.5188C11.687 23.584 10.4164 24.5917 7.68634 24.2264C6.59947 24.0816 4.07077 23.5413 4.04589 23.537L4.06598 23.5413C3.99997 23.5178 2.48064 22.9775 1.5435 23.4766C1.20672 23.6564 0.987143 23.9445 0.888598 24.3366C0.53412 25.7595 1.9903 26.2585 2.00513 26.2628C2.52608 26.4469 3.10062 26.5778 3.56225 26.6823L3.95452 26.7734C3.98753 26.7806 7.30077 27.573 9.92754 27.5337C11.6545 27.5073 15.8513 27.1214 17.494 24.1032C17.7342 23.6621 18.0724 22.1026 17.904 21.128C17.705 19.9789 17.3615 18.8418 15.393 17.5652C14.8926 17.2406 13.3355 16.7267 11.9626 16.2718C11.2321 16.0302 10.5423 15.8015 10.1806 15.6553C7.76384 14.6763 7.32947 13.7943 7.28833 13.3546C7.26155 13.0891 7.34383 12.5032 7.80929 11.9879C8.38669 11.3493 9.35062 11.0262 10.6762 11.0262C12.6658 11.0262 13.4408 11.2578 14.2607 11.5027C14.4147 11.5507 14.5759 11.5981 14.7429 11.6427C15.3643 11.8129 15.8561 11.7889 16.2067 11.5708C16.6229 11.3134 16.7109 10.8637 16.7416 10.7161C16.7937 10.4462 16.8568 9.52237 15.4117 8.94374C15.2887 8.89484 12.3242 7.7347 7.66099 8.38236C7.0037 8.47393 5.71878 8.72657 4.50992 9.39868C2.74518 10.3786 1.83292 11.8925 1.86497 13.777C1.88889 15.1298 2.52417 16.337 3.7005 17.2718C4.58263 17.9717 5.44419 18.2689 5.48246 18.279M34.6926 22.4036C31.9821 22.4036 29.7776 20.1986 29.7776 17.4882C29.7776 14.7772 31.9821 12.5727 34.6926 12.5727C37.4036 12.5727 39.6077 14.7772 39.6077 17.4882C39.6077 20.1986 37.4036 22.4036 34.6926 22.4036ZM34.6925 3.72117C27.0893 3.72117 20.926 9.88437 20.926 17.4884C20.926 25.0919 27.0893 31.2561 34.6925 31.2561C42.2967 31.2561 48.4609 25.0919 48.4609 17.4884C48.4609 9.88437 42.2967 3.72117 34.6925 3.72117ZM52.9275 11.1498V24.5125C52.9275 24.5139 52.926 24.5149 52.926 24.5168V24.5293V24.5303C52.926 25.7377 53.8728 26.7227 55.064 26.7866C55.1049 26.7895 55.1447 26.7909 55.187 26.7909C55.2274 26.7909 55.2677 26.7895 55.3081 26.7866C56.4911 26.7232 57.4331 25.7493 57.4456 24.5524V24.5303V24.5058V18.752C57.4456 15.3925 58.1017 11.2944 62.1136 11.2944C64.1877 11.2944 64.0618 13.9227 64.0618 14.6434V24.5995H64.0652C64.1012 25.8165 65.0979 26.7909 66.3223 26.7909C67.5693 26.7909 68.5814 25.7786 68.5814 24.5303C68.5814 24.4846 68.5804 24.439 68.5776 24.3948V18.8909C68.5776 18.8909 67.9589 11.1883 73.2859 11.2983C75.2169 11.3377 75.1981 13.5441 75.1981 14.6463V24.4861H75.1991C75.1991 24.501 75.1981 24.5149 75.1981 24.5303C75.1981 25.7786 76.2097 26.7909 77.4582 26.7909C78.7051 26.7909 79.7167 25.7786 79.7167 24.5303C79.7167 24.4784 79.7148 24.4279 79.7105 24.377V12.475C79.7105 10.0558 77.5864 8.93384 75.6641 8.93384C72.0399 8.93384 71.6666 10.1581 69.6036 10.1581C67.6601 10.1581 67.4819 8.93048 64.326 8.93048C61.5708 8.93048 61.0189 10.1586 58.9889 10.1586C57.3976 10.1586 56.5339 8.93528 55.187 8.93528C52.8112 8.93528 52.9275 11.1498 52.9275 11.1498ZM113.524 16.1389C113.019 17.1468 112.41 17.7254 111.853 17.7254C111.204 17.7254 110.791 17.6467 109.857 16.0555L106.462 10.0958C106.211 9.61377 105.715 9.28959 105.051 9.07708C104.384 8.86315 103.696 8.88436 103.212 9.13409C102.926 9.28016 102.731 9.49833 102.648 9.76172C102.572 9.99732 102.591 10.2546 102.701 10.5109L102.702 10.5128C102.728 10.5717 108.745 22.4482 109.101 23.2045C109.27 23.5626 109.357 23.9537 109.357 24.3688C109.357 26.5424 108.149 27.4763 107.56 27.9598C106.08 29.1792 103.984 29.0972 103.963 29.0967L103.96 29.0958H103.957H102.813C102.097 29.0958 101.298 29.6895 101.298 30.5395C101.298 31.4475 101.906 31.9899 102.924 31.9899H103.966L103.954 31.9889C104.011 31.9937 105.345 32.1001 107.055 31.5507C108.634 31.0428 110.831 29.8544 112.342 27.0725C112.703 26.4109 115.823 19.3934 118.1 14.2696L119.804 10.4435C119.813 10.419 119.958 9.88329 119.879 9.69481C119.781 9.45922 119.621 9.25189 119.359 9.11666C118.917 8.88813 118.303 8.91828 117.679 9.06435C117.112 9.1977 116.522 9.79094 116.344 10.1015L113.524 16.1389Z"></path>
					</svg>
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <div class="max-w-3xl mx-auto scroll-fade py-4">
                <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-4 md:mb-6 border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ __('messages.start_your_project') }}
                </span>
                <h2 class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 md:mb-6 leading-tight px-4">
                    {{ SiteSetting::getTranslated('cta_title', __('messages.ready_to_start')) }}
                </h2>
                <p class="text-base md:text-lg lg:text-xl text-blue-200 mb-6 md:mb-8 leading-relaxed px-4 max-w-2xl mx-auto">
                    {{ SiteSetting::getTranslated('cta_description', __('messages.cta_description')) }}
                </p>
                <x-cta-buttons class="px-4"
                    :primary-href="route('contact')"
                    :primary-label="__('messages.request_quote')"
                    :secondary-href="route('portfolio')"
                    :secondary-label="__('messages.view_our_work')" />
            </div>
        </div>
    </section>
@endsection
