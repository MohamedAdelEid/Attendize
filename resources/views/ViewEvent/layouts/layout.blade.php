<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'rtl' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Four-Links - {{ $event->title }}</title>
    
    
    <meta property="og:site_name" content="{{ $event->title }}">
	<meta property="og:title" content="{{ $event->title }}" />
	<meta property="og:description" content="To register for the Invitation, you will need to fill the Registration Form. Please enter the details required below. 
	Once you've signed up we'll check your information and back to you with confirmation." />
	<meta property="og:image" itemprop="image" content="{{ asset($event->bg_image_path) }}">
	<meta property="og:type" content="website" />
	
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Custom Toastr Styling */
        .toast {
            border-radius: 5px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            font-family: inherit !important;
        }
        
        .toast-success {
            background-color: #10B981 !important; /* Tailwind green-500 */
        }
        
        .toast-error {
            background-color: #EF4444 !important; /* Tailwind red-500 */
        }
        
        .toast-info {
            background-color: #3B82F6 !important; /* Tailwind blue-500 */
        }
        
        .toast-warning {
            background-color: #F59E0B !important; /* Tailwind amber-500 */
        }
        
        .toast-title {
            font-weight: 600 !important;
            margin-bottom: 4px !important;
        }
        
        .toast-message {
            font-size: 0.95rem !important;
        }
        
        .toast-close-button {
            color: #fff !important;
            opacity: 0.7 !important;
        }
        
        .toast-close-button:hover {
            opacity: 1 !important;
        }
        
        .toast-progress {
            height: 4px !important;
            opacity: 0.7 !important;
        }
    </style>
    
    <!-- jQuery (required for Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @stack('styles')
    <style>
       
        html[lang="en"] body {
            font-family: 'Poppins', sans-serif;
        }
        
        html[lang="ar"] body {
            font-family: 'Cairo', sans-serif;
        }
        
        /* Animation classes */
        .animate-on-scroll {
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .animate-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        /* RTL support */
        [dir="rtl"] .rtl\:space-x-reverse > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 1;
        }

        [dir="rtl"] .rtl\:mr-1 {
            margin-right: 0.25rem;
        }

        [dir="rtl"] .rtl\:ml-0 {
            margin-left: 0;
        }

        [dir="rtl"] .rtl\:right-auto {
            right: auto;
        }

        [dir="rtl"] .rtl\:left-0 {
            left: 0;
        }
        
        [dir="rtl"] .rtl\:text-right {
            text-align: right;
        }
        
        [dir="rtl"] .rtl\:text-left {
            text-align: left;
        }
        
        [dir="rtl"] .rtl\:flex-row-reverse {
            flex-direction: row-reverse;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .dark ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        
        /* Primary color classes */
        .bg-primary-50 { background-color: #f0f9ff; }
        .bg-primary-100 { background-color: #e0f2fe; }
        .bg-primary-200 { background-color: #bae6fd; }
        .bg-primary-300 { background-color: #7dd3fc; }
        .bg-primary-400 { background-color: #38bdf8; }
        .bg-primary-500 { background-color: #0ea5e9; }
        .bg-primary-600 { background-color: #0284c7; }
        .bg-primary-700 { background-color: #0369a1; }
        .bg-primary-800 { background-color: #075985; }
        .bg-primary-900 { background-color: #0c4a6e; }
        
        .text-primary-50 { color: #f0f9ff; }
        .text-primary-100 { color: #e0f2fe; }
        .text-primary-200 { color: #bae6fd; }
        .text-primary-300 { color: #7dd3fc; }
        .text-primary-400 { color: #38bdf8; }
        .text-primary-500 { color: #0ea5e9; }
        .text-primary-600 { color: #0284c7; }
        .text-primary-700 { color: #0369a1; }
        .text-primary-800 { color: #075985; }
        .text-primary-900 { color: #0c4a6e; }
        
        .hover\:text-primary-600:hover { color: #0284c7; }
        .hover\:bg-primary-600:hover { background-color: #0284c7; }
        
        /* Hide scrollbar but allow scrolling */
        .hide-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        
        .hide-scrollbar::-webkit-scrollbar {
            display: none;  /* Chrome, Safari, Opera */
        }
    </style>
    <style>
        price-updated {
  transition: all 0.3s ease;
  transform: scale(1.1);
}

/* Hide scrollbar for the registration cards container */
.hide-scrollbar {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}

.hide-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari and Opera */
}

/* Animation for elements */
.animate-on-scroll {
  animation: fadeInUp 0.8s ease forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom focus styles */
input:focus,
select:focus,
textarea:focus {
  @apply ring-2 ring-primary-500 ring-opacity-50 border-primary-500;
}
input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            height: 3.5rem;
            font-size: 1.125rem;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.2s ease;
        }

        textarea {
            height: auto;
            min-height: 6rem;
        }

        /* Custom file input styling */
        input[type="file"] {
            padding: 0.5rem;
            height: auto;
        }

        /* Enhance focus states */
        input:focus,
        select:focus,
        textarea:focus {
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
        }

        /* Form section styling */
        .form-section {
            background-color: #f9fafb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Registration image styling */
        .registration-image {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
        }
/* Custom file input styling */
input[type="file"] {
  @apply cursor-pointer;
}

/* Custom styles for the countdown timer */
.countdown-timer .days,
.countdown-timer .hours,
.countdown-timer .minutes,
.countdown-timer .seconds {
  @apply transition-all duration-300;
}
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    .registration-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .registration-card:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
    }
</style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 transition-colors duration-300">
    <div id="app" class="min-h-screen flex flex-col">
        @include('ViewEvent.layouts.partials.header')
        
        <main class="flex-grow">
            @yield('content')
        </main>
        
        @include('ViewEvent.layouts.partials.footer')
    </div>
    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Extract end date from PHP variable using data attribute
        const endDateStr = "{{ $event->end_date->format('Y-m-d H:i:s') }}";
        const endDate = new Date(endDateStr.replace(/-/g, '/'));
        
        // Get countdown element
        const countdownElement = document.getElementById('event-countdown');
        
        // Update countdown every second
        const countdownInterval = setInterval(function() {
            const now = new Date();
            const timeRemaining = endDate - now;
            
            // If countdown is over
            if (timeRemaining <= 0) {
                clearInterval(countdownInterval);
                countdownElement.innerHTML = '<div class="text-xl font-bold text-red-600">This event has ended</div>';
                return;
            }
            
            // Calculate time units
            const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
            
            // Update the countdown display
            countdownElement.innerHTML = `
                <div class="grid grid-cols-4 gap-4 text-center">
                    <div class="flex flex-col">
                        <div class="flex items-center justify-center px-4 py-6 text-3xl font-bold bg-white rounded-lg shadow-md text-primary-600">
                            ${days.toString().padStart(2, '0')}
                        </div>
                        <span class="mt-2 text-sm font-medium text-gray-600">Days</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center justify-center px-4 py-6 text-3xl font-bold bg-white rounded-lg shadow-md text-primary-600">
                            ${hours.toString().padStart(2, '0')}
                        </div>
                        <span class="mt-2 text-sm font-medium text-gray-600">Hours</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center justify-center px-4 py-6 text-3xl font-bold bg-white rounded-lg shadow-md text-primary-600">
                            ${minutes.toString().padStart(2, '0')}
                        </div>
                        <span class="mt-2 text-sm font-medium text-gray-600">Minutes</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center justify-center px-4 py-6 text-3xl font-bold bg-white rounded-lg shadow-md text-primary-600">
                            ${seconds.toString().padStart(2, '0')}
                        </div>
                        <span class="mt-2 text-sm font-medium text-gray-600">Seconds</span>
                    </div>
                </div>
            `;
        }, 1000);
    });
</script>
 <!-- Add toast notifications inline -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configure Toastr
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            @if(session('error'))
                toastr.error("{{ session('error') }}", "Error");
            @endif
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize countdown timer
            initCountdownTimer();

            // Load conferences via AJAX
            if (document.getElementById('conference_id')) {
                loadConferences();
            }
        });

        function initCountdownTimer() {
            const countdownTimer = document.querySelector('.countdown-timer');
            if (!countdownTimer) return;

            const endDate = new Date(countdownTimer.dataset.end).getTime();

            // Update the countdown every second
            const countdownInterval = setInterval(function() {
                // Get current date and time
                const now = new Date().getTime();

                // Calculate the time remaining
                const distance = endDate - now;

                // Calculate days, hours, minutes, and seconds
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Update the timer display
                countdownTimer.querySelector('.days').textContent = days.toString().padStart(2, '0');
                countdownTimer.querySelector('.hours').textContent = hours.toString().padStart(2, '0');
                countdownTimer.querySelector('.minutes').textContent = minutes.toString().padStart(2, '0');
                countdownTimer.querySelector('.seconds').textContent = seconds.toString().padStart(2, '0');

                // If the countdown is finished, display expired message
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    const container = countdownTimer.closest('.countdown-container');
                    if (container) {
                        container.innerHTML =
                        '<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative" role="alert">Registration period has ended</div>';
                    }
                }
            }, 1000);
        }

        // Load conferences via AJAX
        function loadConferences() {
            const conferenceSelect = document.getElementById('conference_id');
            const categoryId = {{ $registration->category_id ?? 'null' }};

            if (!conferenceSelect || !categoryId) return;

            // Show loading state
            conferenceSelect.innerHTML = '<option value="">-- Loading Conferences... --</option>';
            conferenceSelect.disabled = true;

            // Fetch conferences for this category
            fetch(`/e/api/categories/${categoryId}/conferences`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Clear loading option
                    conferenceSelect.innerHTML = '<option value="">-- Select Conference --</option>';

                    if (data.conferences && data.conferences.length > 0) {
                        data.conferences.forEach(conference => {
                            // Format the price to 2 decimal places
                            const formattedPrice = parseFloat(conference.price).toFixed(2);

                            // Include the price in the option text
                            const optionText = `${conference.name} - $${formattedPrice}`;

                            const option = new Option(optionText, conference.id);
                            option.dataset.price = conference.price;
                            option.dataset.name = conference.name;
                            conferenceSelect.add(option);
                        });

                        // Enable the select
                        conferenceSelect.disabled = false;

                        // Add change event listener
                        conferenceSelect.addEventListener('change', function() {
                            loadProfessions(this.value);
                            updateFeeCard();
                        });
                    } else {
                        conferenceSelect.innerHTML = '<option value="">No conferences available</option>';
                        document.getElementById('conference-error').textContent =
                            'No conferences are currently available for this registration.';
                        document.getElementById('conference-error').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading conferences:', error);
                    conferenceSelect.innerHTML = '<option value="">Error loading conferences</option>';
                    document.getElementById('conference-error').textContent =
                        'Failed to load conferences. Please try again later.';
                    document.getElementById('conference-error').classList.remove('hidden');
                });
        }

        // Load professions via AJAX
        function loadProfessions(conferenceId) {
            const professionSelect = document.getElementById('profession_id');

            if (!professionSelect || !conferenceId) {
                professionSelect.disabled = true;
                professionSelect.innerHTML = '<option value="">-- Select Profession --</option>';
                return;
            }

            // Show loading state
            professionSelect.innerHTML = '<option value="">-- Loading Professions... --</option>';
            professionSelect.disabled = true;

            // Fetch professions for the selected conference
            fetch(`/e/api/conferences/${conferenceId}/professions`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Clear loading option
                    professionSelect.innerHTML = '<option value="">-- Select Profession --</option>';

                    if (data.professions && data.professions.length > 0) {
                        data.professions.forEach(profession => {
                            const option = new Option(profession.name, profession.id);
                            professionSelect.add(option);
                        });

                        // Enable the select
                        professionSelect.disabled = false;
                        document.getElementById('profession-error').classList.add('hidden');
                    } else {
                        professionSelect.innerHTML = '<option value="">No professions available</option>';
                        document.getElementById('profession-error').textContent =
                            'No professions are available for this conference.';
                        document.getElementById('profession-error').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading professions:', error);
                    professionSelect.innerHTML = '<option value="">Error loading professions</option>';
                    document.getElementById('profession-error').textContent =
                        'Failed to load professions. Please try again later.';
                    document.getElementById('profession-error').classList.remove('hidden');
                });
        }

        // Update the fee card with selected conference details
        function updateFeeCard() {
            const conferenceSelect = document.getElementById('conference_id');
            const feeCard = document.getElementById('fee-card');
            const feeCardConference = document.getElementById('fee-card-conference');
            const feeCardAmount = document.getElementById('fee-card-amount');

            if (!conferenceSelect || !feeCard || !feeCardConference || !feeCardAmount) return;

            const selectedOption = conferenceSelect.options[conferenceSelect.selectedIndex];

            if (selectedOption && selectedOption.value) {
                const price = selectedOption.dataset.price || 0;
                const conferenceName = selectedOption.dataset.name || 'Selected Conference';

                // Update the fee card content
                feeCardConference.textContent = conferenceName;
                feeCardAmount.textContent = `$${parseFloat(price).toFixed(2)}`;

                // Show the fee card with animation
                feeCard.classList.remove('hidden');
                feeCard.classList.add('block');

                // Add animation effect
                feeCardAmount.classList.add('scale-110');
                setTimeout(() => {
                    feeCardAmount.classList.remove('scale-110');
                }, 500);
            } else {
                // Hide the fee card if no conference is selected
                feeCard.classList.remove('block');
                feeCard.classList.add('hidden');
            }
        }
    </script>
   
    <!-- Animation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animate elements when they enter the viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
            
            // Toggle mobile menu
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Language switcher
            const languageSwitcher = document.getElementById('language-switcher');
            const languageDropdown = document.getElementById('language-dropdown');
            
            if (languageSwitcher && languageDropdown) {
                languageSwitcher.addEventListener('click', function() {
                    languageDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!languageSwitcher.contains(event.target) && !languageDropdown.contains(event.target)) {
                        languageDropdown.classList.add('hidden');
                    }
                });
            }
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80, // Adjust for header height
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Header scroll effect
            const header = document.querySelector('header');
            if (header) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        header.classList.add('py-2', 'shadow-md');
                        header.classList.remove('py-4');
                    } else {
                        header.classList.add('py-4');
                        header.classList.remove('py-2', 'shadow-md');
                    }
                });
            }
            
            // Registration cards horizontal scroll
            const scrollLeftBtn = document.getElementById('scroll-left');
            const scrollRightBtn = document.getElementById('scroll-right');
            const cardsContainer = document.getElementById('registration-cards-container');
            
            if (scrollLeftBtn && scrollRightBtn && cardsContainer) {
                // Calculate scroll amount based on card width
                const scrollAmount = () => {
                    const card = document.querySelector('.registration-card');
                    if (card) {
                        return card.offsetWidth + 24; // card width + margin
                    }
                    return 300; // fallback value
                };
                
                scrollLeftBtn.addEventListener('click', () => {
                    cardsContainer.scrollBy({
                        left: -scrollAmount(),
                        behavior: 'smooth'
                    });
                });
                
                scrollRightBtn.addEventListener('click', () => {
                    cardsContainer.scrollBy({
                        left: scrollAmount(),
                        behavior: 'smooth'
                    });
                });
                
                // Show/hide scroll buttons based on scroll position
                cardsContainer.addEventListener('scroll', () => {
                    if (cardsContainer.scrollLeft <= 0) {
                        scrollLeftBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        scrollLeftBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                    
                    if (cardsContainer.scrollLeft + cardsContainer.clientWidth >= cardsContainer.scrollWidth - 10) {
                        scrollRightBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        scrollRightBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
                
                // Initial check
                if (cardsContainer.scrollLeft <= 0) {
                    scrollLeftBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
            
            // Countdown timers
            const countdowns = document.querySelectorAll('.countdown');
            
            function updateCountdowns() {
                const now = new Date().getTime();
                
                countdowns.forEach(countdown => {
                    const endDateStr = countdown.getAttribute('data-end');
                    if (!endDateStr) return;
                    
                    const endDate = new Date(endDateStr).getTime();
                    const timeLeft = endDate - now;
                    
                    if (timeLeft <= 0) {
                        countdown.innerHTML = `
                            <div class="text-xs text-gray-600 mb-1">${countdown.getAttribute('data-closed-text') || '{{ __('messages.registration_closed') }}'}</div>
                            <div class="text-sm font-semibold text-gray-800">{{ __('messages.no_longer_available') }}</div>
                        `;
                        return;
                    }
                    
                    // Calculate time units
                    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    
                    // Update countdown elements
                    const daysEl = countdown.querySelector('.days');
                    const hoursEl = countdown.querySelector('.hours');
                    const minutesEl = countdown.querySelector('.minutes');
                    
                    if (daysEl) daysEl.textContent = days;
                    if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
                    if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
                });
            }
            
            // Initial update
            updateCountdowns();
            
            // Update every minute
            setInterval(updateCountdowns, 60000);
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Horizontal scroll buttons
        const scrollLeftBtn = document.getElementById('scroll-left');
        const scrollRightBtn = document.getElementById('scroll-right');
        const container = document.getElementById('registration-cards-container');
        
        if (scrollLeftBtn && scrollRightBtn && container) {
            const scrollAmount = 300;
            
            scrollLeftBtn.addEventListener('click', function() {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });
            
            scrollRightBtn.addEventListener('click', function() {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });
        }
        
        // Initialize countdown timers
        const countdownTimers = document.querySelectorAll('.countdown');
        
        countdownTimers.forEach(timer => {
            const endDate = new Date(timer.dataset.end).getTime();
            
            // Update the countdown every second
            const countdownInterval = setInterval(function() {
                // Get current date and time
                const now = new Date().getTime();
                
                // Calculate the time remaining
                const distance = endDate - now;
                
                // Calculate days, hours, minutes, and seconds
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // Update the timer display
                const daysElement = timer.querySelector('.days');
                const hoursElement = timer.querySelector('.hours');
                const minutesElement = timer.querySelector('.minutes');
                const secondsElement = timer.querySelector('.seconds');
                
                if (daysElement) daysElement.textContent = days.toString().padStart(2, '0');
                if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
                if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
                if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
                
                // If the countdown is finished, display expired message
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    
                    // Check if this is a "opens in" countdown or a "time remaining" countdown
                    const isOpeningCountdown = timer.querySelector('.mb-1')?.textContent.includes('opens');
                    
                    if (isOpeningCountdown) {
                        // Reload the page to show the now-open registration
                        window.location.reload();
                    } else {
                        // Show expired message
                        timer.innerHTML = `
                            <div class="mb-1 text-xs text-gray-600">${timer.querySelector('.mb-1').textContent}</div>
                            <div class="text-sm font-semibold text-red-600">${__('messages.registration_closed')}</div>
                        `;
                        
                        // Disable the register button
                        const card = timer.closest('.registration-card');
                        const registerBtn = card?.querySelector('a.bg-primary-600');
                        
                        if (registerBtn) {
                            registerBtn.classList.remove('bg-primary-600', 'hover:bg-primary-700');
                            registerBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                            registerBtn.setAttribute('disabled', 'true');
                            registerBtn.textContent = __('messages.registration_closed');
                        }
                    }
                }
            }, 1000);
        });
        
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
    });
</script>
@if(session('success') || session('error') || session('info') || session('warning'))
    <script>
        // Configure Toastr options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        
        // Display messages
        @if(session('success'))
            toastr.success('{{ session('success') }}', 'Success');
        @endif
        
        @if(session('error'))
            toastr.error('{{ session('error') }}', 'Error');
        @endif
        
        @if(session('info'))
            toastr.info('{{ session('info') }}', 'Information');
        @endif
        
        @if(session('warning'))
            toastr.warning('{{ session('warning') }}', 'Warning');
        @endif
    </script>
@endif
</body>
</html>