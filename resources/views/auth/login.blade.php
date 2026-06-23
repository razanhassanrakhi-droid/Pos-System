<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('login.title') }} — Digital Age POS</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* ═══════════════════════════ RESET ═══════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background: #040b18;
            overflow: hidden;
        }

        /* ═══════════════════════════ MAIN WRAPPER ═══════════════════════════ */
        .login-wrapper {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: radial-gradient(ellipse 80% 80% at 50% 50%, #06112b 0%, #040b18 100%);
            transition: background 0.4s;
        }

        /* ── Background grid ── */
        .login-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,200,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,200,255,0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
            transition: opacity 0.4s;
        }

        /* ═══════════════════════════ LIGHT MODE OVERRIDES ═══════════════════════════ */
        body.light-mode {
            background: #eef2ff;
        }
        body.light-mode .login-wrapper {
            background: radial-gradient(ellipse 80% 80% at 50% 50%, #dce8ff 0%, #eef2ff 100%);
        }
        body.light-mode .login-wrapper::before {
            background-image:
                linear-gradient(rgba(0,80,200,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,80,200,0.05) 1px, transparent 1px);
        }
        body.light-mode .glow-blob.cyan  { background: rgba(0,150,255,0.10); }
        body.light-mode .glow-blob.pink  { background: rgba(200,0,100,0.07); }
        body.light-mode .glow-blob.blue  { background: rgba(60,80,255,0.06); }
        body.light-mode .welcome-title   { color: #0f172a; }
        body.light-mode .welcome-sub     { color: rgba(0,0,0,0.45); }
        body.light-mode .brand-logo-pos  { color: rgba(0,0,0,0.40); }
        body.light-mode .brand-logo-tagline { color: rgba(0,0,0,0.30); }
        body.light-mode .brand-logo-tagline::before,
        body.light-mode .brand-logo-tagline::after { color: rgba(0,0,0,0.15); }
        body.light-mode .field-input {
            background: #ffffff;
            border-color: rgba(0, 0, 0, 0.20);
            color: #0f172a;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        body.light-mode .field-input::placeholder { color: rgba(0, 0, 0, 0.48); }
        body.light-mode .field-input:focus {
            background: #ffffff;
            border-color: rgba(0, 100, 255, 0.65);
            box-shadow: 0 0 0 3px rgba(0, 100, 255, 0.15), 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        body.light-mode .field-icon { color: rgba(0, 0, 0, 0.45); }
        body.light-mode .field-wrap:focus-within .field-icon { color: rgba(0,100,255,0.75); }
        body.light-mode .pw-toggle  { color: rgba(0,0,0,0.28); }
        body.light-mode .pw-toggle:hover { color: #0066ff; }
        body.light-mode .remember-label { color: rgba(0,0,0,0.48); }
        body.light-mode .divider { color: rgba(0,0,0,0.18); }
        body.light-mode .divider::before,
        body.light-mode .divider::after { background: rgba(0,0,0,0.08); }
        body.light-mode .btn-touch {
            background: rgba(0,0,0,0.04);
            border-color: rgba(0,0,0,0.10);
            color: rgba(0,0,0,0.55);
        }
        body.light-mode .btn-touch:hover {
            border-color: #0066ff;
            color: #0066ff;
            background: rgba(0,100,255,0.06);
        }
        body.light-mode .lang-pill:not(.active-lang) { color: rgba(0,0,0,0.3); border-color: rgba(0,0,0,0.10); }
        body.light-mode .support-btn { color: rgba(0,0,0,0.3); border-color: rgba(0,0,0,0.08); }
        body.light-mode .support-btn:hover { color: #0066ff; border-color: #0066ff; background: rgba(0,100,255,0.05); }
        body.light-mode .footer-copy { color: rgba(0,0,0,0.2); }
        body.light-mode .version-badge { color: rgba(0,0,0,0.18); }
        body.light-mode .device-neon-ring {
            background:
                linear-gradient(#eef2ff, #eef2ff) padding-box,
                conic-gradient(from 195deg,
                    #0044ff 0%, #6600ff 22%, #cc00ff 40%,
                    #ff00aa 55%, #cc00ff 68%, #6600ff 80%, #0044ff 100%
                ) border-box;
        }
        body.light-mode .floor-dots {
            background-image: radial-gradient(rgba(100,50,200,0.18) 1.5px, transparent 1.5px);
        }

        /* ═══════════════════════════ THEME TOGGLE BUTTON ═══════════════════════════ */
        .theme-toggle-wrap {
            position: absolute;
            top: 20px;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 20px;
            z-index: 100;
        }
        .theme-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 30px;
            padding: 7px 12px 7px 10px;
            cursor: pointer;
            font-size: 0.73rem;
            color: rgba(255,255,255,0.7);
            font-weight: 600;
            backdrop-filter: blur(10px);
            transition: all 0.2s;
            font-family: inherit;
            user-select: none;
        }
        .theme-toggle-btn:hover {
            background: rgba(255,255,255,0.13);
            border-color: rgba(0,200,255,0.35);
            color: #fff;
        }
        body.light-mode .theme-toggle-btn {
            background: rgba(0,0,0,0.06);
            border-color: rgba(0,0,0,0.12);
            color: rgba(0,0,0,0.6);
        }
        body.light-mode .theme-toggle-btn:hover {
            background: rgba(0,0,0,0.10);
            border-color: rgba(0,80,200,0.35);
            color: #0f172a;
        }
        /* The toggle switch pill */
        .toggle-pill {
            width: 34px; height: 19px;
            background: rgba(255,255,255,0.15);
            border-radius: 99px;
            position: relative;
            transition: background 0.3s;
            flex-shrink: 0;
        }
        .toggle-pill.on { background: #00C8FF; }
        body.light-mode .toggle-pill { background: rgba(0,0,0,0.12); }
        body.light-mode .toggle-pill.on { background: #0066ff; }
        .toggle-thumb {
            width: 15px; height: 15px;
            background: #fff;
            border-radius: 50%;
            position: absolute;
            top: 2px; left: 2px;
            transition: left 0.25s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 0 2px 5px rgba(0,0,0,0.35);
        }
        .toggle-pill.on .toggle-thumb { left: 17px; }

        /* Outer ambient glow blobs */
        .glow-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
        }
        .glow-blob.cyan {
            width: 500px; height: 500px;
            background: rgba(0, 200, 255, 0.12);
            top: 50%;
            right: 10%;
            transform: translateY(-50%);
        }
        .glow-blob.pink {
            width: 400px; height: 400px;
            background: rgba(255, 20, 147, 0.10);
            bottom: -10%;
            right: 5%;
        }
        .glow-blob.blue {
            width: 350px; height: 350px;
            background: rgba(59, 130, 246, 0.08);
            top: -5%;
            left: 30%;
        }

        /* ═══════════════════════════ FORM SECTION ═══════════════════════════ */
        /* LTR=English: form on LEFT | RTL=Arabic: form on RIGHT */
        .left-section {
            position: absolute;
            {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 0;
            top: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 70px 56px 40px 56px;
            min-width: 360px;
            width: 55%;
            height: 100%;
            text-align: center;
            overflow-y: auto;
            scrollbar-width: none; /* Hide scrollbar Firefox */
        }
        .left-section::-webkit-scrollbar {
            display: none; /* Hide scrollbar Chrome/Safari/Opera */
        }

        /* Brand logo — centered, big */
        .brand-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            margin-bottom: 28px;
            animation: fadeInLeft 0.8s cubic-bezier(.16,1,.3,1) both;
        }
        .brand-logo img {
            width: 110px;
            height: 110px;
            object-fit: contain;
            filter:
                drop-shadow(0 0 20px rgba(0,200,255,0.55))
                drop-shadow(0 0 40px rgba(255,20,147,0.25));
            margin-bottom: 14px;
        }
        .brand-logo-name {
            font-size: 2rem;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .brand-logo-name .cyan { color: #00C8FF; }
        .brand-logo-name .pink { color: #FF1493; }
        .brand-logo-pos {
            font-size: 0.7rem;
            font-weight: 700;
            color: rgba(255,255,255,0.45);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 6px;
        }
        .brand-logo-tagline {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.35);
            letter-spacing: 0.5px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .brand-logo-tagline::before,
        .brand-logo-tagline::after {
            content: '—';
            color: rgba(255,255,255,0.2);
        }

        /* Form card — no card, transparent */
        .form-card {
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 0;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            box-shadow: none;
            animation: fadeInLeft 0.9s cubic-bezier(.16,1,.3,1) 0.1s both;
            width: 100%;
            max-width: 440px;
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-24px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Welcome heading */
        .welcome-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 4px;
            line-height: 1.2;
            text-align: center;
        }
        .welcome-sub {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            margin-bottom: 24px;
            text-align: center;
        }

        /* Alerts */
        .alert-modern {
            border-radius: 12px;
            padding: 11px 15px;
            font-size: .82rem;
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 20px;
            border: 1.5px solid transparent;
        }
        .alert-modern.error {
            background: rgba(255,20,147,0.10);
            color: #ff6eb7;
            border-color: rgba(255,20,147,0.30);
        }
        .alert-modern.success {
            background: rgba(0,200,255,0.10);
            color: #00C8FF;
            border-color: rgba(0,200,255,0.25);
        }

        /* Field groups */
        .field-group { margin-bottom: 14px; }

        .field-label {
            display: none; /* labels hidden — placeholders used instead */
        }

        .field-wrap { position: relative; }

        .field-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 16px;
            font-size: 1rem;
            color: rgba(255,255,255,0.28);
            pointer-events: none;
            transition: color .2s;
            z-index: 2;
        }

        .field-input {
            width: 100%;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 14px 16px;
            padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 46px;
            font-size: .88rem;
            color: #ffffff;
            font-family: inherit;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .field-input::placeholder { color: rgba(255,255,255,0.28); }
        .field-input:focus {
            background: rgba(0,200,255,0.07);
            border-color: rgba(0,200,255,0.5);
            box-shadow: 0 0 0 3px rgba(0,200,255,0.12);
        }
        .field-wrap:focus-within .field-icon { color: rgba(0,200,255,0.8); }

        .pw-toggle {
            position: absolute;
            top: 50%; transform: translateY(-50%);
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 13px;
            background: none; border: none;
            color: rgba(255,255,255,0.3);
            font-size: .9rem;
            cursor: pointer; padding: 2px; z-index: 2;
            transition: color .15s;
        }
        .pw-toggle:hover { color: #00C8FF; }

        /* Options row */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 10px 0 20px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            font-size: .78rem;
            color: rgba(255,255,255,0.5);
            user-select: none;
        }
        .remember-check {
            width: 16px; height: 16px;
            accent-color: #3b82f6;
            cursor: pointer;
        }

        .forgot-link {
            font-size: .78rem;
            font-weight: 600;
            color: #FF1493;
            text-decoration: none;
            transition: opacity .15s;
        }
        .forgot-link:hover { opacity: .75; }

        /* Login button */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, #e040fb 0%, #7c3aed 40%, #00bcd4 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: .95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            box-shadow: 0 8px 32px rgba(124,58,237,0.40), 0 4px 16px rgba(0,188,212,0.25);
            transition: transform .2s, box-shadow .2s, filter .2s;
            position: relative;
            overflow: hidden;
            letter-spacing: .5px;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.12) 0%, transparent 60%);
            pointer-events: none;
        }
        .btn-login::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s;
        }
        .btn-login:hover::after { left: 150%; }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 40px rgba(124,58,237,0.50), 0 8px 20px rgba(0,188,212,0.35);
            filter: brightness(1.06);
        }
        .btn-login:active { transform: translateY(0); }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 16px 0;
            color: rgba(255,255,255,0.2);
            font-size: .7rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        /* Touch ID button */
        .btn-touch {
            width: 100%;
            padding: 12px;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid rgba(255,255,255,0.10);
            border-radius: 12px;
            color: rgba(255,255,255,0.6);
            font-size: .85rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: all .2s;
        }
        .btn-touch:hover {
            border-color: #00C8FF;
            color: #00C8FF;
            background: rgba(0,200,255,0.06);
        }

        /* Lang bar */
        .lang-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 18px;
        }
        .lang-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: .73rem;
            font-weight: 700;
            text-decoration: none;
            transition: all .2s;
            border: 1.5px solid transparent;
        }
        .lang-pill.active-lang {
            background: rgba(0,200,255,0.12);
            border-color: #00C8FF;
            color: #00C8FF;
        }
        .lang-pill:not(.active-lang) {
            background: transparent;
            color: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.10);
        }
        .lang-pill:not(.active-lang):hover {
            border-color: #00C8FF;
            color: #00C8FF;
            background: rgba(0,200,255,0.08);
        }

        /* Footer */
        .login-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 18px;
        }
        .support-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .7rem;
            font-weight: 600;
            color: rgba(255,255,255,0.3);
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.08);
            transition: all .2s;
        }
        .support-btn:hover {
            color: #00C8FF;
            border-color: #00C8FF;
            background: rgba(0,200,255,0.06);
        }
        .footer-copy {
            font-size: .66rem;
            color: rgba(255,255,255,0.2);
        }
        .footer-copy span {
            background: linear-gradient(135deg, #00C8FF, #FF1493);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

        /* ═══════════════════════════ DEVICE SECTION ═══════════════════════════ */
        /* LTR=English: device on RIGHT | RTL=Arabic: device on LEFT */
        .right-section {
            position: absolute;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 0;
            top: 0;
            width: 45%;
            height: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 44px;
            z-index: 5;
            overflow: hidden;
        }

        /* ── Neon oval ring behind device ── */
        .device-neon-ring {
            position: absolute;
            width: 520px;
            height: 490px;
            top: 46%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 1;
            border-radius: 50%;
            border: 2.5px solid transparent;
            background:
                linear-gradient(#040b18, #040b18) padding-box,
                conic-gradient(from 195deg,
                    #0044ff 0%,
                    #6600ff 22%,
                    #cc00ff 40%,
                    #ff00aa 55%,
                    #cc00ff 68%,
                    #6600ff 80%,
                    #0044ff 100%
                ) border-box;
            box-shadow:
                0 0 28px rgba(0, 60, 255, 0.35),
                0 0 55px rgba(100, 0, 255, 0.22),
                inset 0 0 28px rgba(0, 60, 255, 0.06);
        }
        .device-neon-ring::after {
            content: '';
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 14px solid transparent;
            background:
                transparent padding-box,
                conic-gradient(from 195deg,
                    rgba(0,60,255,0.28) 0%,
                    rgba(120,0,255,0.38) 25%,
                    rgba(255,0,180,0.28) 55%,
                    rgba(120,0,255,0.20) 75%,
                    rgba(0,60,255,0.28) 100%
                ) border-box;
            filter: blur(12px);
        }

        /* ── Floor dots grid ── */
        .floor-dots {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 42%;
            background-image: radial-gradient(rgba(160, 80, 255, 0.22) 1.5px, transparent 1.5px);
            background-size: 22px 22px;
            pointer-events: none;
            z-index: 0;
            -webkit-mask-image: linear-gradient(to top, rgba(0,0,0,0.65) 0%, transparent 85%);
            mask-image: linear-gradient(to top, rgba(0,0,0,0.65) 0%, transparent 85%);
        }

        .device-wrap {
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            width: 100%;
            max-width: 620px;
            z-index: 2;
        }

        .device-img {
            max-height: 65vh;
            max-width: 85%;
            object-fit: contain;
            filter:
                drop-shadow(0 30px 60px rgba(0,0,0,0.75))
                drop-shadow(0 0 30px rgba(0,40,200,0.18))
                drop-shadow(0 0 80px rgba(100,0,255,0.10));
            animation: none;
            position: relative;
            z-index: 2;
        }

        /* Ground glow under device */
        .device-glow {
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 72%;
            height: 36px;
            background: radial-gradient(ellipse,
                rgba(180,60,255,0.55) 0%,
                rgba(0,70,255,0.28) 40%,
                transparent 75%
            );
            filter: blur(14px);
            border-radius: 50%;
            z-index: 1;
            animation: glowPulse 5s ease-in-out infinite;
        }
        @keyframes glowPulse {
            0%, 100% { opacity: 0.75; transform: translateX(-50%) scaleX(1); }
            50%       { opacity: 1;   transform: translateX(-50%) scaleX(1.08); }
        }

        /* Version badge */
        .version-badge {
            position: absolute;
            bottom: 22px;
            right: 24px;
            z-index: 20;
            font-size: .62rem;
            color: rgba(255,255,255,0.2);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .version-badge::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: #00C8FF;
            box-shadow: 0 0 6px #00C8FF;
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* ═══════════════════════════ RESPONSIVE ═══════════════════════════ */
        @media (max-width: 900px) {
            .right-section { display: none; }
            .left-section {
                position: relative;
                right: auto; left: auto;
                width: 100%;
                padding: 55px 24px 30px 24px;
                align-items: center;
            }
            .form-card { max-width: 420px; }

            .brand-logo img {
                width: 85px;
                height: 85px;
                margin-bottom: 10px;
            }
            .brand-logo-name {
                font-size: 1.65rem;
            }
            .brand-logo {
                margin-bottom: 20px;
            }
            .theme-toggle-wrap {
                top: 12px;
                {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 12px;
            }
            .theme-toggle-btn {
                padding: 4px 8px;
                font-size: 0.65rem;
            }
        }
        @media (max-width: 480px) {
            .form-card { padding: 28px 20px; border-radius: 20px; }
            .welcome-title { font-size: 1.35rem; }
        }

        /* Prevent transitions on page load */
        .no-transition,
        .no-transition * {
            -webkit-transition: none !important;
            -moz-transition: none !important;
            -ms-transition: none !important;
            -o-transition: none !important;
            transition: none !important;
        }
    </style>
</head>
<body class="no-transition">
<script>
    (function() {
        const saved = localStorage.getItem('da_login_theme') || 'dark';
        if (saved === 'light') {
            document.body.classList.add('light-mode');
        }
    })();
</script>

<div class="login-wrapper">

    {{-- Glow blobs --}}
    <div class="glow-blob cyan"></div>
    <div class="glow-blob pink"></div>
    <div class="glow-blob blue"></div>

    {{-- Theme Toggle Button --}}
    <div class="theme-toggle-wrap">
        <button class="theme-toggle-btn" id="themeToggleBtn" onclick="toggleLoginTheme()" type="button">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
            <span id="themeLabel">{{ app()->getLocale() == 'ar' ? 'الوضع الداكن' : 'Dark Mode' }}</span>
            <div class="toggle-pill" id="togglePill">
                <div class="toggle-thumb"></div>
            </div>
        </button>
    </div>

    {{-- ═══════════ RIGHT — Device Image (visually LEFT) ═══════════ --}}
    <div class="right-section">
        {{-- Neon ring behind device --}}
        <div class="device-neon-ring"></div>
        {{-- Floor dot grid --}}
        <div class="floor-dots"></div>
        {{-- Device --}}
        <div class="device-wrap">
            <img class="device-img"
                 src="{{ asset('images/dv.png') }}"
                 alt="Digital Age POS Device"
                 onerror="this.style.display='none'">
            <div class="device-glow"></div>
        </div>
    </div>

    {{-- ═══════════ LEFT — Brand + Form (visually RIGHT) ═══════════ --}}
    <div class="left-section">

        {{-- Logo — Big Centered --}}
        <div class="brand-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Digital Age Logo"
                 onerror="this.src='{{ asset('images/logo3.png') }}'">
            @if(app()->getLocale() == 'ar')
                <div class="brand-logo-name"><span class="cyan">ديجيتال</span> <span class="pink">إيج</span></div>
                <div class="brand-logo-pos">نظام نقاط البيع</div>
                <div class="brand-logo-tagline">ابتكار المستقبل الرقمي</div>
            @else
                <div class="brand-logo-name"><span class="cyan">Digital</span> <span class="pink">Age</span></div>
                <div class="brand-logo-pos">POS SYSTEM</div>
                <div class="brand-logo-tagline">Innovating the Digital Future</div>
            @endif
        </div>

        {{-- Form — No card, transparent --}}
        <div class="form-card">

            {{-- Welcome --}}
            <div class="welcome-title">
                {{ app()->getLocale() == 'ar' ? 'مرحباً بعودتك' : 'Welcome Back' }}
            </div>
            <div class="welcome-sub">
                {{ app()->getLocale() == 'ar' ? 'سجّل دخولك للمتابعة' : 'Please sign in to continue' }}
            </div>

            {{-- Alerts --}}
            @if ($errors->any())
                <div class="alert-modern error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="alert-modern success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                {{-- Username --}}
                <div class="field-group">
                    <label class="field-label" for="username">
                        {{ app()->getLocale() == 'ar' ? 'اسم المستخدم' : 'Username' }}
                    </label>
                    <div class="field-wrap">
                        <i class="bi bi-person field-icon"></i>
                        <input
                            id="username"
                            name="login"
                            type="text"
                            class="field-input"
                            placeholder="{{ app()->getLocale() == 'ar' ? 'اسم المستخدم' : 'Username' }}"
                            value="{{ old('login') }}"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <label class="field-label" for="password">
                        {{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }}
                    </label>
                    <div class="field-wrap">
                        <i class="bi bi-lock field-icon"></i>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="field-input"
                            placeholder="{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }}"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="pw-toggle" id="pwToggle" aria-label="Toggle password">
                            <i class="bi bi-eye-slash" id="pwIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Options --}}
                <div class="options-row" style="justify-content: flex-end;">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            {{ app()->getLocale() == 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot password?' }}
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login" id="loginBtn">
                    {{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
                </button>
            </form>



            {{-- Language switcher --}}
            <div class="lang-bar">
                <a href="{{ url('/change-language/ar') }}"
                   class="lang-pill {{ app()->getLocale() == 'ar' ? 'active-lang' : '' }}">
                    <span>🇸🇦</span> العربية
                </a>
                <a href="{{ url('/change-language/en') }}"
                   class="lang-pill {{ app()->getLocale() == 'en' ? 'active-lang' : '' }}">
                    <span>🇬🇧</span> English
                </a>
            </div>

            {{-- Footer --}}
            <div class="login-footer" style="justify-content: center;">
                <div class="footer-copy">© 2026 <span>{{ app()->getLocale() == 'ar' ? 'ديجيتال إيج' : 'Digital Age' }}</span></div>
            </div>
        </div>

    </div>

    {{-- Version badge --}}
    <div class="version-badge">v2.0.0</div>

</div>

<script>
    /* ─── Password Toggle ─── */
    const pwInput = document.getElementById('password');
    const pwIcon  = document.getElementById('pwIcon');
    document.getElementById('pwToggle').addEventListener('click', function() {
        const isHidden = pwInput.type === 'password';
        pwInput.type = isHidden ? 'text' : 'password';
        pwIcon.className = isHidden ? 'bi bi-eye' : 'bi bi-eye-slash';
    });

    /* ─── Login Button Loading State ─── */
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> {{ app()->getLocale() == "ar" ? "جاري الدخول..." : "Signing in..." }}';
        btn.disabled = true;
    });

    /* ─── Dark / Light Mode Toggle ─── */
    const isAr = document.documentElement.dir === 'rtl';
    const THEME_KEY = 'da_login_theme';

    const themeLabels = {
        dark:  { en: 'Dark Mode',  ar: 'الوضع الداكن' },
        light: { en: 'Light Mode', ar: 'الوضع الفاتح' }
    };

    function applyLoginTheme(theme) {
        const body   = document.body;
        const icon   = document.getElementById('themeIcon');
        const label  = document.getElementById('themeLabel');
        const pill   = document.getElementById('togglePill');

        if (theme === 'light') {
            body.classList.add('light-mode');
            if (icon)  icon.className = 'bi bi-sun-fill';
            if (label) label.textContent = isAr ? themeLabels.light.ar : themeLabels.light.en;
            if (pill)  pill.classList.add('on');
        } else {
            body.classList.remove('light-mode');
            if (icon)  icon.className = 'bi bi-moon-stars-fill';
            if (label) label.textContent = isAr ? themeLabels.dark.ar : themeLabels.dark.en;
            if (pill)  pill.classList.remove('on');
        }
        localStorage.setItem(THEME_KEY, theme);
    }

    function toggleLoginTheme() {
        const isDark = !document.body.classList.contains('light-mode');
        applyLoginTheme(isDark ? 'light' : 'dark');
    }

    /* Load saved theme on page load */
    (function() {
        const saved = localStorage.getItem(THEME_KEY) || 'dark';
        applyLoginTheme(saved);
    })();

    // Remove no-transition class after theme is applied to prevent transition flash on load
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            document.body.classList.remove('no-transition');
        }, 50);
    });
</script>

</body>
</html>
