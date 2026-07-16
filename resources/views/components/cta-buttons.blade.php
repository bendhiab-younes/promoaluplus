@props([
    'primaryHref',
    'primaryLabel',
    'secondaryHref',
    'secondaryLabel',
    'secondaryIcon' => 'eye',
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row gap-4 justify-center']) }}>
    <a href="{{ $primaryHref }}" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center shadow-2xl hover:shadow-orange-500/40 hover:-translate-y-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        {{ $primaryLabel }}
    </a>
    <a href="{{ $secondaryHref }}" class="group bg-white/10 backdrop-blur-sm hover:bg-white text-white hover:text-blue-900 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 inline-flex items-center justify-center border-2 border-white/30 hover:border-white hover:-translate-y-1">
        @if($secondaryIcon === 'services')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 me-2 group-hover:scale-110 transition-transform flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
        @endif
        {{ $secondaryLabel }}
    </a>
</div>
