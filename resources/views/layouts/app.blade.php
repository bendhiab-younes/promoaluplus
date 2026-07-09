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
    <meta property="og:image" content="@yield('og_image', asset('images/promo-alu-plus-logo.png'))">
    <meta property="og:locale" content="{{ ['fr' => 'fr_FR', 'ar' => 'ar_TN', 'en' => 'en_US'][app()->getLocale()] ?? 'fr_FR' }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'PromoAlu+ - Menuiserie Aluminium & Inox')">
    <meta name="twitter:description" content="@yield('og_description', __('messages.site_description'))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/promo-alu-plus-logo.png'))">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script defer src="https://unpkg.com/lucide@0.544.0/dist/umd/lucide.min.js" integrity="sha384-hK2uiaqTSh/v1VqRxmuMQL4xmt5n0DdyBCOItx2fAs7Wv+WC8Tu0yDW1j12JooyM" crossorigin="anonymous"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    @if(app()->getLocale() === 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @endif
    
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
            font-family: 'Manrope', sans-serif;
        }

        .font-display {
            font-family: 'Playfair Display', serif;
        }

        .font-arabic {
            font-family: 'Noto Sans Arabic', 'Tajawal', 'Cairo', 'Amiri', sans-serif;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Scroll margin for anchor links */
        [id] {
            scroll-margin-top: 100px;
        }

        .scroll-mt-24 {
            scroll-margin-top: 6rem;
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

        /* Elegant glass effect - Always blue */
        .glass-effect {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.45) 0%, rgba(30, 58, 138, 0.45) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-scrolled {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.82) 0%, rgba(30, 58, 138, 0.82) 100%) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .navbar-scrolled a, .navbar-scrolled span, .navbar-scrolled button {
            color: white !important;
        }

        .navbar-scrolled .nav-link:hover {
            color: #60a5fa !important;
        }

        .navbar-scrolled .logo-icon {
            color: #f97316 !important;
        }

        /* Elegant buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.22);
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
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.3);
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

        .btn-primary:active,
        .btn-secondary:active,
        .portfolio-filter:active {
            filter: brightness(0.95);
        }

        .btn-primary:focus-visible,
        .btn-secondary:focus-visible,
        .nav-link:focus-visible,
        .mobile-menu a:focus-visible,
        .portfolio-filter:focus-visible,
        #mobile-menu-btn:focus-visible,
        #close-menu-btn:focus-visible,
        #language-switcher-btn:focus-visible,
        .back-to-top:focus-visible,
        .whatsapp-float:focus-visible,
        #chatbot-toggle:focus-visible,
        .quick-reply-btn:focus-visible {
            outline: 3px solid rgba(249, 115, 22, 0.75);
            outline-offset: 3px;
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

        /* Carousel animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out forwards;
        }

        .carousel-slide {
            transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, z-index;
        }

        .carousel-slide.active {
            opacity: 1 !important;
        }

        /* Slide content animation */
        .carousel-slide .slide-content > * {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .carousel-slide.active .slide-content > *:nth-child(1) {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.2s;
        }

        .carousel-slide.active .slide-content > *:nth-child(2) {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.4s;
        }

        .carousel-slide.active .slide-content > *:nth-child(3) {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.6s;
        }

        .carousel-slide.active .slide-content > *:nth-child(4) {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.8s;
        }

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
            justify-content: flex-start;
            align-items: center;
            padding: calc(4rem + env(safe-area-inset-top)) 1.5rem calc(2rem + env(safe-area-inset-bottom));
            overflow-y: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
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
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.2);
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .back-to-top:hover {
            transform: translateX(-50%) translateY(-3px) scale(1.1);
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.28);
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
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.25);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: pulse-green 2s infinite;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.32);
        }

        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(15, 23, 42, 0.25); }
            50% { box-shadow: 0 0 0 15px rgba(15, 23, 42, 0); }
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
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.2);
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
            .home-hero-section {
                height: auto !important;
                min-height: 100svh;
                padding-top: calc(var(--site-header-height, 96px) + 0.75rem) !important;
                padding-bottom: 1.5rem !important;
                overflow-x: hidden;
                overflow-y: visible;
            }

            .home-hero-section .carousel-container {
                height: auto;
                min-height: calc(100svh - var(--site-header-height, 96px));
                overflow: visible;
            }

            .home-hero-section .carousel-slides {
                height: auto;
            }

            .home-hero-section .carousel-slide {
                position: relative;
                inset: auto;
                height: auto;
                min-height: calc(100svh - var(--site-header-height, 96px));
            }

            .home-hero-section .carousel-slide:not(.active) {
                display: none;
            }

            .home-hero-section .carousel-slide > .relative {
                min-height: inherit;
            }

            .home-hero-section .carousel-slide img {
                min-height: inherit;
                object-position: 50% 20%;
            }

            .home-hero-section .slide-content {
                padding-top: 0.75rem !important;
                padding-bottom: 2rem !important;
            }

            .home-hero-section .slide-content h1,
            .home-hero-section .slide-content h2 {
                margin-top: 0.5rem !important;
            }

            .home-hero-section .slide-content p {
                margin-bottom: 1.5rem !important;
            }

            .home-hero-section .carousel-prev,
            .home-hero-section .carousel-next {
                display: none;
            }

            .home-hero-section .carousel-dots {
                bottom: 0.75rem;
            }

            .glass-effect,
            .navbar-scrolled,
            .btn-secondary {
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }

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

        /*
         * Desktop hero: the slide content is vertically centered in the area below the
         * fixed header. On short viewports (e.g. 1366x768 laptops) the full-size type is
         * taller than that area, so centering pushes the badge up under the translucent
         * header and the buttons down off the bottom. Compress type + spacing by viewport
         * height so the content always fits with breathing room on both ends.
         */
        @media (min-width: 769px) and (max-height: 860px) {
            .home-hero-section .slide-content {
                padding-top: 0.5rem;
                padding-bottom: 3.5rem;
            }

            .home-hero-section .slide-content > span {
                margin-bottom: 0.85rem;
            }

            .home-hero-section .slide-content h1,
            .home-hero-section .slide-content h2 {
                font-size: 3.25rem;
                line-height: 1.12;
                margin-bottom: 1rem;
            }

            .home-hero-section .slide-content h1 span,
            .home-hero-section .slide-content h2 span {
                margin-top: 0.4rem;
            }

            .home-hero-section .slide-content p {
                font-size: 1.1rem;
                line-height: 1.5;
                margin-bottom: 1.25rem;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        }

        @media (min-width: 769px) and (max-height: 700px) {
            .home-hero-section .slide-content {
                padding-top: 0.25rem;
                padding-bottom: 3rem;
            }

            .home-hero-section .slide-content h1,
            .home-hero-section .slide-content h2 {
                font-size: 2.5rem;
                margin-bottom: 0.85rem;
            }

            .home-hero-section .slide-content p {
                font-size: 1rem;
                margin-bottom: 1rem;
                -webkit-line-clamp: 2;
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

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }

            .scroll-fade,
            .scroll-fade-left,
            .scroll-fade-right,
            .carousel-slide .slide-content > * {
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>

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
            <a href="{{ route('portfolio') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_portfolio') }}</a>
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
                    <img src="{{ asset('images/promo-alu-plus-logo.png') }}" alt="PromoAlu+" class="h-16 md:h-20 w-auto">
                    <span class="font-display text-xl md:text-2xl font-bold text-white leading-none">PromoAlu+</span>
                </a>
                
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('home') ? 'text-blue-300' : '' }}">{{ __('messages.nav_home') }}</a>
                    <a href="{{ route('services') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('services') ? 'text-blue-300' : '' }}">{{ __('messages.nav_services') }}</a>
                    <a href="{{ route('portfolio') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('portfolio') ? 'text-blue-300' : '' }}">{{ __('messages.nav_portfolio') }}</a>
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
        $footerServices = \App\Support\CanonicalServiceCatalog::translatedOptions();
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
            'logo' => asset('images/promo-alu-plus-logo.png'),
            'image' => asset('images/promo-alu-plus-logo.png'),
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
                        <img src="{{ asset('images/promo-alu-plus-logo.png') }}" alt="PromoAlu+" class="h-12 w-auto">
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
                        <li><a href="{{ route('portfolio') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_portfolio') }}</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_about') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_contact') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.nav_services') }}</h3>
                    <ul class="space-y-2">
                        @foreach($footerServices as $serviceSlug => $serviceLabel)
                            <li><a href="{{ route('services') }}#{{ $serviceSlug }}" class="text-gray-400 hover:text-white transition-colors">{{ $serviceLabel }}</a></li>
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
            const handleScroll = () => {
                const y = window.pageYOffset;
                if (backToTopBtn) {
                    backToTopBtn.classList.toggle('show', y > 300);
                }
                if (header) {
                    header.classList.toggle('navbar-scrolled', y > 100);
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
