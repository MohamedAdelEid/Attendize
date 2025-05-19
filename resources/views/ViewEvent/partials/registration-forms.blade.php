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
                    <!-- Card 1 -->
                    <div class="flex-shrink-0 w-full overflow-hidden transition-transform bg-white shadow-md registration-card sm:w-1/2 md:w-1/3 lg:w-1/4 rounded-xl hover:shadow-lg hover:-translate-y-1">
                        <div class="relative">
                            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ __('messages.registration_image') }}" class="w-full h-[180px] object-cover">
                            <div class="absolute px-3 py-1 text-xs font-bold text-white bg-green-500 rounded-full top-4 left-4">
                                {{ __('messages.open') }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="mb-1 text-lg font-bold text-gray-900">{{ __('messages.early_bird_registration') }}</h3>
                            <p class="mb-2 text-sm text-gray-600">{{ __('messages.general_admission') }}</p>
                            
                            <div class="flex justify-between mb-3 text-xs text-gray-500">
                                <span>{{ __('messages.starts') }}: 01 {{ __('messages.october') }} 2023</span>
                                <span>{{ __('messages.ends') }}: 20 {{ __('messages.october') }} 2023</span>
                            </div>
                            
                            <div class="p-2 text-center bg-gray-100 rounded-lg countdown" data-end="2023-10-20T23:59:59">
                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.time_remaining') }}</div>
                                <div class="text-sm font-semibold text-gray-800">
                                    <span class="days">15</span> {{ __('messages.days') }} 
                                    <span class="hours">08</span> {{ __('messages.hours') }} 
                                    <span class="minutes">45</span> {{ __('messages.minutes') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="flex-shrink-0 w-full overflow-hidden transition-transform bg-white shadow-md registration-card sm:w-1/2 md:w-1/3 lg:w-1/4 rounded-xl hover:shadow-lg hover:-translate-y-1">
                        <div class="relative">
                            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ __('messages.registration_image') }}" class="w-full h-[180px] object-cover">
                            <div class="absolute px-3 py-1 text-xs font-bold text-white bg-green-500 rounded-full top-4 left-4">
                                {{ __('messages.open') }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="mb-1 text-lg font-bold text-gray-900">{{ __('messages.standard_registration') }}</h3>
                            <p class="mb-2 text-sm text-gray-600">{{ __('messages.general_admission') }}</p>
                            
                            <div class="flex justify-between mb-3 text-xs text-gray-500">
                                <span>{{ __('messages.starts') }}: 05 {{ __('messages.october') }} 2023</span>
                                <span>{{ __('messages.ends') }}: 25 {{ __('messages.october') }} 2023</span>
                            </div>
                            
                            <div class="p-2 text-center bg-gray-100 rounded-lg countdown" data-end="2023-10-25T23:59:59">
                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.time_remaining') }}</div>
                                <div class="text-sm font-semibold text-gray-800">
                                    <span class="days">20</span> {{ __('messages.days') }} 
                                    <span class="hours">12</span> {{ __('messages.hours') }} 
                                    <span class="minutes">30</span> {{ __('messages.minutes') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 3 -->
                    <div class="flex-shrink-0 w-full overflow-hidden transition-transform bg-white shadow-md registration-card sm:w-1/2 md:w-1/3 lg:w-1/4 rounded-xl hover:shadow-lg hover:-translate-y-1">
                        <div class="relative">
                            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ __('messages.registration_image') }}" class="w-full h-[180px] object-cover">
                            <div class="absolute px-3 py-1 text-xs font-bold text-white bg-green-500 rounded-full top-4 left-4">
                                {{ __('messages.open') }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="mb-1 text-lg font-bold text-gray-900">{{ __('messages.vip_registration') }}</h3>
                            <p class="mb-2 text-sm text-gray-600">{{ __('messages.premium_access') }}</p>
                            
                            <div class="flex justify-between mb-3 text-xs text-gray-500">
                                <span>{{ __('messages.starts') }}: 01 {{ __('messages.october') }} 2023</span>
                                <span>{{ __('messages.ends') }}: 14 {{ __('messages.october') }} 2023</span>
                            </div>
                            
                            <div class="p-2 text-center bg-gray-100 rounded-lg countdown" data-end="2023-10-14T23:59:59">
                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.time_remaining') }}</div>
                                <div class="text-sm font-semibold text-gray-800">
                                    <span class="days">9</span> {{ __('messages.days') }} 
                                    <span class="hours">16</span> {{ __('messages.hours') }} 
                                    <span class="minutes">20</span> {{ __('messages.minutes') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 4 -->
                    <div class="flex-shrink-0 w-full overflow-hidden transition-transform bg-white shadow-md registration-card sm:w-1/2 md:w-1/3 lg:w-1/4 rounded-xl hover:shadow-lg hover:-translate-y-1">
                        <div class="relative">
                            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ __('messages.registration_image') }}" class="w-full h-[180px] object-cover">
                            <div class="absolute px-3 py-1 text-xs font-bold text-white bg-yellow-500 rounded-full top-4 left-4">
                                {{ __('messages.limited') }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="mb-1 text-lg font-bold text-gray-900">{{ __('messages.workshop_registration') }}</h3>
                            <p class="mb-2 text-sm text-gray-600">{{ __('messages.technical_workshop') }}</p>
                            
                            <div class="flex justify-between mb-3 text-xs text-gray-500">
                                <span>{{ __('messages.starts') }}: 10 {{ __('messages.october') }} 2023</span>
                                <span>{{ __('messages.ends') }}: 15 {{ __('messages.october') }} 2023</span>
                            </div>
                            
                            <div class="p-2 text-center bg-gray-100 rounded-lg countdown" data-end="2023-10-15T23:59:59">
                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.time_remaining') }}</div>
                                <div class="text-sm font-semibold text-gray-800">
                                    <span class="days">10</span> {{ __('messages.days') }} 
                                    <span class="hours">5</span> {{ __('messages.hours') }} 
                                    <span class="minutes">15</span> {{ __('messages.minutes') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 5 -->
                    <div class="flex-shrink-0 w-full overflow-hidden transition-transform bg-white shadow-md registration-card sm:w-1/2 md:w-1/3 lg:w-1/4 rounded-xl hover:shadow-lg hover:-translate-y-1">
                        <div class="relative">
                            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ __('messages.registration_image') }}" class="w-full h-[180px] object-cover">
                            <div class="absolute px-3 py-1 text-xs font-bold text-white bg-red-500 rounded-full top-4 left-4">
                                {{ __('messages.closed') }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="mb-1 text-lg font-bold text-gray-900">{{ __('messages.speaker_registration') }}</h3>
                            <p class="mb-2 text-sm text-gray-600">{{ __('messages.speaker_submission') }}</p>
                            
                            <div class="flex justify-between mb-3 text-xs text-gray-500">
                                <span>{{ __('messages.starts') }}: 15 {{ __('messages.september') }} 2023</span>
                                <span>{{ __('messages.ends') }}: 30 {{ __('messages.september') }} 2023</span>
                            </div>
                            
                            <div class="p-2 text-center bg-gray-100 rounded-lg">
                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.registration_closed') }}</div>
                                <div class="text-sm font-semibold text-gray-800">
                                    {{ __('messages.submissions_under_review') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 6 -->
                    <div class="flex-shrink-0 w-full overflow-hidden transition-transform bg-white shadow-md registration-card sm:w-1/2 md:w-1/3 lg:w-1/4 rounded-xl hover:shadow-lg hover:-translate-y-1">
                        <div class="relative">
                            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ __('messages.registration_image') }}" class="w-full h-[180px] object-cover">
                            <div class="absolute px-3 py-1 text-xs font-bold text-white bg-green-500 rounded-full top-4 left-4">
                                {{ __('messages.open') }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="mb-1 text-lg font-bold text-gray-900">{{ __('messages.student_registration') }}</h3>
                            <p class="mb-2 text-sm text-gray-600">{{ __('messages.student_discount') }}</p>
                            
                            <div class="flex justify-between mb-3 text-xs text-gray-500">
                                <span>{{ __('messages.starts') }}: 05 {{ __('messages.october') }} 2023</span>
                                <span>{{ __('messages.ends') }}: 12 {{ __('messages.october') }} 2023</span>
                            </div>
                            
                            <div class="p-2 text-center bg-gray-100 rounded-lg countdown" data-end="2023-10-12T23:59:59">
                                <div class="mb-1 text-xs text-gray-600">{{ __('messages.time_remaining') }}</div>
                                <div class="text-sm font-semibold text-gray-800">
                                    <span class="days">7</span> {{ __('messages.days') }} 
                                    <span class="hours">14</span> {{ __('messages.hours') }} 
                                    <span class="minutes">30</span> {{ __('messages.minutes') }}
                                </div>
                            </div>
                        </div>
                    </div>
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