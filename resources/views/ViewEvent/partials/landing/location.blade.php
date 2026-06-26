@if(!empty($landing['location']['enabled']))
<section id="location" class="py-20 bg-secondary/30 relative">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4"><span class="text-gold-gradient">{{ $landing['location']['title'] }}</span></h2>
            @if(!empty($landing['location']['venue_name']))
            <p class="text-muted-foreground text-lg">{{ $landing['location']['venue_name'] }}</p>
            @endif
        </div>
        <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            @if(!empty($landing['location']['map_embed_url']))
            <div class="card-navy rounded-2xl overflow-hidden">
                <div class="relative aspect-video md:aspect-square">
                    <iframe src="{{ $landing['location']['map_embed_url'] }}" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="absolute inset-0 w-full h-full"></iframe>
                </div>
            </div>
            @endif
            <div class="space-y-4">
                @if(!empty($landing['location']['address']))
                <div class="card-navy rounded-xl p-6 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-foreground mb-1">Address</h3>
                        <p class="text-muted-foreground">{{ $landing['location']['address'] }}</p>
                    </div>
                </div>
                @endif

                @if(!empty($landing['location']['date_time_text']))
                <div class="card-navy rounded-xl p-6 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-foreground mb-1">Date & Time</h3>
                        <p class="text-muted-foreground">{{ $landing['location']['date_time_text'] }}</p>
                    </div>
                </div>
                @endif

                @if(!empty($landing['location']['whatsapp_url']) || !empty($landing['location']['phone']))
                <div class="card-navy rounded-xl p-6 flex items-start gap-4">
                    <div>
                        <h3 class="font-bold text-foreground mb-1">Contact</h3>
                        @if(!empty($landing['location']['whatsapp_url']))
                        <p class="text-muted-foreground"><a href="{{ $landing['location']['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">{{ $landing['location']['phone'] ?: 'WhatsApp' }}</a></p>
                        @elseif(!empty($landing['location']['phone']))
                        <p class="text-muted-foreground">{{ $landing['location']['phone'] }}</p>
                        @endif
                    </div>
                </div>
                @endif

                @if(!empty($landing['location']['notes']))
                <div class="card-navy rounded-xl p-6">
                    <p class="text-muted-foreground text-sm">{{ $landing['location']['notes'] }}</p>
                </div>
                @endif

                @if(!empty($landing['location']['directions_url']))
                <a href="{{ $landing['location']['directions_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-gold w-full flex items-center justify-center gap-2 py-3">
                    <span>Get Directions</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                @elseif(!empty($landing['location']['google_maps_url']))
                <a href="{{ $landing['location']['google_maps_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-gold w-full flex items-center justify-center gap-2 py-3">
                    <span>Get Directions</span>
                </a>
                @endif
            </div>
        </div>
    </div>
    <div class="section-divider absolute bottom-0 left-0 right-0"></div>
</section>
@endif
