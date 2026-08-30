<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $abstract->name }} — {{ $event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: {
                            50: '#f8f9fa',
                            100: '#f1f3f4',
                            200: '#e8eaed',
                            300: '#dadce0',
                            400: '#bdc1c6',
                            500: '#9aa0a6',
                            600: '#80868b',
                            700: '#5f6368',
                            800: '#3c4043',
                            900: '#202124',
                            950: '#171717'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.35s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(8px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(16px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .abstract-instructions h1,
        .abstract-instructions h2,
        .abstract-instructions h3 { font-weight: 600; color: #202124; margin: 0.75rem 0 0.4rem; }
        .abstract-instructions p { margin: 0.4rem 0; }
        .abstract-instructions ul { list-style: disc; padding-left: 1.25rem; margin: 0.5rem 0; }
        .abstract-instructions ol { list-style: decimal; padding-left: 1.25rem; margin: 0.5rem 0; }
        .abstract-instructions a { color: #202124; text-decoration: underline; }
        .field-input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid #e8eaed;
            background: #fff;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            color: #202124;
            transition: border-color .15s, box-shadow .15s;
        }
        .field-input:focus {
            outline: none;
            border-color: #202124;
            box-shadow: 0 0 0 3px rgba(32, 33, 36, 0.08);
        }
        .field-label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #3c4043;
        }
    </style>
</head>
<body class="h-full font-sans antialiased bg-gray-50 text-ink-900">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-3xl px-4 mx-auto sm:px-6">
            <div class="flex items-center h-16 space-x-3">
                <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-black rounded-lg">
                    <i class="text-white fas fa-file-alt"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-base font-bold text-gray-900 truncate sm:text-lg">{{ $event->title }}</h1>
                    <p class="text-xs text-gray-500 sm:text-sm">Abstract submission</p>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl px-4 py-8 mx-auto sm:px-6 sm:py-12 animate-fade-in">
        <div class="mb-8">
            <p class="mb-2 text-xs font-medium tracking-wide text-gray-500 uppercase">Submit your work</p>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">{{ $abstract->name }}</h2>
        </div>

        @if(session('success'))
            <div class="flex items-start p-4 mb-6 space-x-3 text-green-800 bg-green-50 border border-green-200 rounded-xl animate-slide-up">
                <i class="mt-0.5 fas fa-check-circle"></i>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-start p-4 mb-6 space-x-3 text-red-800 bg-red-50 border border-red-200 rounded-xl">
                <i class="mt-0.5 fas fa-exclamation-circle"></i>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 mb-6 text-red-800 bg-red-50 border border-red-200 rounded-xl">
                <ul class="space-y-1 text-sm list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($abstract->instructions)
            <div class="p-5 mb-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center mb-3 space-x-2">
                    <i class="text-gray-400 fas fa-info-circle"></i>
                    <h3 class="text-sm font-semibold text-gray-900">@lang('Abstract.instructions')</h3>
                </div>
                <div class="text-sm leading-relaxed text-gray-600 abstract-instructions">
                    {!! $abstract->instructions !!}
                </div>
            </div>
        @endif

        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl sm:p-8 animate-slide-up">
            @if($isRegisteredOnly)
                <div id="verify-step">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">@lang('Abstract.verify')</h3>
                        <p class="mt-1 text-sm text-gray-500">@lang('Abstract.verify_identifier_help')</p>
                    </div>

                    <div id="verify-message" class="hidden mb-4 rounded-xl px-4 py-3 text-sm"></div>

                    <div class="space-y-4">
                        <div>
                            <label class="field-label" for="verify-identifier">@lang('Abstract.verify_identifier')</label>
                            <input type="text" id="verify-identifier" autocomplete="email"
                                   class="field-input"
                                   placeholder="name@example.com or registration code">
                        </div>
                        <button type="button" id="verify-btn"
                                class="inline-flex items-center justify-center w-full px-5 py-3 text-sm font-medium text-white transition bg-black rounded-xl hover:bg-gray-800 sm:w-auto">
                            <i class="mr-2 fas fa-shield-alt"></i>
                            @lang('Abstract.verify')
                        </button>
                    </div>
                </div>

                <div id="upload-step" class="hidden">
                    <div id="verified-user-info" class="flex items-start p-4 mb-6 space-x-3 text-green-800 bg-green-50 border border-green-200 rounded-xl">
                        <i class="mt-0.5 fas fa-user-check"></i>
                        <p class="text-sm font-medium"></p>
                    </div>

                    <form method="POST"
                          action="{{ route('postEventAbstractSubmission', ['event_id' => $event->id, 'slug' => $abstract->slug]) }}"
                          enctype="multipart/form-data"
                          class="space-y-5">
                        @csrf
                        @include('Public.ViewEvent.Partials.AbstractCategorySelect', ['abstract' => $abstract])
                        <div>
                            <label class="field-label">@lang('Abstract.file_upload') <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="file" name="file" required accept=".pdf,.ppt,.pptx,.doc,.docx,.zip"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-gray-100 file:text-gray-800 file:font-medium hover:file:bg-gray-200">
                            </div>
                            <p class="mt-2 text-xs text-gray-400">PDF, PPT, DOC, ZIP — max 20MB</p>
                        </div>
                        <button type="submit"
                                class="inline-flex items-center justify-center w-full px-5 py-3 text-sm font-medium text-white transition bg-black rounded-xl hover:bg-gray-800">
                            <i class="mr-2 fas fa-paper-plane"></i>
                            Submit Abstract
                        </button>
                    </form>
                </div>
            @else
                <form method="POST"
                      action="{{ route('postEventAbstractSubmission', ['event_id' => $event->id, 'slug' => $abstract->slug]) }}"
                      enctype="multipart/form-data"
                      class="space-y-5">
                    @csrf

                    @include('Public.ViewEvent.Partials.AbstractCategorySelect', ['abstract' => $abstract])

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="field-label">@lang('Abstract.full_name') <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required class="field-input" placeholder="Your full name">
                        </div>
                        <div>
                            <label class="field-label">@lang('Abstract.email') <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="field-input" placeholder="name@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="field-label">@lang('Abstract.phone')</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="field-input" placeholder="+966...">
                    </div>

                    <div>
                        <label class="field-label">@lang('Abstract.authors')</label>
                        <textarea name="authors" rows="2" class="field-input" placeholder="Author names">{{ old('authors') }}</textarea>
                    </div>

                    <div>
                        <label class="field-label">@lang('Abstract.details')</label>
                        <textarea name="details" rows="4" class="field-input" placeholder="Brief description of your abstract">{{ old('details') }}</textarea>
                    </div>

                    <div>
                        <label class="field-label">@lang('Abstract.domain')</label>
                        <input type="text" name="domain" value="{{ old('domain') }}" class="field-input" placeholder="Research domain / topic">
                    </div>

                    <div>
                        <label class="field-label">@lang('Abstract.file_upload') <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required accept=".pdf,.ppt,.pptx,.doc,.docx,.zip"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-gray-100 file:text-gray-800 file:font-medium hover:file:bg-gray-200">
                        <p class="mt-2 text-xs text-gray-400">PDF, PPT, DOC, ZIP — max 20MB</p>
                    </div>

                    @foreach($abstract->dynamicFormFields as $field)
                        <div>
                            <label class="field-label">
                                {{ $field->label }}
                                @if($field->is_required)<span class="text-red-500">*</span>@endif
                            </label>
                            @if($field->description)
                                <p class="mb-2 text-xs text-gray-400">{{ $field->description }}</p>
                            @endif

                            @if($field->type === 'textarea')
                                <textarea name="fields[{{ $field->id }}]" rows="3" class="field-input"
                                          {{ $field->is_required ? 'required' : '' }}>{{ old('fields.'.$field->id) }}</textarea>
                            @elseif($field->type === 'select')
                                <select name="fields[{{ $field->id }}]" class="field-input" {{ $field->is_required ? 'required' : '' }}>
                                    <option value="">Select...</option>
                                    @foreach(($field->options ?? []) as $opt)
                                        <option value="{{ $opt }}" {{ old('fields.'.$field->id) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif($field->type === 'radio')
                                <div class="space-y-2">
                                    @foreach(($field->options ?? []) as $opt)
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input type="radio" name="fields[{{ $field->id }}]" value="{{ $opt }}"
                                                   class="text-black border-gray-300 focus:ring-black"
                                                   {{ $field->is_required ? 'required' : '' }}
                                                   {{ old('fields.'.$field->id) == $opt ? 'checked' : '' }}>
                                            {{ $opt }}
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($field->type === 'checkbox')
                                <div class="space-y-2">
                                    @foreach(($field->options ?? []) as $opt)
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input type="checkbox" name="fields[{{ $field->id }}][]" value="{{ $opt }}"
                                                   class="text-black border-gray-300 rounded focus:ring-black"
                                                   {{ is_array(old('fields.'.$field->id)) && in_array($opt, old('fields.'.$field->id)) ? 'checked' : '' }}>
                                            {{ $opt }}
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($field->type === 'file')
                                <input type="file" name="fields[{{ $field->id }}]"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-gray-100 file:text-gray-800"
                                       {{ $field->is_required ? 'required' : '' }}>
                            @else
                                <input type="{{ $field->type === 'tel' ? 'tel' : ($field->type ?: 'text') }}"
                                       name="fields[{{ $field->id }}]"
                                       value="{{ old('fields.'.$field->id) }}"
                                       class="field-input"
                                       {{ $field->is_required ? 'required' : '' }}>
                            @endif
                        </div>
                    @endforeach

                    <div class="pt-2">
                        <button type="submit"
                                class="inline-flex items-center justify-center w-full px-5 py-3 text-sm font-medium text-white transition bg-black rounded-xl hover:bg-gray-800">
                            <i class="mr-2 fas fa-paper-plane"></i>
                            Submit Abstract
                        </button>
                    </div>
                </form>
            @endif
        </div>

        <p class="mt-8 text-xs text-center text-gray-400">
            {{ $event->title }}
        </p>
    </main>

    @if($isRegisteredOnly)
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var verifyBtn = document.getElementById('verify-btn');
        var msgEl = document.getElementById('verify-message');
        var uploadStep = document.getElementById('upload-step');
        var verifyStep = document.getElementById('verify-step');
        var userInfo = document.getElementById('verified-user-info').querySelector('p');

        function showMsg(text, type) {
            msgEl.className = 'mb-4 rounded-xl px-4 py-3 text-sm border';
            if (type === 'success') msgEl.className += ' bg-green-50 border-green-200 text-green-800';
            else if (type === 'warning') msgEl.className += ' bg-yellow-50 border-yellow-200 text-yellow-800';
            else msgEl.className += ' bg-red-50 border-red-200 text-red-800';
            msgEl.textContent = text;
            msgEl.classList.remove('hidden');
        }

        verifyBtn.addEventListener('click', function () {
            var identifier = document.getElementById('verify-identifier').value.trim();
            if (!identifier) {
                showMsg('Please enter your email or registration code.', 'error');
                return;
            }
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<i class="mr-2 fas fa-spinner fa-spin"></i> Verifying...';

            fetch('{{ route('postEventAbstractVerify', ['event_id' => $event->id, 'slug' => $abstract->slug]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ identifier: identifier })
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (res) {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="mr-2 fas fa-shield-alt"></i> @lang('Abstract.verify')';
                var d = res.data;
                if (d.status === 'approved') {
                    showMsg(d.message, 'success');
                    userInfo.textContent = (d.user ? (d.user.full_name + ' · ' + d.user.email) : d.message);
                    verifyStep.classList.add('hidden');
                    uploadStep.classList.remove('hidden');
                } else if (d.status === 'pending' || d.status === 'rejected') {
                    showMsg(d.message, 'warning');
                    uploadStep.classList.add('hidden');
                } else {
                    showMsg(d.message || 'Verification failed.', 'error');
                    uploadStep.classList.add('hidden');
                }
            })
            .catch(function () {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="mr-2 fas fa-shield-alt"></i> @lang('Abstract.verify')';
                showMsg('Verification failed. Please try again.', 'error');
            });
        });
    });
    </script>
    @endif
</body>
</html>
