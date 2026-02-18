@extends('ViewEvent.layouts.symposium-layout')

@section('title', isset($event) ? 'Program - ' . $event->title : 'Program')

@section('content')
<main class="min-h-screen">
    @php
        $navItemCount = 4 + (isset($landingUserTypes) ? $landingUserTypes->count() : 0);
        $navBreakpoint = $navItemCount > 5 ? 'lg' : 'md';
    @endphp
    <header id="header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 header-sticky py-4 header-scrolled">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}" class="flex items-center gap-4" aria-label="Home">
                    <img src="{{ asset('images/sgss-logo.png') }}" alt="SGSS" class="bg-white w-12 h-12 md:w-16 md:h-14 object-contain" onerror="this.style.display='none'">
                    <img src="https://cdn4.premiumread.com/?url=https://www.al-madina.com/uploads/images/2020/06/24/1786780.jpg&w=800&q=100&f=jpg" alt="Logo 2" class="h-12 w-12 md:h-14 md:w-14 object-contain" onerror="this.style.display='none'">
                </a>
            </div>
            <nav class="hidden {{ $navBreakpoint === 'lg' ? 'lg:flex' : 'md:flex' }} items-center gap-8">
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}" class="link-gold font-medium text-foreground">Home</a>
                @if(isset($landingUserTypes) && $landingUserTypes->count() > 0)
                    @foreach($landingUserTypes as $ut)
                        @if($ut->options && $ut->options->count() > 0)
                            <div class="relative group inline-block committee-dropdown">
                                <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug]) }}" class="link-gold font-medium text-foreground inline-flex items-center gap-1">
                                    {{ $ut->name }}
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </a>
                                <div class="absolute left-0 top-full pt-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[180px]">
                                    <div class="rounded-lg shadow-xl border border-border overflow-hidden" style="background: linear-gradient(145deg, hsl(220, 55%, 14%) 0%, hsl(220, 55%, 10%) 100%);">
                                        <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug]) }}" class="block px-4 py-3 text-left font-medium hover:bg-primary/20 transition-colors" style="color: hsl(45, 70%, 50%); border-bottom: 1px solid hsl(220, 40%, 25%);">{{ $ut->name }} (All)</a>
                                        @foreach($ut->options as $opt)
                                            <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug, 'option_slug' => $opt->slug]) }}" class="block px-4 py-3 text-left hover:bg-primary/20 transition-colors text-foreground">{{ $opt->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug]) }}" class="link-gold font-medium text-foreground">{{ $ut->name }}</a>
                        @endif
                    @endforeach
                @endif
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#pricing" class="link-gold font-medium text-foreground">Fees</a>
                <span class="link-gold font-medium text-foreground border-b-2 border-primary pb-1">Program</span>
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#footer" class="link-gold font-medium text-foreground">Contact Us</a>
            </nav>
            <div class="flex items-center gap-2 {{ $navBreakpoint === 'lg' ? 'lg:hidden' : 'md:hidden' }}">
                <button type="button" id="mobile-menu-toggle" class="p-2 rounded-lg text-foreground hover:bg-secondary/50" aria-expanded="false" aria-label="Open menu">
                    <svg id="mobile-menu-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg id="mobile-menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#registration" class="btn-gold text-sm font-bold px-4 py-2">Register Now</a>
            </div>
        </div>
    </header>

    <div id="mobile-menu" class="{{ $navBreakpoint === 'lg' ? 'lg:hidden' : 'md:hidden' }} hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; min-height: 100vh; z-index: 9999;">
        <div id="mobile-menu-backdrop" role="button" tabindex="0" aria-label="Close menu" style="position: absolute; inset: 0; background: rgba(19, 28, 46, 0.92); backdrop-filter: blur(8px); cursor: pointer;"></div>
        <div id="mobile-menu-panel" style="position: absolute; top: 0; right: 0; width: 280px; max-width: 85vw; height: 100%; min-height: 100vh; background: linear-gradient(145deg, hsl(220, 55%, 14%) 0%, hsl(220, 55%, 10%) 100%); border-left: 1px solid hsl(220, 40%, 25%); box-shadow: -8px 0 32px rgba(0,0,0,0.4); overflow-y: auto;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid hsl(220, 40%, 25%); display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 1.125rem; font-weight: 600; color: hsl(0, 0%, 98%);">Menu</span>
                <button type="button" id="mobile-menu-close" class="p-2 rounded-lg hover:opacity-80" style="color: hsl(0, 0%, 98%);" aria-label="Close menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav style="padding: 1rem 1.5rem; display: flex; flex-direction: column;">
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}" class="mobile-nav-link block py-3" style="color: hsl(45, 70%, 50%); font-weight: 500; border-bottom: 1px solid hsl(220, 40%, 25%);">Home</a>
                @if(isset($landingUserTypes) && $landingUserTypes->count() > 0)
                    @foreach($landingUserTypes as $ut)
                        @if($ut->options && $ut->options->count() > 0)
                            <div class="mobile-committee-wrap" style="border-bottom: 1px solid hsl(220, 40%, 25%);">
                                <button type="button" class="mobile-nav-link block w-full text-left py-3 flex items-center justify-between" style="color: hsl(45, 70%, 50%); font-weight: 500;" data-committee-toggle="{{ $ut->id }}">
                                    {{ $ut->name }}
                                    <svg class="committee-arrow w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="mobile-committee-sub hidden" data-committee-panel="{{ $ut->id }}" style="padding-left: 1rem; padding-bottom: 0.5rem;">
                                    <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug]) }}" class="block py-2 text-sm" style="color: hsl(0, 0%, 90%);">{{ $ut->name }} (All)</a>
                                    @foreach($ut->options as $opt)
                                        <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug, 'option_slug' => $opt->slug]) }}" class="block py-2 text-sm" style="color: hsl(0, 0%, 90%);">{{ $opt->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug]) }}" class="mobile-nav-link block py-3" style="color: hsl(45, 70%, 50%); font-weight: 500; border-bottom: 1px solid hsl(220, 40%, 25%);">{{ $ut->name }}</a>
                        @endif
                    @endforeach
                @endif
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#pricing" class="mobile-nav-link block py-3" style="color: hsl(45, 70%, 50%); font-weight: 500; border-bottom: 1px solid hsl(220, 40%, 25%);">Fees</a>
                <span class="block py-3" style="color: hsl(45, 70%, 50%); font-weight: 600; border-bottom: 1px solid hsl(220, 40%, 25%);">Program</span>
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#footer" class="mobile-nav-link block py-3" style="color: hsl(45, 70%, 50%); font-weight: 500;">Contact Us</a>
            </nav>
            <div style="padding: 1.5rem;">
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#registration" class="mobile-nav-link w-full text-center font-bold py-3 rounded-lg block" style="background: linear-gradient(135deg, hsl(45, 75%, 45%) 0%, hsl(45, 70%, 55%) 100%); color: hsl(220, 60%, 8%);">Register Now</a>
            </div>
        </div>
    </div>

    {{-- Coming Soon Section --}}
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-24">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('assets/images/hero-bg.jpg') }}');">
            <div class="absolute inset-0 bg-gradient-to-b from-background/90 via-background/70 to-background"></div>
        </div>
        <div class="relative z-10 container mx-auto px-4 py-20 text-center">
            <div class="max-w-3xl mx-auto">
                <div class="inline-block mb-6 opacity-0 animate-fade-up">
                    <span class="px-4 py-2 rounded-full border border-primary/30 bg-secondary/50 text-primary text-sm font-medium">Program</span>
                </div>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6 opacity-0 animate-fade-up delay-100">
                    <span class="text-gold-gradient">Coming Soon</span>
                </h1>
                <p class="text-xl md:text-2xl text-muted-foreground mb-8 opacity-0 animate-fade-up delay-200 font-serif max-w-2xl mx-auto">
                    The event program will be published here soon. Stay tuned for updates!
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 opacity-0 animate-fade-up delay-300">
                    <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}" class="btn-gold text-lg px-8 py-4">Back to Home</a>
                    <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#registration" class="px-8 py-4 rounded-lg font-bold bg-secondary border border-border text-foreground hover:bg-secondary/80 transition-colors">Register Now</a>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent"></div>
    </section>
</main>

<script>
(function() {
    var toggle = document.getElementById('mobile-menu-toggle');
    var menu = document.getElementById('mobile-menu');
    var backdrop = document.getElementById('mobile-menu-backdrop');
    var iconOpen = document.getElementById('mobile-menu-icon-open');
    var iconClose = document.getElementById('mobile-menu-icon-close');
    if (!toggle || !menu) return;
    function openMenu() {
        menu.classList.remove('hidden');
        if (iconOpen) iconOpen.classList.add('hidden');
        if (iconClose) iconClose.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
    }
    function closeMenu() {
        menu.classList.add('hidden');
        if (iconOpen) iconOpen.classList.remove('hidden');
        if (iconClose) iconClose.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
    }
    toggle.addEventListener('click', function() {
        if (menu.classList.contains('hidden')) openMenu(); else closeMenu();
    });
    if (backdrop) backdrop.addEventListener('click', closeMenu);
    var closeBtn = document.getElementById('mobile-menu-close');
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    document.querySelectorAll('[data-committee-toggle]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.getAttribute('data-committee-toggle');
            var panelEl = document.querySelector('[data-committee-panel="' + id + '"]');
            var arrow = this.querySelector('.committee-arrow');
            if (panelEl && panelEl.classList.contains('hidden')) {
                panelEl.classList.remove('hidden');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            } else if (panelEl) {
                panelEl.classList.add('hidden');
                if (arrow) arrow.style.transform = '';
            }
        });
    });
})();
</script>
@endsection
