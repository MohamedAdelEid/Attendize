@extends('ViewEvent.layouts.symposium-layout')

@section('title', isset($event) ? $event->title . ' - Speakers' : 'Speakers')

@section('content')
<main class="min-h-screen">
    {{-- Header --}}
    <header id="header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 header-sticky py-4 header-scrolled">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('showSymposiumRoot') }}" class="flex items-center gap-4">
                    <img src="{{ asset('images/logo-placeholder.svg') }}" alt="Logo 1" class="h-12 w-12 md:h-14 md:w-14 object-contain" onerror="this.style.display='none'">
                    <img src="{{ asset('images/logo-placeholder-2.svg') }}" alt="Logo 2" class="h-12 w-12 md:h-14 md:w-14 object-contain" onerror="this.style.display='none'">
                </a>
            </div>
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('showSymposiumRoot') }}" class="link-gold font-medium text-foreground">Home</a>
                <span class="link-gold font-medium text-foreground border-b-2 border-primary pb-1">Speakers</span>
                <a href="{{ route('showSymposiumRoot') }}#pricing" class="link-gold font-medium text-foreground">Pricing</a>
                <a href="{{ route('showSymposiumRoot') }}#registration" class="link-gold font-medium text-foreground">Registration</a>
                <a href="{{ route('showSymposiumRoot') }}#location" class="link-gold font-medium text-foreground">Location</a>
                <a href="{{ route('showSymposiumRoot') }}#footer" class="link-gold font-medium text-foreground">Contact Us</a>
            </nav>
            <a href="{{ route('showSymposiumRoot') }}#registration" class="btn-gold text-sm md:hidden font-bold px-4 py-2">Register Now</a>
        </div>
    </header>

    {{-- Coming Soon Section --}}
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-24">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('assets/images/hero-bg.jpg') }}');">
            <div class="absolute inset-0 bg-gradient-to-b from-background/90 via-background/70 to-background"></div>
        </div>
        <div class="relative z-10 container mx-auto px-4 py-20 text-center">
            <div class="max-w-3xl mx-auto">
                <div class="inline-block mb-6 opacity-0 animate-fade-up">
                    <span class="px-4 py-2 rounded-full border border-primary/30 bg-secondary/50 text-primary text-sm font-medium">Speakers</span>
                </div>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6 opacity-0 animate-fade-up delay-100">
                    <span class="text-gold-gradient">Coming Soon</span>
                </h1>
                <p class="text-xl md:text-2xl text-muted-foreground mb-8 opacity-0 animate-fade-up delay-200 font-serif max-w-2xl mx-auto">
                    We're preparing an exceptional lineup of speakers for this event. Stay tuned for updates!
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 opacity-0 animate-fade-up delay-300">
                    <a href="{{ route('showSymposiumRoot') }}" class="btn-gold text-lg px-8 py-4">Back to Home</a>
                    <a href="{{ route('showSymposiumRoot') }}#registration" class="px-8 py-4 rounded-lg font-bold bg-secondary border border-border text-foreground hover:bg-secondary/80 transition-colors">Register Now</a>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent"></div>
    </section>
</main>
@endsection
