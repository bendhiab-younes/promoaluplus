@extends('layouts.app')

@php
    use App\Models\SiteSetting;
    use App\Support\CanonicalServiceCatalog;

    $contactPhone = SiteSetting::get('contact_phone', '+21626192898');
    $contactWhatsApp = SiteSetting::get('contact_whatsapp', $contactPhone);
    $formatPhone = function($phone) {
        $clean = preg_replace('/[^0-9+]/', '', $phone);
        if (preg_match('/(\+216)(\d{2})(\d{3})(\d{3})/', $clean, $matches)) {
            return $matches[1] . ' ' . $matches[2] . ' ' . $matches[3] . ' ' . $matches[4];
        }
        return $phone;
    };
    $contactEmail = SiteSetting::get('contact_email', 'promoaluplus@gmail.com');
    $contactAddress = SiteSetting::get('contact_address', __('messages.full_address'));
    $projectTypeOptions = CanonicalServiceCatalog::quoteOptions();
@endphp

@section('title', __('messages.seo_title_contact'))
@section('meta_description', __('messages.seo_desc_contact'))
@section('og_title', __('messages.seo_title_contact'))
@section('og_description', __('messages.seo_desc_contact'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-28 md:pt-32 pb-16 md:pb-20 relative">
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-4 border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                {{ __('messages.nav_contact') }}
            </span>
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 md:mb-6">{{ __('messages.contact_us') }}</h1>
            <p class="text-lg md:text-xl text-blue-100/90 max-w-2xl mx-auto leading-relaxed">
                {{ __('messages.contact_intro') }}
            </p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-12 md:py-20 lg:py-24 bg-white">
        <div class="container mx-auto px-6 md:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8 mb-12 md:mb-16">
                <div class="scroll-fade stagger-1 group">
                    <div class="text-center p-6 md:p-8 bg-gradient-to-br from-white to-blue-50 rounded-2xl border border-gray-100 hover:border-blue-200 hover:shadow-xl transition-all duration-300 h-full {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="phone" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.phone') }}</h3>
                        <p class="text-gray-700 font-medium mb-1 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}" dir="ltr">{{ $formatPhone($contactPhone) }}</p>
                        <p class="text-sm text-gray-500 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.working_hours') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-2 group">
                    <div class="text-center p-6 md:p-8 bg-gradient-to-br from-white to-green-50 rounded-2xl border border-gray-100 hover:border-green-200 hover:shadow-xl transition-all duration-300 h-full {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="mail" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">Email</h3>
                        <p class="text-gray-700 font-medium mb-1 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}" dir="ltr">{{ $contactEmail }}</p>
                        <p class="text-sm text-gray-500 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.response_time') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-3 group">
                    <div class="text-center p-6 md:p-8 bg-gradient-to-br from-white to-emerald-50 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-xl transition-all duration-300 h-full {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 md:w-8 md:h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">WhatsApp</h3>
                        <p class="text-gray-700 font-medium mb-1 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}" dir="ltr">{{ $formatPhone($contactWhatsApp) }}</p>
                        <p class="text-sm text-gray-500 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.response_time') }}</p>
                    </div>
                </div>

                <div class="scroll-fade stagger-4 group sm:col-span-2 lg:col-span-1">
                    <div class="text-center p-6 md:p-8 bg-gradient-to-br from-white to-orange-50 rounded-2xl border border-gray-100 hover:border-orange-200 hover:shadow-xl transition-all duration-300 h-full {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="map-pin" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.address') }}</h3>
                        <p class="text-gray-700 font-medium mb-1 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">
                            {{ $contactAddress }}
                        </p>
                        <p class="text-sm text-gray-500 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.showroom_appointment') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quote Form -->
            <div class="max-w-4xl mx-auto scroll-fade">
                <div class="bg-gradient-to-br from-gray-50 to-blue-50/30 p-6 md:p-10 lg:p-12 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="text-center mb-8">
                        <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11H3v10h6V11z"/><path d="M9 11V7a3 3 0 1 1 6 0v4"/><path d="M15 11h6v10h-6V11z"/></svg>
                            {{ __('messages.free_quote') }}
                        </span>
                        <h2 class="font-display text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ __('messages.request_free_quote') }}</h2>
                    </div>

                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl mb-6 flex items-start {{ app()->getLocale() === 'ar' ? 'text-right font-arabic' : '' }}">
                        <i data-lucide="check-circle" class="w-5 h-5 me-3 mt-0.5 text-green-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl mb-6 {{ app()->getLocale() === 'ar' ? 'text-right font-arabic' : '' }}">
                        <div class="flex items-start mb-2">
                            <i data-lucide="alert-circle" class="w-5 h-5 me-3 mt-0.5 text-red-600"></i>
                            <span class="font-semibold">{{ __('messages.please_correct_errors') }}</span>
                        </div>
                        <ul class="list-disc list-inside {{ app()->getLocale() === 'ar' ? 'mr-8' : 'ml-8' }} text-sm">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form id="quote-form" action="{{ route('quote.store') }}" method="POST" class="space-y-6 {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}" data-quote-form>
                        @csrf
                        <div class="rounded-2xl border border-blue-100/80 bg-white/80 p-4 md:p-6">
                            <div class="grid md:grid-cols-2 gap-4 md:gap-5">
                                <div>
                                    <label for="quote-first-name" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.first_name') }} <span class="text-red-500">*</span></label>
                                    <input id="quote-first-name" type="text" name="first_name" value="{{ old('first_name') }}" required maxlength="100" autocomplete="given-name"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                        placeholder="{{ __('messages.your_first_name') }}">
                                    @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="quote-name" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.last_name') }} <span class="text-red-500">*</span></label>
                                    <input id="quote-name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                        placeholder="{{ __('messages.your_last_name') }}">
                                    @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-200/80 bg-white/80 p-4 md:p-6">
                            <div class="grid md:grid-cols-2 gap-4 md:gap-5">
                                <div>
                                    <label for="quote-phone" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.phone') }} <span class="text-red-500">*</span></label>
                                    <input id="quote-phone" type="tel" name="phone" value="{{ old('phone') }}" required maxlength="50" autocomplete="tel"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                        placeholder="+216 XX XXX XXX">
                                    @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="quote-email" class="block text-gray-700 font-semibold mb-2 text-sm">Email <span class="text-red-500">*</span></label>
                                    <input id="quote-email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                        placeholder="email@example.com">
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4 md:gap-5 mt-4">
                                <div>
                                    <label for="quote-country" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.country') }}</label>
                                    <input id="quote-country" type="text" name="country" value="{{ old('country') }}" maxlength="100" autocomplete="country-name"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                        placeholder="{{ __('messages.country_residence') }}">
                                    @error('country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="quote-city" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.city') }}</label>
                                    <input id="quote-city" type="text" name="city" value="{{ old('city') }}" maxlength="100" autocomplete="address-level2"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                        placeholder="{{ __('messages.project_city') }}">
                                    @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-orange-100/80 bg-white/90 p-4 md:p-6">
                            <div>
                                <span class="block text-gray-700 font-semibold mb-1 text-sm">{{ __('messages.project_type') }} <span class="text-red-500">*</span></span>
                                <p class="text-xs text-gray-500 mb-3">{{ __('messages.select_type') }}</p>
                                @php $oldProjectTypes = old('project_types', []); @endphp
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" role="group" aria-label="{{ __('messages.project_type') }}">
                                    @foreach($projectTypeOptions as $projectTypeValue => $projectTypeLabel)
                                        <label class="relative flex items-center justify-center text-center px-3 py-3 rounded-xl border-2 border-gray-200 bg-white cursor-pointer select-none transition-all text-sm font-medium text-gray-700 hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 has-[:checked]:ring-2 has-[:checked]:ring-blue-500/20">
                                            <input type="checkbox" name="project_types[]" value="{{ $projectTypeValue }}" class="peer sr-only"
                                                {{ in_array($projectTypeValue, $oldProjectTypes) ? 'checked' : '' }}>
                                            <span class="hidden peer-checked:flex absolute -top-2 -right-2 w-5 h-5 items-center justify-center rounded-full bg-blue-600 text-white shadow ring-2 ring-white">
                                                <i data-lucide="check" class="w-3 h-3" stroke-width="3"></i>
                                            </span>
                                            {{ $projectTypeLabel }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('project_types')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('project_types.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mt-4 md:mt-5">
                                <label for="quote-timeline" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.timeline') }}</label>
                                <select id="quote-timeline" name="timeline"
                                    class="w-full md:w-1/2 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white">
                                    <option value="">{{ __('messages.select_timeline') }}</option>
                                    <option value="urgent" {{ old('timeline') == 'urgent' ? 'selected' : '' }}>{{ __('messages.urgent') }}</option>
                                    <option value="1-3 months" {{ old('timeline') == '1-3 months' ? 'selected' : '' }}>1-3 {{ __('messages.months') }}</option>
                                    <option value="3-6 months" {{ old('timeline') == '3-6 months' ? 'selected' : '' }}>3-6 {{ __('messages.months') }}</option>
                                    <option value="6+ months" {{ old('timeline') == '6+ months' ? 'selected' : '' }}>6+ {{ __('messages.months') }}</option>
                                </select>
                                @error('timeline')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-200/80 bg-white/80 p-4 md:p-6">
                            <label for="quote-description" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.project_description') }} <span class="text-red-500">*</span></label>
                            <textarea id="quote-description" name="description" rows="5" required maxlength="2000"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white resize-none"
                                placeholder="{{ __('messages.describe_project') }}">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="text-center pt-4">
                            <button type="submit" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-10 md:px-12 py-4 rounded-xl text-lg font-semibold transition-all duration-300 shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:-translate-y-1 inline-flex items-center {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}" data-quote-submit>
                                <i data-lucide="send" class="w-5 h-5 me-2 rtl:-scale-x-100 group-hover:translate-x-1 transition-transform"></i>
                                {{ __('messages.send_request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-12 md:py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10 md:mb-12 scroll-fade">
                <span class="inline-block px-4 py-1.5 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block me-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    FAQ
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.frequently_asked_questions') }}</h2>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-4">
                @foreach($faqs as $index => $faq)
                <div class="scroll-fade stagger-{{ ($index % 3) + 1 }} bg-white p-5 md:p-6 rounded-2xl border border-gray-100 hover:border-purple-200 hover:shadow-lg transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-start">
                        <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center me-3 flex-shrink-0 text-purple-600 text-sm font-bold">{{ $index + 1 }}</span>
                        <span class="flex-1 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ $faq->getTranslatedQuestion() }}</span>
                    </h3>
                    <p class="text-gray-600 ms-11 ps-11 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ $faq->getTranslatedAnswer() }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @php
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => trim(strip_tags((string) $faq->getTranslatedQuestion())),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim(strip_tags((string) $faq->getTranslatedAnswer())),
                ],
            ])->values()->all(),
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const quoteForm = document.querySelector('[data-quote-form]');

    if (!quoteForm) {
        return;
    }

    const submitButton = quoteForm.querySelector('[data-quote-submit]');

    if (!submitButton) {
        return;
    }

    quoteForm.addEventListener('submit', function (event) {
        if (quoteForm.dataset.submitting === '1') {
            event.preventDefault();
            return;
        }

        quoteForm.dataset.submitting = '1';
        submitButton.disabled = true;
        submitButton.setAttribute('aria-disabled', 'true');
        submitButton.classList.add('opacity-70', 'cursor-not-allowed');
    });
});
</script>
@endpush
