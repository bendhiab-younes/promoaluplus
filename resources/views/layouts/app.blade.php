<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', __('messages.site_description'))">
    <meta name="keywords" content="menuiserie aluminium tunisie, fenêtres aluminium, portes, façades, expatriés tunisiens">
    <meta name="author" content="AluminiumCraft Tunisie">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AluminiumCraft Tunisie') - {{ __('messages.site_tagline') }}</title>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'AluminiumCraft Tunisie - Menuiserie Aluminium')">
    <meta property="og:description" content="@yield('og_description', __('messages.site_description'))">
    <meta property="og:type" content="website">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --light-blue: #60a5fa;
            --primary-orange: #f97316;
            --dark-orange: #ea580c;
        }

        .hero-gradient {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 50%, var(--light-blue) 100%);
        }

        .glass-effect {
            background: rgba(30, 58, 138, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .navbar-scrolled {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-scrolled a, .navbar-scrolled span, .navbar-scrolled button {
            color: #1e3a8a !important;
        }

        .navbar-scrolled .nav-link:hover {
            color: #3b82f6 !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange), var(--dark-orange));
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--primary-blue);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: transparent;
            color: white;
        }

        .service-card {
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.6s ease forwards;
        }

        .fade-in-delay-1 { animation-delay: 0.2s; }
        .fade-in-delay-2 { animation-delay: 0.4s; }
        .fade-in-delay-3 { animation-delay: 0.6s; }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(30, 58, 138, 0.98);
            z-index: 100;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-menu.active {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--primary-blue);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 50;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--secondary-blue);
            transform: translateY(-3px);
        }

        .portfolio-item {
            transition: all 0.3s ease;
        }

        .portfolio-item:hover {
            transform: scale(1.02);
        }

        .portfolio-filter.active {
            background: var(--primary-blue) !important;
            color: white !important;
        }

        [dir="rtl"] {
            text-align: right;
        }

        [dir="rtl"] .space-x-2 > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 1;
        }

        [dir="rtl"] .space-x-4 > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 1;
        }

        [dir="rtl"] .space-x-8 > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 1;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-white">
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu">
        <button id="close-menu-btn" class="absolute top-4 right-4 text-white">
            <i data-lucide="x" class="w-8 h-8"></i>
        </button>
        <nav class="flex flex-col space-y-6 mt-12">
            <a href="{{ route('home') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_home') }}</a>
            <a href="{{ route('services') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_services') }}</a>
            <a href="{{ route('portfolio') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_portfolio') }}</a>
            <a href="{{ route('about') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_about') }}</a>
            <a href="{{ route('contact') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_contact') }}</a>
        </nav>
    </div>

    <!-- Header -->
    <header class="fixed w-full top-0 z-50 glass-effect">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <i data-lucide="building-2" class="w-8 h-8 text-blue-400 transition-colors duration-300"></i>
                    <span class="text-xl font-bold text-white transition-colors duration-300">AluminiumCraft</span>
                </a>
                
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('home') ? 'text-blue-300' : '' }}">{{ __('messages.nav_home') }}</a>
                    <a href="{{ route('services') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('services') ? 'text-blue-300' : '' }}">{{ __('messages.nav_services') }}</a>
                    <a href="{{ route('portfolio') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('portfolio') ? 'text-blue-300' : '' }}">{{ __('messages.nav_portfolio') }}</a>
                    <a href="{{ route('about') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('about') ? 'text-blue-300' : '' }}">{{ __('messages.nav_about') }}</a>
                    <a href="{{ route('contact') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('contact') ? 'text-blue-300' : '' }}">{{ __('messages.nav_contact') }}</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative hidden md:block">
                        <select id="language-selector" onchange="window.location.href='/locale/' + this.value" class="bg-transparent text-white border border-white/30 rounded px-2 py-1 cursor-pointer transition-colors duration-300">
                            <option value="fr" {{ app()->getLocale() === 'fr' ? 'selected' : '' }}>🇫🇷 FR</option>
                            <option value="ar" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>🇹🇳 AR</option>
                            <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>🇬🇧 EN</option>
                        </select>
                    </div>
                    <a href="{{ route('contact') }}" class="hidden md:block bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('messages.free_quote') }}
                    </a>
                </div>
                
                <button id="mobile-menu-btn" class="md:hidden text-white">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i data-lucide="building-2" class="w-8 h-8 text-blue-500"></i>
                        <span class="text-xl font-bold">AluminiumCraft</span>
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
                        <li><a href="{{ route('portfolio') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_portfolio') }}</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_about') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_contact') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.nav_services') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('services') }}#windows" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.windows') }}</a></li>
                        <li><a href="{{ route('services') }}#doors" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.doors') }}</a></li>
                        <li><a href="{{ route('services') }}#facades" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.facades') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.free_quote') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.nav_contact') }}</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i data-lucide="map-pin" class="w-5 h-5 mr-2 mt-1"></i>
                            <span>Tunis, Tunisie</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="phone" class="w-5 h-5 mr-2"></i>
                            <span>+216 12 345 678</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="mail" class="w-5 h-5 mr-2"></i>
                            <span>contact@aluminiumcraft.tn</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} AluminiumCraft Tunisie. {{ __('messages.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top">
        <i data-lucide="arrow-up" class="w-6 h-6"></i>
    </button>

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
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });

                if (closeMenuBtn) {
                    closeMenuBtn.addEventListener('click', () => {
                        mobileMenu.classList.remove('active');
                        document.body.style.overflow = '';
                    });
                }

                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.remove('active');
                        document.body.style.overflow = '';
                    });
                });
            }

            // Back to Top
            const backToTopBtn = document.getElementById('back-to-top');
            if (backToTopBtn) {
                window.addEventListener('scroll', () => {
                    if (window.pageYOffset > 300) {
                        backToTopBtn.classList.add('show');
                    } else {
                        backToTopBtn.classList.remove('show');
                    }
                });

                backToTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }

            // Navbar Scroll Effect
            const header = document.querySelector('header');
            if (header) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 100) {
                        header.classList.add('navbar-scrolled');
                    } else {
                        header.classList.remove('navbar-scrolled');
                    }
                    lucide.createIcons();
                });
            }
        });

        // WhatsApp Integration
        function openWhatsApp() {
            const phoneNumber = '21612345678';
            const message = encodeURIComponent('{{ __("messages.whatsapp_message") }}');
            window.open(`https://wa.me/${phoneNumber}?text=${message}`, '_blank');
        }
    </script>

    @stack('scripts')
</body>
</html>
