@extends('layouts.app')

@section('title', __('messages.nav_contact'))

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient pt-32 pb-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">{{ __('messages.contact_us') }}</h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                {{ __('messages.contact_intro') }}
            </p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <div class="text-center p-8 bg-gray-50 rounded-xl">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="phone" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('messages.phone') }}</h3>
                    <p class="text-gray-600 mb-2">+216 12 345 678</p>
                    <p class="text-sm text-gray-500">{{ __('messages.working_hours') }}</p>
                </div>
                
                <div class="text-center p-8 bg-gray-50 rounded-xl">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="mail" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Email</h3>
                    <p class="text-gray-600 mb-2">contact@aluminiumcraft.tn</p>
                    <p class="text-sm text-gray-500">{{ __('messages.response_time') }}</p>
                </div>
                
                <div class="text-center p-8 bg-gray-50 rounded-xl">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="map-pin" class="w-8 h-8 text-orange-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('messages.address') }}</h3>
                    <p class="text-gray-600 mb-2">Zone Industrielle,<br>Tunis, Tunisie</p>
                    <p class="text-sm text-gray-500">{{ __('messages.showroom_appointment') }}</p>
                </div>
            </div>

            <!-- Quote Form -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-gray-50 p-8 md:p-12 rounded-2xl">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2 text-center">{{ __('messages.request_free_quote') }}</h2>
                    <p class="text-gray-600 text-center mb-8">{{ __('messages.quote_form_intro') }}</p>

                    @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form id="quote-form" action="{{ route('quote.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">{{ __('messages.full_name') }} *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="{{ __('messages.your_name') }}">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="email@example.com">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">{{ __('messages.phone') }} *</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="+216 XX XXX XXX">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">{{ __('messages.country') }}</label>
                                <input type="text" name="country" value="{{ old('country') }}"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="{{ __('messages.country_residence') }}">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">{{ __('messages.city') }}</label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="{{ __('messages.project_city') }}">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">{{ __('messages.project_type') }} *</label>
                                <select name="project_type" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">{{ __('messages.select_type') }}</option>
                                    <option value="windows" {{ old('project_type') == 'windows' ? 'selected' : '' }}>{{ __('messages.windows') }}</option>
                                    <option value="doors" {{ old('project_type') == 'doors' ? 'selected' : '' }}>{{ __('messages.doors') }}</option>
                                    <option value="facades" {{ old('project_type') == 'facades' ? 'selected' : '' }}>{{ __('messages.facades') }}</option>
                                    <option value="veranda" {{ old('project_type') == 'veranda' ? 'selected' : '' }}>{{ __('messages.veranda') }}</option>
                                    <option value="other" {{ old('project_type') == 'other' ? 'selected' : '' }}>{{ __('messages.other') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">{{ __('messages.budget_range') }}</label>
                                <select name="budget_range"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">{{ __('messages.select_budget') }}</option>
                                    <option value="< 5000 TND" {{ old('budget_range') == '< 5000 TND' ? 'selected' : '' }}>< 5 000 TND</option>
                                    <option value="5000-15000 TND" {{ old('budget_range') == '5000-15000 TND' ? 'selected' : '' }}>5 000 - 15 000 TND</option>
                                    <option value="15000-30000 TND" {{ old('budget_range') == '15000-30000 TND' ? 'selected' : '' }}>15 000 - 30 000 TND</option>
                                    <option value="> 30000 TND" {{ old('budget_range') == '> 30000 TND' ? 'selected' : '' }}>> 30 000 TND</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">{{ __('messages.timeline') }}</label>
                                <select name="timeline"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">{{ __('messages.select_timeline') }}</option>
                                    <option value="urgent" {{ old('timeline') == 'urgent' ? 'selected' : '' }}>{{ __('messages.urgent') }}</option>
                                    <option value="1-3 months" {{ old('timeline') == '1-3 months' ? 'selected' : '' }}>1-3 {{ __('messages.months') }}</option>
                                    <option value="3-6 months" {{ old('timeline') == '3-6 months' ? 'selected' : '' }}>3-6 {{ __('messages.months') }}</option>
                                    <option value="6+ months" {{ old('timeline') == '6+ months' ? 'selected' : '' }}>6+ {{ __('messages.months') }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2">{{ __('messages.project_description') }} *</label>
                            <textarea name="description" rows="5" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="{{ __('messages.describe_project') }}">{{ old('description') }}</textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn-primary px-12 py-4 text-lg">
                                <i data-lucide="send" class="w-5 h-5 inline mr-2"></i>
                                {{ __('messages.send_request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-800 mb-12 text-center">{{ __('messages.faq_title') }}</h2>
            
            <div class="max-w-3xl mx-auto space-y-4">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-2">{{ __('messages.faq_q1') }}</h3>
                    <p class="text-gray-600">{{ __('messages.faq_a1') }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-2">{{ __('messages.faq_q2') }}</h3>
                    <p class="text-gray-600">{{ __('messages.faq_a2') }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-2">{{ __('messages.faq_q3') }}</h3>
                    <p class="text-gray-600">{{ __('messages.faq_a3') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
