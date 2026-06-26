@extends('ViewEvent.layouts.symposium-layout')

@section('title', isset($event) ? $event->title : 'Event')

@section('content')
<main class="min-h-screen">
    @include('ViewEvent.partials.landing.header')
    @include('ViewEvent.partials.landing.hero')
    @include('ViewEvent.partials.landing.pricing')

    @if(!empty($landing['registration']['enabled']))
    {{-- Registration Section --}}
    <section id="registration" class="py-20 hero-bg-custom relative">
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4"><span class="text-gold-gradient">{{ $landing['registration']['title'] ?? 'Registration' }}</span></h2>
                @if(!empty($landing['registration']['description']))
                <p class="text-muted-foreground text-lg">{{ $landing['registration']['description'] }}</p>
                @endif
            </div>
            <div class="max-w-2xl mx-auto card-navy rounded-2xl p-6 md:p-8">
                @if(isset($is_private_form) && $is_private_form && isset($registration))
                    {{-- Private form only: show this registration form only (no Non-Members / Members tabs). Reuse same ids so existing JS works. --}}
                    <div id="reg-non-members" class="space-y-6">
                        <p class="text-center text-primary font-semibold text-lg mb-4">Free registration – التسجيل مجاني</p>
                        @if(isset($registration_expired) && $registration_expired)
                            <div class="p-6 rounded-xl border border-amber-500/40 bg-amber-500/10 text-amber-800 dark:text-amber-200 text-center">
                                <p class="font-semibold">Registration period has ended for this form.</p>
                                <p class="text-sm mt-2 opacity-90">This registration option is no longer available.</p>
                            </div>
                        @else
                        <div id="non-member-form-wrap">
                            @include('ViewEvent.partials.SymposiumRegistrationForm', ['landingRegistration' => $registration, 'event' => $event, 'countries' => $countries ?? collect()])
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
                                    <p class="text-sm mt-1 opacity-90">Please check your email (including spam/junk folder) for confirmation.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                @else
                <div class="flex rounded-xl overflow-hidden mb-8" role="tablist">
                    <button type="button" data-tab="non-members" class="reg-tab flex-1 py-3 px-4 font-bold transition-all duration-300 tab-active">Non-Members</button>
                    <button type="button" data-tab="members" class="reg-tab flex-1 py-3 px-4 font-bold transition-all duration-300 tab-inactive">Members</button>
                    <button type="button" data-tab="virtual" class="reg-tab flex-1 py-3 px-4 font-bold transition-all duration-300 tab-inactive">Virtual Reg Zoom</button>
                </div>
                {{-- Non-Members tab (active by default) --}}
                <div id="reg-non-members" class="reg-panel space-y-6">
                    @php
                        $landingForms = isset($landingRegistrations) && $landingRegistrations->count() > 0
                            ? $landingRegistrations
                            : collect($landingRegistration ? [$landingRegistration] : []);
                    @endphp
                    @if($landingForms->count() > 1)
                        <div>
                            <label for="landing-registration-select" class="block text-foreground mb-2 font-medium">Select registration form</label>
                            <select id="landing-registration-select" class="w-full input-navy rounded-lg py-3 px-4">
                                @foreach($landingForms as $landingForm)
                                    <option value="{{ $landingForm->id }}">{{ $landingForm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div id="non-member-form-wrap">
                        @forelse($landingForms as $landingForm)
                            <div class="landing-registration-form-panel {{ $loop->first ? '' : 'hidden' }}" data-registration-id="{{ $landingForm->id }}">
                                @include('ViewEvent.partials.SymposiumRegistrationForm', [
                                    'landingRegistration' => $landingForm,
                                    'event' => $event,
                                    'countries' => $countries ?? collect(),
                                    'formId' => 'symposium-registration-form-' . $landingForm->id,
                                ])
                            </div>
                        @empty
                            @include('ViewEvent.partials.SymposiumRegistrationForm', ['landingRegistration' => null, 'event' => $event, 'countries' => $countries ?? collect()])
                        @endforelse
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
                                <p class="text-sm mt-1 opacity-90">Please check your email (including spam/junk folder) for confirmation.</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Members tab --}}
                <div id="reg-members" class="reg-panel hidden space-y-6" data-members-registration-id="{{ isset($membersRegistration) && $membersRegistration ? $membersRegistration->id : '' }}">
                    @if(isset($displaySearchFields) && $displaySearchFields->isNotEmpty())
                    @php
                        $searchLabel = $displaySearchFields->pluck('label')->join(' or ');
                    @endphp
                    <div id="member-lookup-form" data-event-id="{{ $event->id }}" data-search-label="{{ e($searchLabel) }}">
                        <label class="block text-foreground mb-2 font-medium">{{ $searchLabel }}</label>
                        <div class="flex gap-3">
                            <input type="text" id="member-unique-value" class="flex-1 input-navy rounded-lg py-3 px-4" placeholder="{{ $searchLabel }}">
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
                                            <p class="text-sm mt-1 opacity-90">Please check your email (including spam/junk folder) for confirmation.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-muted-foreground text-center py-4">Members lookup is not configured. Add at least one &quot;Display &amp; search&quot; member field in Event → Members.</p>
                    @endif
                </div>
                
                 {{-- Virtual Reg tab --}}
                 <div id="reg-virtual" class="reg-panel hidden space-y-6" data-members-registration-id="{{ isset($virtualRegistration) && $virtualRegistration ? $virtualRegistration->id : '' }}">
                    <div id="virtual-form-wrap">
                        <p>Online attendees will not receive a certificate of attendance and are not eligible for CME hours</br>
                            الحضور عن بعد لا يشمل شهادة حضور و لا يتم احتساب ساعات علمية
                        </p>
                        @include('ViewEvent.partials.SymposiumRegistrationForm', ['landingRegistration' => $virtualRegistration, 'event' => $event, 'countries' => $countries ?? collect(), 'formId' => 'virtual-registration-form'])
                    
                    </div>
                    <p id="virtual-register-status" class="text-sm text-muted-foreground hidden"></p>
                    <div id="virtual-payment-wrap" class="hidden mt-4 rounded-xl border border-primary/30 bg-primary/5 p-6">
                        <h4 class="text-lg font-semibold text-foreground mb-4">إتمام الدفع</h4>
                        <p class="text-muted-foreground mb-4">يرجى إدخال بيانات الدفع أدناه.</p>
                        <form id="virtual-hyperpay-form" action="#" class="paymentWidgets" data-brands="VISA MASTER MADA AMEX"></form>
                    </div>
                    <div id="virtual-register-success" class="hidden mt-4 p-4 rounded-xl border border-green-500/40 bg-green-500/10 text-green-800 dark:text-green-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-semibold">Registration Successful</p>
                                <p class="text-sm mt-1 opacity-90">Your registration request has been received. It will be reviewed by our team.</p>
                                <p class="text-sm mt-1 opacity-90">Please check your email (including spam/junk folder) for confirmation.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @endif
            </div>
        </div>
    </section>
    @endif

    @include('ViewEvent.partials.landing.location')
    @include('ViewEvent.partials.landing.footer')
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

    // Non-Members form(s): submit via AJAX, show success or payment on same page (no redirect/reload)
    var landingRegistrationSelect = document.getElementById('landing-registration-select');
    if (landingRegistrationSelect) {
        landingRegistrationSelect.addEventListener('change', function() {
            var selectedId = this.value;
            document.querySelectorAll('.landing-registration-form-panel').forEach(function(panel) {
                panel.classList.toggle('hidden', panel.getAttribute('data-registration-id') !== selectedId);
            });
        });
    }

    function bindNonMemberRegistrationForm(form) {
        if (!form || form.dataset.bound === '1') {
            return;
        }
        form.dataset.bound = '1';
        form.addEventListener('submit', function(e) {
            e.preventDefault();
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

    document.querySelectorAll('[id^="symposium-registration-form"]').forEach(bindNonMemberRegistrationForm);
    var legacyNonMemberForm = document.getElementById('symposium-registration-form');
    if (legacyNonMemberForm) {
        bindNonMemberRegistrationForm(legacyNonMemberForm);
    }

    
    
     var virtualForm = document.getElementById('virtual-registration-form');
    if (virtualForm) {
        virtualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var statusEl = document.getElementById('virtual-register-status');
            var formWrap = document.getElementById('virtual-form-wrap');
            var successEl = document.getElementById('virtual-register-success');
            var paymentWrap = document.getElementById('virtual-payment-wrap');
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
                body: JSON.stringify({ value: value })
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

(function mobileMenu() {
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
    if (backdrop) backdrop.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); closeMenu(); } });
    var closeBtn = document.getElementById('mobile-menu-close');
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    menu.addEventListener('click', function(e) {
        if (e.target.id === 'mobile-menu-backdrop') closeMenu();
    });

    document.querySelectorAll('.mobile-nav-link').forEach(function(el) {
        el.addEventListener('click', function() {
            if (el.getAttribute('data-committee-toggle')) return;
            var id = el.getAttribute('data-scroll');
            if (id) {
                var target = document.getElementById(id);
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            }
            closeMenu();
        });
    });

    document.querySelectorAll('[data-committee-toggle]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-committee-toggle');
            var panel = document.querySelector('[data-committee-panel="' + id + '"]');
            var arrow = this.querySelector('.committee-arrow');
            if (panel && panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            } else if (panel) {
                panel.classList.add('hidden');
                if (arrow) arrow.style.transform = '';
            }
        });
    });
})();
</script>
@endsection
