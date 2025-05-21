<section id="registration" class="py-16 bg-gray-50">
    <div class="container px-4 mx-auto">
        <!-- Section Title -->
        <div class="mb-10 text-center translate-y-8 opacity-0 animate-on-scroll">
            <h2 class="mb-4 text-3xl font-bold text-gray-900 md:text-4xl">{{ __('messages.registration_forms') }}</h2>
            <div class="w-24 h-1 mx-auto bg-primary-600"></div>
        </div>
        
        <!-- Registration Cards Container with Navigation -->
        <div class="relative">
            <!-- Left Arrow -->
            <button id="scroll-left" class="absolute left-0 z-10 p-2 transition-colors -translate-y-1/2 bg-white rounded-full shadow-lg top-1/2 focus:outline-none hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            
            <!-- Registration Cards Scroll Container -->
            <div id="registration-cards-container" class="overflow-x-auto hide-scrollbar">
                <div id="registration-cards" class="flex px-2 py-4 space-x-6">
                    @if ($event->registrations->count() > 0 )
                    @foreach ($event->registrations->sortByDesc('created_at') as $registration)
                                <!-- Registration Card -->
                                <div class="flex-shrink-0 w-full overflow-hidden transition-transform bg-white shadow-md registration-card sm:w-1/2 md:w-1/3 lg:w-1/4 rounded-xl hover:shadow-lg hover:-translate-y-1">
                                    <div class="relative">
                                        @if ($registration->image)
                                            <img src="{{ asset('storage/' . $registration->image) }}" alt="{{ $registration->name }}" class="w-full h-[180px] object-cover">
                                        @else
                                            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ $registration->name }}" class="w-full h-[180px] object-cover">
                                        @endif
                                        
                                        <!-- Status Badge -->
                                        @php
                                            $now = now();
                                            $badgeClass = 'bg-green-500';
                                            $badgeText = __('messages.open');
                                            
                                            if ($registration->start_date > $now) {
                                                $badgeClass = 'bg-blue-500';
                                                $badgeText = __('messages.upcoming');
                                            } elseif ($registration->end_date < $now) {
                                                $badgeClass = 'bg-red-500';
                                                $badgeText = __('messages.closed');
                                            } 
                                        @endphp
                                        
                                        <div class="absolute px-3 py-1 text-xs font-bold text-white rounded-full top-4 left-4 {{ $badgeClass }}">
                                            {{ $badgeText }}
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="mb-1 text-lg font-bold text-gray-900">{{ $registration->name }}</h3>
                                        @if($registration->category)    
                                            <p class="mb-2 text-sm text-red-600">{{ $registration->category->name }}</p>
                                        @endif
                                        
                                        <div class="flex justify-between mb-3 text-xs text-gray-500">
                                            <span>{{ __('messages.starts') }}: {{ $registration->start_date->format('d M Y') }}</span>
                                            <span>{{ __('messages.ends') }}: {{ $registration->end_date->format('d M Y') }}</span>
                                        </div>
                                        @if($registration->category && $registration->category->conferences && $registration->category->conferences->count() > 0)
                                                @php
                                                    $minPrice = $registration->category->conferences->min('price');
                                                    $maxPrice = $registration->category->conferences->max('price');
                                                @endphp
                                                <div class="mb-3 p-2 bg-gray-50 rounded-lg text-center">
                                                    <div class="text-xs text-gray-600 mb-1">{{ __('messages.price') }}</div>
                                                    <div class="text-base font-bold text-primary-600">
                                                        @if($minPrice == $maxPrice)
                                                            ${{ number_format($minPrice, 2) }}
                                                        @else
                                                            ${{ number_format($minPrice, 2) }} - ${{ number_format($maxPrice, 2) }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @if($registration->end_date > now() && $registration->start_date <= now())
                                            <div class="p-2 text-center bg-gray-100 rounded-lg countdown" data-end="{{ $registration->end_date->format('Y-m-d\TH:i:s') }}">
                                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.time_remaining') }}</div>
                                                <div class="text-sm font-semibold text-gray-800">
                                                    <span class="days">--</span> {{ __('messages.days') }} 
                                                    <span class="hours">--</span> {{ __('messages.hours') }} 
                                                    <span class="minutes">--</span> {{ __('messages.minutes') }}
                                                    <span class="seconds">--</span> {{ __('messages.seconds') }}
                                                </div>
                                            </div>
                                        @elseif($registration->start_date > now())
                                            <div class="p-2 text-center bg-gray-100 rounded-lg countdown" data-end="{{ $registration->start_date->format('Y-m-d\TH:i:s') }}">
                                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.opens_in') }}</div>
                                                <div class="text-sm font-semibold text-gray-800">
                                                    <span class="days">--</span> {{ __('messages.days') }} 
                                                    <span class="hours">--</span> {{ __('messages.hours') }} 
                                                    <span class="minutes">--</span> {{ __('messages.minutes') }}
                                                    <span class="seconds">--</span> {{ __('messages.second') }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-2 text-center bg-gray-100 rounded-lg">
                                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.registration_closed') }}</div>
                                                <div class="text-sm font-semibold text-gray-800">
                                                    {{ __('messages.no_longer_available') }}
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Register Button -->
                                        <div class="mt-4 text-center">
                                            @if($registration->end_date > now() && $registration->start_date <= now() && 
                                                (!$registration->capacity || $registration->registrants_count < $registration->capacity))
                                                <a href="{{ route('showEventRegistrationForm', ['event_id' => $event->id, 'event_slug' => $event->slug, 'registration_id' => $registration->id]) }}" 
                                                   class="px-4 py-2 text-sm font-medium text-white transition-all bg-primary-600 rounded-lg hover:bg-primary-700 hover:shadow-md">
                                                    {{ __('messages.register_now') }}
                                                </a>
                                            @elseif($registration->start_date > now())
                                                <button disabled class="px-4 py-2 text-sm font-medium text-white transition-all bg-gray-400 rounded-lg cursor-not-allowed">
                                                    {{ __('messages.coming_soon') }}
                                                </button>
                                            @else
                                                <button disabled class="px-4 py-2 text-sm font-medium text-white transition-all bg-gray-400 rounded-lg cursor-not-allowed">
                                                    {{ __('messages.registration_closed') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    @else
                        <div class="w-full p-6 text-center bg-white rounded-xl">
                            <p class="text-gray-600">{{ __('messages.no_registrations_available') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Right Arrow -->
            <button id="scroll-right" class="absolute right-0 z-10 p-2 transition-colors -translate-y-1/2 bg-white rounded-full shadow-lg top-1/2 focus:outline-none hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>
</section>

