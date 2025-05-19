@extends('ViewEvent.layouts.layout')

@section('content')
    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 z-0 opacity-10">
            <div class="absolute inset-0 bg-gradient-to-b from-primary-100 to-transparent"></div>
            <div class="absolute inset-0 bg-[url('/images/pattern.svg')] bg-repeat opacity-30"></div>
        </div>
        
        <div class="container relative z-10 px-4 mx-auto">
            <div class="grid items-center grid-cols-1 gap-12 lg:grid-cols-2">
                <div class="transition-all duration-700 ease-out translate-y-8 opacity-0 animate-on-scroll rtl:text-right">
                    <!-- Breadcrumb -->
                    <nav class="flex mb-6 rtl:flex-row-reverse" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
                            <li class="inline-flex items-center">
                                <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                                    {{ __('messages.all_events') }}
                                </a>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 rtl:mr-1 rtl:ml-0">{{ __('messages.event_name') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    
                    <!-- Event Title -->
                    <h1 class="mb-6 text-4xl font-bold leading-tight text-gray-900 md:text-5xl lg:text-6xl">
                        {{ __('messages.event_name') }}
                    </h1>
                    
                    <!-- Event Description -->
                    <div class="p-6 mb-8 shadow-sm bg-white/70 backdrop-blur-sm rounded-xl">
                        <p class="text-lg text-gray-700">
                            {{ __('messages.event_description') }}
                        </p>
                    </div>
                    
                    <!-- Event Date/Time -->
                    <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center rtl:flex-row-reverse">
                        <!-- Start Date -->
                        <div class="flex items-center">
                            <div class="flex overflow-hidden bg-white rounded-lg shadow-md rtl:flex-row-reverse">
                                <div class="flex items-center justify-center w-20 h-20 p-4 text-white bg-primary-600">
                                    <span class="text-3xl font-bold">15</span>
                                </div>
                                <div class="p-4">
                                    <div class="text-sm text-gray-600">
                                        {{ __('messages.october') }} 2023
                                    </div>
                                    <div class="font-semibold">
                                        09:00
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Arrow -->
                        <div class="items-center hidden px-4 sm:flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>
                        
                        <!-- End Date -->
                        <div class="flex items-center">
                            <div class="flex overflow-hidden bg-white rounded-lg shadow-md rtl:flex-row-reverse">
                                <div class="flex items-center justify-center w-20 h-20 p-4 text-white bg-primary-600">
                                    <span class="text-3xl font-bold">17</span>
                                </div>
                                <div class="p-4">
                                    <div class="text-sm text-gray-600">
                                        {{ __('messages.october') }} 2023
                                    </div>
                                    <div class="font-semibold">
                                        18:00
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Event Image Placeholder -->
                <div class="transition-all duration-700 ease-out delay-300 translate-y-8 opacity-0 animate-on-scroll">
                    <div class="overflow-hidden bg-gray-200 shadow-lg rounded-2xl">
                        <!-- Image dimensions: 600px width x 400px height -->
                        <div class="w-full h-[400px] flex items-center justify-center">
                            <p class="text-center text-gray-500">
                                {{ __('messages.event_image') }}<br>
                                <span class="text-sm">600 x 400 px</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Registration Forms Section -->
    @include('ViewEvent.partials.registration-forms')
@endsection