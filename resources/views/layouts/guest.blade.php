<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Ari Gita Grosir — Distributor minuman grosir terpercaya di Denpasar, Bali. Masuk atau daftar untuk mulai memesan.">

        <title>{{ config('app.name', 'Ari Gita Grosir') }} — Masuk atau Daftar</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            body.clay-body {
                font-family: 'Nunito', sans-serif;
                min-height: 100vh;
                background: linear-gradient(135deg, #FFF5E4 0%, #FFE8D6 35%, #F5D0C5 70%, #FDDEDE 100%);
                overflow-x: hidden;
                color: #4A3728;
            }

            /* ── Floating blobs ── */
            .blob {
                position: fixed;
                border-radius: 50%;
                filter: blur(60px);
                opacity: 0.45;
                z-index: 0;
                animation: blobFloat 8s ease-in-out infinite alternate;
            }
            .blob-1 { width: 340px; height: 340px; background: #FFD6A5; top: -80px; left: -100px; animation-delay: 0s; }
            .blob-2 { width: 280px; height: 280px; background: #FFADAD; bottom: -60px; right: -80px; animation-delay: 2s; }
            .blob-3 { width: 200px; height: 200px; background: #CAFFBF; top: 50%; left: 60%; animation-delay: 4s; }
            .blob-4 { width: 160px; height: 160px; background: #BDB2FF; bottom: 20%; left: 10%; animation-delay: 1s; }

            @keyframes blobFloat {
                0%   { transform: translate(0, 0) scale(1); }
                100% { transform: translate(30px, -20px) scale(1.08); }
            }

            /* ── Main container ── */
            .auth-container {
                position: relative;
                z-index: 1;
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
                padding: 24px;
                gap: 48px;
            }

            /* ── Branding panel (left) ── */
            .branding-panel {
                flex: 0 1 480px;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 40px 20px;
            }
            .branding-panel img {
                width: 100%;
                max-width: 360px;
                border-radius: 32px;
                margin-bottom: 32px;
                /* Claymorphism on the image */
                background: rgba(255,255,255,0.5);
                box-shadow:
                    8px 8px 20px rgba(0,0,0,0.08),
                    -4px -4px 12px rgba(255,255,255,0.9),
                    inset 2px 2px 6px rgba(255,255,255,0.6);
                padding: 12px;
            }
            .branding-title {
                font-size: 2rem;
                font-weight: 800;
                color: #3D2C1E;
                line-height: 1.2;
                margin-bottom: 12px;
            }
            .branding-title span {
                background: linear-gradient(135deg, #E8734A, #D35B3E);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .branding-subtitle {
                font-size: 1.05rem;
                color: #7A6353;
                line-height: 1.6;
                max-width: 360px;
            }

            /* ── Features badges ── */
            .feature-badges {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 28px;
            }
            .feature-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                border-radius: 50px;
                font-size: 0.8rem;
                font-weight: 700;
                background: rgba(255,255,255,0.65);
                backdrop-filter: blur(8px);
                box-shadow:
                    4px 4px 12px rgba(0,0,0,0.06),
                    -2px -2px 8px rgba(255,255,255,0.8),
                    inset 1px 1px 3px rgba(255,255,255,0.7);
                color: #5D4037;
            }
            .feature-badge svg { width: 16px; height: 16px; fill: #E8734A; }

            /* ── Form card (right) ── */
            .clay-card {
                flex: 0 1 480px;
                width: 100%;
                max-width: 480px;
                background: rgba(255, 255, 255, 0.55);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border-radius: 28px;
                padding: 40px 36px;
                box-shadow:
                    10px 10px 30px rgba(0, 0, 0, 0.08),
                    -6px -6px 20px rgba(255, 255, 255, 0.85),
                    inset 2px 2px 8px rgba(255, 255, 255, 0.7),
                    inset -1px -1px 4px rgba(0, 0, 0, 0.03);
                border: 1.5px solid rgba(255, 255, 255, 0.6);
            }

            /* ── Tab toggle ── */
            .clay-tabs {
                display: flex;
                background: rgba(0,0,0,0.04);
                border-radius: 16px;
                padding: 5px;
                margin-bottom: 28px;
                box-shadow: inset 2px 2px 6px rgba(0,0,0,0.06);
            }
            .clay-tab {
                flex: 1;
                padding: 12px 16px;
                border-radius: 12px;
                border: none;
                font-family: 'Nunito', sans-serif;
                font-size: 0.95rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                background: transparent;
                color: #9E8E82;
                position: relative;
            }
            .clay-tab.active {
                background: rgba(255,255,255,0.85);
                color: #3D2C1E;
                box-shadow:
                    4px 4px 12px rgba(0,0,0,0.07),
                    -2px -2px 8px rgba(255,255,255,0.9),
                    inset 1px 1px 3px rgba(255,255,255,0.8);
                transform: translateY(-1px);
            }
            .clay-tab:hover:not(.active) {
                color: #6D5D51;
                background: rgba(255,255,255,0.3);
            }

            /* ── Form subtitle ── */
            .form-subtitle {
                font-size: 0.9rem;
                color: #8B7966;
                margin-bottom: 24px;
                line-height: 1.5;
            }

            /* ── Clay input groups ── */
            .clay-field {
                margin-bottom: 20px;
            }
            .clay-field label {
                display: block;
                font-size: 0.85rem;
                font-weight: 700;
                color: #5D4037;
                margin-bottom: 6px;
            }
            .clay-input {
                width: 100%;
                padding: 14px 16px;
                border-radius: 14px;
                border: 1.5px solid rgba(0,0,0,0.06);
                background: rgba(255,255,255,0.6);
                font-family: 'Nunito', sans-serif;
                font-size: 0.95rem;
                color: #3D2C1E;
                transition: all 0.25s ease;
                box-shadow:
                    inset 2px 2px 6px rgba(0,0,0,0.04),
                    inset -1px -1px 4px rgba(255,255,255,0.8);
            }
            .clay-input:focus {
                outline: none;
                border-color: #E8734A;
                background: rgba(255,255,255,0.85);
                box-shadow:
                    inset 2px 2px 6px rgba(0,0,0,0.04),
                    inset -1px -1px 4px rgba(255,255,255,0.8),
                    0 0 0 3px rgba(232, 115, 74, 0.15);
            }
            .clay-input::placeholder {
                color: #BBA99A;
            }
            textarea.clay-input {
                resize: vertical;
                min-height: 80px;
            }
            select.clay-input {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238B7966' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 14px center;
                padding-right: 40px;
            }

            /* ── Clay checkbox ── */
            .clay-checkbox-group {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
            }
            .clay-checkbox-group input[type="checkbox"] {
                width: 20px;
                height: 20px;
                border-radius: 6px;
                border: 1.5px solid rgba(0,0,0,0.1);
                background: rgba(255,255,255,0.6);
                box-shadow: inset 1px 1px 3px rgba(0,0,0,0.05);
                accent-color: #E8734A;
                cursor: pointer;
            }
            .clay-checkbox-group label {
                font-size: 0.88rem;
                color: #7A6353;
                cursor: pointer;
                font-weight: 600;
            }

            /* ── Links ── */
            .clay-link {
                color: #E8734A;
                font-size: 0.85rem;
                font-weight: 700;
                text-decoration: none;
                transition: color 0.2s;
            }
            .clay-link:hover {
                color: #D35B3E;
                text-decoration: underline;
            }

            /* ── Clay button ── */
            .clay-btn {
                display: block;
                width: 100%;
                padding: 16px;
                border: none;
                border-radius: 16px;
                font-family: 'Nunito', sans-serif;
                font-size: 1rem;
                font-weight: 800;
                color: #fff;
                cursor: pointer;
                background: linear-gradient(135deg, #E8734A 0%, #D35B3E 100%);
                box-shadow:
                    6px 6px 18px rgba(211, 91, 62, 0.25),
                    -3px -3px 10px rgba(255, 255, 255, 0.4),
                    inset 1px 1px 4px rgba(255, 255, 255, 0.3);
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                margin-top: 8px;
                letter-spacing: 0.3px;
            }
            .clay-btn:hover {
                transform: translateY(-2px);
                box-shadow:
                    8px 8px 24px rgba(211, 91, 62, 0.3),
                    -4px -4px 12px rgba(255, 255, 255, 0.5),
                    inset 1px 1px 4px rgba(255, 255, 255, 0.3);
            }
            .clay-btn:active {
                transform: translateY(1px);
                box-shadow:
                    2px 2px 8px rgba(211, 91, 62, 0.2),
                    inset 2px 2px 6px rgba(0, 0, 0, 0.1);
            }

            /* ── Action row ── */
            .form-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 8px;
            }

            /* ── Error messages ── */
            .clay-error {
                margin-top: 6px;
                font-size: 0.8rem;
                color: #D35B3E;
                font-weight: 600;
            }
            .clay-error li {
                list-style: none;
            }

            /* ── Session status ── */
            .clay-status {
                padding: 12px 16px;
                border-radius: 12px;
                background: rgba(202, 255, 191, 0.5);
                color: #2E7D32;
                font-size: 0.88rem;
                font-weight: 600;
                margin-bottom: 20px;
                box-shadow: inset 1px 1px 4px rgba(0,0,0,0.04);
            }

            /* ── Transitions ── */
            .form-panel {
                transition: opacity 0.3s ease, transform 0.3s ease;
            }

            /* ── Responsive ── */
            @media (max-width: 900px) {
                .auth-container {
                    flex-direction: column;
                    gap: 24px;
                    padding: 20px 16px;
                }
                .branding-panel {
                    flex: none;
                    padding: 20px 16px 0;
                }
                .branding-panel img {
                    max-width: 200px;
                    margin-bottom: 20px;
                }
                .branding-title { font-size: 1.5rem; }
                .feature-badges { margin-top: 16px; }
                .clay-card {
                    flex: none;
                    padding: 28px 24px;
                }
            }
        </style>
    </head>
    <body class="clay-body">
        <!-- Floating decorative blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>

        <div class="auth-container">
            <!-- Branding Panel -->
            <div class="branding-panel">
                <img src="{{ asset('images/pos-clay-illustration.png') }}" alt="Ari Gita Grosir — Minuman Grosir">

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
    </body>
</html>
