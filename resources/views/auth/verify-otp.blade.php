<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() == 'ar' ? 'التحقق من الكود' : 'Verify OTP' }} — DigitalAge POS</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --magenta:       #D11B8D;
            --magenta-light: #f7d6ee;
            --magenta-soft:  rgba(209,27,141,.08);
            --teal:          #2EC4B6;
            --teal-light:    #d1f5f2;
            --teal-soft:     rgba(46,196,182,.08);
            --white:         #ffffff;
            --gray-50:       #f9fafb;
            --gray-100:      #f3f4f6;
            --gray-200:      #e5e7eb;
            --gray-400:      #9ca3af;
            --gray-600:      #4b5563;
            --gray-800:      #1f2937;
            --grad-brand:    linear-gradient(135deg, #2EC4B6 0%, #1aa89c 40%, #D11B8D 100%);
        }
        *,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
        html,body {
            height:100%;
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background:var(--gray-50);
            overflow:hidden;
        }

        .split-wrapper { display:flex; height:100vh; width:100vw; overflow:hidden; }

        /* LEFT */
        .left-panel {
            flex:1.1;
            position:relative;
            background:linear-gradient(145deg,#f0fafa 0%,#fdf0f8 60%,#eaf9f8 100%);
            overflow:hidden;
            display:flex; align-items:center; justify-content:center;
        }
        .left-panel::before {
            content:''; position:absolute; inset:0;
            background-image:
                radial-gradient(circle at 20% 20%,rgba(46,196,182,.12) 0,transparent 40%),
                radial-gradient(circle at 80% 80%,rgba(209,27,141,.08) 0,transparent 40%);
            pointer-events:none;
        }
        .left-panel::after {
            content:''; position:absolute; inset:0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232EC4B6' fill-opacity='0.03'%3E%3Crect x='0' y='0' width='2' height='60'/%3E%3Crect x='8' y='0' width='3' height='60'/%3E%3Crect x='18' y='0' width='2' height='60'/%3E%3Crect x='28' y='0' width='3' height='60'/%3E%3Crect x='38' y='0' width='2' height='60'/%3E%3Crect x='48' y='0' width='3' height='60'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events:none; opacity:.5;
        }

        .left-illustration {
            position:relative; z-index:2;
            display:flex; flex-direction:column; align-items:center; gap:28px;
            animation:floatUp .9s cubic-bezier(.16,1,.3,1) both;
        }
        @keyframes floatUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }

        .big-icon-ring {
            width:180px; height:180px; border-radius:50%;
            background:linear-gradient(135deg,var(--teal-light) 0%,var(--magenta-light) 100%);
            display:flex; align-items:center; justify-content:center;
            box-shadow:0 0 0 20px rgba(46,196,182,.06),0 0 0 40px rgba(209,27,141,.03),0 24px 48px rgba(46,196,182,.18);
            animation:pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100%{box-shadow:0 0 0 20px rgba(46,196,182,.06),0 0 0 40px rgba(209,27,141,.03),0 24px 48px rgba(46,196,182,.18)}
            50%     {box-shadow:0 0 0 28px rgba(46,196,182,.08),0 0 0 52px rgba(209,27,141,.04),0 32px 56px rgba(46,196,182,.22)}
        }
        .big-icon-inner {
            width:120px; height:120px; border-radius:50%;
            background:#fff; display:flex; align-items:center; justify-content:center;
            box-shadow:0 8px 24px rgba(0,0,0,.08);
        }
        .big-icon-svg { font-size:3.2rem; background:var(--grad-brand); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }

        /* Steps */
        .steps-strip { display:flex; align-items:center; }
        .step-item { display:flex; flex-direction:column; align-items:center; gap:6px; }
        .step-circle {
            width:36px; height:36px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:.75rem; font-weight:700; font-family:'Inter',sans-serif;
        }
        .step-circle.done    { background:var(--teal); color:#fff; box-shadow:0 4px 12px rgba(46,196,182,.35); }
        .step-circle.active  { background:var(--grad-brand); color:#fff; box-shadow:0 4px 12px rgba(209,27,141,.3); }
        .step-circle.pending { background:var(--gray-100); color:var(--gray-400); border:2px solid var(--gray-200); }
        .step-label { font-size:.58rem; color:var(--gray-400); font-family:'Inter',sans-serif; text-align:center; max-width:60px; }
        .step-line { width:48px; height:2px; background:var(--gray-200); margin-bottom:20px; }
        .step-line.filled { background:linear-gradient(90deg,var(--teal),var(--magenta)); }

        /* Email info */
        .email-hint-card {
            background:#fff; border-radius:14px; padding:14px 18px;
            border:1px solid var(--gray-200); box-shadow:0 4px 16px rgba(0,0,0,.05);
            display:flex; align-items:center; gap:12px; max-width:280px;
        }
        .email-icon { width:40px; height:40px; border-radius:10px; background:var(--teal-light); color:var(--teal); display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
        .email-title { font-size:.78rem; font-weight:700; color:var(--gray-800); font-family:'Inter',sans-serif; }
        .email-sub   { font-size:.65rem; color:var(--gray-400); font-family:'Inter',sans-serif; margin-top:2px; }

        /* Timer card */
        .timer-card {
            background:#fff; border-radius:14px; padding:12px 18px;
            border:1px solid rgba(209,27,141,.15); box-shadow:0 4px 16px rgba(209,27,141,.06);
            display:flex; align-items:center; gap:10px;
        }
        .timer-icon { width:34px; height:34px; border-radius:9px; background:var(--magenta-light); color:var(--magenta); display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }
        .timer-val  { font-size:.85rem; font-weight:700; color:var(--gray-800); font-family:'Inter',sans-serif; }
        .timer-lbl  { font-size:.6rem; color:var(--gray-400); font-family:'Inter',sans-serif; }

        .left-brand-badge { position:absolute; bottom:32px; left:50%; transform:translateX(-50%); z-index:10; text-align:center; }
        .left-brand-text  { font-size:.7rem; color:var(--gray-400); font-family:'Inter',sans-serif; }
        .left-brand-name  { font-size:.9rem; font-weight:700; letter-spacing:-.5px; }
        .left-brand-name .t { color:var(--teal); }
        .left-brand-name .m { color:var(--magenta); }

        /* RIGHT */
        .right-panel {
            width:440px; min-width:380px;
            display:flex; align-items:center; justify-content:center;
            background:var(--white);
            border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}:1px solid var(--gray-200);
            position:relative; overflow:hidden; padding:32px 24px; z-index:20;
            box-shadow:{{ app()->getLocale() == 'ar' ? '8px 0 40px rgba(0,0,0,.06)' : '-8px 0 40px rgba(0,0,0,.06)' }};
        }
        .right-panel::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--grad-brand); }
        .right-panel::after  { content:''; position:absolute; bottom:-60px; {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}:-60px; width:200px; height:200px; border-radius:50%; background:radial-gradient(circle,rgba(46,196,182,.06) 0%,transparent 70%); pointer-events:none; }

        .form-card { width:100%; max-width:360px; animation:slideIn .8s cubic-bezier(.16,1,.3,1) both; }
        @keyframes slideIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }

        .logo-area { text-align:center; margin-bottom:22px; }
        .logo-img-wrap {
            width:56px; height:56px; border-radius:15px;
            background:linear-gradient(135deg,var(--teal-light),var(--magenta-light));
            display:inline-flex; align-items:center; justify-content:center;
            margin-bottom:10px;
            box-shadow:0 6px 16px rgba(46,196,182,.18),0 3px 8px rgba(209,27,141,.1);
        }
        .logo-img-wrap img { width:40px; height:40px; object-fit:contain; border-radius:10px; }
        .brand-name { font-size:1.2rem; font-weight:800; letter-spacing:-1px; line-height:1; margin-bottom:2px; }
        .brand-name .t { color:var(--teal); }
        .brand-name .m { color:var(--magenta); }
        .brand-sub { font-size:.68rem; color:var(--gray-400); font-weight:500; letter-spacing:.5px; text-transform:uppercase; }

        .page-header { margin-bottom:22px; }
        .page-icon-wrap {
            width:46px; height:46px; border-radius:13px;
            background:linear-gradient(135deg,var(--teal-soft),var(--magenta-soft));
            display:flex; align-items:center; justify-content:center; margin-bottom:10px;
            border:1px solid rgba(46,196,182,.15);
        }
        .page-icon-wrap i { font-size:1.2rem; background:var(--grad-brand); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .page-title { font-size:1.2rem; font-weight:700; color:var(--gray-800); margin-bottom:4px; }
        .page-desc  { font-size:.8rem; color:var(--gray-400); line-height:1.5; }

        .alert-modern { border-radius:12px; padding:10px 14px; font-size:.82rem; display:flex; align-items:center; gap:8px; margin-bottom:18px; border:none; }
        .alert-modern.error { background:rgba(209,27,141,.07); color:#9b0d6a; border-left:3px solid var(--magenta); }

        /* OTP Digits */
        .otp-label { font-size:.78rem; font-weight:600; color:var(--gray-600); margin-bottom:12px; display:block; letter-spacing:.2px; }
        .otp-row { display:flex; gap:8px; justify-content:center; margin-bottom:22px; }
        .otp-digit {
            width:48px; height:56px;
            border:2px solid var(--gray-200);
            border-radius:14px;
            background:var(--gray-50);
            text-align:center;
            font-size:1.4rem;
            font-weight:700;
            color:var(--gray-800);
            font-family:'Inter',sans-serif;
            outline:none;
            transition:border-color .2s, box-shadow .2s, background .2s, transform .1s;
            caret-color:var(--teal);
        }
        .otp-digit:focus {
            border-color:var(--teal);
            background:#fff;
            box-shadow:0 0 0 3px rgba(46,196,182,.15);
            transform:scale(1.05);
        }
        .otp-digit.filled { border-color:var(--teal); background:#fff; color:var(--teal); }

        .btn-action {
            width:100%; padding:13px;
            background:var(--grad-brand); color:#fff;
            border:none; border-radius:14px;
            font-size:.92rem; font-weight:700; font-family:inherit;
            cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;
            box-shadow:0 8px 24px rgba(46,196,182,.22),0 4px 12px rgba(209,27,141,.14);
            transition:transform .2s,box-shadow .2s,filter .2s;
            position:relative; overflow:hidden; margin-bottom:12px;
        }
        .btn-action::before { content:''; position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,.15) 0%,transparent 60%); pointer-events:none; }
        .btn-action:hover { transform:translateY(-2px); box-shadow:0 14px 32px rgba(46,196,182,.28),0 6px 16px rgba(209,27,141,.18); filter:brightness(1.04); }
        .btn-action:active { transform:translateY(0); }

        .resend-row { text-align:center; margin-bottom:12px; font-size:.78rem; color:var(--gray-400); }
        .resend-link { color:var(--magenta); font-weight:600; text-decoration:none; transition:opacity .15s; }
        .resend-link:hover { opacity:.75; }

        .back-link {
            display:flex; align-items:center; justify-content:center; gap:7px;
            color:var(--gray-400); font-size:.82rem; font-weight:600;
            text-decoration:none; padding:10px; border-radius:10px;
            border:1.5px solid var(--gray-200); background:var(--gray-50);
            transition:all .2s;
        }
        .back-link:hover { border-color:var(--teal); color:var(--teal); background:var(--teal-soft); }

        /* Countdown timer */
        .countdown-strip {
            display:flex; align-items:center; justify-content:center; gap:8px;
            background:linear-gradient(135deg,rgba(46,196,182,.05),rgba(209,27,141,.04));
            border:1px solid rgba(46,196,182,.15); border-radius:12px;
            padding:10px 14px; margin-top:16px;
        }
        .countdown-strip i { color:var(--teal); font-size:.9rem; }
        .countdown-strip span { font-size:.75rem; color:var(--gray-600); }
        #countdown-display { font-weight:700; color:var(--magenta); }

        .form-footer { text-align:center; margin-top:18px; }
        .footer-dots { display:flex; align-items:center; justify-content:center; gap:6px; margin-bottom:6px; }
        .footer-dot { width:6px; height:6px; border-radius:50%; }
        .footer-dot.t { background:var(--teal); }
        .footer-dot.m { background:var(--magenta); }
        .footer-dot.g { background:var(--gray-200); }
        .footer-text  { font-size:.68rem; color:var(--gray-400); letter-spacing:.3px; }

        @media (max-width:900px) { .left-panel{display:none;} .right-panel{width:100%;min-width:unset;border:none;box-shadow:none;} }
    </style>
</head>
<body>
<div class="split-wrapper">

    {{-- LEFT --}}
    <div class="left-panel">
        <div class="left-illustration">

            <div class="big-icon-ring">
                <div class="big-icon-inner">
                    <i class="bi bi-envelope-check-fill big-icon-svg"></i>
                </div>
            </div>

            <div class="steps-strip">
                <div class="step-item">
                    <div class="step-circle done"><i class="bi bi-check-lg" style="font-size:.8rem"></i></div>
                    <div class="step-label">{{ app()->getLocale() == 'ar' ? 'التحقق' : 'Verify' }}</div>
                </div>
                <div class="step-line filled"></div>
                <div class="step-item">
                    <div class="step-circle active">2</div>
                    <div class="step-label">{{ app()->getLocale() == 'ar' ? 'كود OTP' : 'OTP Code' }}</div>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-circle pending">3</div>
                    <div class="step-label">{{ app()->getLocale() == 'ar' ? 'كلمة جديدة' : 'New Password' }}</div>
                </div>
            </div>

            <div class="email-hint-card" style="animation:floatUp .9s .2s both">
                <div class="email-icon"><i class="bi bi-envelope-fill"></i></div>
                <div>
                    <div class="email-title">{{ app()->getLocale() == 'ar' ? 'تحقق من بريدك' : 'Check Your Email' }}</div>
                    <div class="email-sub">{{ app()->getLocale() == 'ar' ? 'الكود مكون من 6 أرقام' : '6-digit code was sent' }}</div>
                </div>
            </div>

            <div class="timer-card" style="animation:floatUp .9s .35s both">
                <div class="timer-icon"><i class="bi bi-clock-fill"></i></div>
                <div>
                    <div class="timer-val">15:00</div>
                    <div class="timer-lbl">{{ app()->getLocale() == 'ar' ? 'صلاحية الكود' : 'Code validity' }}</div>
                </div>
            </div>

        </div>

        <div class="left-brand-badge">
            <div class="left-brand-text">Powered by</div>
            <div class="left-brand-name"><span class="t">Digital</span><span class="m">Age</span></div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="right-panel">
        <div class="form-card">

            <div class="logo-area">
                <div class="logo-img-wrap">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                         onerror="this.style.display='none';this.parentElement.innerHTML='<svg width=\'32\' height=\'32\' viewBox=\'0 0 36 36\' fill=\'none\'><rect width=\'36\' height=\'36\' rx=\'8\' fill=\'url(#lg)\'/><defs><linearGradient id=\'lg\' x1=\'0\' y1=\'0\' x2=\'36\' y2=\'36\'><stop stop-color=\'#2EC4B6\'/><stop offset=\'1\' stop-color=\'#D11B8D\'/></linearGradient></defs><text x=\'50%\' y=\'56%\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-size=\'14\' font-weight=\'800\' fill=\'white\' font-family=\'Inter\'>DA</text></svg>'">
                </div>
                <div class="brand-name"><span class="t">{{ __('massage.company_name_1') }}</span><span class="m">{{ __('massage.company_name_2') }}</span></div>
                <div class="brand-sub">Point of Sale System</div>
            </div>

            <div class="page-header">
                <div class="page-icon-wrap">
                    <i class="bi bi-shield-check-fill"></i>
                </div>
                <div class="page-title">{{ app()->getLocale() == 'ar' ? 'التحقق من الهوية' : 'Identity Verification' }}</div>
                <div class="page-desc">{{ app()->getLocale() == 'ar' ? 'لقد أرسلنا كود التحقق المكون من 6 أرقام إلى بريدك الإلكتروني.' : 'We sent a 6-digit verification code to your email address.' }}</div>
            </div>

            @if(session('error'))
                <div class="alert-modern error">
                    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;font-size:1rem"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- FORM (all logic unchanged) --}}
            <form method="POST" action="{{ route('password.otp.process') }}" id="otpForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user_id }}">
                <input type="hidden" name="code" id="fullCode">

                <label class="otp-label">{{ app()->getLocale() == 'ar' ? 'أدخل الكود المكون من 6 أرقام' : 'Enter the 6-digit code' }}</label>
                <div class="otp-row">
                    <input type="text" class="otp-digit" maxlength="1" pattern="\d*" inputmode="numeric" autocomplete="one-time-code">
                    <input type="text" class="otp-digit" maxlength="1" pattern="\d*" inputmode="numeric">
                    <input type="text" class="otp-digit" maxlength="1" pattern="\d*" inputmode="numeric">
                    <input type="text" class="otp-digit" maxlength="1" pattern="\d*" inputmode="numeric">
                    <input type="text" class="otp-digit" maxlength="1" pattern="\d*" inputmode="numeric">
                    <input type="text" class="otp-digit" maxlength="1" pattern="\d*" inputmode="numeric">
                </div>

                <button type="submit" class="btn-action">
                    <i class="bi bi-shield-check"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'تحقق الآن' : 'Verify Now' }}</span>
                </button>
            </form>

            <div class="resend-row">
                {{ app()->getLocale() == 'ar' ? 'لم يصلك الكود؟' : "Didn't receive the code?" }}
                <a href="#" class="resend-link" onclick="window.history.back();return false;">
                    {{ app()->getLocale() == 'ar' ? 'إعادة المحاولة' : 'Try again' }}
                </a>
            </div>

            <a href="{{ route('login') }}" class="back-link">
                <i class="bi bi-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}" style="font-size:.9rem"></i>
                {{ __('massage.form_cancel') }}
            </a>

            <div class="countdown-strip">
                <i class="bi bi-clock-history"></i>
                <span>
                    {{ app()->getLocale() == 'ar' ? 'الكود ينتهي خلال:' : 'Code expires in:' }}
                    <span id="countdown-display">15:00</span>
                </span>
            </div>

            <div class="form-footer">
                <div class="footer-dots">
                    <div class="footer-dot t"></div>
                    <div class="footer-dot g"></div>
                    <div class="footer-dot m"></div>
                </div>
                <div class="footer-text">© {{ date('Y') }} DigitalAge · Enterprise POS System · All rights reserved</div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // OTP digits logic (preserved from original)
    const digits        = document.querySelectorAll('.otp-digit');
    const fullCodeInput = document.getElementById('fullCode');
    const form          = document.getElementById('otpForm');

    digits.forEach((digit, index) => {
        digit.addEventListener('input', () => {
            digit.value = digit.value.replace(/\D/g,'');
            if (digit.value) {
                digit.classList.add('filled');
                if (index < digits.length - 1) digits[index + 1].focus();
            } else {
                digit.classList.remove('filled');
            }
            updateFullCode();
        });

        digit.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !digit.value && index > 0) {
                digits[index - 1].focus();
                digits[index - 1].value = '';
                digits[index - 1].classList.remove('filled');
            }
        });

        digit.addEventListener('paste', (e) => {
            const data = e.clipboardData.getData('text').replace(/\D/g,'').slice(0, 6);
            e.preventDefault();
            if (data) {
                data.split('').forEach((char, i) => {
                    if (digits[i]) { digits[i].value = char; digits[i].classList.add('filled'); }
                });
                if (digits[data.length - 1]) digits[data.length - 1].focus();
                updateFullCode();
            }
        });
    });

    function updateFullCode() {
        let code = '';
        digits.forEach(d => code += d.value);
        fullCodeInput.value = code;
    }

    form.addEventListener('submit', (e) => {
        updateFullCode();
        if (fullCodeInput.value.length !== 6) {
            e.preventDefault();
            alert('{{ app()->getLocale() == "ar" ? "يرجى إدخال كود التحقق المكون من 6 أرقام" : "Please enter the 6-digit verification code" }}');
        }
    });

    // Countdown timer (15 min)
    let totalSeconds = 15 * 60;
    const display    = document.getElementById('countdown-display');
    const timer      = setInterval(() => {
        totalSeconds--;
        if (totalSeconds <= 0) { clearInterval(timer); display.textContent = '00:00'; return; }
        const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const s = String(totalSeconds % 60).padStart(2, '0');
        display.textContent = m + ':' + s;
        if (totalSeconds <= 60) display.style.color = '#D11B8D';
    }, 1000);

    digits[0].focus();
</script>
</body>
</html>
