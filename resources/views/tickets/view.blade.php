@extends('ViewEvent.layouts.layout')

@push('styles')
<style>
@media print {
    header, footer, .ticket-actions, .no-print { display: none !important; }
    body { background: #fff !important; }
    .ticket-print-wrapper { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
    main { padding-top: 0 !important; }
}
</style>
@endpush

@section('content')
<div class="max-w-4xl px-4 py-12 mx-auto ticket-print-wrapper">
    <div class="overflow-hidden bg-white shadow-lg rounded-xl">
        <div class="p-6 md:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6 ticket-actions">
                <h1 class="text-2xl font-bold text-gray-900">Your Event Ticket</h1>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print();" class="inline-flex items-center px-4 py-2 text-white transition-colors rounded-md bg-primary-600 hover:bg-primary-700 print:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H6a2 2 0 01-2-2v-4a2 2 0 012-2h10a2 2 0 012 2v4a2 2 0 01-2 2h-2m-4-4v.01M17 17v-2m0-4v-2m0 4h.01M7 17h.01M7 13h.01" />
                        </svg>
                        Print Ticket
                    </button>
                    <a href="{{ route('downloadTicket', ['token' => $user->ticket_token]) }}" class="inline-flex items-center px-4 py-2 text-white transition-colors rounded-md bg-primary-600 hover:bg-primary-700 no-print">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="p-6 mb-6 rounded-lg bg-gray-50">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $event->title }}</h2>
                        <p class="text-gray-600">{{ $event->start_date->format('F j, Y') }} - {{ $event->end_date->format('F j, Y') }}</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                            Approved
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
                <div>
                    <h3 class="mb-3 text-lg font-medium text-gray-900">Attendee Information</h3>
                    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                @if($user->phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->phone }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Registration Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->registration->name }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-lg font-medium text-gray-900">Registration Code</h3>
                    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg">
                        <div class="px-4 py-5 text-center sm:p-6">
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500">Your Unique Code</span>
                                <div class="inline-block px-4 py-2 mt-2 text-3xl font-bold tracking-wider rounded-md text-primary-600 bg-primary-50">
                                    {{ $user->unique_code }}
                                </div>
                            </div>

                            <div class="mt-6">
                                <span class="block mb-3 text-sm font-medium text-gray-500">QR Code</span>
                                <img src="{{ asset('storage/' . $user->qr_code_path) }}" alt="QR Code" class="w-48 h-48 mx-auto">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-l-4 border-blue-400 rounded bg-blue-50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Please present this ticket (printed or digital) at the event entrance. Your unique code and QR code will be scanned for check-in.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
