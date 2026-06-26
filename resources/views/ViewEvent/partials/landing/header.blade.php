@php
    if (!isset($landing) && isset($event)) {
        $landing = app(\App\Services\EventLandingPageService::class)->resolve($event);
    }
    $landing = $landing ?? [];
    $navItemCount = 4 + (isset($landingUserTypes) ? $landingUserTypes->count() : 0);
    $navBreakpoint = $navItemCount > 5 ? 'xl' : 'lg';
    $defaultSymposiumEventId = config('attendize.default_symposium_event_id', 2);
    $homeUrl = (isset($is_private_form) && $is_private_form && isset($registration))
        ? route('showPrivateFormByName', ['registration_name' => $registration->name])
        : (
            isset($event) && (int) $event->id === (int) $defaultSymposiumEventId
                ? route('showSymposiumRoot')
                : route('showEventSymposium', ['event_id' => $event->id])
          );
    $logoUrl = $landing['header']['logo_url'] ?? null;
    $secondaryLogoUrl = $landing['header']['secondary_logo_url'] ?? null;
@endphp
<header id="header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 header-sticky py-4 header-scrolled">
    <div class="container mx-auto px-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ $homeUrl }}" class="flex items-center gap-4" aria-label="Home">
                @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $event->title ?? 'Event' }}" class="bg-white w-12 h-12 md:w-16 md:h-14 object-contain" onerror="this.style.display='none'">
                @endif
                @if($secondaryLogoUrl)
                <img src="{{ $secondaryLogoUrl }}" alt="Logo" class="h-12 w-12 md:h-14 md:w-14 object-contain" onerror="this.style.display='none'">
                @endif
            </a>
        </div>
        <nav class="hidden {{ $navBreakpoint === 'lg' ? 'lg:flex' : 'md:flex' }} items-center gap-8">
            <a href="{{ $homeUrl }}" class="link-gold font-medium text-foreground">Home</a>
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
            @if(!empty($landing['pricing']['enabled']) && (!isset($is_private_form) || !$is_private_form))
            <button type="button" onclick="document.getElementById('pricing').scrollIntoView({behavior:'smooth'})" class="link-gold font-medium text-foreground">Fees</button>
            @endif
            <a href="{{ route('showEventProgram', ['event_id' => $event->id]) }}" class="link-gold font-medium text-foreground">Program</a>
            @if(!empty($landing['footer']['enabled']))
            <button type="button" onclick="document.getElementById('footer').scrollIntoView({behavior:'smooth'})" class="link-gold font-medium text-foreground">Contact Us</button>
            @endif
            @foreach($landing['header']['nav_links'] ?? [] as $navLink)
            <a href="{{ $navLink['url'] }}" class="link-gold font-medium text-foreground">{{ $navLink['label'] }}</a>
            @endforeach
        </nav>
        <div class="flex items-center gap-2 {{ $navBreakpoint === 'lg' ? 'lg:hidden' : 'md:hidden' }}">
            <button type="button" id="mobile-menu-toggle" class="p-2 rounded-lg text-foreground hover:bg-secondary/50" aria-expanded="false" aria-label="Open menu">
                <svg id="mobile-menu-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="mobile-menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            @if(!empty($landing['registration']['enabled']))
            <button type="button" onclick="document.getElementById('registration').scrollIntoView({behavior:'smooth'})" class="btn-gold text-sm font-bold px-4 py-2">Register Now</button>
            @endif
        </div>
    </div>
</header>

@include('ViewEvent.partials.landing.mobile-menu')
