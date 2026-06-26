@if(!empty($landing['pricing']['enabled']) && (!isset($is_private_form) || !$is_private_form) && !empty($landing['pricing']['cards']))
<section id="pricing" class="py-20 bg-secondary/30 relative">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4"><span class="text-gold-gradient">{{ $landing['pricing']['title'] }}</span></h2>
            @if(!empty($landing['pricing']['description']))
            <p class="text-muted-foreground text-lg max-w-2xl mx-auto">{{ $landing['pricing']['description'] }}</p>
            @endif
        </div>
        <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
            @foreach($landing['pricing']['cards'] as $card)
            <div class="relative rounded-2xl overflow-hidden transition-all duration-300 hover:scale-[1.02] card-navy {{ $card['is_highlighted'] ? 'border-2 border-primary/50 shadow-lg shadow-primary/10' : 'border border-border' }}">
                @if(!empty($card['badge']))
                <div class="absolute top-0 right-0 bg-primary text-primary-foreground text-xs font-bold px-3 py-1 rounded-bl-lg">{{ $card['badge'] }}</div>
                @endif
                <div class="p-6 pb-4 {{ $card['is_highlighted'] ? 'bg-primary/10' : '' }}">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 rounded-lg {{ $card['is_highlighted'] ? 'bg-primary/20 text-primary' : 'bg-secondary text-muted-foreground' }}">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-foreground">{{ $card['title'] }}</h3>
                            @if(!empty($card['description']))
                            <p class="text-sm text-muted-foreground">{{ $card['description'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-6 pt-2 space-y-4">
                    @foreach($card['tiers'] as $tier)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-background/50 border border-border/50">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-foreground text-sm md:text-base">{{ $tier['label'] }}</span>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <span class="text-2xl font-bold text-gold-gradient">{{ $tier['price_formatted'] }}</span>
                            <span class="text-muted-foreground text-sm block">{{ $tier['currency'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="p-6 pt-2">
                    <button type="button" onclick="document.getElementById('registration').scrollIntoView({behavior:'smooth'})" class="w-full py-3 rounded-lg font-bold {{ $card['is_highlighted'] ? 'btn-gold' : 'bg-secondary border border-border text-foreground hover:bg-secondary/80' }}">Register Now</button>
                </div>
            </div>
            @endforeach
        </div>
        @if(!empty($landing['pricing']['footer_note']))
        <div class="mt-10 text-center">
            <p class="text-muted-foreground text-sm">{{ $landing['pricing']['footer_note'] }}</p>
        </div>
        @endif
    </div>
    <div class="section-divider absolute bottom-0 left-0 right-0"></div>
</section>
@endif
