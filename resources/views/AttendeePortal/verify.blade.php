@extends('AttendeePortal.layouts.app', ['hideNav' => true])

@section('title', trans('AttendeePortal.verify'))

@section('main_class', 'min-h-full flex items-center justify-center px-4 py-12')

@section('content')
<div class="w-full max-w-md animate-fade-in">
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 mb-4 bg-indigo-600 rounded-xl">
            <i class="text-2xl text-white fas fa-envelope-open-text"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">@lang('AttendeePortal.verify')</h1>
        <p class="mt-1 text-sm text-gray-500">@lang('AttendeePortal.verify_subtitle')</p>
    </div>

    <div class="p-8 bg-white border border-gray-200 shadow-sm rounded-2xl">
        @if(session('success'))
            <div class="flex items-start p-3 mb-5 text-sm text-green-800 border border-green-200 rounded-xl bg-green-50">
                <i class="mt-0.5 mr-2 fas fa-check-circle text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="flex items-start p-3 mb-5 text-sm text-red-700 border border-red-200 rounded-xl bg-red-50">
                <i class="mt-0.5 mr-2 fas fa-exclamation-circle text-red-500"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('postAttendeePortalVerify', ['event_id' => $event->id]) }}" id="otp-form" class="space-y-6">
            @csrf
            <input type="hidden" name="identifier" value="{{ $identifier }}">
            <input type="hidden" name="code" id="code" value="{{ old('code') }}">

            <div>
                <label class="block mb-3 text-sm font-medium text-center text-gray-700">@lang('AttendeePortal.code_label')</label>
                <div id="otp-boxes" class="flex justify-center gap-2 sm:gap-3" dir="ltr">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text"
                            inputmode="numeric"
                            autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                            maxlength="1"
                            data-otp-index="{{ $i }}"
                            aria-label="Digit {{ $i + 1 }}"
                            class="otp-digit w-11 h-12 sm:w-12 sm:h-14 text-center text-xl sm:text-2xl font-semibold text-gray-900 bg-gray-50 border-2 border-gray-200 rounded-xl outline-none transition-all duration-200 focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-100 hover:border-gray-300">
                    @endfor
                </div>
            </div>

            <div class="flex items-center justify-center">
                <input type="checkbox" name="remember" id="remember" value="1"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-600">
                <label for="remember" class="ml-2 text-sm text-gray-600">@lang('AttendeePortal.remember_me')</label>
            </div>

            <button type="submit" id="otp-submit"
                class="w-full px-4 py-3 text-sm font-semibold text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                @lang('AttendeePortal.verify_button')
            </button>
        </form>

        <p class="mt-6 text-sm text-center text-gray-500">
            <a href="{{ route('showAttendeePortalLogin', ['event_id' => $event->id]) }}" class="text-indigo-600 hover:underline">
                @lang('AttendeePortal.back_to_login')
            </a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var boxes = Array.prototype.slice.call(document.querySelectorAll('.otp-digit'));
    var hidden = document.getElementById('code');
    var form = document.getElementById('otp-form');
    var submitBtn = document.getElementById('otp-submit');
    var oldCode = (hidden.value || '').replace(/\D/g, '').slice(0, 6);

    function syncHidden() {
        hidden.value = boxes.map(function (b) { return b.value; }).join('');
        submitBtn.disabled = hidden.value.length !== 6;
    }

    function fillFrom(startIndex, digits) {
        var i = startIndex;
        digits.split('').forEach(function (d) {
            if (i < boxes.length && /\d/.test(d)) {
                boxes[i].value = d;
                boxes[i].classList.add('animate-pop', 'border-indigo-600', 'bg-white');
                i++;
            }
        });
        syncHidden();
        if (i < boxes.length) {
            boxes[i].focus();
        } else {
            boxes[boxes.length - 1].focus();
            if (hidden.value.length === 6) {
                form.requestSubmit ? form.requestSubmit() : form.submit();
            }
        }
    }

    boxes.forEach(function (box, index) {
        box.addEventListener('input', function (e) {
            var val = e.target.value.replace(/\D/g, '');
            if (val.length > 1) {
                fillFrom(index, val);
                return;
            }
            e.target.value = val.slice(-1);
            if (e.target.value) {
                e.target.classList.add('animate-pop', 'border-indigo-600', 'bg-white');
                if (index < boxes.length - 1) {
                    boxes[index + 1].focus();
                } else if (boxes.every(function (b) { return b.value; })) {
                    syncHidden();
                    form.requestSubmit ? form.requestSubmit() : form.submit();
                    return;
                }
            }
            syncHidden();
        });

        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (!box.value && index > 0) {
                    boxes[index - 1].value = '';
                    boxes[index - 1].classList.remove('border-indigo-600', 'bg-white');
                    boxes[index - 1].focus();
                    syncHidden();
                    e.preventDefault();
                } else if (box.value) {
                    box.value = '';
                    box.classList.remove('border-indigo-600', 'bg-white');
                    syncHidden();
                }
            } else if (e.key === 'ArrowLeft' && index > 0) {
                boxes[index - 1].focus();
                e.preventDefault();
            } else if (e.key === 'ArrowRight' && index < boxes.length - 1) {
                boxes[index + 1].focus();
                e.preventDefault();
            }
        });

        box.addEventListener('paste', function (e) {
            e.preventDefault();
            var text = (e.clipboardData || window.clipboardData).getData('text') || '';
            var digits = text.replace(/\D/g, '').slice(0, 6);
            if (digits) {
                fillFrom(0, digits);
            }
        });

        box.addEventListener('focus', function () {
            box.select();
        });
    });

    if (oldCode) {
        fillFrom(0, oldCode);
    } else {
        boxes[0].focus();
        syncHidden();
    }
})();
</script>
@endpush
