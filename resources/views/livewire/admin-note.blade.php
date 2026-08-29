{{-- Uses Filament's own <x-filament::section>, so the callout inherits panel
     styling (light/dark, spacing, radius) instead of shipping classes that
     Filament's compiled CSS would not contain. --}}
<div class="fi-admin-note">
    <x-filament::section
        :heading="$heading"
        :description="$title"
        icon="heroicon-o-information-circle"
        icon-color="info"
        compact
    >
        <x-slot name="headerEnd">
            <div class="flex items-center" role="group" aria-label="{{ $languageLabel }}">
                @foreach ($locales as $code => $label)
                    <x-filament::button
                        :color="$locale === $code ? 'info' : 'gray'"
                        :outlined="$locale !== $code"
                        :aria-pressed="$locale === $code ? 'true' : 'false'"
                        size="xs"
                        grouped
                        wire:click="switchLocale('{{ $code }}')"
                        wire:loading.attr="disabled"
                    >
                        {{ $label }}
                    </x-filament::button>
                @endforeach
            </div>
        </x-slot>

        <div
            @if ($isRtl) dir="rtl" @endif
            @class([
                'space-y-2 text-sm text-gray-600 dark:text-gray-400',
                'text-right' => $isRtl,
            ])
        >
            @foreach ($paragraphs as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </x-filament::section>
</div>
