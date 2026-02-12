{{-- Registration form for symposium (landing or members). Uses $landingRegistration, $event, $countries. Optional: $formId for unique id. --}}
@if($landingRegistration)
@php
    $formId = $formId ?? 'symposium-registration-form';
@endphp
@php
    $conferenceField = $landingRegistration->dynamicFormFields && $landingRegistration->dynamicFormFields->count() > 0
        ? $landingRegistration->dynamicFormFields->where('type', 'conference')->first()
        : null;
    $professionField = $landingRegistration->dynamicFormFields && $landingRegistration->dynamicFormFields->count() > 0
        ? $landingRegistration->dynamicFormFields->where('type', 'profession')->first()
        : null;
    $externalPaymentField = $landingRegistration->dynamicFormFields && $landingRegistration->dynamicFormFields->count() > 0
        ? $landingRegistration->dynamicFormFields->where('type', 'external_payment')->first()
        : null;
    $currencyCode = $event->currency ? $event->currency->code : 'SAR';
@endphp
<form method="POST" action="{{ route('postEventRegistration', ['event_id' => $event->id, 'registration_id' => $landingRegistration->id]) }}" enctype="multipart/form-data" class="space-y-6" id="{{ $formId }}">
    @csrf
    @if($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3">
        <p class="font-medium mb-1">يرجى تصحيح الأخطاء التالية:</p>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if($conferenceField && $professionField)
    <input type="hidden" name="fields[{{ $conferenceField->id }}]" id="{{ $formId }}-conference-id" value="">
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="{{ $formId }}-first_name" class="block text-foreground mb-2 font-medium">First Name <span class="text-primary">*</span></label>
            <input type="text" id="{{ $formId }}-first_name" name="first_name" value="{{ old('first_name') }}" required class="w-full input-navy rounded-lg py-3 px-4" placeholder="First Name">
        </div>
        <div>
            <label for="{{ $formId }}-last_name" class="block text-foreground mb-2 font-medium">Last Name <span class="text-primary">*</span></label>
            <input type="text" id="{{ $formId }}-last_name" name="last_name" value="{{ old('last_name') }}" required class="w-full input-navy rounded-lg py-3 px-4" placeholder="Last Name">
        </div>
    </div>
    <div>
        <label for="{{ $formId }}-email" class="block text-foreground mb-2 font-medium">Email <span class="text-primary">*</span></label>
        <input type="email" id="{{ $formId }}-email" name="email" value="{{ old('email') }}" required class="w-full input-navy rounded-lg py-3 px-4" placeholder="Email">
    </div>
    <div>
        <label for="{{ $formId }}-phone" class="block text-foreground mb-2 font-medium">Phone</label>
        <input type="tel" id="{{ $formId }}-phone" name="phone" value="{{ old('phone') }}" class="w-full input-navy rounded-lg py-3 px-4" placeholder="Phone">
    </div>
    @if($landingRegistration->dynamicFormFields && $landingRegistration->dynamicFormFields->count() > 0)
    <div class="border-t border-border pt-6 space-y-6">
        @foreach($landingRegistration->dynamicFormFields as $field)
        @if($field->type == 'external_payment')
        {{-- External payment: label/input rendered in external-payment-section below (bank details + receipt) --}}
        @continue
        @endif
        <div>
            <label for="field_{{ $field->id }}" class="block text-foreground mb-2 font-medium">{{ $field->label }}{{ $field->is_required ? ' *' : '' }}</label>
            @if($field->type == 'text' || $field->type == 'email' || $field->type == 'tel' || $field->type == 'number')
            <input type="{{ $field->type }}" id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" value="{{ old('fields.'.$field->id) }}" {{ $field->is_required ? 'required' : '' }} class="w-full input-navy rounded-lg py-3 px-4">
            @elseif($field->type == 'textarea')
            <textarea id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" rows="3" {{ $field->is_required ? 'required' : '' }} class="w-full input-navy rounded-lg py-3 px-4">{{ old('fields.'.$field->id) }}</textarea>
            @elseif($field->type == 'date')
            <input type="date" id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" value="{{ old('fields.'.$field->id) }}" {{ $field->is_required ? 'required' : '' }} class="w-full input-navy rounded-lg py-3 px-4">
            @elseif($field->type == 'file')
            <input type="file" id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }} class="w-full input-navy rounded-lg py-3 px-4 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/20 file:text-primary file:font-medium" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp">
            <p class="text-sm text-muted-foreground mt-1">Max 10 MB. Allowed: PDF, Word, images.</p>
            @elseif($field->type == 'select')
            <select id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }} class="w-full input-navy rounded-lg py-3 px-4">
                <option value="">-- Select --</option>
                @if(is_array($field->options))
                    @foreach($field->options as $opt)
                    <option value="{{ trim($opt) }}" {{ old('fields.'.$field->id) == trim($opt) ? 'selected' : '' }}>{{ trim($opt) }}</option>
                    @endforeach
                @endif
            </select>
            @elseif($field->type == 'profession')
            <select id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }} class="w-full input-navy rounded-lg py-3 px-4 symposium-profession-select">
                <option value="" data-price="0">-- Select profession --</option>
                @if($landingRegistration->category && $landingRegistration->category->conferences)
                    @foreach($landingRegistration->category->conferences as $conference)
                        @php $price = $conference->getPriceForCategory($landingRegistration->category_id); @endphp
                        @foreach($conference->professions as $profession)
                            <option value="{{ $profession->id }}" data-conference-id="{{ $conference->id }}" data-price="{{ $price }}" {{ old('fields.'.$field->id) == $profession->id ? 'selected' : '' }}>
                                {{ $profession->name }} - {{ number_format($price, 2) }} {{ $currencyCode }}
                            </option>
                        @endforeach
                    @endforeach
                @endif
            </select>
            @elseif($field->type == 'conference')
            @if(!$professionField)
            <select id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }} class="w-full input-navy rounded-lg py-3 px-4 symposium-conference-select">
                <option value="" data-price="0">-- Select conference --</option>
                @if($landingRegistration->category && $landingRegistration->category->conferences)
                    @php $currencyCode = $event->currency ? $event->currency->code : 'SAR'; @endphp
                    @foreach($landingRegistration->category->conferences as $conf)
                        @php $price = $conf->getPriceForCategory($landingRegistration->category_id); @endphp
                        <option value="{{ $conf->id }}" data-price="{{ $price }}" {{ old('fields.'.$field->id) == $conf->id ? 'selected' : '' }}>
                            {{ $conf->name }} - {{ number_format($price, 2) }} {{ $currencyCode }}
                        </option>
                    @endforeach
                @endif
            </select>
            @endif
            @elseif($field->type == 'external_payment')
            {{-- Rendered below in external-payment-section when profession has price --}}
            @else
            <input type="text" id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" value="{{ old('fields.'.$field->id) }}" {{ $field->is_required ? 'required' : '' }} class="w-full input-navy rounded-lg py-3 px-4">
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($externalPaymentField)
    @php $bankOpts = $externalPaymentField->getBankOptions(); @endphp
    <div id="{{ $formId }}-external-payment-section" class="border-t border-border pt-6 space-y-4 hidden">
        <h4 class="text-lg font-semibold text-foreground">External Payment (Bank Transfer)</h4>
        <div class="rounded-lg border border-primary/30 bg-primary/5 p-4 space-y-3">
            <p class="text-foreground font-medium">Total: <span id="{{ $formId }}-external-payment-total" class="text-primary">0.00</span> {{ $currencyCode }}</p>
            @if(!empty(array_filter($bankOpts)))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                @if(!empty($bankOpts['bank_account_name']))<p><span class="text-muted-foreground">Account Name:</span> <span class="font-medium">{{ $bankOpts['bank_account_name'] }}</span></p>@endif
                @if(!empty($bankOpts['bank_name']))<p><span class="text-muted-foreground">Bank Name:</span> <span class="font-medium">{{ $bankOpts['bank_name'] }}</span></p>@endif
                @if(!empty($bankOpts['bank_iban']))
                <p class="flex items-center gap-2">
                    <span class="text-muted-foreground">IBAN:</span> 
                    <span class="font-medium" id="{{ $formId }}-iban-value">{{ $bankOpts['bank_iban'] }}</span>
                    <button type="button" onclick="copyToClipboard('{{ $bankOpts['bank_iban'] }}', '{{ $formId }}-iban-copy-btn')" id="{{ $formId }}-iban-copy-btn" class="text-primary hover:text-accent transition-colors" title="Copy IBAN">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </p>
                @endif
                @if(!empty($bankOpts['bank_account_number']))
                <p class="flex items-center gap-2">
                    <span class="text-muted-foreground">Account Number:</span> 
                    <span class="font-medium" id="{{ $formId }}-account-value">{{ $bankOpts['bank_account_number'] }}</span>
                    <button type="button" onclick="copyToClipboard('{{ $bankOpts['bank_account_number'] }}', '{{ $formId }}-account-copy-btn')" id="{{ $formId }}-account-copy-btn" class="text-primary hover:text-accent transition-colors" title="Copy Account Number">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </p>
                @endif
            </div>
            @endif
            <div>
                <label for="field_{{ $externalPaymentField->id }}" class="block text-foreground mb-2 font-medium">Upload transfer receipt <span class="text-primary">*</span></label>
                <input type="file" id="field_{{ $externalPaymentField->id }}" name="fields[{{ $externalPaymentField->id }}]" class="w-full input-navy rounded-lg py-3 px-4 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/20 file:text-primary file:font-medium" accept=".pdf,.jpg,.jpeg,.png,.webp">
                <p class="text-sm text-muted-foreground mt-1">Upload image or PDF of your bank transfer receipt.</p>
            </div>
        </div>
    </div>
    @endif

    <button type="submit" class="w-full btn-gold py-3">Submit Registration</button>
</form>
<script>
(function() {
    var formId = '{{ $formId }}';
    var form = document.getElementById(formId);
    if (!form) return;
    var confInput = document.getElementById(formId + '-conference-id');
    var professionSelect = form.querySelector('.symposium-profession-select');
    var conferenceSelect = form.querySelector('.symposium-conference-select');
    var externalSection = document.getElementById(formId + '-external-payment-section');
    var externalTotalEl = document.getElementById(formId + '-external-payment-total');
    var currencyCode = '{{ $currencyCode }}';

    function setConferenceFromProfession() {
        if (confInput && professionSelect) {
            var opt = professionSelect.options[professionSelect.selectedIndex];
            confInput.value = opt && opt.getAttribute('data-conference-id') ? opt.getAttribute('data-conference-id') : '';
        }
    }
    function getSelectedPrice() {
        var price = 0;
        if (professionSelect) {
            var opt = professionSelect.options[professionSelect.selectedIndex];
            if (opt && opt.getAttribute('data-price')) {
                price = parseFloat(opt.getAttribute('data-price'));
            }
        } else if (conferenceSelect) {
            var opt = conferenceSelect.options[conferenceSelect.selectedIndex];
            if (opt && opt.getAttribute('data-price')) {
                price = parseFloat(opt.getAttribute('data-price'));
            }
        }
        return price;
    }
    function updateExternalPaymentSection() {
        if (!externalSection) return;
        var price = getSelectedPrice();
        var receiptInput = externalSection.querySelector('input[type="file"]');
        if (price > 0) {
            externalSection.classList.remove('hidden');
            if (externalTotalEl) externalTotalEl.textContent = price.toFixed(2);
            if (receiptInput) receiptInput.setAttribute('required', 'required');
        } else {
            externalSection.classList.add('hidden');
            if (externalTotalEl) externalTotalEl.textContent = '0.00';
            if (receiptInput) receiptInput.removeAttribute('required');
        }
    }
    if (professionSelect) {
        professionSelect.addEventListener('change', function() {
            setConferenceFromProfession();
            updateExternalPaymentSection();
        });
        setConferenceFromProfession();
        updateExternalPaymentSection();
    }
    if (conferenceSelect) {
        conferenceSelect.addEventListener('change', function() {
            updateExternalPaymentSection();
        });
        updateExternalPaymentSection();
    }
})();

// Copy to clipboard function
function copyToClipboard(text, buttonId) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showCopyFeedback(buttonId, true);
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            fallbackCopyToClipboard(text, buttonId);
        });
    } else {
        fallbackCopyToClipboard(text, buttonId);
    }
}

function fallbackCopyToClipboard(text, buttonId) {
    var textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showCopyFeedback(buttonId, true);
    } catch (err) {
        console.error('Fallback copy failed:', err);
        showCopyFeedback(buttonId, false);
    }
    document.body.removeChild(textArea);
}

function showCopyFeedback(buttonId, success) {
    var button = document.getElementById(buttonId);
    if (!button) return;
    
    var svg = button.querySelector('svg');
    if (!svg) return;
    
    // Save original SVG
    if (!button.dataset.originalSvg) {
        button.dataset.originalSvg = svg.innerHTML;
    }
    
    if (success) {
        // Show checkmark icon
        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
        button.style.color = '#10b981'; // green-500
        button.classList.remove('text-primary');
        
        // Restore after 2 seconds
        setTimeout(function() {
            svg.innerHTML = button.dataset.originalSvg;
            button.style.color = '';
            button.classList.add('text-primary');
        }, 2000);
    }
}
</script>
@else
<p class="text-muted-foreground text-center py-4">No landing registration form is configured. Set one registration as "Display on landing page" in Event → Registration.</p>
@endif
