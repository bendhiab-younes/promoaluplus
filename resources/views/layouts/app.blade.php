<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', __('messages.site_description'))">
    <meta name="keywords" content="menuiserie aluminium tunisie, fenêtres aluminium, portes, garde-corps, inox, pergola, volets, cuisine aluminium">
    <meta name="author" content="Promo Alu Plus">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Promo Alu Plus') - {{ __('messages.site_tagline') }}</title>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'Promo Alu Plus - Menuiserie Aluminium & Inox')">
    <meta property="og:description" content="@yield('og_description', __('messages.site_description'))">
    <meta property="og:type" content="website">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --light-blue: #60a5fa;
            --primary-orange: #f97316;
            --dark-orange: #ea580c;
            --elegant-gold: #d4af37;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        .font-display {
            font-family: 'Playfair Display', serif;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Hero gradient with subtle animation */
        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 40%, #3b82f6 100%);
            position: relative;
        }

        .hero-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        /* Elegant glass effect */
        .glass-effect {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .navbar-scrolled a, .navbar-scrolled span, .navbar-scrolled button {
            color: #1e3a8a !important;
        }

        .navbar-scrolled .nav-link:hover {
            color: #f97316 !important;
        }

        .navbar-scrolled .logo-icon {
            color: #1e3a8a !important;
        }

        /* Elegant buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary-blue);
            border-color: white;
            transform: translateY(-3px);
        }

        /* Elegant cards */
        .service-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-orange));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        /* Scroll animations */
        .scroll-fade {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .scroll-fade.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .scroll-fade-left {
            opacity: 0;
            transform: translateX(-40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .scroll-fade-left.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .scroll-fade-right {
            opacity: 0;
            transform: translateX(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .scroll-fade-right.visible {
            opacity: 1;
            transform: translateX(0);
        }

        /* Stagger animations */
        .stagger-1 { transition-delay: 0.1s; }
        .stagger-2 { transition-delay: 0.2s; }
        .stagger-3 { transition-delay: 0.3s; }
        .stagger-4 { transition-delay: 0.4s; }

        /* Mobile menu elegant */
        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            z-index: 100;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-menu.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-menu a {
            position: relative;
            padding: 0.5rem 0;
        }

        .mobile-menu a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-orange);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .mobile-menu a:hover::after {
            width: 100%;
        }

        /* Back to top elegant - bottom center */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 50;
            box-shadow: 0 4px 20px rgba(30, 58, 138, 0.3);
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .back-to-top:hover {
            transform: translateX(-50%) translateY(-3px) scale(1.1);
            box-shadow: 0 8px 30px rgba(30, 58, 138, 0.4);
        }

        /* WhatsApp floating button - bottom left */
        .whatsapp-float {
            position: fixed;
            bottom: 1.5rem;
            left: 1.5rem;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 50;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: pulse-green 2s infinite;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.5);
        }

        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.4); }
            50% { box-shadow: 0 0 0 15px rgba(37, 211, 102, 0); }
        }

        /* Portfolio elegant */
        .portfolio-item {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 1rem;
            overflow: hidden;
        }

        .portfolio-item:hover {
            transform: scale(1.03);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .portfolio-item img {
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .portfolio-item:hover img {
            transform: scale(1.1);
        }

        .portfolio-filter {
            transition: all 0.3s ease;
            border-radius: 9999px;
        }

        .portfolio-filter.active {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)) !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
        }

        /* Section dividers */
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
        }

        /* Text gradient */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Elegant form inputs */
        .form-input-elegant {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
        }

        .form-input-elegant:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
            outline: none;
        }

        /* RTL Support */
        [dir="rtl"] {
            text-align: right;
        }

        [dir="rtl"] .space-x-2 > :not([hidden]) ~ :not([hidden]),
        [dir="rtl"] .space-x-4 > :not([hidden]) ~ :not([hidden]),
        [dir="rtl"] .space-x-8 > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 1;
        }

        [dir="rtl"] .whatsapp-float {
            left: auto;
            right: 1.5rem;
        }

        /* Mobile responsiveness improvements */
        @media (max-width: 768px) {
            .hero-gradient {
                min-height: auto;
                padding-top: 5rem;
                padding-bottom: 3rem;
            }

            .hero-gradient h1 {
                font-size: 2rem;
                line-height: 1.2;
            }

            .hero-gradient p {
                font-size: 1rem;
            }

            .service-card {
                padding: 1.5rem;
            }

            .back-to-top {
                bottom: 1.5rem;
                width: 44px;
                height: 44px;
            }

            .whatsapp-float {
                bottom: 1.5rem;
                left: 1rem;
                width: 48px;
                height: 48px;
            }

            section {
                padding-top: 3rem;
                padding-bottom: 3rem;
            }

            h2 {
                font-size: 1.75rem !important;
            }

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Tablet adjustments */
        @media (min-width: 768px) and (max-width: 1024px) {
            .hero-gradient h1 {
                font-size: 2.5rem;
            }

            .container {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        /* Image loading */
        .img-loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
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
                    <i data-lucide="hexagon" class="w-8 h-8 text-orange-400 transition-colors duration-300"></i>
                    <span class="text-xl font-bold text-white transition-colors duration-300">Promo Alu Plus</span>
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
                        <i data-lucide="hexagon" class="w-8 h-8 text-orange-500"></i>
                        <span class="text-xl font-bold">Promo Alu Plus</span>
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
                        <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.doors') }} & {{ __('messages.windows') }}</a></li>
                        <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.railings') }}</a></li>
                        <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.pergola') }} & {{ __('messages.shelter') }}</a></li>
                        <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.shutters') }}</a></li>
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
                            <span>contact@promoaluplus.tn</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Promo Alu Plus. {{ __('messages.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <button onclick="openWhatsApp()" class="whatsapp-float" aria-label="Contact WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </button>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top" aria-label="Retour en haut">
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
                        // Re-initialize Lucide icons after element becomes visible
                        setTimeout(() => {
                            if (typeof lucide !== 'undefined') {
                                lucide.createIcons();
                            }
                        }, 100);
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
            const phoneNumber = '21612345678';
            const message = encodeURIComponent('{{ __("messages.whatsapp_message") }}');
            window.open(`https://wa.me/${phoneNumber}?text=${message}`, '_blank');
        }
    </script>

    @stack('scripts')
</body>
</html>
