<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full light" data-theme="blue">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Welcome' }} - {{ config('app.name', 'Wit Expense Tracker') }}</title>

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#2563eb">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Full-screen, no-scroll layout ── */
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .auth-root {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Left hero panel ── */
        .auth-hero {
            position: relative;
            display: none;
            flex-direction: column;
            justify-content: flex-end;
            overflow: hidden;
            background-color: #d4f0c8; /* soft green fallback */
        }

        @media (min-width: 1024px) {
            .auth-hero {
                display: flex;
                width: 55%;
                flex-shrink: 0;
            }
            .auth-form-panel {
                width: 45%;
            }
        }

        /* Hero image fills the entire panel */
        .auth-hero-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
        }

        /* Subtle bottom gradient for branding overlay readability */
        .auth-hero-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(0, 0, 0, 0.35) 0%,
                rgba(0, 0, 0, 0.08) 40%,
                transparent 70%
            );
            pointer-events: none;
        }

        /* Brand badge top-left */
        .auth-hero-brand {
            position: absolute;
            top: 28px;
            left: 28px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .auth-hero-brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--color-primary, #3b82f6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            box-shadow: 0 4px 18px rgba(0,0,0,0.18);
        }

        .auth-hero-brand-text {
            display: flex;
            flex-direction: column;
        }

        .auth-hero-brand-name {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            text-shadow: 0 1px 4px rgba(0,0,0,0.25);
        }

        .auth-hero-brand-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.78);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 500;
        }

        /* Version badge top-right */
        .auth-hero-badge {
            position: absolute;
            top: 28px;
            right: 28px;
            z-index: 10;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 13px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.35);
            color: #fff;
            backdrop-filter: blur(8px);
        }

        .auth-hero-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            background: #34d399;
            animation: pulse 2s cubic-bezier(.4,0,.6,1) infinite;
        }

        /* Hero bottom caption */
        .auth-hero-caption {
            position: relative;
            z-index: 10;
            padding: 0 32px 32px;
        }

        .auth-hero-caption h2 {
            font-size: clamp(1.35rem, 2.2vw, 2rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.25;
            margin: 0 0 8px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .auth-hero-caption p {
            font-size: clamp(0.78rem, 1.1vw, 0.92rem);
            color: rgba(255,255,255,0.82);
            margin: 0;
            text-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }

        /* ── Right form panel ── */
        .auth-form-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            background: #f8fafc;
        }

        .dark .auth-form-panel {
            background: #020617;
        }

        /* Top controls bar — fixed height */
        .auth-topbar {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 32px 0;
        }

        /* Scrollable middle zone */
        .auth-form-scroll {
            flex: 1;
            overflow-y: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 32px;
            /* hide scrollbar but allow scroll if content overflows on very small heights */
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .auth-form-scroll::-webkit-scrollbar {
            display: none;
        }

        .auth-form-inner {
            width: 100%;
            max-width: 400px;
        }

        /* Footer — fixed height */
        .auth-footer {
            flex-shrink: 0;
            text-align: center;
            padding: 0 32px 20px;
            font-size: 11px;
            color: #94a3b8;
        }

        .dark .auth-footer {
            color: #475569;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .4; }
        }
    </style>
</head>
<body class="text-slate-900 dark:text-slate-100 antialiased font-sans selection:bg-primary selection:text-white">

    <div class="auth-root">

        {{-- ── LEFT: Hero Panel ── --}}
        <div class="auth-hero">
            {{-- Full-cover 3D hero image --}}
            <img
                src="{{ asset('images/auth-hero.png') }}"
                alt="Wit Expense Tracker Illustration"
                class="auth-hero-img"
            >

            {{-- Subtle dark-bottom gradient for text legibility --}}
            <div class="auth-hero-gradient"></div>

            {{-- Brand badge top-left --}}
            <div class="auth-hero-brand">
                <a href="{{ url('/') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                    <div class="auth-hero-brand-icon">₹</div>
                    <div class="auth-hero-brand-text">
                        <span class="auth-hero-brand-name">Wit Expense<span style="color:var(--color-primary,#3b82f6)">Tracker</span></span>
                        <span class="auth-hero-brand-sub">Personal Finance</span>
                    </div>
                </a>
            </div>



            {{-- Bottom caption --}}
            <div class="auth-hero-caption">
                <h2>Master your cash flow<br>with confidence &amp; clarity.</h2>
                <p>Track expenses, set budgets, and gain deep insights into your finances.</p>
            </div>
        </div>

        {{-- ── RIGHT: Form Panel ── --}}
        <div class="auth-form-panel">

            {{-- Top Controls Bar --}}
            <div class="auth-topbar">
                {{-- Mobile brand (only shown on small screens where hero is hidden) --}}
                <a href="{{ url('/') }}" class="lg:hidden flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-primary text-white flex items-center justify-center font-bold text-lg shadow-md">
                        ₹
                    </div>
                    <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-slate-100">
                        Wit Expense<span class="text-primary">Tracker</span>
                    </span>
                </a>
                <div class="hidden lg:block"></div>{{-- spacer on desktop --}}


            </div>

            {{-- Main Form Slot (centred, scrollable if needed) --}}
            <div class="auth-form-scroll">
                <div class="auth-form-inner">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer --}}
            <div class="auth-footer">
                <p>&copy; {{ date('Y') }} Wit Expense Tracker. All rights reserved.</p>
            </div>

        </div>
    </div>

    {{-- Top-Right Toast Notifications Component --}}
    <x-toast />

</body>
</html>
