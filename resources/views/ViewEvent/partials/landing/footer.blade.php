@if(!empty($landing['footer']['enabled']))
<footer id="footer" class="bg-card py-12 relative">
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            <div class="text-center md:text-left">
                @if(!empty($landing['footer']['logo_url']))
                <img src="{{ $landing['footer']['logo_url'] }}" alt="Logo" class="h-12 mb-4 object-contain">
                @endif
                @if(!empty($landing['footer']['description']))
                <p class="text-muted-foreground text-sm">{!! nl2br(e($landing['footer']['description'])) !!}</p>
                @endif
            </div>
            <div class="text-center md:text-left">
                <h4 class="font-bold text-foreground mb-4 text-gold-gradient">Contact Us</h4>
                <div class="space-y-3">
                    @if(!empty($landing['footer']['email']))
                    <a href="mailto:{{ $landing['footer']['email'] }}" class="flex items-center justify-center md:justify-start gap-2 text-muted-foreground hover:text-primary transition-colors"><span>{{ $landing['footer']['email'] }}</span></a>
                    @endif
                    @if(!empty($landing['footer']['phone']))
                    <div class="flex items-center justify-center md:justify-start gap-2 text-muted-foreground">{{ $landing['footer']['phone'] }}</div>
                    @endif
                    @if(!empty($landing['footer']['website_url']))
                    <a href="{{ $landing['footer']['website_url'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center md:justify-start gap-2 text-muted-foreground hover:text-primary transition-colors">{{ parse_url($landing['footer']['website_url'], PHP_URL_HOST) ?: $landing['footer']['website_url'] }}</a>
                    @endif
                    @if(!empty($landing['footer']['location_text']))
                    <div class="flex items-center justify-center md:justify-start gap-2 text-muted-foreground">{{ $landing['footer']['location_text'] }}</div>
                    @endif
                    @foreach($landing['footer']['social_links'] as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center md:justify-start gap-2 text-muted-foreground hover:text-primary transition-colors">{{ $social['platform'] }}</a>
                    @endforeach
                </div>
            </div>
            @if(!empty($landing['footer']['nav_links']))
            <div class="text-center md:text-left">
                <h4 class="font-bold text-foreground mb-4 text-gold-gradient">Quick Links</h4>
                <ul class="space-y-2">
                    @foreach($landing['footer']['nav_links'] as $link)
                    <li><a href="{{ $link['url'] }}" class="text-muted-foreground hover:text-primary transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="section-divider mb-6"></div>
        <div class="text-center">
            <p class="text-muted-foreground text-sm">{{ $landing['footer']['copyright'] }}</p>
        </div>
    </div>
</footer>
@endif
