@extends('ViewEvent.layouts.layout')

@section('content')
    <!-- Hero Section -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 z-0 opacity-10">
            <div class="absolute inset-0 bg-gradient-to-b from-primary-100 to-transparent"></div>
            <div class="absolute inset-0 bg-[url('/images/pattern.svg')] bg-repeat opacity-30"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate-on-scroll opacity-0 translate-y-8 transition-all duration-700 ease-out rtl:text-right">
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
                                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 rtl:mr-1 rtl:ml-0">{{ __('messages.event_name') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <!-- Event Title -->
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        {{ __('messages.event_name') }}
                    </h1>

                    <!-- Event Description -->
                    <div class="bg-primary-100 backdrop-blur-sm rounded-xl p-6 mb-8 shadow-sm">
                        <p class="text-lg text-gray-700">
                            {{ __('messages.event_description') }}
                        </p>
                    </div>

                    <!-- Event Date/Time -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 rtl:flex-row-reverse">
                        <!-- Start Date -->
                        <div class="flex items-center">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden flex rtl:flex-row-reverse">
                                <div class="bg-primary-600 text-white flex items-center justify-center p-4 w-20 h-20">
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
                        <div class="hidden sm:flex items-center px-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>

                        <!-- End Date -->
                        <div class="flex items-center">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden flex rtl:flex-row-reverse">
                                <div class="bg-primary-600 text-white flex items-center justify-center p-4 w-20 h-20">
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
                <div class="animate-on-scroll opacity-0 translate-y-8 transition-all duration-700 delay-300 ease-out">
                    <div class="bg-gray-200 rounded-2xl overflow-hidden shadow-lg">
                        <!-- Image dimensions: 600px width x 400px height -->
                        <div class="w-full h-[400px] flex items-center justify-center">
                            <p class="text-gray-500 text-center">
                                {{ __('messages.event_image') }}<br>
                                <span class="text-sm">600 x 400 px</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
