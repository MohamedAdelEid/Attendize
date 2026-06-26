@if(!empty($landing['hero']['enabled']))
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    @if(($landing['hero']['bg_type'] ?? 'image') === 'video' && !empty($landing['hero']['bg_video_url']))
        <div class="absolute inset-0 overflow-hidden">
            <video class="absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline>
                <source src="{{ $landing['hero']['bg_video_url'] }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-gradient-to-b from-background/90 via-background/70 to-background"></div>
        </div>
    @else
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ $landing['hero']['bg_image_url'] }}');">
            <div class="absolute inset-0 bg-gradient-to-b from-background/90 via-background/70 to-background"></div>
        </div>
    @endif

    <div class="relative z-10 container mx-auto px-4 pt-24 pb-16 text-center">
        @if(!empty($landing['hero']['badge_text']))
        <div class="inline-block mb-6 opacity-0 animate-fade-up">
            <span class="px-4 py-2 rounded-full border border-primary/30 bg-secondary/50 text-primary text-sm font-medium">{{ $landing['hero']['badge_text'] }}</span>
        </div>
        @endif

        @if(!empty($landing['hero']['title']))
        <p class="text-2xl md:text-4xl lg:text-5xl font-bold text-foreground mb-3 opacity-0 animate-fade-up delay-100" dir="auto">{{ $landing['hero']['title'] }}</p>
        @endif

        @if(!empty($landing['hero']['title_secondary']))
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4 opacity-0 animate-fade-up delay-100">
            <span class="text-gold-gradient">{{ $landing['hero']['title_secondary'] }}</span>
        </h1>
        @elseif(empty($landing['hero']['title']))
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4 opacity-0 animate-fade-up delay-100">
            <span class="text-gold-gradient">{{ $event->title }}</span>
        </h1>
        @endif

        @if(!empty($landing['hero']['subtitle']))
        <p class="text-xl md:text-2xl lg:text-3xl text-muted-foreground mb-8 opacity-0 animate-fade-up delay-200 font-serif">{{ $landing['hero']['subtitle'] }}</p>
        @endif

        @if(!empty($landing['hero']['date_time_text']) || !empty($landing['hero']['venue_text']))
        <div class="flex flex-col md:flex-row items-center justify-center gap-4 md:gap-8 mb-10 opacity-0 animate-fade-up delay-300">
            @if(!empty($landing['hero']['date_time_text']))
            <div class="flex items-center gap-2 text-foreground">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-lg">{{ $landing['hero']['date_time_text'] }}</span>
            </div>
            @endif
            @if(!empty($landing['hero']['date_time_text']) && !empty($landing['hero']['venue_text']))
            <div class="hidden md:block w-px h-6 bg-border"></div>
            @endif
            @if(!empty($landing['hero']['venue_text']))
            <div class="flex items-center gap-2 text-foreground">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-lg">{{ $landing['hero']['venue_text'] }}</span>
            </div>
            @endif
        </div>
        @endif

        @if(!empty($landing['hero']['target_audience']))
        <div class="mb-10 opacity-0 animate-fade-up delay-400">
            <p class="text-muted-foreground mb-4">Target Audience</p>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach($landing['hero']['target_audience'] as $item)
                <span class="px-4 py-2 rounded-full bg-secondary border border-border text-foreground text-sm">{{ $item }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @foreach($landing['hero']['buttons'] as $btn)
            @php
                $btnClass = ($btn['style'] ?? 'primary') === 'secondary' ? 'bg-secondary border border-border text-foreground hover:bg-secondary/80 px-8 py-4 rounded-lg font-bold text-lg' : 'btn-gold text-lg px-8 py-4';
                $scrollTarget = $btn['scroll_target'] ?? null;
            @endphp
            @if($scrollTarget)
            <button type="button" onclick="document.getElementById('{{ $scrollTarget }}').scrollIntoView({behavior:'smooth'})" class="{{ $btnClass }} opacity-0 animate-fade-up delay-500">{{ $btn['text'] }}</button>
            @elseif(!empty($btn['url']))
            <a href="{{ $btn['url'] }}" class="{{ $btnClass }} opacity-0 animate-fade-up delay-500 inline-block">{{ $btn['text'] }}</a>
            @endif
        @endforeach
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent"></div>
</section>
@endif
