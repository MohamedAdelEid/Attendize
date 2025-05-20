@extends('ViewEvent.layouts.layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- Left Panel: Registration Information -->
                <div class="lg:w-1/3 bg-gray-50 p-6 md:p-8">
                    <div class="sticky top-5">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Registration Details</h2>
                        
                        <!-- Event Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $event->title }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($event->description, 150) }}</p>
                            
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $event->start_date->format('M d, Y') }} - {{ $event->end_date->format('M d, Y') }}</span>
                            </div>
                            
                            @if($event->location)
                            <div class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $event->location }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Registration Type -->
                        <div class="mb-6 bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $registration->name }}</h4>
                                    @if($registration->category)
                                        <span class="text-xs text-primary-600">{{ $registration->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-600">
                                <div class="flex justify-between mb-1">
                                    <span>Registration Opens:</span>
                                    <span class="font-medium">{{ $registration->start_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Registration Closes:</span>
                                    <span class="font-medium">{{ $registration->end_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fee Information -->
                        @if($registration->category && $registration->category->conferences && $registration->category->conferences->count() > 0)
                            <div class="mb-6">
                                <h3 class="text-md font-semibold text-gray-800 mb-3">Available Options</h3>
                                <div class="space-y-2">
                                    @foreach($registration->category->conferences as $conference)
                                        <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-100">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-900">{{ $conference->name }}</span>
                                                <span class="text-sm font-bold text-primary-600">${{ number_format($conference->price, 2) }}</span>
                                            </div>
                                            @if($conference->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($conference->description, 100) }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Selected Fee Card -->
                        <div id="fee-card" class="hidden mb-6 bg-primary-50 border border-primary-100 rounded-lg p-4 transition-all duration-300 ease-in-out">
                            <h3 class="text-md font-semibold text-primary-800 mb-2">Your Selection</h3>
                            <div class="flex justify-between items-center">
                                <span id="fee-card-conference" class="text-sm text-primary-700">Selected Conference</span>
                                <span id="fee-card-amount" class="text-xl font-bold text-primary-600 transition-all duration-300">$0.00</span>
                            </div>
                        </div>
                        
                        <!-- Countdown Timer -->
                        <div class="countdown-container">
                            <div class="countdown-timer bg-white border border-gray-200 rounded-lg p-4 shadow-sm" data-end="{{ $registration->end_date->format('Y-m-d\TH:i:s') }}">
                                <div class="text-sm font-medium text-gray-800 mb-2">Registration Closes In</div>
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
                        
                        <!-- Help Information -->
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-800">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="font-medium mb-1">Need Help?</p>
                                    <p class="text-blue-700">If you have any questions about registration, please contact our support team at <a href="mailto:support@example.com" class="underline">support@example.com</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Panel: Registration Form -->
                <div class="lg:w-2/3 p-6 md:p-8 border-t lg:border-t-0 lg:border-l border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Registration Form</h2>
                    <form method="POST"
                        action="{{ route('postEventRegistration', ['event_id' => $event->id, 'registration_id' => $registration->id]) }}"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">First Name</label>
                                    <input  placeholder="Enter First Name"
  type="text" 
  class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
  id="first_name" 
  name="first_name" 
  required
>

                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Last Name</label>
                                    <input placeholder="Enter Last Name" type="text" 
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                    id="last_name" 
                                    name="last_name" 
                                    required
>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Email Address</label>
                                    <input placeholder="Enter Email Address" type="email" 
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                    id="email" 
                                    name="email" 
                                    required
>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input placeholder="Enter Phone Number" type="tel" 
                                    class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                    id="phone" 
                                    name="phone"
>
                                </div>
                            </div>
                        </div>

                        <!-- Conference Selection (if applicable) -->
                        @if (
                            $registration->category &&
                                $registration->category->conferences &&
                                $registration->category->conferences->count() > 0)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Conference Selection</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="conference_id" class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Select Conference</label>
                                        <select placeholder="Select Conference" 
                                        class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                        id="conference_id" 
                                        name="conference_id" 
                                        required
>
                                            <option value="">-- Loading Conferences... --</option>
                                        </select>
                                        <div class="mt-1 text-sm text-red-600 hidden" id="conference-error">
                                            Please select a conference.
                                        </div>
                                    </div>
                                    <div>
                                        <label for="profession_id" class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Profession</label>
                                        <select placeholder="Select Profession" 
                                        class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                        id="profession_id" 
                                        name="profession_id" 
                                        required 
                                        disabled
>
                                            <option value="">-- Select Profession --</option>
                                        </select>
                                        <div class="mt-1 text-sm text-red-600 hidden" id="profession-error">
                                            Please select a profession.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Dynamic Form Fields -->
                        @if ($registration->dynamicFormFields && $registration->dynamicFormFields->count() > 0)
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                                <div class="space-y-6">
                                    @foreach ($registration->dynamicFormFields as $field)
                                        <div>
                                            <label for="field_{{ $field->id }}"
                                                class="block text-sm font-medium text-gray-700 {{ $field->is_required ? 'after:content-[\'*\'] after:ml-0.5 after:text-red-500' : '' }}">{{ $field->label }}</label>

                                            @if ($field->type == 'text')
                                                <input type="text" 
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                                id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                            @elseif($field->type == 'email')
                                                <input type="email" 
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                                id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                            @elseif($field->type == 'tel')
                                                <input type="tel"  placeholder="Enter Phone Number"
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                                id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                            @elseif($field->type == 'number')
                                                <input type="number" placeholder="Enter Number" 
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                                id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                            @elseif($field->type == 'textarea')
                                                <textarea placeholder="Enter Text" 
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                                id="field_{{ $field->id }}" 
                                                name="fields[{{ $field->id }}]" 
                                                rows="3"
                            {{ $field->is_required ? 'required' : '' }}></textarea>
                                            @elseif($field->type == 'select')
                                                <select 
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                                id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                            <option value="">-- Select Option --</option>
                            @foreach (explode(',', $field->options) as $option)
                                <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                            @endforeach
                        </select>
                                            @elseif($field->type == 'country')
                                                <select 
                                                class="mt-2 block w-full h-14 px-4 text-lg rounded-lg border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                                id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                            <option value="">-- Select Country --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }} - {{ $country->country_code }}</option>
                            @endforeach
                        </select>
                                            @elseif($field->type == 'city')
                                                <input type="text" placeholder="Enter City" 
                                                class="mt-2 block w-full h-10 px-4 text-lg rounded-lg 
                                                border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                                                id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                            @elseif($field->type == 'checkbox')
                                                <div class="mt-1">
                                                    <div class="flex items-start">
                                                        <div class="flex items-center h-5">
                                                            <input class="h-4 w-4 text-primary-600 border border-gray-300 rounded focus:ring-primary-500" type="checkbox" id="field_{{ $field->id }}"
                                        name="fields[{{ $field->id }}]" value="1"
                                        {{ $field->is_required ? 'required' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label class="font-medium text-gray-700" for="field_{{ $field->id }}">{{ $field->description }}</label>
                                </div>
                            </div>
                        </div>
                                            @elseif($field->type == 'radio')
                                                <div class="mt-1 space-y-2">
                                                    @foreach (explode(',', $field->options) as $option)
                                                        <div class="flex items-center">
                                                            <input class="h-4 w-4 text-primary-600 border border-gray-300 focus:ring-primary-500" type="radio"
                                        name="fields[{{ $field->id }}]"
                                        id="field_{{ $field->id }}_{{ $loop->index }}"
                                        value="{{ trim($option) }}" {{ $field->is_required ? 'required' : '' }}>
                                    <label class="ml-3 block text-sm font-medium text-gray-700"
                                        for="field_{{ $field->id }}_{{ $loop->index }}">{{ trim($option) }}</label>
                                </div>
                            @endforeach
                        </div>
                                            @elseif($field->type == 'date')
                                                <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                            @elseif($field->type == 'file')
                                                <input type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" id="field_{{ $field->id }}"
                            name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }}>
                                            @if ($field->description)
                                                <p class="mt-1 text-sm text-gray-500">{{ $field->description }}</p>
                                            @endif
                                        @endif

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
                                Complete Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
@endsection
