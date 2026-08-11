<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', __('messages.site_description'))">
    <meta name="keywords" content="menuiserie aluminium tunisie, fenêtres aluminium, portes, garde-corps, inox, pergola, volets, cuisine aluminium">
    <meta name="author" content="PromoAlu+">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>@yield('title', 'PromoAlu+') - {{ __('messages.site_tagline') }}</title>

    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="PromoAlu+">
    <meta property="og:title" content="@yield('og_title', 'PromoAlu+ - Menuiserie Aluminium & Inox')">
    <meta property="og:description" content="@yield('og_description', __('messages.site_description'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/logo-512.png'))">
    <meta property="og:locale" content="{{ ['fr' => 'fr_FR', 'ar' => 'ar_TN', 'en' => 'en_US'][app()->getLocale()] ?? 'fr_FR' }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'PromoAlu+ - Menuiserie Aluminium & Inox')">
    <meta name="twitter:description" content="@yield('og_description', __('messages.site_description'))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo-512.png'))">

    {{--
        Fonts are self-hosted (see @font-face in resources/css/app.css). Preloading
        them here starts the fetch immediately instead of waiting for the stylesheet
        to parse. crossorigin is required even same-origin: fonts always fetch in
        CORS mode, and omitting it downloads the file twice.
    --}}
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/manrope-latin-var.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/playfair-display-latin-700.woff2') }}" crossorigin>

    <!-- Tailwind CSS (compiled via Vite) -->
    @vite('resources/css/app.css')

    <!-- Lucide Icons -->
    <script defer src="https://unpkg.com/lucide@0.544.0/dist/umd/lucide.min.js" integrity="sha384-hK2uiaqTSh/v1VqRxmuMQL4xmt5n0DdyBCOItx2fAs7Wv+WC8Tu0yDW1j12JooyM" crossorigin="anonymous"></script>

    @if(app()->getLocale() === 'ar')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @endif


    @stack('styles')
</head>
<body class="bg-white">
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu" aria-hidden="true">
        <button id="close-menu-btn" type="button" class="absolute top-4 right-4 text-white" aria-label="Close mobile menu">
            <i data-lucide="x" class="w-8 h-8"></i>
        </button>
        <nav class="flex flex-col space-y-6 mt-12">
            <a href="{{ route('home') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_home') }}</a>
            <a href="{{ route('services') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_services') }}</a>
            @if($portfolioEnabled ?? false)
                <a href="{{ route('portfolio') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_portfolio') }}</a>
            @endif
            <a href="{{ route('about') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_about') }}</a>
            <a href="{{ route('contact') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_contact') }}</a>
            <div class="flex items-center space-x-6 pt-4 border-t border-white/20">
                <a href="/locale/fr" class="flex items-center gap-2 text-white text-lg hover:text-orange-400 transition-colors {{ app()->getLocale() === 'fr' ? 'text-orange-400' : '' }}">
                    <x-locale-flag code="fr" class="h-4 w-auto rounded-[2px] ring-1 ring-white/20" /> FR
                </a>
                <a href="/locale/ar" class="flex items-center gap-2 text-white text-lg hover:text-orange-400 transition-colors {{ app()->getLocale() === 'ar' ? 'text-orange-400' : '' }}">
                    <x-locale-flag code="ar" class="h-4 w-auto rounded-[2px] ring-1 ring-white/20" /> AR
                </a>
                <a href="/locale/en" class="flex items-center gap-2 text-white text-lg hover:text-orange-400 transition-colors {{ app()->getLocale() === 'en' ? 'text-orange-400' : '' }}">
                    <x-locale-flag code="en" class="h-4 w-auto rounded-[2px] ring-1 ring-white/20" /> EN
                </a>
            </div>
        </nav>
    </div>

    <!-- Header -->
    <header class="fixed w-full top-0 z-50 glass-effect">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-160.webp') }}" alt="{{ __('messages.logo_alt') }}" width="160" height="160" class="h-16 md:h-20 w-auto">
                    <span class="font-display text-xl md:text-2xl font-bold text-white leading-none">PromoAlu+</span>
                </a>
                
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('home') ? 'text-blue-300' : '' }}">{{ __('messages.nav_home') }}</a>
                    <a href="{{ route('services') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('services') ? 'text-blue-300' : '' }}">{{ __('messages.nav_services') }}</a>
                    @if($portfolioEnabled ?? false)
                        <a href="{{ route('portfolio') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('portfolio') ? 'text-blue-300' : '' }}">{{ __('messages.nav_portfolio') }}</a>
                    @endif
                    <a href="{{ route('about') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('about') ? 'text-blue-300' : '' }}">{{ __('messages.nav_about') }}</a>
                    <a href="{{ route('contact') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('contact') ? 'text-blue-300' : '' }}">{{ __('messages.nav_contact') }}</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @php
                        $localeOptions = ['fr' => 'Français', 'ar' => 'العربية', 'en' => 'English'];
                        $currentLocale = app()->getLocale();
                    @endphp
                    <div id="language-switcher" class="relative hidden md:block">
                        <button type="button" id="language-switcher-btn" aria-haspopup="listbox" aria-expanded="false" aria-label="Language" class="flex items-center gap-2 bg-white/10 text-white border border-white/30 rounded-lg ps-2 pe-3 py-2 cursor-pointer transition-colors duration-300 hover:bg-white/20">
                            <x-locale-flag :code="$currentLocale" class="h-4 w-auto rounded-[2px] ring-1 ring-white/20" />
                            <span class="text-sm font-semibold uppercase tracking-wide">{{ $currentLocale }}</span>
                            <svg id="language-switcher-chevron" class="w-4 h-4 text-white transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <ul id="language-switcher-menu" role="listbox" class="hidden absolute end-0 mt-2 w-44 bg-white rounded-lg shadow-xl ring-1 ring-black/5 overflow-hidden z-50 py-1">
                            @foreach($localeOptions as $code => $label)
                                <li>
                                    <a href="/locale/{{ $code }}" role="option" aria-selected="{{ $currentLocale === $code ? 'true' : 'false' }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors {{ $currentLocale === $code ? 'bg-gray-50 font-semibold' : '' }}">
                                        <x-locale-flag :code="$code" class="h-4 w-auto rounded-[2px] ring-1 ring-black/10" />
                                        <span>{{ $label }}</span>
                                        @if($currentLocale === $code)
                                            <i data-lucide="check" class="w-4 h-4 ms-auto text-blue-600"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <a href="{{ route('contact') }}" class="hidden md:block bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('messages.free_quote') }}
                    </a>
                </div>
                
                <button id="mobile-menu-btn" type="button" class="md:hidden text-white" aria-label="Open mobile menu" aria-controls="mobile-menu" aria-expanded="false">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    @yield('content')

    @php
        // Straight from the DB so adding, renaming, reordering or deactivating a
        // service in Contenu → Services is reflected here. The anchors match the
        // `id="{{ $service->slug }}"` sections on the services page.
        $footerServices = \App\Models\Service::active()->orderBy('sort_order')->get();
        $footerPhone = \App\Models\SiteSetting::get('contact_phone', '+21626192898');
        $footerWhatsApp = \App\Models\SiteSetting::get('contact_whatsapp', $footerPhone);
        $formatPhone = function($phone) {
            $clean = preg_replace('/[^0-9+]/', '', $phone);
            if (preg_match('/(\+216)(\d{2})(\d{3})(\d{3})/', $clean, $matches)) {
                return $matches[1] . ' ' . $matches[2] . ' ' . $matches[3] . ' ' . $matches[4];
            }
            return $phone;
        };
        $footerEmail = \App\Models\SiteSetting::get('contact_email', 'promoaluplus@gmail.com');
        $footerAddress = \App\Models\SiteSetting::get('contact_address', __('messages.full_address'));

        $localBusinessSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'PromoAlu+',
            'description' => __('messages.site_description'),
            'url' => config('app.url') ?: url('/'),
            'logo' => asset('images/logo-512.png'),
            'image' => asset('images/logo-512.png'),
            'telephone' => $footerPhone,
            'email' => $footerEmail,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $footerAddress,
                'addressCountry' => 'TN',
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Tunisia',
            ],
            'sameAs' => array_values(array_filter([
                $footerWhatsApp ? 'https://wa.me/' . preg_replace('/\D/', '', $footerWhatsApp) : null,
            ])),
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($localBusinessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/logo-160.webp') }}" alt="{{ __('messages.logo_alt') }}" width="160" height="160" class="h-12 w-auto">
                        <span class="text-xl font-bold">PromoAlu+</span>
                    </div>
                    <p class="text-gray-400">
                        {{ __('messages.footer_description') }}
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.navigation') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_home') }}</a></li>
                        <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_services') }}</a></li>
                        @if($portfolioEnabled ?? false)
                            <li><a href="{{ route('portfolio') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_portfolio') }}</a></li>
                        @endif
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_about') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_contact') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.nav_services') }}</h3>
                    <ul class="space-y-2">
                        @foreach($footerServices as $footerService)
                            <li><a href="{{ route('services') }}#{{ $footerService->slug }}" class="text-gray-400 hover:text-white transition-colors">{{ $footerService->getTranslatedTitle() }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.nav_contact') }}</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i data-lucide="map-pin" class="w-5 h-5 me-2 mt-1"></i>
                            <span>{{ $footerAddress }}</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="phone" class="w-5 h-5 me-2"></i>
                            <span>{{ $formatPhone($footerPhone) }}</span>
                        </li>
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                            </svg>
                            <span>{{ $formatPhone($footerWhatsApp) }}</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="mail" class="w-5 h-5 me-2"></i>
                            <span>{{ $footerEmail }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} PromoAlu+. {{ __('messages.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <button onclick="openWhatsApp()" class="whatsapp-float" aria-label="{{ __('messages.whatsapp_contact') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </button>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top" aria-label="{{ __('messages.back_to_top') }}">
        <i data-lucide="arrow-up" class="w-6 h-6"></i>
    </button>

    <!-- Chatbot Widget -->
    @include('components.chatbot')

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide Icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Mobile Menu
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const closeMenuBtn = document.getElementById('close-menu-btn');

            if (mobileMenuBtn && mobileMenu) {
                const setMobileMenuState = (isOpen) => {
                    mobileMenu.classList.toggle('active', isOpen);
                    mobileMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                    mobileMenuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    document.body.style.overflow = isOpen ? 'hidden' : '';
                };

                mobileMenuBtn.addEventListener('click', () => {
                    setMobileMenuState(true);
                });

                if (closeMenuBtn) {
                    closeMenuBtn.addEventListener('click', () => {
                        setMobileMenuState(false);
                    });
                }

                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        setMobileMenuState(false);
                    });
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                        setMobileMenuState(false);
                    }
                });
            }

            // Language Switcher Dropdown
            const langSwitcher = document.getElementById('language-switcher');
            const langBtn = document.getElementById('language-switcher-btn');
            const langMenu = document.getElementById('language-switcher-menu');
            const langChevron = document.getElementById('language-switcher-chevron');

            if (langSwitcher && langBtn && langMenu) {
                const setLangMenu = (isOpen) => {
                    langMenu.classList.toggle('hidden', !isOpen);
                    langBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    if (langChevron) {
                        langChevron.classList.toggle('rotate-180', isOpen);
                    }
                };

                langBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    setLangMenu(langMenu.classList.contains('hidden'));
                });

                document.addEventListener('click', (e) => {
                    if (!langSwitcher.contains(e.target)) {
                        setLangMenu(false);
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        setLangMenu(false);
                    }
                });
            }

            // Back to Top + Navbar Scroll Effect — one passive, rAF-throttled scroll handler
            const backToTopBtn = document.getElementById('back-to-top');
            const header = document.querySelector('header');

            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }

            let scrollTicking = false;
            let lastScrollY = window.pageYOffset;
            const handleScroll = () => {
                const y = window.pageYOffset;
                if (backToTopBtn) {
                    backToTopBtn.classList.toggle('show', y > 300);
                }
                if (header) {
                    header.classList.toggle('navbar-scrolled', y > 100);
                    // Hide on scroll down, reveal on scroll up (5px deadzone against jitter)
                    const delta = y - lastScrollY;
                    if (y <= 100) {
                        header.classList.remove('navbar-hidden');
                        lastScrollY = y;
                    } else if (Math.abs(delta) > 5) {
                        header.classList.toggle('navbar-hidden', delta > 0);
                        lastScrollY = y;
                    }
                } else {
                    lastScrollY = y;
                }
                scrollTicking = false;
            };

            window.addEventListener('scroll', () => {
                if (!scrollTicking) {
                    scrollTicking = true;
                    window.requestAnimationFrame(handleScroll);
                }
            }, { passive: true });

            handleScroll();

            // Scroll Reveal Animation
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.scroll-fade, .scroll-fade-left, .scroll-fade-right').forEach(el => {
                observer.observe(el);
            });

            // Add scroll-fade to service cards if not already added
            document.querySelectorAll('.service-card').forEach((card, index) => {
                if (!card.classList.contains('scroll-fade')) {
                    card.classList.add('scroll-fade', `stagger-${(index % 4) + 1}`);
                    observer.observe(card);
                }
            });
        });

        // WhatsApp Integration
        function openWhatsApp() {
            const configuredNumber = @json(\App\Models\SiteSetting::get('contact_whatsapp', \App\Models\SiteSetting::get('contact_phone', '+21626192898')));
            const phoneNumber = String(configuredNumber || '').replace(/\D/g, '');
            if (!phoneNumber) {
                return;
            }
            const message = encodeURIComponent('{{ __("messages.whatsapp_message") }}');
            window.open(`https://wa.me/${phoneNumber}?text=${message}`, '_blank');
        }
    </script>

    @stack('scripts')
</body>
</html>
