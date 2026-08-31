<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Ari Gita Grosir — Distributor minuman grosir terpercaya di Denpasar, Bali. Masuk atau daftar untuk mulai memesan.">

        <title>{{ config('app.name', 'Ari Gita Grosir') }} — Masuk atau Daftar</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-ag.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            body.clay-body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                min-height: 100vh;
                background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 50%, #E2E8F0 100%);
                overflow-x: hidden;
                color: #0F172A;
            }

            .ambient-glow {
                position: fixed;
                border-radius: 50%;
                filter: blur(100px);
                opacity: 0.25;
                z-index: 0;
                pointer-events: none;
            }
            .glow-1 { width: 450px; height: 450px; background: #93C5FD; top: -120px; left: -100px; }
            .glow-2 { width: 380px; height: 380px; background: #60A5FA; bottom: -80px; right: -80px; }

            /* ── Top Navbar ── */
            .guest-navbar {
                position: sticky;
                top: 0;
                z-index: 100;
                width: 100%;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border-bottom: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            }
            .guest-navbar-inner {
                max-width: 1320px;
                margin: 0 auto;
                padding: 12px 24px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .guest-nav-brand {
                display: flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
            }
            .guest-nav-brand-icon {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                background: #ffffff;
                border: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 3px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                overflow: hidden;
            }
            .guest-nav-brand-icon img { width: 100%; height: 100%; object-fit: contain; }
            .guest-nav-brand-text {
                font-size: 1.05rem;
                font-weight: 800;
                color: #0F172A;
                letter-spacing: -0.2px;
            }
            .guest-nav-brand-text span { color: #2563EB; }
            .guest-nav-link {
                font-size: 0.88rem;
                font-weight: 600;
                color: #475569;
                text-decoration: none;
            }
            .guest-nav-link:hover { color: #0F172A; }

            /* ── Main container ── */
            .auth-container {
                position: relative;
                z-index: 1;
                display: flex;
                min-height: calc(100vh - 57px);
                align-items: center;
                justify-content: center;
                padding: 32px 24px;
                gap: 56px;
            }

            /* ── Branding panel (left) ── */
            .branding-panel {
                flex: 0 1 460px;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 20px;
            }
            .branding-panel .branding-illustration {
                width: 100%;
                max-width: 340px;
                border-radius: 28px;
                margin-bottom: 28px;
                background: #FFFFFF;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
                border: 1px solid #E2E8F0;
                padding: 14px;
            }
            .branding-title {
                font-size: 2.1rem;
                font-weight: 800;
                color: #0F172A;
                line-height: 1.25;
                margin-bottom: 12px;
                letter-spacing: -0.5px;
            }
            .branding-title span {
                color: #2563EB;
            }
            .branding-subtitle {
                font-size: 0.98rem;
                color: #475569;
                line-height: 1.6;
                max-width: 380px;
            }

            /* ── Features badges ── */
            .feature-badges {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 24px;
            }
            .feature-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                border-radius: 50px;
                font-size: 0.8rem;
                font-weight: 700;
                background: #EFF6FF;
                border: 1px solid #DBEAFE;
                color: #1D4ED8;
            }
            .feature-badge svg { width: 15px; height: 15px; fill: #2563EB; }

            /* ── Form card (right) ── */
            .clay-card {
                flex: 0 1 520px;
                width: 100%;
                max-width: 520px;
                background: #FFFFFF;
                border-radius: 28px;
                padding: 40px 36px;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
                border: 1px solid #E2E8F0;
            }

            /* ── Tab toggle ── */
            .clay-tabs {
                display: flex;
                background: #F1F5F9;
                border-radius: 14px;
                padding: 4px;
                margin-bottom: 24px;
            }
            .clay-tab {
                flex: 1;
                padding: 10px 16px;
                border-radius: 10px;
                border: none;
                font-family: inherit;
                font-size: 0.92rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.2s ease;
                background: transparent;
                color: #64748B;
            }
            .clay-tab.active {
                background: #FFFFFF;
                color: #0F172A;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            }
            .clay-tab:hover:not(.active) {
                color: #0F172A;
            }

            /* ── Form subtitle ── */
            .form-subtitle {
                font-size: 0.88rem;
                color: #64748B;
                margin-bottom: 22px;
                line-height: 1.5;
            }

            /* ── Inputs ── */
            .clay-field {
                margin-bottom: 18px;
            }
            .clay-field label {
                display: block;
                font-size: 0.84rem;
                font-weight: 700;
                color: #334155;
                margin-bottom: 6px;
            }
            .clay-input {
                width: 100%;
                padding: 12px 16px;
                border-radius: 12px;
                border: 1px solid #CBD5E1;
                background: #FFFFFF;
                font-family: inherit;
                font-size: 0.92rem;
                color: #0F172A;
                transition: all 0.2s ease;
            }
            .clay-input:focus {
                outline: none;
                border-color: #2563EB;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            }
            .clay-input::placeholder {
                color: #94A3B8;
            }
            textarea.clay-input {
                resize: vertical;
                min-height: 80px;
            }
            select.clay-input {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 14px center;
                padding-right: 40px;
            }

            /* ── Checkbox ── */
            .clay-checkbox-group {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .clay-checkbox-group input[type="checkbox"] {
                width: 18px;
                height: 18px;
                border-radius: 5px;
                accent-color: #2563EB;
                cursor: pointer;
            }
            .clay-checkbox-group label {
                font-size: 0.85rem;
                color: #475569;
                cursor: pointer;
                font-weight: 600;
            }

            /* ── Links ── */
            .clay-link {
                color: #2563EB;
                font-size: 0.84rem;
                font-weight: 700;
                text-decoration: none;
                transition: color 0.2s;
            }
            .clay-link:hover {
                color: #1D4ED8;
                text-decoration: underline;
            }

            /* ── Submit Button ── */
            .clay-btn {
                display: block;
                width: 100%;
                padding: 14px;
                border: none;
                border-radius: 14px;
                font-family: inherit;
                font-size: 0.98rem;
                font-weight: 700;
                color: #fff;
                cursor: pointer;
                background: #2563EB;
                box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                margin-top: 10px;
            }
            .clay-btn:hover {
                background: #1D4ED8;
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            }
            .clay-btn:active {
                transform: translateY(0);
            }

            .form-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px;
            }

            .clay-error {
                margin-top: 6px;
                font-size: 0.8rem;
                color: #DC2626;
                font-weight: 600;
            }
            .clay-error li {
                list-style: none;
            }

            .clay-status {
                padding: 12px 16px;
                border-radius: 12px;
                background: #EFF6FF;
                color: #1D4ED8;
                font-size: 0.88rem;
                font-weight: 600;
                margin-bottom: 18px;
            }

            @media (max-width: 900px) {
                .auth-container {
                    flex-direction: column;
                    gap: 24px;
                    padding: 20px 16px;
                }
                .branding-panel {
                    flex: none;
                    padding: 10px 16px 0;
                }
                .clay-card {
                    flex: none;
                    padding: 28px 20px;
                }
            }
        </style>
    </head>
    <body class="clay-body">
        <div class="ambient-glow glow-1"></div>
        <div class="ambient-glow glow-2"></div>

        <!-- Navbar -->
        <nav class="guest-navbar">
            <div class="guest-navbar-inner">
                <a href="/" class="guest-nav-brand">
                    <div class="guest-nav-brand-icon">
                        <img src="{{ asset('images/logo-ag.png') }}" alt="Ari Gita Grosir">
                    </div>
                    <span class="guest-nav-brand-text">Ari Gita <span>Grosir</span></span>
                </a>
                <a href="/" class="guest-nav-link">← Kembali ke Beranda</a>
            </div>
        </nav>

        <div class="auth-container">
            <!-- Branding Panel -->
            <div class="branding-panel">
                <div style="display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;background:#fff;border:1px solid #e2e8f0;padding:6px 14px 6px 6px;border-radius:9999px;box-shadow:0 2px 6px rgba(0,0,0,0.04);">
                    <div style="width:32px;height:32px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#fff;border:1px solid #e2e8f0;flex-shrink:0;">
                        <img src="{{ asset('images/logo-ag.png') }}" alt="Logo AG" style="max-width:75%;max-height:75%;object-fit:contain;">
                    </div>
                    <span style="font-size:13px;font-weight:800;color:#0f172a;letter-spacing:0.5px;">CV. ARI GITA</span>
                </div>

                <img class="branding-illustration" src="{{ asset('images/pos-clay-illustration.png') }}" alt="Ari Gita Grosir">

                <h1 class="branding-title">
                    Selamat Datang di<br><span>Ari Gita Grosir</span>
                </h1>
                <p class="branding-subtitle">
                    Masuk atau daftar untuk mulai memesan minuman grosir dengan mudah, cepat, dan harga terbaik.
                </p>

                <div class="feature-badges">
                    <div class="feature-badge">
                        <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                        Harga Grosir
                    </div>
                    <div class="feature-badge">
                        <svg viewBox="0 0 24 24"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>
                        100+ Produk
                    </div>
                    <div class="feature-badge">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        Pesan Online
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="clay-card">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
