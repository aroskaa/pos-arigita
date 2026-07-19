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
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 50%, #E2E8F0 100%);
            overflow-x: hidden;
            color: #0F172A;
        }

        /* Ambient subtle glow elements */
        .ambient-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.25;
            z-index: 0;
            pointer-events: none;
        }
        .glow-1 { width: 500px; height: 500px; background: #93C5FD; top: -150px; left: -100px; }
        .glow-2 { width: 450px; height: 450px; background: #60A5FA; bottom: -120px; right: -80px; }

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
            padding: 20px 56px;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .nav-brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: #2563EB;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .nav-brand-icon svg { width: 22px; height: 22px; fill: white; }
        .nav-brand-text {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.3px;
        }
        .nav-brand-text span {
            color: #2563EB;
        }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-btn {
            padding: 10px 22px;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        .nav-btn-ghost {
            background: #F1F5F9;
            color: #334155;
        }
        .nav-btn-ghost:hover {
            background: #E2E8F0;
            color: #0F172A;
        }
        .nav-btn-primary {
            background: #2563EB;
            color: white;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }
        .nav-btn-primary:hover {
            background: #1D4ED8;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }

        /* ══════════════════════════════════════ */
        /* HERO SECTION                            */
        /* ══════════════════════════════════════ */
        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 64px;
            padding: 72px 56px;
            min-height: calc(100vh - 84px);
        }
        .hero-content {
            flex: 0 1 540px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            color: #1D4ED8;
            margin-bottom: 24px;
        }
        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.15;
            margin-bottom: 20px;
            letter-spacing: -0.8px;
        }
        .hero-title span {
            color: #2563EB;
        }
        .hero-desc {
            font-size: 1.05rem;
            color: #475569;
            line-height: 1.65;
            margin-bottom: 36px;
            max-width: 480px;
        }
        .hero-actions {
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
        }
        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border-radius: 14px;
            font-family: inherit;
            font-size: 0.96rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
        }
        .hero-btn-primary {
            background: #2563EB;
            color: white;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.28);
        }
        .hero-btn-primary:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
        }
        .hero-btn-secondary {
            background: #FFFFFF;
            color: #1E293B;
            border: 1px solid #CBD5E1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .hero-btn-secondary:hover {
            background: #F8FAFC;
            border-color: #94A3B8;
            transform: translateY(-1px);
        }

        .hero-image {
            flex: 0 1 480px;
        }
        .hero-image-wrapper {
            background: #FFFFFF;
            border-radius: 32px;
            padding: 16px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            border: 1px solid #E2E8F0;
            overflow: hidden;
        }
        .hero-image-wrapper img {
            width: 100%;
            border-radius: 20px;
            display: block;
        }

        /* ══════════════════════════════════════ */
        /* FEATURES SECTION                        */
        /* ══════════════════════════════════════ */
        .features {
            padding: 64px 56px 88px;
        }
        .features-header {
            text-align: center;
            margin-bottom: 48px;
        }
        .features-header h2 {
            font-size: 2.1rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        .features-header h2 span {
            color: #2563EB;
        }
        .features-header p {
            font-size: 1.05rem;
            color: #64748B;
            max-width: 500px;
            margin: 0 auto;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            max-width: 1040px;
            margin: 0 auto;
        }
        .feature-card {
            background: #FFFFFF;
            border-radius: 24px;
            padding: 36px 30px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            border: 1px solid #E2E8F0;
            transition: all 0.25s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.1);
            border-color: #BFDBFE;
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
        }
        .feature-icon svg { width: 26px; height: 26px; fill: #2563EB; }
        .feature-card h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 10px;
        }
        .feature-card p {
            font-size: 0.92rem;
            color: #64748B;
            line-height: 1.6;
        }

        /* ══════════════════════════════════════ */
        /* ABOUT SECTION                           */
        /* ══════════════════════════════════════ */
        .about {
            padding: 40px 56px 88px;
        }
        .about-card {
            max-width: 840px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 28px;
            padding: 48px 44px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.05);
            border: 1px solid #E2E8F0;
            display: flex;
            gap: 36px;
            align-items: center;
        }
        .about-icon-wrapper {
            flex-shrink: 0;
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .about-icon-wrapper svg { width: 32px; height: 32px; fill: #2563EB; }
        .about-text h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 12px;
        }
        .about-text p {
            font-size: 0.96rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 10px;
        }
        .about-address {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            font-weight: 700;
            color: #2563EB;
        }
        .about-address svg { width: 16px; height: 16px; fill: #2563EB; }

        /* ══════════════════════════════════════ */
        /* FOOTER                                  */
        /* ══════════════════════════════════════ */
        .footer {
            padding: 32px 56px;
            text-align: center;
            border-top: 1px solid #E2E8F0;
            background: #FFFFFF;
        }
        .footer p {
            font-size: 0.86rem;
            color: #64748B;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .navbar { padding: 16px 20px; }
            .hero { flex-direction: column-reverse; padding: 40px 20px; text-align: center; gap: 32px; }
            .hero-title { font-size: 2.2rem; }
            .features-grid { grid-template-columns: 1fr; }
            .about-card { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>

    <div class="page-wrapper">
        <!-- NAVBAR -->
        <nav class="navbar">
            <a href="/" class="nav-brand">
                <div class="nav-brand-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 12 7.4 15.38 12 17 10.83 14.92 8H20v6z"/></svg>
                </div>
                <div class="nav-brand-text">Ari Gita <span>Grosir</span></div>
            </a>
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="nav-btn nav-btn-ghost">Masuk</a>
                <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">Daftar</a>
            </div>
        </nav>

        <!-- HERO SECTION -->
        <section class="hero">
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
                    <a href="{{ route('register') }}" class="hero-btn hero-btn-primary">
                        Mulai Pesan Sekarang →
                    </a>
                    <a href="{{ route('login') }}" class="hero-btn hero-btn-secondary">
                        Sudah Punya Akun?
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-image-wrapper">
                    <img src="{{ asset('images/pos-clay-illustration.png') }}" alt="Ari Gita Grosir">
                </div>
            </div>
        </section>

        <!-- FEATURES SECTION -->
        <section class="features">
            <div class="features-header">
                <h2>Kenapa Pilih <span>Ari Gita?</span></h2>
                <p>Kami hadir untuk mempermudah kebutuhan belanja grosir minuman Anda.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                    </div>
                    <h3>Harga Grosir Terbaik</h3>
                    <p>Dapatkan harga khusus untuk pembelian dalam jumlah besar. Semakin banyak beli, semakin hemat!</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>
                    </div>
                    <h3>Pemesanan Online Mudah</h3>
                    <p>Pesan minuman kapan saja dari mana saja. Cukup pilih produk, kirim pesanan, dan kami yang urus sisanya.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    </div>
                    <h3>Pengiriman Cepat</h3>
                    <p>Layanan pengiriman ke area Denpasar dan sekitarnya. Barang sampai dengan aman dan tepat waktu.</p>
                </div>
            </div>
        </section>

        <!-- ABOUT SECTION -->
        <section class="about">
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

        <!-- FOOTER -->
        <footer class="footer">
            <p>&copy; {{ date('Y') }} CV. Ari Gita Grosir. Semua hak dilindungi.</p>
        </footer>
    </div>
</body>
</html>
