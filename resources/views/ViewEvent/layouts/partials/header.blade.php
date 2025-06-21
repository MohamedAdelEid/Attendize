<header class="fixed z-50 w-full transition-all duration-300 bg-white/90 backdrop-blur-md shadow-sm" style="background-color:white !important;">
    <div class="container flex items-center justify-between px-4 py-4 mx-auto">
        

       <!-- <nav class="items-center hidden space-x-8 md:flex rtl:space-x-reverse">
            <a href="#registration" class="font-medium text-gray-700 transition-colors hover:text-primary-600">
                {{ __('messages.registration') }}
            </a>
            <a href="#sponsors" class="font-medium text-gray-700 transition-colors hover:text-primary-600">
                {{ __('messages.sponsors') }}
            </a>
            <a href="{{ route('events.about-us', ['event' => $event->id]) }}" class="font-medium text-gray-700 transition-colors hover:text-primary-600">
                {{ __('messages.about_us') }}
            </a>
        </nav>
		-->

        <div class="flex items-center space-x-4 rtl:space-x-reverse">
            <!-- <div class="relative">
                <button id="language-switcher" class="flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <span>{{ app()->getLocale() == 'en' ? 'English' : 'العربية' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 rtl:mr-1 rtl:ml-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="language-dropdown" class="absolute right-0 z-10 hidden w-48 py-1 mt-2 bg-white rounded-md shadow-lg rtl:left-0 rtl:right-auto">
                    <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">English</a>
                    <a href="{{ route('language.switch', ['locale' => 'ar']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">العربية</a>
                </div>
            </div> 

            <button id="mobile-menu-button" class="flex items-center md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>-->
        </div>
    </div>

    
</header>