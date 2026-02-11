<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="FormRegistration">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="flex flex-col lg:flex-row">
            <!-- Left Panel: Registration Information -->
            <div class="lg:w-1/3 bg-gray-50 p-6 md:p-8">
                <div class="sticky top-5">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">تفاصيل الدعوة</h2>

                    <!-- Event Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $event->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($event->description, 150) }}</p>

                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $event->start_date->format('M d, Y') }} -
                                {{ $event->end_date->format('M d, Y') }}</span>
                        </div>

                        @if ($event->location)
                            <div class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-500"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{!! $event->location !!}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Registration Type -->
                    <div class="mb-6 bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                        <div class="flex items-center mb-3">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $registration->name }}</h4>
                                @if ($registration->category)
                                    <span class="text-xs text-primary-600">{{-- $registration->category->name --}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="text-sm text-gray-600">
                            <div class="flex justify-between mb-1">
                                <span>يفتح التسجيل:</span>
                                <span class="font-medium">{{ $registration->start_date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>يغلق التسجيل:</span>
                                <span class="font-medium">{{ $registration->end_date->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- google map iframe -->
                    @if ($event->location_google_place_id != '')
                        <div class="mb-6 bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                            <div class="items-center mb-3">
                                <div class="text-sm font-medium text-gray-800 mb-2">مكان الحفل</div>
                                <div class="cols-4">
                                    {!! $event->location_google_place_id !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Countdown Timer -->
                    <div class="countdown-container">
                        <div class="countdown-timer bg-white border border-gray-200 rounded-lg p-4 shadow-sm"
                            data-end="{{ $registration->end_date->format('Y-m-d\TH:i:s') }}">
                            <div class="text-sm font-medium text-gray-800 mb-2">ينتهي التسجيل بعد </div>
                            <div class="grid grid-cols-4 gap-2 text-center">
                                <div class="bg-primary-50 rounded-md p-2">
                                    <div class="text-2xl font-bold text-primary-600 days">00</div>
                                    <div class="text-xs text-primary-700">Days</div>
                                </div>
                                <div class="bg-primary-50 rounded-md p-2">
                                    <div class="text-2xl font-bold text-primary-600 hours">00</div>
                                    <div class="text-xs text-primary-700">Hours</div>
                                </div>
                                <div class="bg-primary-50 rounded-md p-2">
                                    <div class="text-2xl font-bold text-primary-600 minutes">00</div>
                                    <div class="text-xs text-primary-700">Minutes</div>
                                </div>
                                <div class="bg-primary-50 rounded-md p-2">
                                    <div class="text-2xl font-bold text-primary-600 seconds">00</div>
                                    <div class="text-xs text-primary-700">Seconds</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Registration Form -->
            <div class="lg:w-2/3 p-6 md:p-8 border-t lg:border-t-0 lg:border-l border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">بيانات التسجيل</h2>
                <form method="POST" id="registration-form"
                    action="{{ route('postEventRegistration', ['event_id' => $event->id, 'registration_id' => $registration->id]) }}"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Personal Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">المعلومات الشخصية</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name"
                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">الاسم
                                    الأول</label>
                                <input placeholder="أدخل الاسم الأول" type="text"
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('first_name') border-red-500 @enderror"
                                    id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <p class="text-red-500 text-sm mt-1" dir="rtl">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name"
                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">الاسم
                                    الأخير</label>
                                <input placeholder="أدخل الاسم الأخير" type="text"
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('last_name') border-red-500 @enderror"
                                    id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <p class="text-red-500 text-sm mt-1" dir="rtl">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">البريد
                                    الالكتروني</label>
                                <input placeholder="أدخل البريد الإلكتروني" type="email"
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-500 @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1" dir="rtl">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">رقم
                                    الجوال</label>
                                <input placeholder="أدخل رقم الجوال" type="tel"
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('phone') border-red-500 @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1" dir="rtl">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Form Fields -->
                    @if ($registration->dynamicFormFields && $registration->dynamicFormFields->count() > 0)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات إضافية</h3>
                            <div class="space-y-6">
                                @php
                                    $hasConference = $registration->dynamicFormFields
                                        ->where('type', 'conference')
                                        ->first();
                                    $hasProfession = $registration->dynamicFormFields
                                        ->where('type', 'profession')
                                        ->first();
                                @endphp

                                @foreach ($registration->dynamicFormFields as $field)
                                    <div>
                                        <label for="field_{{ $field->id }}"
                                            class="block text-sm font-medium text-gray-700 {{ $field->is_required ? 'after:content-[\'*\'] after:ml-0.5 after:text-red-500' : '' }}">{{ $field->label }}</label>

                                        @if ($field->type == 'text')
                                            <input type="text"
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                value="{{ old('fields.' . $field->id) }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'email')
                                            <input type="email"
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                value="{{ old('fields.' . $field->id) }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'tel')
                                            <input type="tel" placeholder="أدخل رقم الهاتف"
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                value="{{ old('fields.' . $field->id) }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'number')
                                            <input type="number" placeholder="أدخل رقم"
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                value="{{ old('fields.' . $field->id) }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'textarea')
                                            <textarea placeholder="أدخل النص"
                                                class="mt-2 block w-full px-4 py-3 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" rows="3"
                                                {{ $field->is_required ? 'required' : '' }}>{{ old('fields.' . $field->id) }}</textarea>
                                        @elseif($field->type == 'select')
                                            <select
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                {{ $field->is_required ? 'required' : '' }}>
                                                <option value="">-- اختر خيار --</option>
                                                @if (is_array($field->options))
                                                    @foreach ($field->options as $option)
                                                        <option value="{{ trim($option) }}"
                                                            {{ old('fields.' . $field->id) == trim($option) ? 'selected' : '' }}>
                                                            {{ trim($option) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->type == 'user_types')
                                            <select
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                {{ $field->is_required ? 'required' : '' }}>
                                                <option value="">-- اختر نوع المستخدم --</option>
                                                @foreach ($event->userTypes as $userType)
                                                    <option value="{{ $userType->name }}"
                                                        {{ old('fields.' . $field->id) == $userType->name ? 'selected' : '' }}>
                                                        {{ $userType->name }}</option>
                                                @endforeach
                                                <option value="Delegate"
                                                    {{ old('fields.' . $field->id) == 'Delegate' || !old('fields.' . $field->id) ? 'selected' : '' }}>
                                                    Delegate (Default)</option>
                                            </select>
                                        @elseif($field->type == 'country')
                                            <select
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                {{ $field->is_required ? 'required' : '' }}>
                                                <option value="">-- اختر الدولة --</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ old('fields.' . $field->id) == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }} - {{ $country->country_code }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field->type == 'city')
                                            <input type="text" placeholder="أدخل المدينة"
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                value="{{ old('fields.' . $field->id) }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'conference')
                                            <select
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror conference-select"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                data-field-id="{{ $field->id }}"
                                                data-category-id="{{ $registration->category_id }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                                <option value="">-- اختر المؤتمر --</option>
                                                @if ($registration->category && $registration->category->conferences)
                                                    @foreach ($registration->category->conferences as $conference)
                                                        @php
                                                            $price = $conference->getPriceForCategory(
                                                                $registration->category_id,
                                                            );
                                                            $currencyCode = $event->currency
                                                                ? $event->currency->code
                                                                : 'SAR';
                                                        @endphp
                                                        <option value="{{ $conference->id }}"
                                                            data-price="{{ $price }}"
                                                            data-conference-name="{{ $conference->name }}"
                                                            {{ old('fields.' . $field->id) == $conference->id ? 'selected' : '' }}>
                                                            {{ $conference->name }} - {{ number_format($price, 2) }}
                                                            {{ $currencyCode }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->type == 'profession')
                                            <select
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror profession-select"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                data-field-id="{{ $field->id }}"
                                                {{ !$hasConference ? '' : 'disabled' }}>
                                                <option value="">-- اختر المهنة --</option>
                                                @if (!$hasConference && $registration->category && $registration->category->conferences)
                                                    @foreach ($registration->category->conferences as $conference)
                                                        @foreach ($conference->professions as $profession)
                                                            <option value="{{ $profession->id }}"
                                                                {{ old('fields.' . $field->id) == $profession->id ? 'selected' : '' }}>
                                                                {{ $profession->name }}</option>
                                                        @endforeach
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->type == 'checkbox')
                                            <div class="mt-1">
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input
                                                            class="h-4 w-4 text-primary-600 border border-gray-300 rounded focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                            type="checkbox" id="field_{{ $field->id }}"
                                                            name="fields[{{ $field->id }}]" value="1"
                                                            {{ old('fields.' . $field->id) ? 'checked' : '' }}
                                                            {{ $field->is_required ? 'required' : '' }}>
                                                    </div>
                                                    <div class="mr-3 text-sm">
                                                        <label class="font-medium text-gray-700"
                                                            for="field_{{ $field->id }}">{{ $field->description }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($field->type == 'radio')
                                            <div class="mt-1 space-y-2">
                                                @if (is_array($field->options))
                                                    @foreach ($field->options as $option)
                                                        <div class="flex items-center">
                                                            <input
                                                                class="h-4 w-4 text-primary-600 border border-gray-300 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                                type="radio" name="fields[{{ $field->id }}]"
                                                                id="field_{{ $field->id }}_{{ $loop->index }}"
                                                                value="{{ trim($option) }}"
                                                                {{ old('fields.' . $field->id) == trim($option) ? 'checked' : '' }}
                                                                {{ $field->is_required ? 'required' : '' }}>
                                                            <label class="mr-3 block text-sm font-medium text-gray-700"
                                                                for="field_{{ $field->id }}_{{ $loop->index }}">{{ trim($option) }}</label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($field->type == 'date')
                                            <input type="date"
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                value="{{ old('fields.' . $field->id) }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'file')
                                            <input type="file"
                                                class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('fields.' . $field->id) border-red-500 @enderror"
                                                id="field_{{ $field->id }}" name="fields[{{ $field->id }}]"
                                                {{ $field->is_required ? 'required' : '' }}>
                                            @if ($field->description)
                                                <p class="mt-1 text-sm text-gray-500">{{ $field->description }}</p>
                                            @endif
                                        @endif

                                        @error('fields.' . $field->id)
                                            <p class="text-red-500 text-sm mt-1" dir="rtl">{{ $message }}</p>
                                        @enderror

                                        @if ($field->description && $field->type != 'checkbox' && $field->type != 'file')
                                            <p class="mt-1 text-sm text-gray-500">{{ $field->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Payment Summary Section -->
                    <div id="payment-summary" class="mt-6 hidden bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">تفاصيل الدفع</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">المؤتمر:</span>
                                <span id="summary-conference" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">المهنة:</span>
                                <span id="summary-profession" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="border-t border-gray-300 pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">السعر النهائي:</span>
                                    <span id="summary-total-price"
                                        class="text-xl font-bold text-primary-600">0.00</span>
                                </div>
                            </div>
                            <!-- Payment method selection - shown when price > 0 -->
                            <div id="payment-method-section" class="mt-4 pt-4 border-t border-gray-200 hidden">
                                <p class="text-sm font-medium text-gray-700 mb-3">اختر طريقة الدفع</p>
                                <input type="hidden" name="payment_brand" id="selected-payment-brand" value="">
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="payment-method-card flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-primary-400 hover:bg-primary-50"
                                        data-brand="MADA" title="مدى">
                                        <img src="https://www.mada.com.sa/sites/mada/files/inline-images/logo.svg" alt="مدى" class="h-8 w-auto object-contain mb-2" loading="lazy">
                                        <span class="text-sm font-medium text-gray-700">مدى</span>
                                    </div>
                                    <div class="payment-method-card flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-primary-400 hover:bg-primary-50"
                                        data-brand="VISA" title="فيزا">
                                        <img src="https://www.freepnglogos.com/uploads/visa-card-logo-9.png" alt="فيزا" class="h-8 w-auto object-contain mb-2" loading="lazy">
                                        <span class="text-sm font-medium text-gray-700">فيزا</span>
                                    </div>
                                    <div class="payment-method-card flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-primary-400 hover:bg-primary-50"
                                        data-brand="MASTER" title="ماستركارد">
                                        <img src="https://mea.mastercard.com/content/dam/public/mastercardcom/en-region-mea/Images/consumers/icons/mc-logo-52.svg" alt="ماستركارد" class="h-8 w-auto object-contain mb-2" loading="lazy">
                                        <span class="text-sm font-medium text-gray-700">ماستركارد</span>
                                    </div>
                                </div>
                                <p id="payment-method-error" class="text-red-500 text-sm mt-2 hidden">يرجى اختيار طريقة الدفع</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" id="submit-button"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            إكمال التسجيل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const currencyCode = '{{ $event->currency ? $event->currency->code : 'SAR' }}';
        let paymentWidgetLoaded = false;
        let currentCheckoutId = null;

        function updatePaymentSummary() {
            const conferenceSelect = $('.conference-select');
            const professionSelect = $('.profession-select');
            const conferenceId = conferenceSelect.val();
            const professionId = professionSelect.val();
            const paymentSummary = $('#payment-summary');
            const submitButton = $('#submit-button');

            if (conferenceId) {
                const selectedOption = conferenceSelect.find('option:selected');
                const price = parseFloat(selectedOption.data('price')) || 0;
                const conferenceName = selectedOption.data('conference-name') || selectedOption.text().split(
                    ' - ')[0];
                const professionName = professionSelect.find('option:selected').text();

                $('#summary-conference').text(conferenceName);
                $('#summary-profession').text(professionName !== '-- اختر المهنة --' ? professionName : '-');
                $('#summary-total-price').text(price.toFixed(2) + ' ' + currencyCode);

                paymentSummary.removeClass('hidden');

                if (price > 0) {
                    submitButton.text('الدفع الآن');
                    $('#payment-method-section').removeClass('hidden');
                } else {
                    submitButton.text('إكمال التسجيل');
                    $('#payment-method-section').addClass('hidden');
                    $('#selected-payment-brand').val('');
                    $('.payment-method-card').removeClass('selected border-primary-500 bg-primary-100 ring-2 ring-primary-200').addClass('border-gray-200');
                    $('#payment-method-error').addClass('hidden');
                }
            } else {
                paymentSummary.addClass('hidden');
                $('#payment-method-section').addClass('hidden');
                submitButton.text('إكمال التسجيل');
            }
        }

        $('.payment-method-card').on('click', function() {
            const card = $(this);
            const brand = card.data('brand');
$('.payment-method-card').removeClass('selected border-primary-500 bg-primary-100 ring-2 ring-primary-200').addClass('border-gray-200');
                    card.removeClass('border-gray-200').addClass('selected border-primary-500 bg-primary-100 ring-2 ring-primary-200');
            $('#selected-payment-brand').val(brand);
            $('#payment-method-error').addClass('hidden');
        });

        $('.conference-select').on('change', function() {
            const conferenceId = $(this).val();
            const professionSelect = $('.profession-select');

            if (conferenceId) {
                professionSelect.prop('disabled', false);

                $.ajax({
                    url: '/e/api/conferences/' + conferenceId + '/professions',
                    type: 'GET',
                    success: function(response) {
                        if (response.professions && response.professions.length > 0) {
                            professionSelect.html(
                                '<option value="">-- اختر المهنة --</option>');
                            $.each(response.professions, function(index, profession) {
                                professionSelect.append('<option value="' +
                                    profession.id + '">' + profession.name +
                                    '</option>');
                            });
                        } else {
                            professionSelect.html(
                                '<option value="">-- لا توجد مهن متاحة --</option>');
                        }
                        updatePaymentSummary();
                    },
                    error: function(xhr) {
                        console.error('Error loading professions:', xhr);
                        professionSelect.html(
                            '<option value="">-- خطأ في تحميل المهن --</option>');
                        updatePaymentSummary();
                    }
                });
            } else {
                professionSelect.prop('disabled', true).html(
                    '<option value="">-- اختر المهنة --</option>');
                updatePaymentSummary();
            }
        });

        $('.profession-select').on('change', function() {
            updatePaymentSummary();
        });

        updatePaymentSummary();

        $('#close-payment-modal').on('click', function() {
            $('#hyperpay-payment-modal').addClass('hidden').css('display', 'none');
            const brand = $('#selected-payment-brand').val() || 'MADA';
            $('#hyperpay-widget-container').html(
                '<form id="hyperpay-payment-form" action="#" class="paymentWidgets" data-brands="' + brand + '"></form>'
            );
            paymentWidgetLoaded = false;
        });

        $('#hyperpay-payment-modal').on('click', function(e) {
            if ($(e.target).is('#hyperpay-payment-modal')) {
                $('#hyperpay-payment-modal').addClass('hidden').css('display', 'none');
            }
        });

        $('#registration-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitButton = $('#submit-button');
            const originalText = submitButton.text();
            const formData = new FormData(this);

            submitButton.prop('disabled', true).text('جاري المعالجة...');

            $('.error-message').remove();
            $('.border-red-500').removeClass('border-red-500');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Registration Response:', response);

                    if (response.status === 'success') {
                        if (response.requires_payment && response.checkout_id) {
                            const selectedBrand = $('#selected-payment-brand').val();
                            if (!selectedBrand) {
                                submitButton.prop('disabled', false).text(originalText);
                                $('#payment-method-error').removeClass('hidden');
                                $('html, body').animate({ scrollTop: $('#payment-method-section').offset().top - 100 }, 300);
                                return;
                            }
                            console.log('Opening payment modal with checkout_id:', response.checkout_id, 'brand:', selectedBrand);
                            submitButton.prop('disabled', false).text(originalText);
                            showPaymentModal(response, selectedBrand);
                        } else if (response.requires_payment && response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else if (response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else {
                            submitButton.prop('disabled', false).text(originalText);
                            alert(response.message || 'تم التسجيل بنجاح');
                            location.reload();
                        }
                    } else {
                        submitButton.prop('disabled', false).text(originalText);
                        alert(response.message || 'حدث خطأ أثناء التسجيل');
                    }
                },
                error: function(xhr) {
                    submitButton.prop('disabled', false).text(originalText);

                    // Show detailed error in console for debugging
                    if (xhr.responseJSON) {
                        console.error('Registration Error:', xhr.responseJSON);
                        if (xhr.responseJSON.debug) {
                            console.error('Debug Info:', xhr.responseJSON.debug);
                        }
                    }

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = [];

                        $.each(errors, function(field, messages) {
                            const fieldName = field.replace('fields.', 'field_');
                            const input = $('#' + fieldName + ', [name="' + field +
                                '"]').first();

                            if (input.length) {
                                input.addClass('border-red-500');
                                const errorMsg = Array.isArray(messages) ? messages[
                                    0] : messages;
                                input.after(
                                    '<p class="error-message text-red-500 text-sm mt-1" dir="rtl">' +
                                    errorMsg + '</p>');
                                errorMessages.push(errorMsg);
                            }
                        });

                        if ($('.error-message').length) {
                            $('html, body').animate({
                                scrollTop: $('.error-message').first().offset()
                                    .top - 100
                            }, 500);
                        }

                        if (errorMessages.length > 0) {
                            alert('يرجى تصحيح الأخطاء التالية:\n' + errorMessages.join(
                                '\n'));
                        }
                    } else {
                        const message = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.';
                        alert(message);
                    }
                }
            });
        });

        function showPaymentModal(response, selectedBrand) {
            selectedBrand = selectedBrand || $('#selected-payment-brand').val() || 'MADA';
            console.log('showPaymentModal called with:', response, 'brand:', selectedBrand);

            const totalPrice = parseFloat(response.total_price) || 0;
            $('#payment-total-amount').text(totalPrice.toFixed(2) +
                ' {{ $event->currency->code ?? 'SAR' }}');

            const modal = $('#hyperpay-payment-modal');
            modal.removeClass('hidden');
            modal.css({
                'display': 'block',
                'z-index': '9999'
            });

            $('#payment-loading').show();
            $('#hyperpay-widget-container').hide();

            // Single brand = no dropdown in widget, only the card form for that brand
            $('#hyperpay-widget-container').html(
                '<form id="hyperpay-payment-form" action="#" class="paymentWidgets" data-brands="' + selectedBrand + '"></form>'
            );

            console.log('Modal display:', modal.css('display'));
            console.log('Modal is visible:', modal.is(':visible'));

            if (response.checkout_id && response.widget_url) {
                currentCheckoutId = response.checkout_id;

                $('script[src*="paymentWidgets.js"]').remove();

                const widgetScriptUrl = response.widget_url + '?checkoutId=' + response.checkout_id;
                console.log('Loading widget from:', widgetScriptUrl);

                const script = document.createElement('script');
                script.src = widgetScriptUrl;

                // Only use integrity in production, skip it in test mode to avoid mismatch issues
                const isTestMode = response.widget_url.includes('eu-test.oppwa.com');
                if (response.integrity && !isTestMode) {
                    script.integrity = response.integrity;
                    script.crossOrigin = 'anonymous';
                    console.log('Integrity hash set:', response.integrity);
                } else {
                    console.log('Skipping integrity check (test mode or no integrity provided)');
                }

                script.async = true;

                // Set wpwl options BEFORE loading the script
                // HyperPay widget checks for wpwlOptions before initializing
                if (typeof window.wpwlOptions === 'undefined') {
                    window.wpwlOptions = {};
                }

                const callbackUrl =
                    '{{ route('checkRegistrationPaymentStatus', ['event_id' => $event->id]) }}';

                window.wpwlOptions.onReady = function() {
                    console.log('Widget ready callback fired');
                    $('#payment-loading').hide();
                    $('#hyperpay-widget-container').show();
                    paymentWidgetLoaded = true;
                };

                window.wpwlOptions.onSuccess = function(response) {
                    console.log('Payment success:', response);
                    handlePaymentSuccess(response);
                };

                window.wpwlOptions.onError = function(error) {
                    console.error('Payment error:', error);
                    handlePaymentError(error);
                };

                console.log('wpwlOptions configured before script load');

                script.onload = function() {
                    console.log('Widget script loaded successfully');

                    // Set form action
                    $('#hyperpay-payment-form').attr('action', callbackUrl);

                    // Wait for widget to render (check for form content or wpwl)
                    let attempts = 0;
                    const maxAttempts = 150; // 15 seconds (increased timeout)
                    const checkWidget = setInterval(function() {
                        attempts++;
                        const form = $('#hyperpay-payment-form');
                        const formContent = form.html() || '';
                        const hasFormContent = formContent.trim().length > 0;
                        const hasWpwl = typeof window.wpwl !== 'undefined';
                        const hasFormInputs = form.find(
                            'input, select, button, .wpwl-group, .wpwl-container').length > 0;

                        // Check if widget has rendered by looking for HyperPay-specific classes or elements
                        const hasWidgetElements = form.find(
                            '.wpwl-group, .wpwl-wrapper, .wpwl-control, .wpwl-form').length > 0;

                        if (hasWidgetElements || (hasWpwl && hasFormContent)) {
                            clearInterval(checkWidget);
                            console.log('Widget fully loaded after', attempts, 'attempts');

                            // Merge options if wpwl exists
                            if (window.wpwl && window.wpwl.options) {
                                Object.assign(window.wpwl.options, window.wpwlOptions);
                            }

                            $('#payment-loading').hide();
                            $('#hyperpay-widget-container').show();
                            paymentWidgetLoaded = true;
                            return;
                        }

                        // If wpwl exists but form is still empty, wait a bit more (widget might be loading)
                        if (hasWpwl && !hasFormContent && attempts < maxAttempts) {
                            // Continue waiting - widget might still be rendering
                            if (attempts % 20 === 0) {
                                console.log('Waiting for widget to render...', attempts,
                                    'attempts (wpwl available)');
                            }
                            return;
                        }

                        // Form has content but no wpwl yet - show it
                        if (hasFormContent && !hasWpwl) {
                            clearInterval(checkWidget);
                            console.log('Form content found, showing widget (wpwl may load later)');
                            $('#payment-loading').hide();
                            $('#hyperpay-widget-container').show();
                            paymentWidgetLoaded = true;
                            return;
                        }

                        // Timeout reached
                        if (attempts >= maxAttempts) {
                            clearInterval(checkWidget);
                            console.error('Widget not fully loaded after', maxAttempts, 'attempts');
                            console.log('Form content length:', formContent.length);
                            console.log('Form inputs count:', form.find('input, select, button')
                                .length);
                            console.log('wpwl available:', hasWpwl);
                            console.log('Widget elements found:', hasWidgetElements);

                            $('#payment-loading').hide();

                            // If wpwl exists, show the form anyway (widget might be functional)
                            if (hasWpwl) {
                                console.log('Showing form anyway - wpwl is available');
                                $('#hyperpay-widget-container').show();
                                // Don't show error if wpwl is available
                            } else if (formContent.trim().length > 0 || hasFormInputs) {
                                // Show form if it has any content
                                $('#hyperpay-widget-container').show();
                                console.log('Showing form with content');
                            } else {
                                // Only show error if nothing is loaded
                                $('#hyperpay-widget-container').html(
                                    '<div class="alert alert-danger">فشل في تحميل نموذج الدفع. يرجى تحديث الصفحة والمحاولة مرة أخرى.</div>'
                                );
                            }
                        }
                    }, 100);
                };

                script.onerror = function(error) {
                    console.error('Script load error:', error);
                    $('#payment-loading').hide();
                    $('#hyperpay-widget-container').html(
                        '<div class="alert alert-danger">فشل في تحميل نموذج الدفع. يرجى المحاولة مرة أخرى.<br>URL: ' +
                        widgetScriptUrl + '</div>'
                    );
                };

                document.body.appendChild(script);
                console.log('Script element appended to body');
            } else {
                console.error('Missing checkout_id or widget_url', response);
                $('#payment-loading').hide();
                $('#hyperpay-widget-container').html(
                    '<div class="alert alert-danger">لم يتم إنشاء جلسة الدفع. يرجى المحاولة مرة أخرى.</div>'
                );
            }
        }

        function handlePaymentSuccess(response) {
            $('#payment-loading').show().html(
                '<div class="text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div><p class="mt-2 text-gray-600">جاري إتمام عملية الدفع...</p></div>'
            );

            const callbackUrl = '{{ route('checkRegistrationPaymentStatus', ['event_id' => $event->id]) }}';

            $.ajax({
                url: callbackUrl,
                type: 'POST',
                data: {
                    resourcePath: response.resourcePath || '',
                    id: response.id || currentCheckoutId
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if (result.status === 'success') {
                        window.location.href = result.redirect_url ||
                            '{{ route('showRegistrationConfirmation', ['event_id' => $event->id]) }}';
                    } else {
                        alert(result.message || 'فشلت عملية الدفع');
                        $('#hyperpay-payment-modal').addClass('hidden').css('display', 'none');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message ?
                        xhr.responseJSON.message : 'حدث خطأ أثناء إتمام عملية الدفع';
                    alert(message);
                    $('#hyperpay-payment-modal').addClass('hidden').css('display', 'none');
                }
            });
        }

        function handlePaymentError(error) {
            alert(error.message || 'فشلت عملية الدفع. يرجى المحاولة مرة أخرى.');
            $('#hyperpay-payment-modal').addClass('hidden').css('display', 'none');
        }
    });
</script>

<!-- HyperPay Payment Modal -->
<div id="hyperpay-payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto"
    style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-right w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-headline">
                            إتمام عملية الدفع
                        </h3>
                        <div class="mt-2">
                            <div id="payment-summary" class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">المبلغ الإجمالي: <span id="payment-total-amount"
                                        class="font-bold text-lg text-gray-900"></span></p>
                            </div>

                            <div id="hyperpay-widget-container" class="mt-4" style="min-height: 400px;">
                                <form id="hyperpay-payment-form" action="#" class="paymentWidgets" data-brands="MADA"></form>
                            </div>

                            <div id="payment-loading" class="text-center py-8">
                                <div
                                    class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600">
                                </div>
                                <p class="mt-2 text-gray-600">جاري تحميل نموذج الدفع...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="close-payment-modal"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>


<style>
    /* Custom styling for HyperPay widget */
    #hyperpay-widget-container {
        min-height: 400px;
    }

    .paymentWidgets {
        width: 100%;
    }

    .paymentWidgets input[type="text"],
    .paymentWidgets input[type="tel"],
    .paymentWidgets select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        margin-bottom: 15px;
        direction: ltr;
        text-align: left;
    }

    .paymentWidgets .wpwl-label {
        text-align: right;
        direction: rtl;
    }

    .paymentWidgets .wpwl-button {
        background-color: #4F46E5;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
        margin-top: 10px;
    }

    .paymentWidgets .wpwl-button:hover {
        background-color: #4338CA;
    }
</style>
