@extends('ViewEvent.layouts.symposium-layout')

@section('title', isset($event) ? 'User Type - ' . $pageTitle . ' - ' . $event->title : 'User Type - ' . $pageTitle)

@section('content')
<main class="min-h-screen">
    {{-- Header: if nav items > 8 show hamburger from lg; else from md to avoid overflow --}}
    @php
        $navItemCount = 4 + (isset($landingUserTypes) ? $landingUserTypes->count() : 0);
        $navBreakpoint = $navItemCount > 8 ? 'lg' : 'md';
    @endphp
    <header id="header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 header-sticky py-4 header-scrolled">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}" class="flex items-center gap-4">
                    <img src="{{ asset('images/sgss-logo.png') }}" alt="الجمعية السعودية للجراحة العامة - SGSS" class="bg-white w-12 h-12 md:w-16 md:h-14 object-contain" onerror="this.style.display='none'">
                    <img src="https://cdn4.premiumread.com/?url=https://www.al-madina.com/uploads/images/2020/06/24/1786780.jpg&w=800&q=100&f=jpg" alt="Logo 2" class="h-12 w-12 md:h-14 md:w-14 object-contain" onerror="this.style.display='none'">
                </a>
                @if(!isset($event))
                <span class="text-lg font-semibold">Event</span>
                @endif
            </div>
            <nav class="hidden {{ $navBreakpoint === 'lg' ? 'lg:flex' : 'md:flex' }} items-center gap-8">
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}" class="link-gold font-medium text-foreground">Home</a>
                @if(isset($landingUserTypes) && $landingUserTypes->count() > 0)
                    @foreach($landingUserTypes as $ut)
                        @if($ut->options && $ut->options->count() > 0)
                            <div class="relative group inline-block user-type-dropdown">
                                <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug]) }}" class="link-gold font-medium text-foreground inline-flex items-center gap-1 {{ (isset($userType) && $userType->id === $ut->id) ? 'border-b-2 border-primary pb-1' : '' }}">
                                    {{ $ut->name }}
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </a>
                                <div class="absolute left-0 top-full pt-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[180px]">
                                    <div class="rounded-lg shadow-xl border border-border overflow-hidden" style="background: linear-gradient(145deg, hsl(220, 55%, 14%) 0%, hsl(220, 55%, 10%) 100%);">
                                        <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug]) }}" class="block px-4 py-3 text-left font-medium hover:bg-primary/20 transition-colors {{ (isset($userType) && $userType->id === $ut->id && !isset($option)) ? 'bg-primary/20' : '' }}" style="color: hsl(45, 70%, 50%); border-bottom: 1px solid hsl(220, 40%, 25%);">{{ $ut->name }} (All)</a>
                                        @foreach($ut->options as $opt)
                                            <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug, 'option_slug' => $opt->slug]) }}" class="block px-4 py-3 text-left hover:bg-primary/20 transition-colors {{ (isset($option) && $option->id === $opt->id) ? 'bg-primary/20' : '' }} text-foreground">{{ $opt->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('showEventUserType', ['event_id' => $event->id, 'user_type_slug' => $ut->slug]) }}" class="link-gold font-medium text-foreground {{ (isset($userType) && $userType->id === $ut->id) ? 'border-b-2 border-primary pb-1' : '' }}">{{ $ut->name }}</a>
                        @endif
                    @endforeach
                @endif
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#pricing" class="link-gold font-medium text-foreground">Fees</a>
                <a href="{{ route('showEventProgram', ['event_id' => $event->id]) }}" class="link-gold font-medium text-foreground">Program</a>
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

    {{-- Mobile menu overlay: visible below same breakpoint as hamburger --}}
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
                            <div class="mobile-user-type-wrap" style="border-bottom: 1px solid hsl(220, 40%, 25%);">
                                <button type="button" class="mobile-nav-link block w-full text-left py-3 flex items-center justify-between" style="color: hsl(45, 70%, 50%); font-weight: 500;" data-user-type-toggle="{{ $ut->id }}">
                                    {{ $ut->name }}
                                    <svg class="user-type-arrow w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="mobile-user-type-sub hidden" data-user-type-panel="{{ $ut->id }}" style="padding-left: 1rem; padding-bottom: 0.5rem;">
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
                <a href="{{ route('showEventProgram', ['event_id' => $event->id]) }}" class="mobile-nav-link block py-3" style="color: hsl(45, 70%, 50%); font-weight: 500; border-bottom: 1px solid hsl(220, 40%, 25%);">Program</a>
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#footer" class="mobile-nav-link block py-3" style="color: hsl(45, 70%, 50%); font-weight: 500;">Contact Us</a>
            </nav>
            <div style="padding: 1.5rem;">
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}#registration" class="mobile-nav-link w-full text-center font-bold py-3 rounded-lg block" style="background: linear-gradient(135deg, hsl(45, 75%, 45%) 0%, hsl(45, 70%, 55%) 100%); color: hsl(220, 60%, 8%);">Register Now</a>
            </div>
        </div>
    </div>

    {{-- User Type content --}}
    <section class="relative pt-28 pb-20 min-h-screen">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('assets/images/hero-bg.jpg') }}');">
            <div class="absolute inset-0 bg-gradient-to-b from-background/90 via-background/70 to-background"></div>
        </div>
        <div class="relative z-10 container mx-auto px-4">
            <div class="text-center mb-12">
                <p class="text-primary text-sm font-medium mb-2">User Type</p>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4">
                    <span class="text-gold-gradient">{{ $pageTitle }}</span>
                </h1>
                @if(isset($userType) && $option)
                    <p class="text-muted-foreground text-lg">{{ $userType->name }}</p>
                @endif
            </div>

            @if($users->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                    @foreach($users as $u)
                        @php
                            $displayTrack = $pageTitle;
                            if (isset($option)) {
                                $displayTrack = $option->name;
                            } else {
                                $utPivot = $u->userTypes->where('id', $userType->id)->first();
                                if ($utPivot && $utPivot->pivot->user_type_option_id) {
                                    $optModel = $userType->options->firstWhere('id', $utPivot->pivot->user_type_option_id);
                                    $displayTrack = $optModel ? $optModel->name : $userType->name;
                                } else {
                                    $displayTrack = $userType->name;
                                }
                            }
                            $avatarUrl = $u->avatar ? asset('storage/' . $u->avatar) : '';
                            $initials = substr($u->first_name, 0, 1) . substr($u->last_name, 0, 1);
                        @endphp
                        <div class="rounded-2xl overflow-hidden border border-border bg-card shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col items-center text-center p-6">
                            <div class="w-24 h-24 md:w-28 md:h-28 rounded-full overflow-hidden flex-shrink-0 mb-4 border-2 border-primary/30">
                                @if($u->avatar)
                                    <img src="{{ $avatarUrl }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl font-bold text-primary bg-secondary/80">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                            <h3 class="text-lg md:text-xl font-bold text-foreground mb-1">
                                @if($u->title)
                                    <span class="text-primary text-sm font-medium block mb-1">{{ $u->title }}</span>
                                @endif
                                {{ $u->first_name }} {{ $u->last_name }}
                            </h3>
                            <p class="text-sm text-muted-foreground mb-2">{{ $displayTrack }}</p>
                            @if($u->bio)
                                <p class="text-xs text-muted-foreground mt-2 line-clamp-3" style="max-height: 4.5em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">{{ $u->bio }}</p>
                            @endif
                            {{-- VIEW PROFILE (disabled: card not clickable, no modal)
                            <span class="text-primary font-medium text-sm hover:underline inline-flex items-center gap-1 view-profile-link">
                                VIEW PROFILE <span>&gt;</span>
                            </span>
                            --}}
                        </div>
                    @endforeach
                </div>
            @else
                <div class="relative rounded-2xl border border-border overflow-hidden text-center py-16 px-8 max-w-2xl mx-auto" style="background: linear-gradient(160deg, hsl(220, 55%, 16%) 0%, hsl(220, 55%, 12%) 100%);">
                    <div class="inline-block mb-4">
                        <span class="px-4 py-2 rounded-full border border-primary/30 bg-secondary/50 text-primary text-sm font-medium">{{ $pageTitle }}</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">
                        <span class="text-gold-gradient">Coming Soon</span>
                    </h2>
                    <p class="text-lg text-muted-foreground mb-8 max-w-md mx-auto">
                        We're preparing the list of members for this category. Stay tuned for updates!
                    </p>
                    <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}" class="btn-gold inline-block">Back to event</a>
                </div>
            @endif
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent pointer-events-none"></div>
    </section>

    {{-- User profile modal (disabled: cards not clickable)
    <div id="user-profile-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4" ...>...</div>
    --}}
</main>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fade-in { animation: fadeIn 0.2s ease-out; }
</style>

<script>
(function() {
    var toggle = document.getElementById('mobile-menu-toggle');
    var menu = document.getElementById('mobile-menu');
    var backdrop = document.getElementById('mobile-menu-backdrop');
    var panel = document.getElementById('mobile-menu-panel');
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
    document.querySelectorAll('[data-user-type-toggle]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.getAttribute('data-user-type-toggle');
            var panelEl = document.querySelector('[data-user-type-panel="' + id + '"]');
            var arrow = this.querySelector('.user-type-arrow');
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

/* User profile modal disabled: cards are not clickable
(function userProfileModal() { ... })();
*/
</script>
@endsection
