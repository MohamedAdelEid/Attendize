<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', isset($event) ? $event->title : 'Event')</title>
    @php
        if (!isset($landing) && isset($event)) {
            $landing = app(\App\Services\EventLandingPageService::class)->resolve($event);
        }
        $themeConfig = isset($landing) ? ($landing['theme'] ?? []) : [];
        $colors = $themeConfig['colors'] ?? [
            'background' => '220 60% 8%',
            'foreground' => '0 0% 98%',
            'primary' => '45 70% 50%',
            'secondary' => '220 50% 18%',
            'accent' => '45 65% 55%',
            'muted' => '220 45% 20%',
            'border' => '220 40% 25%',
        ];
        $fonts = $themeConfig['fonts'] ?? ['body' => 'Inter', 'heading' => 'Playfair Display'];
        $og = $themeConfig['og'] ?? [];
        $faviconUrl = $themeConfig['favicon_url'] ?? route('symposiumFavicon');
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <meta property="og:site_name" content="{{ $og['site_name'] ?? ($event->title ?? 'Event') }}">
    <meta property="og:title" content="{{ $og['title'] ?? ($event->title ?? 'Event') }}" />
    <meta property="og:description" content="{{ $og['description'] ?? '' }}" />
    @if(!empty($og['image_url']))
    <meta property="og:image" content="{{ $og['image_url'] }}" />
    @endif
    <meta property="og:type" content="website" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: 'hsl({{ $colors['background'] ?? '220 60% 8%' }})',
                        foreground: 'hsl({{ $colors['foreground'] ?? '0 0% 98%' }})',
                        card: 'hsl({{ $colors['secondary'] ?? '220 50% 18%' }})',
                        primary: { DEFAULT: 'hsl({{ $colors['primary'] ?? '45 70% 50%' }})', foreground: 'hsl({{ $colors['background'] ?? '220 60% 8%' }})' },
                        secondary: 'hsl({{ $colors['secondary'] ?? '220 50% 18%' }})',
                        muted: 'hsl({{ $colors['muted'] ?? '220 45% 20%' }})',
                        'muted-foreground': 'hsl(220 15% 70%)',
                        border: 'hsl({{ $colors['border'] ?? '220 40% 25%' }})',
                        accent: 'hsl({{ $colors['accent'] ?? '45 65% 55%' }})',
                    },
                    fontFamily: {
                        serif: ['{{ $fonts['heading'] ?? 'Playfair Display' }}', 'Georgia', 'serif'],
                        sans: ['{{ $fonts['body'] ?? 'Inter' }}', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($fonts['body'] ?? 'Inter') }}:wght@400;500;600;700&family={{ urlencode($fonts['heading'] ?? 'Playfair Display') }}:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-background: {{ $colors['background'] ?? '220 60% 8%' }};
            --color-primary: {{ $colors['primary'] ?? '45 70% 50%' }};
            --color-secondary: {{ $colors['secondary'] ?? '220 50% 18%' }};
            --color-border: {{ $colors['border'] ?? '220 40% 25%' }};
        }
        * { border-color: hsl(var(--color-border)); }
        body { font-family: '{{ $fonts['body'] ?? 'Inter' }}', system-ui, sans-serif; background: hsl(var(--color-background)); color: hsl({{ $colors['foreground'] ?? '0 0% 98%' }}); -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, h5, h6 { font-family: '{{ $fonts['heading'] ?? 'Playfair Display' }}', Georgia, serif; }

        .hero-bg-custom {
            background: linear-gradient(180deg, hsl(var(--color-background)) 0%, hsl(var(--color-secondary)) 50%, hsl(var(--color-background)) 100%);
            position: relative;
        }
        .hero-bg-custom::before {
            content: '';
            position: absolute;
            inset: 0;
            @if(!empty($themeConfig['decorative_pattern_url']))
            background: url('{{ $themeConfig['decorative_pattern_url'] }}');
            @else
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            @endif
            opacity: 0.03;
            pointer-events: none;
        }

        .text-gold-gradient {
            background: linear-gradient(135deg, hsl(var(--color-primary)) 0%, hsl({{ $colors['accent'] ?? '45 65% 55%' }}) 50%, hsl(var(--color-primary)) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-navy {
            background: linear-gradient(145deg, hsl(var(--color-secondary)) 0%, hsl(var(--color-background)) 100%);
            border: 1px solid hsl(var(--color-border));
            box-shadow: 0 8px 32px -8px rgba(0,0,0,0.4);
        }

        .btn-gold {
            background: linear-gradient(135deg, hsl(var(--color-primary)) 0%, hsl({{ $colors['accent'] ?? '45 65% 55%' }}) 50%, hsl(var(--color-primary)) 100%);
            color: hsl(var(--color-background));
            font-weight: 700;
            padding: 0.875rem 2.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px -4px hsla(45, 70%, 50%, 0.3);
            display: inline-block;
            text-align: center;
        }
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px -4px hsla(45, 70%, 50%, 0.4);
        }

        .header-sticky {
            backdrop-filter: blur(12px);
            background: hsla(var(--color-background), 0.9);
            border-bottom: 1px solid hsla(var(--color-border), 0.5);
        }
        .header-scrolled { box-shadow: 0 4px 30px -10px rgba(0,0,0,0.5); }

        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, hsla(var(--color-primary), 0.3), transparent);
        }

        .input-navy {
            background: hsl(var(--color-secondary));
            border: 1px solid hsl(var(--color-border));
            color: hsl({{ $colors['foreground'] ?? '0 0% 98%' }});
            transition: all 0.3s ease;
        }
        .input-navy:focus {
            border-color: hsl(var(--color-primary));
            box-shadow: 0 0 0 3px hsla(var(--color-primary), 0.1);
            outline: none;
        }

        .tab-active {
            background: linear-gradient(135deg, hsl(var(--color-primary)) 0%, hsl({{ $colors['accent'] ?? '45 65% 55%' }}) 50%, hsl(var(--color-primary)) 100%);
            color: hsl(var(--color-background));
        }
        .tab-inactive {
            background: hsl(var(--color-secondary));
            color: hsl({{ $colors['foreground'] ?? '0 0% 98%' }});
        }
        .tab-inactive:hover { background: hsl(var(--color-border)); }

        .link-gold { position: relative; transition: color 0.3s ease; }
        .link-gold::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: hsl(var(--color-primary));
            transition: width 0.3s ease;
        }
        .link-gold:hover { color: hsl(var(--color-primary)); }
        .link-gold:hover::after { width: 100%; }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fade-up 0.8s ease-out forwards; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }
        .member-result-enter { animation: member-result-enter 0.5s ease-out forwards; }
        @keyframes member-result-enter {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @if(!empty($themeConfig['custom_css']))
        {!! $themeConfig['custom_css'] !!}
        @endif
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-background text-foreground">
    @yield('content')
    @stack('scripts')
</body>
</html>
