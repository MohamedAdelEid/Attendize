@php
    $navBreakpoint = isset($navBreakpoint) ? $navBreakpoint : 'lg';
    $homeUrl = isset($homeUrl) ? $homeUrl : '#';
@endphp
<div id="mobile-menu" class="{{ $navBreakpoint === 'lg' ? 'lg:hidden' : 'md:hidden' }} hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; min-height: 100vh; min-height: 100dvh; z-index: 9999;">
    <div id="mobile-menu-backdrop" role="button" tabindex="0" aria-label="Close menu" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(19, 28, 46, 0.92); backdrop-filter: blur(8px); cursor: pointer;"></div>
    <div id="mobile-menu-panel" style="position: absolute; top: 0; right: 0; width: 280px; max-width: 85vw; height: 100%; min-height: 100vh; background: linear-gradient(145deg, hsl(220, 55%, 14%) 0%, hsl(220, 55%, 10%) 100%); border-left: 1px solid hsl(220, 40%, 25%); box-shadow: -8px 0 32px rgba(0,0,0,0.4); overflow-y: auto;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid hsl(220, 40%, 25%); display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 1.125rem; font-weight: 600; color: hsl(0, 0%, 98%);">Menu</span>
            <button type="button" id="mobile-menu-close" class="p-2 rounded-lg hover:opacity-80" style="color: hsl(0, 0%, 98%);" aria-label="Close menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav style="padding: 1rem 1.5rem; display: flex; flex-direction: column;">
            <a href="{{ $homeUrl }}" class="mobile-nav-link block py-3" style="color: hsl(45, 70%, 50%); font-weight: 500; border-bottom: 1px solid hsl(220, 40%, 25%);">Home</a>
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
            @if(!empty($landing['pricing']['enabled']) && (!isset($is_private_form) || !$is_private_form))
            <button type="button" class="mobile-nav-link block w-full text-left py-3" style="color: hsl(45, 70%, 50%); font-weight: 500; border-bottom: 1px solid hsl(220, 40%, 25%);" data-scroll="pricing">Fees</button>
            @endif
            <a href="{{ route('showEventProgram', ['event_id' => $event->id]) }}" class="mobile-nav-link block py-3" style="color: hsl(45, 70%, 50%); font-weight: 500; border-bottom: 1px solid hsl(220, 40%, 25%);">Program</a>
            @if(!empty($landing['footer']['enabled']))
            <button type="button" class="mobile-nav-link block w-full text-left py-3" style="color: hsl(45, 70%, 50%); font-weight: 500;" data-scroll="footer">Contact Us</button>
            @endif
        </nav>
        @if(!empty($landing['registration']['enabled']))
        <div style="padding: 1.5rem;">
            <button type="button" class="mobile-nav-link w-full text-center font-bold py-3 rounded-lg" style="background: linear-gradient(135deg, hsl(45, 75%, 45%) 0%, hsl(45, 70%, 55%) 100%); color: hsl(220, 60%, 8%);" data-scroll="registration">Register Now</button>
        </div>
        @endif
    </div>
</div>
