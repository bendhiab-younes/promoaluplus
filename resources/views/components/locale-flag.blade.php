@props(['code'])

@php
    // Unique suffix so repeated SVG clipPath ids never collide when the
    // component is rendered multiple times on the same page.
    $uid = \Illuminate\Support\Str::random(6);
@endphp

@switch($code)
    @case('ar')
        {{-- Tunisia --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 200" {{ $attributes->merge(['class' => 'inline-block shrink-0']) }} role="img" aria-label="Tunisia">
            <rect width="300" height="200" fill="#E70013"/>
            <circle cx="150" cy="100" r="60" fill="#fff"/>
            <circle cx="150" cy="100" r="40" fill="#E70013"/>
            <circle cx="170" cy="100" r="34" fill="#fff"/>
            <polygon fill="#E70013" points="178,78 183.05,93.04 198.92,93.2 186.18,102.66 190.93,117.8 178,108.6 165.07,117.8 169.82,102.66 157.08,93.2 172.95,93.04"/>
        </svg>
        @break

    @case('en')
        {{-- United Kingdom --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" {{ $attributes->merge(['class' => 'inline-block shrink-0']) }} role="img" aria-label="United Kingdom">
            <clipPath id="uk-{{ $uid }}"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath>
            <rect width="60" height="30" fill="#012169"/>
            <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
            <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#uk-{{ $uid }})" stroke="#C8102E" stroke-width="4"/>
            <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
            <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/>
        </svg>
        @break

    @default
        {{-- France --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3 2" {{ $attributes->merge(['class' => 'inline-block shrink-0']) }} role="img" aria-label="France">
            <rect width="3" height="2" fill="#fff"/>
            <rect width="1" height="2" fill="#002395"/>
            <rect x="2" width="1" height="2" fill="#ED2939"/>
        </svg>
@endswitch
