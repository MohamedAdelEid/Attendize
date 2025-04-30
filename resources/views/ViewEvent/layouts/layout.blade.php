<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.event_name') }} - {{ __('messages.platform_name') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        /* Font settings based on language */
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
        });
    </script>
</body>
</html>
