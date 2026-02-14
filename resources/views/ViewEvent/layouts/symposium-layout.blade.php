<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Event')</title>
    {{-- Favicon (SGSS logo on white background) --}}
    <link rel="icon" type="image/png" href="{{ route('symposiumFavicon') }}">
    {{-- Open Graph / Social meta --}}
    <meta property="og:site_name" content="SGSS 2026 - Registration">
    <meta property="og:title" content="SGSS 2026 Symposium" />
    <meta property="og:description" content="Medicine & Judiciary Symposium - Legal Liability in Surgical Professions - May 2, 2026 - Crowne Plaza, Al Hamra, Jeddah" />
    <meta property="og:type" content="website" />
    <!-- Tailwind CSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: 'hsl(220 60% 8%)',
                        foreground: 'hsl(0 0% 98%)',
                        card: 'hsl(220 55% 12%)',
                        primary: { DEFAULT: 'hsl(45 70% 50%)', foreground: 'hsl(220 60% 8%)' },
                        secondary: 'hsl(220 50% 18%)',
                        muted: 'hsl(220 45% 20%)',
                        'muted-foreground': 'hsl(220 15% 70%)',
                        border: 'hsl(220 40% 25%)',
                        accent: 'hsl(45 65% 55%)',
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * { border-color: hsl(220 40% 25%); }
        body { font-family: 'Inter', system-ui, sans-serif; background: hsl(220 60% 8%); color: hsl(0 0% 98%); -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', Georgia, serif; }

        .hero-bg-custom {
            background: linear-gradient(180deg, hsl(220, 60%, 6%) 0%, hsl(220, 55%, 12%) 50%, hsl(220, 50%, 8%) 100%);
            position: relative;
        }
        .hero-bg-custom::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.03;
            pointer-events: none;
        }

        .text-gold-gradient {
            background: linear-gradient(135deg, hsl(45, 75%, 45%) 0%, hsl(45, 70%, 55%) 50%, hsl(45, 65%, 45%) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-navy {
            background: linear-gradient(145deg, hsl(220, 55%, 14%) 0%, hsl(220, 55%, 10%) 100%);
            border: 1px solid hsl(220 40% 25%);
            box-shadow: 0 8px 32px -8px rgba(0,0,0,0.4);
        }

        .btn-gold {
            background: linear-gradient(135deg, hsl(45, 75%, 45%) 0%, hsl(45, 70%, 55%) 50%, hsl(45, 65%, 45%) 100%);
            color: hsl(220 60% 8%);
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
            background: hsla(220, 60%, 8%, 0.9);
            border-bottom: 1px solid hsla(220, 40%, 25%, 0.5);
        }
        .header-scrolled { box-shadow: 0 4px 30px -10px rgba(0,0,0,0.5); }

        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, hsla(45, 70%, 50%, 0.3), transparent);
        }

        .input-navy {
            background: hsl(220 50% 18%);
            border: 1px solid hsl(220 40% 25%);
            color: hsl(0 0% 98%);
            transition: all 0.3s ease;
        }
        .input-navy:focus {
            border-color: hsl(45 70% 50%);
            box-shadow: 0 0 0 3px hsla(45, 70%, 50%, 0.1);
            outline: none;
        }

        .tab-active {
            background: linear-gradient(135deg, hsl(45, 75%, 45%) 0%, hsl(45, 70%, 55%) 50%, hsl(45, 65%, 45%) 100%);
            color: hsl(220 60% 8%);
        }
        .tab-inactive {
            background: hsl(220 50% 18%);
            color: hsl(0 0% 98%);
        }
        .tab-inactive:hover { background: hsl(220 45% 25%); }

        .link-gold { position: relative; transition: color 0.3s ease; }
        .link-gold::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: hsl(45 70% 50%);
            transition: width 0.3s ease;
        }
        .link-gold:hover { color: hsl(45 70% 50%); }
        .link-gold:hover::after { width: 100%; }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-up { animation: fade-up 0.8s ease-out forwards; }
        .animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }
  .member-result-enter {
    animation: member-result-enter 0.5s ease-out forwards;
  }
  @keyframes member-result-enter {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
  }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-background text-foreground">
    @yield('content')
    @stack('scripts')
</body>
</html>
