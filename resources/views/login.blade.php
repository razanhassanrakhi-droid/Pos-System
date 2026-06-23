<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('massage.login') ?? 'Login' }} — {{ app()->getLocale() == 'ar' ? 'ماما أفريكا' : 'Mama Africa' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:    #3b82f6;
            --indigo:  #6366f1;
            --violet:  #8b5cf6;
            --blue-dk: #1d4ed8;
            --slate:   #0f172a;
            --muted:   #64748b;
            --border:  rgba(226,232,240,.7);
            --bg:      #f8faff;
        }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--slate);
            -webkit-font-smoothing: antialiased;
        }

        /* ══════════════════════════════
           LAYOUT
        ══════════════════════════════ */
        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 480px;
            min-height: 100vh;
        }
        @media (max-width: 1024px) {
            .login-wrapper { grid-template-columns: 1fr; }
            .left-panel { display: none; }
        }

        /* ══════════════════════════════
           LEFT PANEL
        ══════════════════════════════ */
        .left-panel {
            position: relative;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #0f172a 100%);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
        }

        /* Ambient glows */
        .glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }
        .glow-1 { width: 500px; height: 500px; background: rgba(99,102,241,.18); top: -100px; left: -100px; }
        .glow-2 { width: 400px; height: 400px; background: rgba(59,130,246,.14); bottom: -80px; right: -80px; }
        .glow-3 { width: 250px; height: 250px; background: rgba(139,92,246,.12); top: 50%; left: 50%; transform: translate(-50%,-50%); }

        /* Dot grid */
        .dot-grid {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Hero text */
        .hero-content {
            position: relative;
            z-index: 5;
            max-width: 520px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(99,102,241,.15);
            border: 1px solid rgba(99,102,241,.3);
            border-radius: 99px;
            padding: 6px 16px;
            font-size: .78rem;
            font-weight: 600;
            color: #a5b4fc;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .hero-badge span.dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #6366f1;
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%,100% { opacity:1; transform:scale(1); }
            50% { opacity:.5; transform:scale(1.3); }
        }
        .hero-title {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.15;
            color: #fff;
            margin-bottom: 16px;
            letter-spacing: -.5px;
        }
        .hero-title span {
            background: linear-gradient(135deg, #818cf8 0%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-sub {
            font-size: .95rem;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 48px;
            font-weight: 400;
        }

        /* Floating cards */
        .float-cards {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .glass-card {
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 18px;
            padding: 18px 22px;
            transition: transform .4s ease;
        }
        .glass-card:hover { transform: translateY(-4px); }

        /* Card 1 – Stock overview */
        .card-stock {
            animation: float-a 4s ease-in-out infinite;
        }
        @keyframes float-a {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .card-stock-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .card-stock-title { font-size: .78rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .6px; }
        .card-stock-value { font-size: 1.6rem; font-weight: 800; color: #fff; }
        .card-stock-sub { font-size: .75rem; color: #64748b; margin-top: 2px; }
        .stock-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(16,185,129,.15);
            color: #34d399;
            border-radius: 8px;
            padding: 4px 10px;
            font-size: .72rem;
            font-weight: 700;
        }
        .mini-bars {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 32px;
            margin-top: 14px;
        }
        .mini-bar {
            flex: 1;
            border-radius: 4px;
            background: linear-gradient(180deg, #6366f1 0%, #3b82f6 100%);
            opacity: .8;
            animation: bar-grow 2s ease-out forwards;
        }
        @keyframes bar-grow {
            from { transform: scaleY(0); transform-origin: bottom; }
            to   { transform: scaleY(1); transform-origin: bottom; }
        }

        /* Card 2 – Products row */
        .card-products {
            animation: float-b 4.5s ease-in-out infinite;
        }
        @keyframes float-b {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .product-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .product-row:last-child { border-bottom: none; }
        .product-dot {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .product-dot.blue   { background: rgba(59,130,246,.2); }
        .product-dot.indigo { background: rgba(99,102,241,.2); }
        .product-dot.green  { background: rgba(16,185,129,.2); }
        .product-name { flex: 1; font-size: .82rem; font-weight: 600; color: #e2e8f0; }
        .product-stock-bar {
            width: 80px; height: 6px;
            background: rgba(255,255,255,.08);
            border-radius: 99px;
            overflow: hidden;
        }
        .product-stock-fill {
            height: 100%;
            border-radius: 99px;
        }

        /* Card 3 – Quick stats */
        .card-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            animation: float-a 3.8s ease-in-out infinite;
        }
        .stat-item {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }
        .stat-item .val { font-size: 1.15rem; font-weight: 800; color: #fff; }
        .stat-item .lbl { font-size: .68rem; color: #64748b; font-weight: 500; margin-top: 2px; }
        .stat-item .chg { font-size: .7rem; font-weight: 700; margin-top: 4px; }
        .chg.up   { color: #34d399; }
        .chg.down { color: #f87171; }

        /* ══════════════════════════════
           RIGHT PANEL
        ══════════════════════════════ */
        .right-panel {
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 52px;
            position: relative;
            border-left: 1px solid var(--border);
            min-height: 100vh;
        }

        .lang-toggle {
            position: absolute;
            top: 28px;
            right: 28px;
        }
        [dir="rtl"] .lang-toggle { right: auto; left: 28px; }
        .lang-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #f1f5f9;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 16px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }
        .lang-btn:hover { background: #e2e8f0; color: var(--slate); }

        .login-card {
            width: 100%;
            max-width: 380px;
        }

        /* Logo */
        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 36px;
        }
        .logo-img-wrap {
            width: 64px; height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, #eff6ff 0%, #eef2ff 100%);
            border: 1.5px solid rgba(99,102,241,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            box-shadow: 0 4px 16px rgba(99,102,241,.1);
        }
        .logo-img-wrap img {
            width: 44px; height: 44px;
            object-fit: contain;
        }
        .logo-name {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--slate);
            letter-spacing: -.4px;
        }
        .logo-tagline {
            font-size: .8rem;
            color: var(--muted);
            font-weight: 500;
            margin-top: 4px;
        }

        /* Headline */
        .welcome-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--slate);
            letter-spacing: -.4px;
            margin-bottom: 6px;
        }
        .welcome-sub {
            font-size: .875rem;
            color: var(--muted);
            font-weight: 400;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        /* Error alert */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff1f2;
            border: 1px solid rgba(244,63,94,.2);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 22px;
            font-size: .83rem;
            font-weight: 500;
            color: #be123c;
        }
        .alert-error i { font-size: 1rem; color: #f43f5e; flex-shrink: 0; }

        /* Form */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 16px;
            color: #94a3b8;
            font-size: .95rem;
            pointer-events: none;
            transition: color .2s;
        }
        [dir="rtl"] .input-icon { left: auto; right: 16px; }

        .form-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 13px 16px 13px 44px;
            font-size: .875rem;
            font-family: inherit;
            background: #f8fafc;
            color: var(--slate);
            outline: none;
            transition: all .22s ease;
            box-shadow: inset 0 1px 3px rgba(15,23,42,.04);
        }
        [dir="rtl"] .form-input { padding: 13px 44px 13px 16px; }

        .form-input::placeholder { color: #cbd5e1; }
        .form-input:focus {
            border-color: var(--indigo);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99,102,241,.08), inset 0 1px 3px rgba(15,23,42,.02);
        }
        .form-input:focus + .input-icon,
        .input-wrap:focus-within .input-icon { color: var(--indigo); }

        .pass-toggle {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: .95rem;
            padding: 4px;
            transition: color .2s;
            line-height: 1;
        }
        [dir="rtl"] .pass-toggle { right: auto; left: 14px; }
        .pass-toggle:hover { color: var(--indigo); }

        /* Remember + forgot */
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: .83rem;
            font-weight: 500;
            color: var(--muted);
            user-select: none;
        }
        .remember-check {
            width: 16px; height: 16px;
            border-radius: 5px;
            border: 1.5px solid #cbd5e1;
            accent-color: var(--indigo);
            cursor: pointer;
        }
        .forgot-link {
            font-size: .83rem;
            font-weight: 600;
            color: var(--indigo);
            text-decoration: none;
            transition: color .2s;
        }
        .forgot-link:hover { color: var(--blue-dk); }

        /* Submit button */
        .btn-login {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 14px 24px;
            font-size: .92rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all .25s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 4px 20px rgba(99,102,241,.3);
            letter-spacing: .2px;
            position: relative;
            overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.12) 0%, transparent 100%);
            opacity: 0;
            transition: opacity .2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99,102,241,.38);
        }
        .btn-login:hover::before { opacity: 1; }
        .btn-login:active { transform: translateY(0); }
        .btn-login i { font-size: 1rem; }

        /* Footer */
        .right-footer {
            position: absolute;
            bottom: 24px;
            left: 0; right: 0;
            text-align: center;
            font-size: .75rem;
            color: #cbd5e1;
            font-weight: 500;
        }

        /* ── Feature pills ── */
        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 32px;
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 5px 13px;
            font-size: .73rem;
            font-weight: 600;
            color: #475569;
        }
        .pill i { color: var(--indigo); font-size: .8rem; }

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }
        .divider-line { flex: 1; height: 1px; background: var(--border); }
        .divider-text { font-size: .75rem; font-weight: 600; color: #cbd5e1; text-transform: uppercase; letter-spacing: .6px; }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- ═══════════════════════════════
         LEFT PANEL
    ═══════════════════════════════ -->
    <div class="left-panel">
        <div class="glow glow-1"></div>
        <div class="glow glow-2"></div>
        <div class="glow glow-3"></div>
        <div class="dot-grid"></div>

        <div class="hero-content">

            <div class="hero-badge">
                <span class="dot"></span>
                {{ app()->getLocale() == 'ar' ? 'منصة إدارة متكاملة' : 'Inventory Management Platform' }}
            </div>

            <h1 class="hero-title">
                {{ app()->getLocale() == 'ar' ? 'تحكم كامل في' : 'Full Control Over' }}<br>
                <span>{{ app()->getLocale() == 'ar' ? 'مخزونك وأعمالك' : 'Your Inventory & Business' }}</span>
            </h1>

            <p class="hero-sub">
                {{ app()->getLocale() == 'ar'
                    ? 'منصة إدارة متقدمة تمنحك رؤية شاملة لمنتجاتك ومبيعاتك ومخزونك في الوقت الفعلي.'
                    : 'An advanced management platform giving you a real-time view of your products, sales, and stock.' }}
            </p>

            <div class="float-cards">

                <!-- Stock overview card -->
                <div class="glass-card card-stock">
                    <div class="card-stock-header">
                        <div>
                            <div class="card-stock-title">{{ app()->getLocale() == 'ar' ? 'إجمالي المخزون' : 'Total Stock Value' }}</div>
                            <div class="card-stock-value">{{ app()->getLocale() == 'ar' ? '١٢٤٬٥٠٠' : '124,500' }} <span style="font-size:.9rem;color:#94a3b8;">{{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</span></div>
                            <div class="card-stock-sub">{{ app()->getLocale() == 'ar' ? 'آخر تحديث: منذ دقيقتين' : 'Last updated: 2 min ago' }}</div>
                        </div>
                        <div class="stock-badge">
                            <i class="bi bi-arrow-up-short" style="font-size:1rem;margin:-2px;"></i>
                            +12.4%
                        </div>
                    </div>
                    <div class="mini-bars">
                        <div class="mini-bar" style="height:40%;animation-delay:.0s;"></div>
                        <div class="mini-bar" style="height:65%;animation-delay:.1s;"></div>
                        <div class="mini-bar" style="height:45%;animation-delay:.15s;"></div>
                        <div class="mini-bar" style="height:80%;animation-delay:.2s;"></div>
                        <div class="mini-bar" style="height:55%;animation-delay:.25s;"></div>
                        <div class="mini-bar" style="height:90%;animation-delay:.3s;"></div>
                        <div class="mini-bar" style="height:70%;animation-delay:.35s;background:linear-gradient(180deg,#a78bfa 0%,#6366f1 100%);"></div>
                        <div class="mini-bar" style="height:100%;animation-delay:.4s;background:linear-gradient(180deg,#a78bfa 0%,#6366f1 100%);"></div>
                    </div>
                </div>

                <!-- Products list card -->
                <div class="glass-card card-products">
                    <div style="font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;margin-bottom:12px;">
                        {{ app()->getLocale() == 'ar' ? 'أبرز المنتجات' : 'Top Products' }}
                    </div>
                    <div class="product-row">
                        <div class="product-dot blue"><span style="font-size:1.1rem;">📦</span></div>
                        <div class="product-name">{{ app()->getLocale() == 'ar' ? 'منتج أ' : 'Product A' }}</div>
                        <div class="product-stock-bar"><div class="product-stock-fill" style="width:85%;background:linear-gradient(90deg,#6366f1,#818cf8);"></div></div>
                    </div>
                    <div class="product-row">
                        <div class="product-dot indigo"><span style="font-size:1.1rem;">🛒</span></div>
                        <div class="product-name">{{ app()->getLocale() == 'ar' ? 'منتج ب' : 'Product B' }}</div>
                        <div class="product-stock-bar"><div class="product-stock-fill" style="width:62%;background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div></div>
                    </div>
                    <div class="product-row">
                        <div class="product-dot green"><span style="font-size:1.1rem;">⚡</span></div>
                        <div class="product-name">{{ app()->getLocale() == 'ar' ? 'منتج ج' : 'Product C' }}</div>
                        <div class="product-stock-bar"><div class="product-stock-fill" style="width:41%;background:linear-gradient(90deg,#10b981,#34d399);"></div></div>
                    </div>
                </div>

                <!-- Quick stats card -->
                <div class="glass-card">
                    <div class="card-stats">
                        <div class="stat-item">
                            <div class="val">{{ app()->getLocale() == 'ar' ? '٣٨٢' : '382' }}</div>
                            <div class="lbl">{{ app()->getLocale() == 'ar' ? 'منتج' : 'Products' }}</div>
                            <div class="chg up">↑ 5%</div>
                        </div>
                        <div class="stat-item">
                            <div class="val">{{ app()->getLocale() == 'ar' ? '٢٤' : '24' }}</div>
                            <div class="lbl">{{ app()->getLocale() == 'ar' ? 'فئة' : 'Categories' }}</div>
                            <div class="chg up">↑ 2%</div>
                        </div>
                        <div class="stat-item">
                            <div class="val">{{ app()->getLocale() == 'ar' ? '٧' : '7' }}</div>
                            <div class="lbl">{{ app()->getLocale() == 'ar' ? 'تنبيه' : 'Alerts' }}</div>
                            <div class="chg down">↓ 3</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- ═══════════════════════════════
         RIGHT PANEL
    ═══════════════════════════════ -->
    <div class="right-panel">

        <!-- Language toggle -->
        <div class="lang-toggle">
            @if(app()->getLocale() == 'ar')
                <a href="{{ url('change-language/en') }}" class="lang-btn">
                    <i class="bi bi-globe2"></i> English
                </a>
            @else
                <a href="{{ url('change-language/ar') }}" class="lang-btn">
                    <i class="bi bi-globe2"></i> العربية
                </a>
            @endif
        </div>

        <div class="login-card">

            <!-- Logo -->
            <div class="logo-wrap">
                <div class="logo-img-wrap">
                    <img src="{{ asset('Images/y.png') }}" alt="Mama Africa Logo">
                </div>
                <div class="logo-name">{{ app()->getLocale() == 'ar' ? 'ماما أفريكا' : 'Mama Africa' }}</div>
                <div class="logo-tagline">{{ app()->getLocale() == 'ar' ? 'نظام إدارة المخزون والمبيعات' : 'Inventory & Sales Management' }}</div>
            </div>

            <!-- Welcome -->
            <h2 class="welcome-title">{{ __('massage.login') ?? 'Welcome back' }} 👋</h2>
            <p class="welcome-sub">{{ app()->getLocale() == 'ar' ? 'أدخل بيانات حسابك للوصول إلى لوحة التحكم.' : 'Enter your credentials to access your dashboard.' }}</p>

            <!-- Error -->
            @if(session('error'))
                <div class="alert-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Username -->
                <div class="form-group">
                    <label class="form-label" for="username">
                        {{ __('massage.username') ?? 'Username' }}
                    </label>
                    <div class="input-wrap">
                        <input
                            name="name"
                            type="text"
                            id="username"
                            class="form-input"
                            placeholder="{{ __('massage.enter_username') ?? 'Enter your username' }}"
                            required
                            autocomplete="username"
                        >
                        <i class="bi bi-person input-icon"></i>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="password">
                        {{ __('massage.form_password') ?? 'Password' }}
                    </label>
                    <div class="input-wrap">
                        <input
                            name="password"
                            type="password"
                            id="password"
                            class="form-input"
                            placeholder="{{ __('massage.enter_password') ?? 'Enter your password' }}"
                            required
                            dir="ltr"
                            autocomplete="current-password"
                        >
                        <i class="bi bi-lock input-icon"></i>
                        <button type="button" class="pass-toggle" onclick="togglePwd()" id="pwd-toggle-btn" aria-label="Toggle password">
                            <i class="bi bi-eye" id="pwd-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember + Forgot -->
                <div class="form-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" class="remember-check">
                        {{ app()->getLocale() == 'ar' ? 'تذكرني' : 'Remember me' }}
                    </label>
                    <a href="{{ route('password.forgot') }}" class="forgot-link">
                        {{ __('massage.forgot_password') ?? 'Forgot password?' }}
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="login-btn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    {{ __('massage.login') ?? 'Sign In' }}
                </button>

            </form>

            <!-- Feature pills -->
            <div class="feature-pills">
                <span class="pill"><i class="bi bi-shield-check"></i> {{ app()->getLocale() == 'ar' ? 'آمن ومشفر' : 'Secure & Encrypted' }}</span>
                <span class="pill"><i class="bi bi-graph-up-arrow"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات فورية' : 'Real-time Analytics' }}</span>
                <span class="pill"><i class="bi bi-boxes"></i> {{ app()->getLocale() == 'ar' ? 'إدارة المخزون' : 'Stock Control' }}</span>
            </div>

        </div>

        <!-- Footer -->
        <div class="right-footer">
            &copy; {{ date('Y') }} {{ app()->getLocale() == 'ar' ? 'ماما أفريكا. جميع الحقوق محفوظة.' : 'Mama Africa. All rights reserved.' }}
        </div>

    </div>
</div>

<script>
function togglePwd() {
    const field = document.getElementById('password');
    const eye   = document.getElementById('pwd-eye');
    if (field.type === 'password') {
        field.type = 'text';
        eye.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        eye.className = 'bi bi-eye';
    }
}

// Button loading state on submit
document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('login-btn');
    btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;display:inline-block;"></span> {{ app()->getLocale() == 'ar' ? "جارٍ الدخول…" : "Signing in…" }}</span>';
    btn.style.opacity = '.85';
    btn.style.pointerEvents = 'none';
});
</script>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

</body>
</html>