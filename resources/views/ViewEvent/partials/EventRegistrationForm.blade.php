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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $event->start_date->format('M d, Y') }} - {{ $event->end_date->format('M d, Y') }}</span>
                        </div>

                        @if($event->location)
                        <div class="flex items-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
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
                                @if($registration->category)
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
                    @if($event->location_google_place_id != "" )
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
                        <div class="countdown-timer bg-white border border-gray-200 rounded-lg p-4 shadow-sm" data-end="{{ $registration->end_date->format('Y-m-d\TH:i:s') }}">
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
                <form method="POST"
                    action="{{ route('postEventRegistration', ['event_id' => $event->id, 'registration_id' => $registration->id]) }}"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Personal Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">المعلومات الشخصية</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">الاسم الأول</label>
                                <input placeholder="أدخل الاسم الأول"
                                    type="text"
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('first_name') border-red-500 @enderror"
                                    id="first_name"
                                    name="first_name"
                                    value="{{ old('first_name') }}"
                                    required>
                                @error('first_name')
                                    <p class="text-red-500 text-sm mt-1" dir="rtl">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">الاسم الأخير</label>
                                <input placeholder="أدخل الاسم الأخير"
                                    type="text"
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('last_name') border-red-500 @enderror"
                                    id="last_name"
                                    name="last_name"
                                    value="{{ old('last_name') }}"
                                    required>
                                @error('last_name')
                                    <p class="text-red-500 text-sm mt-1" dir="rtl">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">البريد الالكتروني</label>
                                <input placeholder="أدخل البريد الإلكتروني"
                                    type="email"
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-500 @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1" dir="rtl">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">رقم الجوال</label>
                                <input placeholder="أدخل رقم الجوال"
                                    type="tel"
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('phone') border-red-500 @enderror"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}">
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
                                    $hasConference = $registration->dynamicFormFields->where('type', 'conference')->first();
                                    $hasProfession = $registration->dynamicFormFields->where('type', 'profession')->first();
                                @endphp

                                @foreach ($registration->dynamicFormFields as $field)
                                    <div>
                                        <label for="field_{{ $field->id }}"
                                            class="block text-sm font-medium text-gray-700 {{ $field->is_required ? 'after:content-[\'*\'] after:ml-0.5 after:text-red-500' : '' }}">{{ $field->label }}</label>

                                        @if ($field->type == 'text')
                                            <input type="text"
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            value="{{ old('fields.'.$field->id) }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'email')
                                            <input type="email"
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            value="{{ old('fields.'.$field->id) }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'tel')
                                            <input type="tel"  placeholder="أدخل رقم الهاتف"
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            value="{{ old('fields.'.$field->id) }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'number')
                                            <input type="number" placeholder="أدخل رقم"
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            value="{{ old('fields.'.$field->id) }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'textarea')
                                            <textarea placeholder="أدخل النص"
                                            class="mt-2 block w-full px-4 py-3 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            rows="3"
                                            {{ $field->is_required ? 'required' : '' }}>{{ old('fields.'.$field->id) }}</textarea>
                                        @elseif($field->type == 'select')
                                            <select
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                                <option value="">-- اختر خيار --</option>
                                                @if(is_array($field->options))
                                                    @foreach ($field->options as $option)
                                                        <option value="{{ trim($option) }}" {{ old('fields.'.$field->id) == trim($option) ? 'selected' : '' }}>{{ trim($option) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->type == 'user_types')
                                            <select
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                                <option value="">-- اختر نوع المستخدم --</option>
                                                @foreach($event->userTypes as $userType)
                                                    <option value="{{ $userType->name }}" {{ old('fields.'.$field->id) == $userType->name ? 'selected' : '' }}>{{ $userType->name }}</option>
                                                @endforeach
                                                <option value="Delegate" {{ old('fields.'.$field->id) == 'Delegate' || !old('fields.'.$field->id) ? 'selected' : '' }}>Delegate (Default)</option>
                                            </select>
                                        @elseif($field->type == 'country')
                                            <select
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                                <option value="">-- اختر الدولة --</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}" {{ old('fields.'.$field->id) == $country->id ? 'selected' : '' }}>{{ $country->name }} - {{ $country->country_code }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field->type == 'city')
                                            <input type="text" placeholder="أدخل المدينة"
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            value="{{ old('fields.'.$field->id) }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'conference')
                                            <select
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror conference-select"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            data-field-id="{{ $field->id }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                                <option value="">-- اختر المؤتمر --</option>
                                                @foreach($event->conferences as $conference)
                                                    <option value="{{ $conference->id }}" {{ old('fields.'.$field->id) == $conference->id ? 'selected' : '' }}>{{ $conference->name }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field->type == 'profession')
                                            <select
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror profession-select"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            data-field-id="{{ $field->id }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            {{ !$hasConference ? '' : 'disabled' }}>
                                                <option value="">-- اختر المهنة --</option>
                                                @if(!$hasConference)
                                                    @foreach($professions as $profession)
                                                        <option value="{{ $profession->id }}" {{ old('fields.'.$field->id) == $profession->id ? 'selected' : '' }}>{{ $profession->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->type == 'checkbox')
                                            <div class="mt-1">
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input class="h-4 w-4 text-primary-600 border border-gray-300 rounded focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                                        type="checkbox"
                                                        id="field_{{ $field->id }}"
                                                        name="fields[{{ $field->id }}]"
                                                        value="1"
                                                        {{ old('fields.'.$field->id) ? 'checked' : '' }}
                                                        {{ $field->is_required ? 'required' : '' }}>
                                                    </div>
                                                    <div class="mr-3 text-sm">
                                                        <label class="font-medium text-gray-700" for="field_{{ $field->id }}">{{ $field->description }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($field->type == 'radio')
                                            <div class="mt-1 space-y-2">
                                                @if(is_array($field->options))
                                                    @foreach ($field->options as $option)
                                                        <div class="flex items-center">
                                                            <input class="h-4 w-4 text-primary-600 border border-gray-300 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                                            type="radio"
                                                            name="fields[{{ $field->id }}]"
                                                            id="field_{{ $field->id }}_{{ $loop->index }}"
                                                            value="{{ trim($option) }}"
                                                            {{ old('fields.'.$field->id) == trim($option) ? 'checked' : '' }}
                                                            {{ $field->is_required ? 'required' : '' }}>
                                                            <label class="mr-3 block text-sm font-medium text-gray-700"
                                                                for="field_{{ $field->id }}_{{ $loop->index }}">{{ trim($option) }}</label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($field->type == 'date')
                                            <input type="date"
                                            class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]"
                                            value="{{ old('fields.'.$field->id) }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        @elseif($field->type == 'file')
                                            <input type="file"
                                            class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('fields.'.$field->id) border-red-500 @enderror"
                                            id="field_{{ $field->id }}"
                                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                            @if ($field->description)
                                                <p class="mt-1 text-sm text-gray-500">{{ $field->description }}</p>
                                            @endif
                                        @endif

                                        @error('fields.'.$field->id)
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

                    <div class="mt-8">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
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
    // Conference-Profession dependency
    $('.conference-select').on('change', function() {
        const conferenceId = $(this).val();
        const professionSelect = $('.profession-select');

        if (conferenceId) {
            // Enable profession select and load professions for selected conference
            professionSelect.prop('disabled', false);

            $.ajax({
                url: '{{ route("getConferenceProfessions", ["event_id" => $event->id, "conference_id" => "__ID__"]) }}'.replace('__ID__', conferenceId),
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        professionSelect.html('<option value="">-- اختر المهنة --</option>');
                        $.each(response.professions, function(id, name) {
                            professionSelect.append('<option value="' + id + '">' + name + '</option>');
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading professions:', xhr);
                }
            });
        } else {
            // Disable profession select and clear options
            professionSelect.prop('disabled', true).html('<option value="">-- اختر المهنة --</option>');
        }
    });
});
</script>
