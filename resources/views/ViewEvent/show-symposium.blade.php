@extends('ViewEvent.layouts.symposium-layout')

@section('title', isset($event) ? $event->title : 'Medicine & Judiciary Symposium')

@section('content')
<main class="min-h-screen">
    {{-- Header --}}
    <header id="header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 header-sticky py-4 header-scrolled">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <img src="https://sgss.org.sa/wp-content/uploads/2026/01/Asset-3@3x.png" alt="Logo 1" class="h-12 w-12 md:h-14 md:w-14 object-contain bg-white " onerror="this.style.display='none'">
                <img src="https://cdn4.premiumread.com/?url=https://www.al-madina.com/uploads/images/2020/06/24/1786780.jpg&w=800&q=100&f=jpg" alt="Logo 2" class="h-12 w-12 md:h-14 md:w-14 object-contain" onerror="this.style.display='none'">
                @if(!isset($event))
                <span class="text-lg font-semibold">Event</span>
                @endif
            </div>
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('showSpeakersRoot') }}" class="link-gold font-medium text-foreground">Speakers</a>
                <button type="button" onclick="document.getElementById('pricing').scrollIntoView({behavior:'smooth'})" class="link-gold font-medium text-foreground">Pricing</button>
                <button type="button" onclick="document.getElementById('registration').scrollIntoView({behavior:'smooth'})" class="link-gold font-medium text-foreground">Registration</button>
                <button type="button" onclick="document.getElementById('location').scrollIntoView({behavior:'smooth'})" class="link-gold font-medium text-foreground">Location</button>
                <button type="button" onclick="document.getElementById('footer').scrollIntoView({behavior:'smooth'})" class="link-gold font-medium text-foreground">Contact Us</button>
            </nav>
            <button type="button" onclick="document.getElementById('registration').scrollIntoView({behavior:'smooth'})" class="btn-gold text-sm md:hidden font-bold px-4 py-2">Register Now</button>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        @php
            // $heroBg = (isset($event) && !empty($event->bg_image_url)) ? $event->bg_image_url : asset('assets/images/hero-bg.jpg');
            $heroBg = asset('assets/images/hero-bg.jpg');
        @endphp
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ $heroBg }}');">
            <div class="absolute inset-0 bg-gradient-to-b from-background/90 via-background/70 to-background"></div>
        </div>
        <div class="relative z-10 container mx-auto px-4 pt-24 pb-16 text-center">
            <div class="inline-block mb-6 opacity-0 animate-fade-up">
                <span class="px-4 py-2 rounded-full border border-primary/30 bg-secondary/50 text-primary text-sm font-medium">Specialized Scientific Seminar</span>
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4 opacity-0 animate-fade-up delay-100">
                <span class="block text-foreground">Medicine &</span>
                <span class="text-gold-gradient">Judiciary Symposium</span>
            </h1>
            <p class="text-xl md:text-2xl lg:text-3xl text-muted-foreground mb-8 opacity-0 animate-fade-up delay-200 font-serif">Legal Liability in Surgical Professions</p>
            <div class="flex flex-col md:flex-row items-center justify-center gap-4 md:gap-8 mb-10 opacity-0 animate-fade-up delay-300">
                <div class="flex items-center gap-2 text-foreground">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{-- <span class="text-lg">{{ isset($event) ? $event->start_date->format('F j, Y') : 'May 2, 2026' }}</span> --}}
                    <span class="text-lg">May 2, 2026</span>
                </div>
                <div class="hidden md:block w-px h-6 bg-border"></div>
                <div class="flex items-center gap-2 text-foreground">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-lg">voco Jeddah Gate by IHG, Jeddah</span>
                </div>
            </div>
            <div class="mb-10 opacity-0 animate-fade-up delay-400">
                <p class="text-muted-foreground mb-4">Target Audience</p>
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach(['Consultants', 'Specialists', 'Residents' , 'Students', 'Nursing'] as $item)
                    <span class="px-4 py-2 rounded-full bg-secondary border border-border text-foreground text-sm">{{ $item }}</span>
                    @endforeach
                </div>
            </div>
            <button type="button" onclick="document.getElementById('registration').scrollIntoView({behavior:'smooth'})" class="btn-gold text-lg px-8 py-4 opacity-0 animate-fade-up delay-500">Register Now</button>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent"></div>
    </section>

    {{-- Pricing Section --}}
    <section id="pricing" class="py-20 bg-secondary/30 relative">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4"><span class="text-gold-gradient">Registration Fees</span></h2>
                <p class="text-muted-foreground text-lg max-w-2xl mx-auto">Choose your registration category. Society members enjoy exclusive discounted rates.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                {{-- Society Members --}}
                <div class="relative rounded-2xl overflow-hidden transition-all duration-300 hover:scale-[1.02] card-navy border-2 border-primary/50 shadow-lg shadow-primary/10">
                    {{-- <div class="absolute top-0 right-0 bg-primary text-primary-foreground text-xs font-bold px-3 py-1 rounded-bl-lg">Best Value</div> --}}
                    <div class="p-6 pb-4 bg-primary/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 rounded-lg bg-primary/20 text-primary">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-foreground">Society Members</h3>
                        </div>
                    </div>
                    <div class="p-6 pt-2 space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-background/50 border border-border/50">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-foreground text-sm md:text-base">Consultants, Specialists & Residents</span>
                            </div>
                            <div class="text-right flex-shrink-0 ml-4"><span class="text-2xl font-bold text-gold-gradient">400</span><span class="text-muted-foreground text-sm block">SAR</span></div>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-background/50 border border-border/50">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-foreground text-sm md:text-base">Students & Nursing</span>
                            </div>
                            <div class="text-right flex-shrink-0 ml-4"><span class="text-2xl font-bold text-gold-gradient">200</span><span class="text-muted-foreground text-sm block">SAR</span></div>
                        </div>
                    </div>
                    <div class="p-6 pt-2">
                        <button type="button" onclick="document.getElementById('registration').scrollIntoView({behavior:'smooth'})" class="w-full py-3 rounded-lg font-bold btn-gold">Register Now</button>
                    </div>
                </div>
                {{-- Non-Members --}}
                <div class="relative rounded-2xl overflow-hidden transition-all duration-300 hover:scale-[1.02] card-navy border border-border">
                    <div class="p-6 pb-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 rounded-lg bg-secondary text-muted-foreground">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-foreground">Non-Members</h3>
                        </div>
                    </div>
                    <div class="p-6 pt-2 space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-background/50 border border-border/50">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-foreground text-sm md:text-base">Consultants, Specialists & Residents</span>
                            </div>
                            <div class="text-right flex-shrink-0 ml-4"><span class="text-2xl font-bold text-gold-gradient">500</span><span class="text-muted-foreground text-sm block">SAR</span></div>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-background/50 border border-border/50">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-foreground text-sm md:text-base">Students & Nursing</span>
                            </div>
                            <div class="text-right flex-shrink-0 ml-4"><span class="text-2xl font-bold text-gold-gradient">300</span><span class="text-muted-foreground text-sm block">SAR</span></div>
                        </div>
                    </div>
                    <div class="p-6 pt-2">
                        <button type="button" onclick="document.getElementById('registration').scrollIntoView({behavior:'smooth'})" class="w-full py-3 rounded-lg font-bold bg-secondary border border-border text-foreground hover:bg-secondary/80">Register Now</button>
                    </div>
                </div>
            </div>
            <div class="mt-10 text-center">
                <p class="text-muted-foreground text-sm">All prices include seminar materials, lunch, and certificate of attendance.</p>
            </div>
        </div>
        <div class="section-divider absolute bottom-0 left-0 right-0"></div>
    </section>

    {{-- Registration Section --}}
    <section id="registration" class="py-20 hero-bg-custom relative">
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4"><span class="text-gold-gradient">Registration</span></h2>
                <p class="text-muted-foreground text-lg">Register now to attend the Medicine & Judiciary Symposium</p>
            </div>
            <div class="max-w-2xl mx-auto card-navy rounded-2xl p-6 md:p-8">
                <div class="flex rounded-xl overflow-hidden mb-8" role="tablist">
                    <button type="button" data-tab="non-members" class="reg-tab flex-1 py-3 px-4 font-bold transition-all duration-300 tab-active">Non-Members</button>
                    <button type="button" data-tab="members" class="reg-tab flex-1 py-3 px-4 font-bold transition-all duration-300 tab-inactive">Members</button>
                </div>
                {{-- Non-Members tab (active by default) --}}
                <div id="reg-non-members" class="reg-panel space-y-6">
                    <div id="non-member-form-wrap">
                        @include('ViewEvent.partials.SymposiumRegistrationForm', ['landingRegistration' => $landingRegistration ?? null, 'event' => $event, 'countries' => $countries ?? collect()])
                    </div>
                    <p id="non-member-register-status" class="text-sm text-muted-foreground hidden"></p>
                    <div id="non-member-payment-wrap" class="hidden mt-4 rounded-xl border border-primary/30 bg-primary/5 p-6">
                        <h4 class="text-lg font-semibold text-foreground mb-4">إتمام الدفع</h4>
                        <p class="text-muted-foreground mb-4">يرجى إدخال بيانات الدفع أدناه.</p>
                        <form id="non-member-hyperpay-form" action="#" class="paymentWidgets" data-brands="VISA MASTER MADA AMEX"></form>
                    </div>
                    <div id="non-member-register-success" class="hidden mt-4 p-4 rounded-xl border border-green-500/40 bg-green-500/10 text-green-800 dark:text-green-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-semibold">Registration Successful</p>
                                <p class="text-sm mt-1 opacity-90">Your registration request has been received. It will be reviewed by our team.</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Members tab --}}
                <div id="reg-members" class="reg-panel hidden space-y-6" data-members-registration-id="{{ isset($membersRegistration) && $membersRegistration ? $membersRegistration->id : '' }}">
                    @if(isset($uniqueMemberField) && $uniqueMemberField)
                    <div id="member-lookup-form" data-event-id="{{ $event->id }}" data-field-key="{{ $uniqueMemberField->field_key }}">
                        <label class="block text-foreground mb-2 font-medium">{{ $uniqueMemberField->label }}</label>
                        <div class="flex gap-3">
                            <input type="text" id="member-unique-value" class="flex-1 input-navy rounded-lg py-3 px-4" placeholder="{{ $uniqueMemberField->label }}">
                            <button type="button" id="btn-member-lookup" class="btn-gold py-3 px-6 whitespace-nowrap">Verify</button>
                        </div>
                        <p id="member-lookup-message" class="mt-2 text-sm text-muted-foreground hidden"></p>
                    </div>
                    <div id="member-result" class="hidden rounded-xl overflow-hidden border border-primary/30 bg-primary/5 member-result-enter">
                        <div class="p-6">
                            <div id="member-expired-alert" class="hidden mb-4 p-4 rounded-lg bg-red-500/20 border border-red-500/40">
                                <p class="text-red-200 font-medium mb-2">Your membership has expired.</p>
                                <p class="text-sm text-muted-foreground mb-3">You cannot register from here. Please renew or contact us using the link below.</p>
                                <a id="member-renewal-link" href="https://forms.gle/MSUgd1H8Hw2wnvu68" target="_blank" rel="noopener noreferrer" class="inline-block btn-gold py-2 px-4 text-sm">Renew / Contact us</a>
                            </div>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <h4 id="member-name" class="text-xl font-bold text-foreground"></h4>
                                    <p id="member-email" class="text-sm text-muted-foreground"></p>
                                </div>
                            </div>
                            <div id="member-fields" class="space-y-2"></div>
                            <div id="member-register-actions" class="mt-6 pt-4 border-t border-border/50 hidden">
                                <input type="hidden" id="member-event-member-id" value="">
                                <div id="members-registration-form-container" class="hidden mt-4">
                                    @if(isset($membersRegistration) && $membersRegistration)
                                    @include('ViewEvent.partials.SymposiumRegistrationForm', ['landingRegistration' => $membersRegistration, 'event' => $event, 'countries' => $countries ?? collect(), 'formId' => 'members-registration-form'])
                                    @endif
                                </div>
                                <p id="member-register-status" class="mt-2 text-sm text-muted-foreground hidden"></p>
                                <div id="member-register-success" class="hidden mt-4 p-4 rounded-xl border border-green-500/40 bg-green-500/10 text-green-800 dark:text-green-200">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-6 h-6 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div>
                                            <p class="font-semibold">Registration Successful</p>
                                            <p class="text-sm mt-1 opacity-90">Your registration request has been received. It will be reviewed by our team.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-muted-foreground text-center py-4">Members lookup is not configured. Add a unique member field in Event → Members.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Location Section --}}
    <section id="location" class="py-20 bg-secondary/30 relative">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4"><span class="text-gold-gradient">Venue Location</span></h2>
                <p class="text-muted-foreground text-lg">voco Jeddah Gate by IHG, Jeddah</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="card-navy rounded-2xl overflow-hidden">
                    <div class="relative aspect-video md:aspect-square">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3712.04569652667!2d39.20396467526981!3d21.505929980265574!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x15c3cfc9ce8ada75%3A0xd4e277b25872b830!2svoco%20Jeddah%20Gate%20by%20IHG!5e0!3m2!1sen!2ssa!4v1770895702331!5m2!1sen!2ssa" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="absolute inset-0" title="voco Jeddah Gate"></iframe>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="card-navy rounded-xl p-6 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-foreground mb-1">Address</h3>
                            <p class="text-muted-foreground">voco Jeddah Gate by IHG, Jeddah, Saudi Arabia</p>
                        </div>
                    </div>
                    <div class="card-navy rounded-xl p-6 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-foreground mb-1">Date & Time</h3>
                            <p class="text-muted-foreground">May 2, 2026 — Full Day Event</p>
                        </div>
                    </div>
                    <a href="https://maps.google.com/?q=Crowne+Plaza+Jeddah+Al+Hamra" target="_blank" rel="noopener noreferrer" class="btn-gold w-full flex items-center justify-center gap-2 py-3">
                        <span>Get Directions</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="section-divider absolute bottom-0 left-0 right-0"></div>
    </section>

    {{-- Footer --}}
    <footer id="footer" class="bg-card py-12 relative">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div class="text-center md:text-left">
                    <p class="text-muted-foreground text-sm">Medicine & Judiciary Symposium<br>Legal Liability in Surgical Professions</p>
                </div>
                <div class="text-center md:text-left">
                    <h4 class="font-bold text-foreground mb-4 text-gold-gradient">Contact Us</h4>
                    <div class="space-y-3">
                        <a href="mailto:info@sgss.org.sa" class="flex items-center justify-center md:justify-start gap-2 text-muted-foreground hover:text-primary transition-colors"><span>info@sgss.org.sa</span></a>
                        <a href="https://sgss.org.sa" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center md:justify-start gap-2 text-muted-foreground hover:text-primary transition-colors">sgss.org.sa</a>
                        <div class="flex items-center justify-center md:justify-start gap-2 text-muted-foreground">Jeddah, Saudi Arabia</div>
                    </div>
                </div>
                <div class="text-center md:text-left">
                    <h4 class="font-bold text-foreground mb-4 text-gold-gradient">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Terms & Conditions</a></li>
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="section-divider mb-6"></div>
            <div class="text-center">
                <p class="text-muted-foreground text-sm">© {{ date('Y') }} Saudi General Surgery Society. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</main>
<script>
(function() {
    // Show validation errors under each field; clear previous first.
    function showFormFieldErrors(form, errors) {
        if (!form || !errors || typeof errors !== 'object') return;
        form.querySelectorAll('.form-field-error').forEach(function(el) { el.remove(); });
        Object.keys(errors).forEach(function(key) {
            var msg = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
            if (!msg) return;
            var name = key.indexOf('fields.') === 0 ? 'fields[' + key.split('.')[1] + ']' : key;
            var input = form.querySelector('[name="' + name + '"]');
            if (!input) return;
            var errEl = document.createElement('p');
            errEl.className = 'form-field-error text-red-500 dark:text-red-400 text-sm mt-1';
            errEl.setAttribute('role', 'alert');
            errEl.textContent = msg;
            input.classList.add('border-red-500', 'dark:border-red-400');
            input.insertAdjacentElement('afterend', errEl);
        });
    }
    function clearFormFieldErrors(form) {
        if (!form) return;
        form.querySelectorAll('.form-field-error').forEach(function(el) { el.remove(); });
        form.querySelectorAll('[name].border-red-500').forEach(function(el) { el.classList.remove('border-red-500', 'dark:border-red-400'); });
    }

    var tabs = document.querySelectorAll('.reg-tab');
    var panels = document.querySelectorAll('.reg-panel');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-tab');
            tabs.forEach(function(t) {
                t.classList.remove('tab-active');
                t.classList.add('tab-inactive');
            });
            this.classList.remove('tab-inactive');
            this.classList.add('tab-active');
            panels.forEach(function(p) {
                p.classList.add('hidden');
                if (p.id === 'reg-' + target) p.classList.remove('hidden');
            });
        });
    });

    // Non-Members form: submit via AJAX, show success or payment on same page (no redirect/reload)
    var nonMemberForm = document.getElementById('symposium-registration-form');
    if (nonMemberForm) {
        nonMemberForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var statusEl = document.getElementById('non-member-register-status');
            var formWrap = document.getElementById('non-member-form-wrap');
            var successEl = document.getElementById('non-member-register-success');
            var paymentWrap = document.getElementById('non-member-payment-wrap');
            clearFormFieldErrors(form);
            statusEl.textContent = 'Submitting...';
            statusEl.classList.remove('hidden', 'text-red-400', 'text-primary', 'rounded-lg', 'p-3', 'bg-red-500/10', 'border', 'border-red-500/30', 'mb-4');
            successEl.classList.add('hidden');
            paymentWrap.classList.add('hidden');
            var formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(function(r) { return r.json(); }).then(function(data) {
                statusEl.classList.add('hidden');
                if (data.status === 'success') {
                    if (data.requires_payment && data.checkout_id && data.widget_url) {
                        formWrap.classList.add('hidden');
                        paymentWrap.classList.remove('hidden');
                        [].forEach.call(document.querySelectorAll('script[src*="paymentWidgets"]'), function(s) { s.remove(); });
                        var script = document.createElement('script');
                        script.src = data.widget_url + '?checkoutId=' + data.checkout_id;
                        script.async = true;
                        document.body.appendChild(script);
                        paymentWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } else {
                        if (formWrap) formWrap.classList.add('hidden');
                        successEl.classList.remove('hidden');
                        successEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                } else {
                    statusEl.innerHTML = '';
                    statusEl.textContent = data.message || 'An error occurred.';
                    statusEl.classList.remove('hidden');
                    statusEl.classList.add('text-red-400', 'rounded-lg', 'p-3', 'bg-red-500/10', 'border', 'border-red-500/30', 'mb-4');
                    if (data.errors && typeof data.errors === 'object') showFormFieldErrors(form, data.errors);
                    form.querySelector('[name="email"]') && form.querySelector('[name="email"]').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }).catch(function() {
                statusEl.textContent = 'Submission failed. Please try again.';
                statusEl.classList.remove('hidden');
                statusEl.classList.add('text-red-400');
            });
        });
    }

    var memberForm = document.getElementById('member-lookup-form');
    if (memberForm) {
        var eventId = memberForm.getAttribute('data-event-id');
        var fieldKey = memberForm.getAttribute('data-field-key');
        var baseUrl = '{{ url('/') }}';
        var lookupUrl = baseUrl + '/e/' + eventId + '/api/member-lookup';
        document.getElementById('btn-member-lookup').addEventListener('click', function() {
            var input = document.getElementById('member-unique-value');
            var msgEl = document.getElementById('member-lookup-message');
            var resultEl = document.getElementById('member-result');
            var value = (input.value || '').trim();
            if (!value) {
                msgEl.textContent = 'Please enter ' + (document.querySelector('#member-lookup-form label')?.textContent || 'the value') + '.';
                msgEl.classList.remove('hidden');
                msgEl.classList.add('text-primary');
                msgEl.classList.remove('text-muted-foreground');
                return;
            }
            msgEl.classList.add('hidden');
            this.disabled = true;
            this.textContent = 'Checking...';
            var btn = this;
            fetch(lookupUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ field_key: fieldKey, value: value })
            }).then(function(r) { return r.json(); }).then(function(data) {
                btn.disabled = false;
                btn.textContent = 'Verify';
                if (data.status === 'success' || data.status === 'expired') {
                    var m = data.member;
                    document.getElementById('member-name').textContent = (m.first_name || '') + ' ' + (m.last_name || '');
                    document.getElementById('member-email').textContent = m.email || '';
                    var fieldsHtml = '';
                    (m.fields || []).forEach(function(f) {
                        if (f.field_key === 'full_name') return;
                        fieldsHtml += '<div class="flex justify-between py-2 border-b border-border/50"><span class="text-muted-foreground">' + (f.label || f.field_key) + '</span><span class="text-foreground font-medium">' + (f.value || '-') + '</span></div>';
                    });
                    document.getElementById('member-fields').innerHTML = fieldsHtml || '<p class="text-muted-foreground text-sm">No additional fields.</p>';
                    var expiredAlert = document.getElementById('member-expired-alert');
                    var renewalLink = document.getElementById('member-renewal-link');
                    var actionsEl = document.getElementById('member-register-actions');
                    if (data.status === 'expired') {
                        expiredAlert.classList.remove('hidden');
                        if (data.renewal_link) renewalLink.setAttribute('href', data.renewal_link);
                        if (actionsEl) actionsEl.classList.add('hidden');
                    } else {
                        expiredAlert.classList.add('hidden');
                        if (m.event_member_id) {
                            document.getElementById('member-event-member-id').value = m.event_member_id;
                            if (actionsEl) actionsEl.classList.remove('hidden');
                            var formContainer = document.getElementById('members-registration-form-container');
                            var memberForm = document.getElementById('members-registration-form');
                            if (formContainer && memberForm && m.mapped_form_data) {
                                var d = m.mapped_form_data;
                                var fid = 'members-registration-form';
                                var firstInput = document.getElementById(fid + '-first_name');
                                var lastInput = document.getElementById(fid + '-last_name');
                                var emailInput = document.getElementById(fid + '-email');
                                var phoneInput = document.getElementById(fid + '-phone');
                                if (firstInput) firstInput.value = d.first_name || '';
                                if (lastInput) lastInput.value = d.last_name || '';
                                if (emailInput) emailInput.value = d.email || '';
                                if (phoneInput) phoneInput.value = d.phone || '';
                                if (d.fields && typeof d.fields === 'object') {
                                    for (var fieldId in d.fields) {
                                        var el = memberForm.querySelector('select[name="fields[' + fieldId + ']"], input[name="fields[' + fieldId + ']"], textarea[name="fields[' + fieldId + ']"]');
                                        if (el) {
                                            el.value = d.fields[fieldId];
                                            el.dispatchEvent(new Event('change', { bubbles: true }));
                                        }
                                    }
                                }
                                formContainer.classList.remove('hidden');
                            }
                        }
                    }
                    resultEl.classList.remove('hidden');
                    resultEl.classList.add('member-result-enter');
                    msgEl.classList.add('hidden');
                } else {
                    msgEl.textContent = data.message || 'Member not found.';
                    msgEl.classList.remove('hidden');
                    msgEl.classList.add('text-primary');
                    resultEl.classList.add('hidden');
                }
            }).catch(function() {
                btn.disabled = false;
                btn.textContent = 'Verify';
                msgEl.textContent = 'Request failed. Try again.';
                msgEl.classList.remove('hidden');
                msgEl.classList.add('text-primary');
            });
        });

        var memberRegForm = document.getElementById('members-registration-form');
        if (memberRegForm) {
            memberRegForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var form = this;
                var statusEl = document.getElementById('member-register-status');
                clearFormFieldErrors(form);
                statusEl.textContent = 'Submitting...';
                statusEl.classList.remove('hidden', 'text-red-400', 'rounded-lg', 'p-3', 'bg-red-500/10', 'border', 'border-red-500/30', 'mb-4');
                var formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                }).then(function(r) { return r.json(); }).then(function(data) {
                    statusEl.classList.add('hidden');
                    if (data.status === 'success') {
                        var successEl = document.getElementById('member-register-success');
                        var formContainer = document.getElementById('members-registration-form-container');
                        if (successEl) successEl.classList.remove('hidden');
                        if (formContainer) formContainer.classList.add('hidden');
                        successEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } else {
                        statusEl.textContent = data.message || 'An error occurred.';
                        statusEl.classList.remove('hidden');
                        statusEl.classList.add('text-red-400', 'rounded-lg', 'p-3', 'bg-red-500/10', 'border', 'border-red-500/30', 'mb-4');
                        if (data.errors && typeof data.errors === 'object') showFormFieldErrors(form, data.errors);
                        form.querySelector('[name="email"]') && form.querySelector('[name="email"]').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }).catch(function() {
                    statusEl.textContent = 'Submission failed. Please try again.';
                    statusEl.classList.remove('hidden');
                    statusEl.classList.add('text-red-400');
                });
            });
        }
    }
})();
</script>
@endsection
