<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CV. Ari Gita Grosir — Distributor minuman grosir terpercaya di Denpasar, Bali. Pesan online dengan mudah, harga grosir terbaik.">

    <title>Ari Gita Grosir — Distributor Minuman Grosir Terpercaya</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
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
            filter: blur(80px);
            opacity: 0.35;
            z-index: 0;
            animation: blobFloat 10s ease-in-out infinite alternate;
        }
        .blob-1 { width: 400px; height: 400px; background: #FFD6A5; top: -120px; left: -120px; }
        .blob-2 { width: 350px; height: 350px; background: #FFADAD; bottom: -100px; right: -100px; animation-delay: 3s; }
        .blob-3 { width: 250px; height: 250px; background: #CAFFBF; top: 40%; left: 65%; animation-delay: 5s; }
        .blob-4 { width: 200px; height: 200px; background: #BDB2FF; bottom: 30%; left: 5%; animation-delay: 1.5s; }
        .blob-5 { width: 180px; height: 180px; background: #FFC6FF; top: 15%; right: 10%; animation-delay: 7s; }

        @keyframes blobFloat {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(25px, -18px) scale(1.06); }
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
        }

        /* ══════════════════════════════════════ */
        /* NAVBAR                                  */
        /* ══════════════════════════════════════ */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 48px;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.5);
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .nav-brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #E8734A, #D35B3E);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                4px 4px 12px rgba(211, 91, 62, 0.2),
                -2px -2px 6px rgba(255,255,255,0.5),
                inset 1px 1px 3px rgba(255,255,255,0.3);
        }
        .nav-brand-icon svg { width: 24px; height: 24px; fill: white; }
        .nav-brand-text {
            font-size: 1.3rem;
            font-weight: 800;
            color: #3D2C1E;
        }
        .nav-brand-text span {
            background: linear-gradient(135deg, #E8734A, #D35B3E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-btn {
            padding: 10px 24px;
            border-radius: 12px;
            font-family: 'Nunito', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            border: none;
        }
        .nav-btn-ghost {
            background: transparent;
            color: #5D4037;
        }
        .nav-btn-ghost:hover {
            background: rgba(255,255,255,0.5);
            color: #3D2C1E;
        }
        .nav-btn-primary {
            background: linear-gradient(135deg, #E8734A, #D35B3E);
            color: white;
            box-shadow:
                4px 4px 12px rgba(211, 91, 62, 0.2),
                -2px -2px 6px rgba(255,255,255,0.4),
                inset 1px 1px 3px rgba(255,255,255,0.25);
        }
        .nav-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow:
                6px 6px 18px rgba(211, 91, 62, 0.3),
                -3px -3px 8px rgba(255,255,255,0.5),
                inset 1px 1px 3px rgba(255,255,255,0.25);
        }

        /* ══════════════════════════════════════ */
        /* HERO SECTION                            */
        /* ══════════════════════════════════════ */
        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 60px;
            padding: 80px 48px 60px;
            min-height: calc(100vh - 84px);
        }
        .hero-content {
            flex: 0 1 540px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(8px);
            box-shadow:
                3px 3px 10px rgba(0,0,0,0.05),
                -2px -2px 6px rgba(255,255,255,0.8),
                inset 1px 1px 3px rgba(255,255,255,0.7);
            color: #E8734A;
            margin-bottom: 24px;
        }
        .hero-title {
            font-size: 3rem;
            font-weight: 900;
            color: #3D2C1E;
            line-height: 1.15;
            margin-bottom: 20px;
        }
        .hero-title span {
            background: linear-gradient(135deg, #E8734A, #D35B3E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-desc {
            font-size: 1.1rem;
            color: #7A6353;
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 460px;
        }
        .hero-actions {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }
        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 16px 32px;
            border-radius: 16px;
            font-family: 'Nunito', sans-serif;
            font-size: 1rem;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
        }
        .hero-btn-primary {
            background: linear-gradient(135deg, #E8734A, #D35B3E);
            color: white;
            box-shadow:
                6px 6px 18px rgba(211, 91, 62, 0.25),
                -3px -3px 10px rgba(255,255,255,0.4),
                inset 1px 1px 4px rgba(255,255,255,0.3);
        }
        .hero-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow:
                8px 8px 24px rgba(211, 91, 62, 0.3),
                -4px -4px 12px rgba(255,255,255,0.5),
                inset 1px 1px 4px rgba(255,255,255,0.3);
        }
        .hero-btn-secondary {
            background: rgba(255,255,255,0.55);
            color: #5D4037;
            backdrop-filter: blur(8px);
            box-shadow:
                4px 4px 12px rgba(0,0,0,0.06),
                -2px -2px 8px rgba(255,255,255,0.8),
                inset 1px 1px 3px rgba(255,255,255,0.7);
        }
        .hero-btn-secondary:hover {
            transform: translateY(-2px);
            background: rgba(255,255,255,0.75);
            box-shadow:
                6px 6px 16px rgba(0,0,0,0.08),
                -3px -3px 10px rgba(255,255,255,0.9),
                inset 1px 1px 3px rgba(255,255,255,0.8);
        }

        .hero-image {
            flex: 0 1 460px;
        }
        .hero-image-wrapper {
            background: rgba(255,255,255,0.5);
            border-radius: 32px;
            padding: 16px;
            box-shadow:
                12px 12px 36px rgba(0,0,0,0.08),
                -6px -6px 24px rgba(255,255,255,0.85),
                inset 2px 2px 8px rgba(255,255,255,0.7),
                inset -1px -1px 4px rgba(0,0,0,0.02);
            border: 1.5px solid rgba(255,255,255,0.5);
            overflow: hidden;
            animation: heroFloat 6s ease-in-out infinite;
        }
        .hero-image-wrapper img {
            width: 100%;
            border-radius: 20px;
            display: block;
        }

        @keyframes heroFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        /* ══════════════════════════════════════ */
        /* FEATURES SECTION                        */
        /* ══════════════════════════════════════ */
        .features {
            padding: 60px 48px 80px;
        }
        .features-header {
            text-align: center;
            margin-bottom: 48px;
        }
        .features-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #3D2C1E;
            margin-bottom: 12px;
        }
        .features-header h2 span {
            background: linear-gradient(135deg, #E8734A, #D35B3E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .features-header p {
            font-size: 1.05rem;
            color: #7A6353;
            max-width: 500px;
            margin: 0 auto;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 36px 28px;
            text-align: center;
            box-shadow:
                8px 8px 24px rgba(0,0,0,0.06),
                -4px -4px 16px rgba(255,255,255,0.8),
                inset 2px 2px 6px rgba(255,255,255,0.7),
                inset -1px -1px 3px rgba(0,0,0,0.02);
            border: 1.5px solid rgba(255,255,255,0.5);
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow:
                12px 12px 32px rgba(0,0,0,0.08),
                -6px -6px 20px rgba(255,255,255,0.9),
                inset 2px 2px 6px rgba(255,255,255,0.7);
        }
        .feature-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow:
                4px 4px 12px rgba(0,0,0,0.08),
                -2px -2px 8px rgba(255,255,255,0.7),
                inset 1px 1px 4px rgba(255,255,255,0.5);
        }
        .feature-icon.orange { background: linear-gradient(135deg, #FFD6A5, #FFBA7A); }
        .feature-icon.green  { background: linear-gradient(135deg, #CAFFBF, #9EE89E); }
        .feature-icon.purple { background: linear-gradient(135deg, #BDB2FF, #9B8FFF); }
        .feature-icon svg { width: 28px; height: 28px; fill: #3D2C1E; }
        .feature-card h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #3D2C1E;
            margin-bottom: 10px;
        }
        .feature-card p {
            font-size: 0.92rem;
            color: #7A6353;
            line-height: 1.6;
        }

        /* ══════════════════════════════════════ */
        /* ABOUT SECTION                           */
        /* ══════════════════════════════════════ */
        .about {
            padding: 60px 48px 80px;
        }
        .about-card {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(16px);
            border-radius: 28px;
            padding: 48px 44px;
            box-shadow:
                10px 10px 30px rgba(0,0,0,0.07),
                -6px -6px 20px rgba(255,255,255,0.85),
                inset 2px 2px 8px rgba(255,255,255,0.7);
            border: 1.5px solid rgba(255,255,255,0.5);
            display: flex;
            gap: 36px;
            align-items: center;
        }
        .about-icon-wrapper {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 22px;
            background: linear-gradient(135deg, #FFE8D6, #F5D0C5);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                4px 4px 12px rgba(0,0,0,0.06),
                -2px -2px 8px rgba(255,255,255,0.7),
                inset 1px 1px 4px rgba(255,255,255,0.6);
        }
        .about-icon-wrapper svg { width: 36px; height: 36px; fill: #D35B3E; }
        .about-text h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3D2C1E;
            margin-bottom: 12px;
        }
        .about-text p {
            font-size: 0.95rem;
            color: #7A6353;
            line-height: 1.7;
            margin-bottom: 8px;
        }
        .about-address {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.88rem;
            font-weight: 700;
            color: #E8734A;
            margin-top: 8px;
        }
        .about-address svg { width: 16px; height: 16px; fill: #E8734A; }

        /* ══════════════════════════════════════ */
        /* FOOTER                                  */
        /* ══════════════════════════════════════ */
        .footer {
            padding: 32px 48px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.2);
        }
        .footer p {
            font-size: 0.85rem;
            color: #9E8E82;
            font-weight: 600;
        }

        /* ══════════════════════════════════════ */
        /* RESPONSIVE                              */
        /* ══════════════════════════════════════ */
        @media (max-width: 900px) {
            .navbar { padding: 16px 20px; }
            .nav-brand-text { font-size: 1.1rem; }
            .nav-btn { padding: 8px 16px; font-size: 0.85rem; }

            .hero {
                flex-direction: column-reverse;
                padding: 40px 20px;
                gap: 32px;
                min-height: auto;
            }
            .hero-content { text-align: center; }
            .hero-title { font-size: 2rem; }
            .hero-desc { margin: 0 auto 28px; }
            .hero-actions { justify-content: center; }
            .hero-image { flex: none; max-width: 320px; }

            .features { padding: 40px 20px; }
            .features-grid { grid-template-columns: 1fr; max-width: 400px; }
            .features-header h2 { font-size: 1.5rem; }

            .about { padding: 40px 20px; }
            .about-card { flex-direction: column; text-align: center; padding: 32px 24px; }

            .footer { padding: 24px 20px; }
        }
    </style>
</head>
<body>
    <!-- Floating decorative blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <div class="blob blob-4"></div>
    <div class="blob blob-5"></div>

    <div class="page-wrapper">
        <!-- ═══ NAVBAR ═══ -->
        <nav class="navbar" id="navbar">
            <a href="/" class="nav-brand">
                <div class="nav-brand-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 12 7.4 15.38 12 17 10.83 14.92 8H20v6z"/></svg>
                </div>
                <div class="nav-brand-text">Ari Gita <span>Grosir</span></div>
            </a>
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="nav-btn nav-btn-ghost" id="nav-login">Masuk</a>
                <a href="{{ route('register') }}" class="nav-btn nav-btn-primary" id="nav-register">Daftar</a>
            </div>
        </nav>

        <!-- ═══ HERO SECTION ═══ -->
        <section class="hero" id="hero">
            <div class="hero-content">
                <div class="hero-badge">
                    🥤 Distributor Minuman Terpercaya di Bali
                </div>
                <h1 class="hero-title">
                    Belanja Minuman Grosir<br>Jadi Lebih <span>Mudah</span>
                </h1>
                <p class="hero-desc">
                    CV. Ari Gita Grosir menyediakan lebih dari 100+ produk minuman dengan harga grosir terbaik. Pesan langsung secara online, tanpa antri, tanpa ribet.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('register') }}" class="hero-btn hero-btn-primary" id="hero-cta-register">
                        Mulai Pesan Sekarang →
                    </a>
                    <a href="{{ route('login') }}" class="hero-btn hero-btn-secondary" id="hero-cta-login">
                        Sudah Punya Akun?
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-image-wrapper">
                    <img src="{{ asset('images/pos-clay-illustration.png') }}" alt="Minuman grosir CV Ari Gita">
                </div>
            </div>
        </section>

        <!-- ═══ FEATURES SECTION ═══ -->
        <section class="features" id="features">
            <div class="features-header">
                <h2>Kenapa Pilih <span>Ari Gita?</span></h2>
                <p>Kami hadir untuk mempermudah kebutuhan belanja grosir minuman Anda.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon orange">
                        <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                    </div>
                    <h3>Harga Grosir Terbaik</h3>
                    <p>Dapatkan harga khusus untuk pembelian dalam jumlah besar. Semakin banyak beli, semakin hemat!</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green">
                        <svg viewBox="0 0 24 24"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>
                    </div>
                    <h3>Pemesanan Online Mudah</h3>
                    <p>Pesan minuman kapan saja dari mana saja. Cukup pilih produk, kirim pesanan, dan kami yang urus sisanya.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon purple">
                        <svg viewBox="0 0 24 24"><path d="M18 18.5c-1.38 0-2.5 1.12-2.5 2.5s1.12 2.5 2.5 2.5 2.5-1.12 2.5-2.5-1.12-2.5-2.5-2.5zM5 1L1 5v14c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2H3V5.5L5.5 3H16v7.5h2V3c0-1.1-.9-2-2-2H5zm10.5 12L11 17.5l-1.5-1.5-2.5 2.5h13l-3-4.5-2.5 3z"/><path d="M18 13l-1.5 2 1.5 2h4l1.5-2-1.5-2h-4z"/></svg>
                    </div>
                    <h3>Pengiriman Cepat</h3>
                    <p>Layanan pengiriman ke area Denpasar dan sekitarnya. Barang sampai dengan aman dan tepat waktu.</p>
                </div>
            </div>
        </section>

        <!-- ═══ ABOUT SECTION ═══ -->
        <section class="about" id="about">
            <div class="about-card">
                <div class="about-icon-wrapper">
                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </div>
                <div class="about-text">
                    <h2>Tentang Kami</h2>
                    <p>
                        CV. Ari Gita Grosir adalah distributor minuman grosir yang telah melayani pelanggan di wilayah Denpasar dan sekitarnya. Kami menyediakan berbagai macam produk minuman dengan harga kompetitif dan pelayanan terpercaya.
                    </p>
                    <div class="about-address">
                        <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        Jl. Trenggana No.112a, Penatih, Denpasar Timur, Bali
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ FOOTER ═══ -->
        <footer class="footer">
            <p>&copy; {{ date('Y') }} CV. Ari Gita Grosir. Semua hak dilindungi.</p>
        </footer>
    </div>
</body>
</html>
