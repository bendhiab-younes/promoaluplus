@extends('layouts.app')

@section('title', __('messages.nav_contact'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-28 md:pt-32 pb-16 md:pb-20 relative">
        <div class="container mx-auto px-6 md:px-8 text-center relative z-10">
            <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-blue-200 rounded-full text-sm font-semibold mb-4 border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                {{ __('messages.get_in_touch') }}
            </span>
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 md:mb-6">{{ __('messages.contact_us') }}</h1>
            <p class="text-lg md:text-xl text-blue-100/90 max-w-2xl mx-auto leading-relaxed">
                {{ __('messages.contact_intro') }}
            </p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-12 md:py-20 bg-white">
        <div class="container mx-auto px-6 md:px-8">
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-8 mb-12 md:mb-16">
                <div class="scroll-fade stagger-1 group">
                    <div class="text-center p-6 md:p-8 bg-gradient-to-br from-white to-blue-50 rounded-2xl border border-gray-100 hover:border-blue-200 hover:shadow-xl transition-all duration-300 h-full {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="phone" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.phone') }}</h3>
                        <p class="text-gray-700 font-medium mb-1 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}" dir="ltr">+216 12 345 678</p>
                        <p class="text-sm text-gray-500 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.working_hours') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-2 group">
                    <div class="text-center p-6 md:p-8 bg-gradient-to-br from-white to-green-50 rounded-2xl border border-gray-100 hover:border-green-200 hover:shadow-xl transition-all duration-300 h-full {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="mail" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">Email</h3>
                        <p class="text-gray-700 font-medium mb-1 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}" dir="ltr">contact@aluminiumcraft.tn</p>
                        <p class="text-sm text-gray-500 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.response_time') }}</p>
                    </div>
                </div>
                
                <div class="scroll-fade stagger-3 group sm:col-span-2 md:col-span-1">
                    <div class="text-center p-6 md:p-8 bg-gradient-to-br from-white to-orange-50 rounded-2xl border border-gray-100 hover:border-orange-200 hover:shadow-xl transition-all duration-300 h-full {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="map-pin" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.address') }}</h3>
                        <p class="text-gray-700 font-medium mb-1 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">
                            {{ __('messages.full_address') }}
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11H3v10h6V11z"/><path d="M9 11V7a3 3 0 1 1 6 0v4"/><path d="M15 11h6v10h-6V11z"/></svg>
                            {{ __('messages.free_quote') }}
                        </span>
                        <h2 class="font-display text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ __('messages.request_free_quote') }}</h2>
                        <p class="text-gray-600">{{ __('messages.quote_form_intro') }}</p>
                    </div>

                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl mb-6 flex items-start {{ app()->getLocale() === 'ar' ? 'flex-row-reverse text-right font-arabic' : '' }}">
                        <i data-lucide="check-circle" class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }} mt-0.5 text-green-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl mb-6 {{ app()->getLocale() === 'ar' ? 'text-right font-arabic' : '' }}">
                        <div class="flex items-start mb-2 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                            <i data-lucide="alert-circle" class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }} mt-0.5 text-red-600"></i>
                            <span class="font-semibold">{{ __('messages.please_correct_errors') }}</span>
                        </div>
                        <ul class="list-disc list-inside {{ app()->getLocale() === 'ar' ? 'mr-8' : 'ml-8' }} text-sm">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form id="quote-form" action="{{ route('quote.store') }}" method="POST" class="space-y-5" data-quote-form>
                        @csrf
                        <div class="grid md:grid-cols-2 gap-4 md:gap-5">
                            <div>
                                <label for="quote-name" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.full_name') }} <span class="text-red-500">*</span></label>
                                <input id="quote-name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                    placeholder="{{ __('messages.your_name') }}">
                                @error('name')
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
                                <label for="quote-country" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.country') }}</label>
                                <input id="quote-country" type="text" name="country" value="{{ old('country') }}" maxlength="100" autocomplete="country-name"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                    placeholder="{{ __('messages.country_residence') }}">
                                @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 md:gap-5">
                            <div>
                                <label for="quote-city" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.city') }}</label>
                                <input id="quote-city" type="text" name="city" value="{{ old('city') }}" maxlength="100" autocomplete="address-level2"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white"
                                    placeholder="{{ __('messages.project_city') }}">
                                @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="quote-project-type" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.project_type') }} <span class="text-red-500">*</span></label>
                                <select id="quote-project-type" name="project_type" required
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white">
                                    <option value="">{{ __('messages.select_type') }}</option>
                                    <option value="windows" {{ old('project_type') == 'windows' ? 'selected' : '' }}>{{ __('messages.windows') }}</option>
                                    <option value="doors" {{ old('project_type') == 'doors' ? 'selected' : '' }}>{{ __('messages.doors') }}</option>
                                    <option value="curtains" {{ old('project_type') == 'curtains' ? 'selected' : '' }}>{{ __('messages.curtains') }}</option>
                                    <option value="railings" {{ old('project_type') == 'railings' ? 'selected' : '' }}>{{ __('messages.railings') }}</option>
                                    <option value="pergola" {{ old('project_type') == 'pergola' ? 'selected' : '' }}>{{ __('messages.pergola') }}</option>
                                    <option value="kitchen" {{ old('project_type') == 'kitchen' ? 'selected' : '' }}>{{ __('messages.kitchen') }}</option>
                                    <option value="shelter" {{ old('project_type') == 'shelter' ? 'selected' : '' }}>{{ __('messages.shelter') }}</option>
                                    <option value="shutters" {{ old('project_type') == 'shutters' ? 'selected' : '' }}>{{ __('messages.shutters') }}</option>
                                    <option value="other" {{ old('project_type') == 'other' ? 'selected' : '' }}>{{ __('messages.other') }}</option>
                                </select>
                                @error('project_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 md:gap-5">
                            <div>
                                <label for="quote-budget-range" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.budget_range') }}</label>
                                <select id="quote-budget-range" name="budget_range"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white">
                                    <option value="">{{ __('messages.select_budget') }}</option>
                                    <option value="< 5000 TND" {{ old('budget_range') == '< 5000 TND' ? 'selected' : '' }}>< 5 000 TND</option>
                                    <option value="5000-15000 TND" {{ old('budget_range') == '5000-15000 TND' ? 'selected' : '' }}>5 000 - 15 000 TND</option>
                                    <option value="15000-30000 TND" {{ old('budget_range') == '15000-30000 TND' ? 'selected' : '' }}>15 000 - 30 000 TND</option>
                                    <option value="> 30000 TND" {{ old('budget_range') == '> 30000 TND' ? 'selected' : '' }}>> 30 000 TND</option>
                                </select>
                                @error('budget_range')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="quote-timeline" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.timeline') }}</label>
                                <select id="quote-timeline" name="timeline"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white">
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

                        <div>
                            <label for="quote-description" class="block text-gray-700 font-semibold mb-2 text-sm">{{ __('messages.project_description') }} <span class="text-red-500">*</span></label>
                            <textarea id="quote-description" name="description" rows="4" required maxlength="2000"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-white resize-none"
                                placeholder="{{ __('messages.describe_project') }}">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="text-center pt-4">
                            <button type="submit" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-10 md:px-12 py-4 rounded-xl text-lg font-semibold transition-all duration-300 shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:-translate-y-1 inline-flex items-center {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}" data-quote-submit>
                                <i data-lucide="send" class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2 -scale-x-100' : 'mr-2' }} group-hover:translate-x-1 transition-transform"></i>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    FAQ
                </span>
                <h2 class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">{{ __('messages.frequently_asked_questions') }}</h2>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-4">
                @foreach($faqs as $index => $faq)
                <div class="scroll-fade stagger-{{ ($index % 3) + 1 }} bg-white p-5 md:p-6 rounded-2xl border border-gray-100 hover:border-purple-200 hover:shadow-lg transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-start {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                        <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center {{ app()->getLocale() === 'ar' ? 'ml-3 mr-0' : 'mr-3' }} flex-shrink-0 text-purple-600 text-sm font-bold">{{ $index + 1 }}</span>
                        <span class="flex-1 {{ app()->getLocale() === 'ar' ? 'font-arabic text-right' : '' }}">{{ $faq->getTranslatedQuestion() }}</span>
                    </h3>
                    <p class="text-gray-600 {{ app()->getLocale() === 'ar' ? 'mr-11 pr-11 font-arabic text-right' : 'ml-11 pl-11' }}">{{ $faq->getTranslatedAnswer() }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
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
