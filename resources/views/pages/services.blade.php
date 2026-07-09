@extends('layouts.app')

@section('title', __('messages.seo_title_services'))
@section('meta_description', __('messages.seo_desc_services'))
@section('og_title', __('messages.seo_title_services'))
@section('og_description', __('messages.seo_desc_services'))

@php
    $servicesSchemaItems = [];

    foreach ($services as $position => $schemaService) {
        $schemaTitle = trim($schemaService->getTranslatedTitle());
        $schemaDescription = trim(strip_tags((string) $schemaService->getTranslatedDescription()));

        if ($schemaDescription === '') {
            $schemaDescription = trim($schemaService->getTranslatedShortDescription());
        }

        $schemaGallery = array_values(array_filter($schemaService->getGalleryImages(), static fn ($image) => is_string($image) && trim($image) !== ''));
        $schemaFeatures = array_values(array_filter($schemaService->getTranslatedFeatures(), static fn ($feature) => is_string($feature) && trim($feature) !== ''));
        $schemaMaterials = array_values(array_filter($schemaService->getTranslatedMaterials(), static fn ($material) => is_string($material) && trim($material) !== ''));
        $schemaSpecs = is_array($schemaService->specs) ? $schemaService->specs : [];

        $schemaProperties = [];

        foreach ($schemaFeatures as $feature) {
            $schemaProperties[] = [
                '@type' => 'PropertyValue',
                'name' => __('messages.key_features'),
                'value' => $feature,
            ];
        }

        foreach ($schemaSpecs as $specKey => $specValue) {
            $specLabel = __('messages.' . $specKey);

            if ($specLabel === 'messages.' . $specKey) {
                $specLabel = ucfirst(str_replace('_', ' ', (string) $specKey));
            }

            if (is_array($specValue)) {
                $specValue = $specValue[app()->getLocale()] ?? $specValue['fr'] ?? '';
            }

            if (! is_string($specValue) || trim($specValue) === '') {
                continue;
            }

            $schemaProperties[] = [
                '@type' => 'PropertyValue',
                'name' => $specLabel,
                'value' => trim($specValue),
            ];
        }

        $serviceUrl = route('services') . '#' . $schemaService->slug;

        $serviceNode = [
            '@type' => 'Service',
            '@id' => $serviceUrl,
            'name' => $schemaTitle,
            'serviceType' => $schemaTitle,
            'description' => $schemaDescription,
            'url' => $serviceUrl,
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => 'PromoAlu+',
                'url' => config('app.url') ?: url('/'),
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Tunisia',
            ],
        ];

        if (! empty($schemaGallery)) {
            $serviceNode['image'] = $schemaGallery;
        }

        if (! empty($schemaMaterials)) {
            $serviceNode['material'] = $schemaMaterials;
        }

        if (! empty($schemaProperties)) {
            $serviceNode['additionalProperty'] = $schemaProperties;
        }

        $servicesSchemaItems[] = [
            '@type' => 'ListItem',
            'position' => $position + 1,
            'url' => $serviceUrl,
            'item' => $serviceNode,
        ];
    }

    $servicesSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => __('messages.our_services'),
        'itemListElement' => $servicesSchemaItems,
    ];
@endphp

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-28 md:pt-32 pb-16 md:pb-20 relative">
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-4 border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
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
        $serviceColor = $service->getDisplayColor();
        $serviceIcon = $service->getDisplayIcon();
        $features = array_values(array_filter($service->getTranslatedFeatures(), static fn ($feature) => is_string($feature) && trim($feature) !== ''));
        $materials = array_values(array_filter($service->getTranslatedMaterials(), static fn ($material) => is_string($material) && trim($material) !== ''));
        $specs = is_array($service->specs) ? $service->specs : [];
        $descriptionHtml = trim((string) $service->getTranslatedDescription());
        $descriptionText = trim(strip_tags($descriptionHtml));
        $summaryText = trim($service->getTranslatedShortDescription());
        if ($summaryText === '') {
            $summaryText = $descriptionText;
        }
        $featurePreview = array_slice($features, 0, 3);
        $hasDetailsPanel = $descriptionHtml !== '' || count($features) > 0 || count($materials) > 0 || count($specs) > 0;
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
                                   class="rounded-2xl shadow-2xl w-full h-[240px] sm:h-[320px] md:h-[360px] lg:h-[400px] object-cover transition-all duration-500">
                        </div>
                    </div>
                </div>
                <div class="{{ $index % 2 == 1 ? 'order-1 md:order-2' : '' }}">
                    <!-- Service Icon -->
                    <div class="inline-flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-{{ $serviceColor }}-400 to-{{ $serviceColor }}-600 rounded-2xl flex items-center justify-center shadow-lg transform hover:rotate-6 transition-transform duration-300">
                            @if($service->svg_icon)
                                {!! $service->svg_icon !!}
                            @elseif($serviceIcon)
                                <i data-lucide="{{ $serviceIcon }}" class="w-8 h-8 md:w-10 md:h-10 text-white"></i>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            @endif
                        </div>
                        <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900">
                            {{ $service->getTranslatedTitle() }}
                        </h2>
                    </div>

                    <!-- Service Summary -->
                    @if($summaryText !== '')
                    <p class="text-base md:text-lg text-gray-600 mb-6 leading-relaxed">
                        {{ $summaryText }}
                    </p>
                    @endif

                    <!-- Feature Preview -->
                    @if(count($featurePreview) > 0)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($featurePreview as $feature)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-medium">
                            {{ $feature }}
                        </span>
                        @endforeach
                    </div>
                    @endif

                    <!-- Expandable Details -->
                    @if($hasDetailsPanel)
                    <details class="service-details mb-8 bg-white/80 border border-gray-200 rounded-2xl shadow-sm">
                        <summary class="service-details-summary px-5 py-4 cursor-pointer flex items-center justify-between gap-3 rounded-2xl transition-colors duration-200 hover:bg-gray-50/80">
                            <span class="details-label font-semibold text-gray-900 transition-colors duration-200">
                                {{ __('messages.view_details') }}
                            </span>
                            <span class="details-chevron flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </summary>

                        <div class="service-details-content px-5 pb-5 pt-2 space-y-6">
                            @if($descriptionHtml !== '')
                            <div class="prose prose-gray max-w-none text-gray-700">
                                {!! $descriptionHtml !!}
                            </div>
                            @endif

                            @if(count($features) > 0)
                            <div>
                                <h3 class="text-sm uppercase tracking-wide text-gray-500 font-semibold mb-3">{{ __('messages.key_features') }}</h3>
                                <ul class="space-y-3">
                                    @foreach($features as $feature)
                                    <li class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                                        </div>
                                        <span class="text-gray-700 text-base">{{ $feature }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if(count($materials) > 0)
                            <div>
                                <h3 class="text-sm uppercase tracking-wide text-gray-500 font-semibold mb-3">{{ __('messages.materials_used') }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($materials as $material)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-sm font-medium">{{ $material }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(count($specs) > 0)
                            <div>
                                <h3 class="text-sm uppercase tracking-wide text-gray-500 font-semibold mb-3">{{ __('messages.specifications') }}</h3>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($specs as $specKey => $specValue)
                                    @php
                                        $label = __('messages.' . $specKey);
                                        if ($label === 'messages.' . $specKey) {
                                            $label = ucfirst(str_replace('_', ' ', (string) $specKey));
                                        }

                                        $value = $specValue;
                                        if (is_array($value)) {
                                            $value = $value[app()->getLocale()] ?? $value['fr'] ?? '';
                                        }
                                    @endphp

                                    @if(is_string($value) && trim($value) !== '')
                                    <div class="rounded-xl border border-gray-200 p-3 bg-white">
                                        <dt class="text-xs uppercase tracking-wide text-gray-500 font-semibold">{{ $label }}</dt>
                                        <dd class="text-sm text-gray-800 mt-1">{{ $value }}</dd>
                                    </div>
                                    @endif
                                    @endforeach
                                </dl>
                            </div>
                            @endif
                        </div>
                    </details>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center justify-center group shadow-xl hover:shadow-2xl">
                            <i data-lucide="phone" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform"></i>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        {{ __('messages.request_quote') }}
                    </a>
                    <a href="{{ route('portfolio') }}" class="group bg-white/10 backdrop-blur-sm hover:bg-white text-white hover:text-blue-900 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center border-2 border-white/30 hover:border-white hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{ __('messages.view_our_work') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script type="application/ld+json">
        {!! json_encode($servicesSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <style>
        .service-details {
            overflow: hidden;
        }

        .service-details summary {
            list-style: none;
            -webkit-tap-highlight-color: transparent;
        }

        .service-details summary::-webkit-details-marker {
            display: none;
        }

        /* Chevron rotates + fills with brand colour once expanded */
        .service-details[open] .details-chevron {
            transform: rotate(180deg);
            background-color: #dbeafe;
            color: #2563eb;
        }

        .service-details[open] .details-label,
        .service-details summary:hover .details-label {
            color: #2563eb;
        }

        /* Content fades/slides in; JS animates the panel height for the smooth reveal */
        .service-details-content {
            opacity: 0;
            transform: translateY(-6px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .service-details[open] .service-details-content {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .service-details-content {
                opacity: 1;
                transform: none;
                transition: none;
            }

            .details-chevron,
            .details-label {
                transition: none;
            }
        }
    </style>

    <script>
        (() => {
            const panels = document.querySelectorAll('details.service-details');
            if (!panels.length) {
                return;
            }

            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            const duration = 320;

            panels.forEach((details) => {
                const summary = details.querySelector('summary');
                const content = details.querySelector('.service-details-content');

                if (!summary || !content) {
                    return;
                }

                let animation = null;
                let isClosing = false;
                let isExpanding = false;

                const onFinish = (open) => {
                    details.open = open;
                    animation = null;
                    isClosing = false;
                    isExpanding = false;
                    details.style.height = '';
                    details.style.overflow = '';
                };

                const expand = () => {
                    isExpanding = true;
                    const startHeight = `${details.offsetHeight}px`;
                    const endHeight = `${summary.offsetHeight + content.offsetHeight}px`;

                    if (animation) {
                        animation.cancel();
                    }

                    animation = details.animate(
                        { height: [startHeight, endHeight] },
                        { duration, easing: 'cubic-bezier(0.4, 0, 0.2, 1)' }
                    );
                    animation.onfinish = () => onFinish(true);
                    animation.oncancel = () => { isExpanding = false; };
                };

                const open = () => {
                    details.style.height = `${details.offsetHeight}px`;
                    details.open = true;
                    window.requestAnimationFrame(expand);
                };

                const shrink = () => {
                    isClosing = true;
                    const startHeight = `${details.offsetHeight}px`;
                    const endHeight = `${summary.offsetHeight}px`;

                    if (animation) {
                        animation.cancel();
                    }

                    animation = details.animate(
                        { height: [startHeight, endHeight] },
                        { duration, easing: 'cubic-bezier(0.4, 0, 0.2, 1)' }
                    );
                    animation.onfinish = () => onFinish(false);
                    animation.oncancel = () => { isClosing = false; };
                };

                summary.addEventListener('click', (event) => {
                    event.preventDefault();

                    if (reduceMotion.matches) {
                        details.open = !details.open;
                        return;
                    }

                    details.style.overflow = 'hidden';

                    if (isClosing || !details.open) {
                        open();
                    } else if (isExpanding || details.open) {
                        shrink();
                    }
                });
            });
        })();
    </script>
@endsection
