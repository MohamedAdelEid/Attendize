@extends('ViewEvent.layouts.layout')

@section('content')
<!-- About Us Hero Section -->
<section class="relative pt-32 pb-20 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute inset-0 bg-gradient-to-b from-primary-100 to-transparent"></div>
        <div class="absolute inset-0 bg-repeat opacity-30" style="background-image: url('{{ asset($event->bg_image_path) }}');"></div>
    </div>
    
    <div class="container relative z-10 px-4 mx-auto">
        <div class="max-w-4xl mx-auto text-center mb-12">
            <!-- Section Title -->
            <div class="inline-block rounded-lg bg-primary-50 px-3 py-1 text-sm text-primary-600 mb-4">About Us</div>
            <h1 class="text-4xl font-bold leading-tight text-gray-900 md:text-5xl lg:text-6xl mb-6">
                {{ $event->title }} Team
            </h1>
            <p class="text-xl text-gray-700">
                {{ $event->description }}
            </p>
        </div>
    </div>
</section>



<!-- Event Details Section -->
<section id="event-details" class="py-16 bg-gray-50">
    <div class="container px-4 mx-auto">
        <!-- Section Title -->
        <div class="mb-12 text-center translate-y-8 opacity-0 animate-on-scroll">
            <h2 class="mb-4 text-3xl font-bold text-gray-900 md:text-4xl">Event Details</h2>
            <div class="w-24 h-1 mx-auto bg-primary-600"></div>
            <p class="mt-4 text-xl text-gray-700 max-w-3xl mx-auto">
                Everything you need to know about our event
            </p>
        </div>
        
        <!-- Event Details Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl translate-y-8 opacity-0 animate-on-scroll max-w-4xl mx-auto">
            <div class="relative">
                @if($event->bg_image_path)
                    <img src="{{ asset($event->bg_image_path) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 bg-gradient-to-r from-primary-500 to-primary-700 flex items-center justify-center">
                        <h3 class="text-white text-2xl font-bold">{{ $event->title }}</h3>
                    </div>
                @endif
                
                <!-- Event Status Badge -->
                @php
                    $now = now();
                    $badgeClass = 'bg-green-500';
                    $badgeText = 'Active';
                    
                    if ($event->start_date > $now) {
                        $badgeClass = 'bg-blue-500';
                        $badgeText = 'Upcoming';
                    } elseif ($event->end_date < $now) {
                        $badgeClass = 'bg-red-500';
                        $badgeText = 'Ended';
                    }
                @endphp
                
                <div class="absolute top-4 right-4 px-3 py-1 text-xs font-bold text-white rounded-full {{ $badgeClass }}">
                    {{ $badgeText }}
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Event Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Event Title</h4>
                                <p class="text-lg font-medium text-gray-900">{{ $event->title }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Description</h4>
                                <p class="text-gray-700">{{ Str::limit($event->description, 150) }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Venue</h4>
                                <p class="text-gray-700">{{ $event->venue_name }}</p>
                                @if($event->venue_name_full)
                                    <p class="text-sm text-gray-500">{{ $event->venue_name_full }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Date & Location</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Event Date</h4>
                                    <p class="text-gray-700">
                                        {{ $event->start_date ? $event->start_date->format('F d, Y - h:i A') : 'TBA' }}
                                        @if($event->end_date && $event->start_date->format('Y-m-d') != $event->end_date->format('Y-m-d'))
                                            <span class="text-gray-500">to</span> {{ $event->end_date->format('F d, Y - h:i A') }}
                                        @elseif($event->end_date)
                                            <span class="text-gray-500">to</span> {{ $event->end_date->format('h:i A') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Location</h4>
                                    <p class="text-gray-700">{{ $event->location ?? $event->location_address ?? 'TBA' }}</p>
                                    @if($event->location_address_line_1)
                                        <p class="text-sm text-gray-500">
                                            {{ $event->location_address_line_1 }}
                                            @if($event->location_address_line_2)
                                                , {{ $event->location_address_line_2 }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($event->location_lat && $event->location_long)
                                <div class="mt-4">
                                    <a href="https://maps.google.com/?q={{ $event->location_lat }},{{ $event->location_long }}" target="_blank" class="inline-flex items-center text-primary-600 hover:text-primary-700">
                                        <span>View on Google Maps</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Social Sharing -->
                @if($event->social_show_facebook || $event->social_show_twitter || $event->social_show_linkedin || $event->social_show_whatsapp || $event->social_show_email)
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Share This Event</h4>
                        <div class="flex space-x-4">
                            @if($event->social_show_facebook)
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/>
                                    </svg>
                                </a>
                            @endif
                            
                            @if($event->social_show_twitter)
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($event->social_share_text ?? $event->title) }}" target="_blank" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center text-white hover:bg-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6.066 9.645c.183 4.04-2.83 8.544-8.164 8.544-1.622 0-3.131-.476-4.402-1.291 1.524.18 3.045-.244 4.252-1.189-1.256-.023-2.317-.854-2.684-1.995.451.086.895.061 1.298-.049-1.381-.278-2.335-1.522-2.304-2.853.388.215.83.344 1.301.359-1.279-.855-1.641-2.544-.889-3.835 1.416 1.738 3.533 2.881 5.92 3.001-.419-1.796.944-3.527 2.799-3.527.825 0 1.572.349 2.096.907.654-.128 1.27-.368 1.824-.697-.215.671-.67 1.233-1.263 1.589.581-.07 1.135-.224 1.649-.453-.384.578-.87 1.084-1.433 1.489z"/>
                                    </svg>
                                </a>
                            @endif
                            
                            @if($event->social_show_linkedin)
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center text-white hover:bg-blue-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2 16h-2v-6h2v6zm-1-6.891c-.607 0-1.1-.496-1.1-1.109 0-.612.492-1.109 1.1-1.109s1.1.497 1.1 1.109c0 .613-.493 1.109-1.1 1.109zm8 6.891h-1.998v-2.861c0-1.881-2.002-1.722-2.002 0v2.861h-2v-6h2v1.093c.872-1.616 4-1.736 4 1.548v3.359z"/>
                                    </svg>
                                </a>
                            @endif
                            
                            @if($event->social_show_whatsapp)
                                <a href="https://wa.me/?text={{ urlencode($event->social_share_text ?? $event->title) }}%20{{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white hover:bg-green-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.129.332.202.043.72.043.419-.101.824z"/>
                                    </svg>
                                </a>
                            @endif
                            
                            @if($event->social_show_email)
                                <a href="mailto:?subject={{ urlencode($event->title) }}&body={{ urlencode($event->social_share_text ?? 'Check out this event: ' . url()->current()) }}" class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center text-white hover:bg-gray-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Event Countdown -->
            @if($event->end_date && $event->end_date > now())
                <div class="bg-gray-50 p-6 border-t border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 text-center">Event Ends In</h4>
                    <div id="event-countdown" class="grid grid-cols-4 gap-4 text-center">
                        <div class="flex flex-col">
                            <div class="flex items-center justify-center px-4 py-6 text-3xl font-bold bg-white rounded-lg shadow-md text-primary-600 days">
                                00
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-600">Days</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center justify-center px-4 py-6 text-3xl font-bold bg-white rounded-lg shadow-md text-primary-600 hours">
                                00
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-600">Hours</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center justify-center px-4 py-6 text-3xl font-bold bg-white rounded-lg shadow-md text-primary-600 minutes">
                                00
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-600">Minutes</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center justify-center px-4 py-6 text-3xl font-bold bg-white rounded-lg shadow-md text-primary-600 seconds">
                                00
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-600">Seconds</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Our Values Section -->
<!-- <section id="values" class="py-16 bg-white">
    <div class="container px-4 mx-auto">
        <div class="mb-12 text-center translate-y-8 opacity-0 animate-on-scroll">
            <h2 class="mb-4 text-3xl font-bold text-gray-900 md:text-4xl">Our Values</h2>
            <div class="w-24 h-1 mx-auto bg-primary-600"></div>
            <p class="mt-4 text-xl text-gray-700 max-w-3xl mx-auto">
                Values Description
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-50 rounded-xl p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-2 translate-y-8 opacity-0 animate-on-scroll">
                <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Value 1 Title</h3>
                <p class="text-gray-700">Value 1 Description</p>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-2 translate-y-8 opacity-0 animate-on-scroll delay-100">
                <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Value 2 Title</h3>
                <p class="text-gray-700">Value 2 Description</p>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-2 translate-y-8 opacity-0 animate-on-scroll delay-200">
                <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Value 3 Title</h3>
                <p class="text-gray-700">Value 3 Description</p>
            </div>
        </div>
    </div>
</section> -->

<!-- Contact Section -->
<section id="contact" class="py-16 bg-gray-50">
    <div class="container px-4 mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="transition-all duration-700 ease-out translate-y-8 opacity-0 animate-on-scroll rtl:text-right">
                <div class="inline-block rounded-lg bg-primary-50 px-3 py-1 text-sm text-primary-600 mb-4">Contact Us</div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Contact Title</h2>
                <p class="text-xl text-gray-700 mb-8">Contact Description</p>
                
                <div class="space-y-6">
                    <!-- Address -->
                    <div class="flex items-start space-x-4 rtl:space-x-reverse">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Address</h3>
                            
                            <p class="text-gray-700">{{$event->location_address_line_2 ?? 'No Address'}}</p>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="flex items-start space-x-4 rtl:space-x-reverse">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                            <p class="text-gray-700">{{$event->organiser->email ?? 'No Email'}}</p>
                        </div>
                    </div>
                    
                    <!-- Phone -->
                    <div class="flex items-start space-x-4 rtl:space-x-reverse">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Phone</h3>
                            <p class="text-gray-700">{{$event->organiser->phone ?? 'No Phone Number'}}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="bg-white p-8 rounded-xl shadow-md transition-all duration-700 ease-out delay-300 translate-y-8 opacity-0 animate-on-scroll">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Send Message</h3>
                <form action="{{ route('events.contact-us.post', $event) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                        <input type="text" placeholder="Your Name" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Your Email</label>
                        <input type="email" placeholder="Your Email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" placeholder="Subject" id="subject" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea placeholder="Message" id="message" name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required></textarea>
                    </div>
                    
                    <button type="submit" class="w-full px-6 py-3 text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function toggleFAQ(element) {
        // Toggle the active state of the button
        element.classList.toggle('active');
        
        // Find the content element
        const content = element.nextElementSibling;
        
        // Toggle the content visibility
        if (content.style.maxHeight) {
            content.style.maxHeight = null;
            content.classList.add('hidden');
            element.querySelector('svg').classList.remove('rotate-180');
        } else {
            content.classList.remove('hidden');
            content.style.maxHeight = content.scrollHeight + "px";
            element.querySelector('svg').classList.add('rotate-180');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Animation on scroll
        const animateElements = document.querySelectorAll('.animate-on-scroll');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        animateElements.forEach(element => {
            observer.observe(element);
        });
        
        // Event countdown timer
        const countdownElement = document.getElementById('event-countdown');
        if (countdownElement) {
            const endDateStr = "{{ $event->end_date ? $event->end_date->format('Y-m-d H:i:s') : '' }}";
            if (endDateStr) {
                const endDate = new Date(endDateStr.replace(/-/g, '/'));
                
                // Update countdown every second
                const countdownInterval = setInterval(function() {
                    const now = new Date();
                    const timeRemaining = endDate - now;
                    
                    // If countdown is over
                    if (timeRemaining <= 0) {
                        clearInterval(countdownInterval);
                        countdownElement.innerHTML = '<div class="text-xl font-bold text-red-600 text-center">This event has ended</div>';
                        return;
                    }
                    
                    // Calculate time units
                    const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
                    
                    // Update the countdown display
                    countdownElement.querySelector('.days').textContent = days.toString().padStart(2, '0');
                    countdownElement.querySelector('.hours').textContent = hours.toString().padStart(2, '0');
                    countdownElement.querySelector('.minutes').textContent = minutes.toString().padStart(2, '0');
                    countdownElement.querySelector('.seconds').textContent = seconds.toString().padStart(2, '0');
                }, 1000);
            }
        }
    });
</script>
@endpush