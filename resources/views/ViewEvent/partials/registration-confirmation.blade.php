@extends('ViewEvent.layouts.layout')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-8">
            <!-- Success Checkmark Animation -->
            <div class="flex justify-center mb-8">
                <div class="checkmark-circle">
                    <div class="checkmark-circle-bg"></div>
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle-check" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
            </div>

            <!-- Confirmation Message -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Thank you for your registration</h2>
                <p class="text-gray-600 mb-2">We have received your registration successfully.</p>
                <p class="text-gray-600 mb-2"><strong>Please check your email</strong> (including your <strong>spam</strong> or <strong>junk</strong> folder) for a confirmation message. Our team will get back to you within 24 hours.</p>
                <p class="text-gray-600 mb-6">We look forward to seeing you at the event.</p>

                <!-- Return Button -->
                <div class="mt-8">
                    <a href="{{ route('showEventPage', ['event_id' => $event->id, 'event_slug' => $event->slug]) }}"
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                        Back to event page
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Checkmark Animation Styles */
.checkmark-circle {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto;
}

.checkmark-circle-bg {
    position: absolute;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: #f0fdf4; /* Light green background */
    animation: scale-in 0.3s ease-out;
}

.checkmark-circle-check {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #22c55e; /* Green color */
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    animation-delay: 0.2s;
}

.checkmark {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #22c55e; /* Green color */
    stroke-miterlimit: 10;
    box-shadow: 0 0 0 rgba(34, 197, 94, 0.4); /* Green shadow */
    animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
}

.checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

@keyframes fill {
    100% {
        box-shadow: 0 0 0 15px rgba(34, 197, 94, 0.1); /* Green shadow */
    }
}

@keyframes scale-in {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
@endsection
