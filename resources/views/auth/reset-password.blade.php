<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('massage.reset_password') }} - DigitalAge</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #1E88E5;
            --primary-glow: rgba(30, 136, 229, 0.4);
            --accent: #46bfa3;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Outfit', sans-serif" }};
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            position: relative;
        }

        /* Hide default browser password reveal eye icon (like in MS Edge) */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        .bg-mesh {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 136, 229, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(70, 191, 163, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 136, 229, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(70, 191, 163, 0.15) 0px, transparent 50%);
        }

        .auth-container {
            width: 100%;
            max-width: 580px;
            z-index: 1;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border-radius: 36px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.04);
            padding: 48px;
            position: relative;
            animation: fadeInScale 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.98) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .lang-switch {
            position: absolute;
            top: 24px;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 24px;
        }

        .lang-link {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.8);
            padding: 6px 14px;
            border-radius: 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .lang-link:hover {
            background: #fff;
            transform: translateY(-2px);
            color: var(--primary);
        }

        .branding {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-img {
            max-width: 80px;
            height: auto;
            margin-bottom: 16px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.05));
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(34, 197, 94, 0.1);
            color: #15803d;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 24px;
        }

        .security-stage {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 28px;
            padding: 28px;
            margin-bottom: 24px;
            transition: all 0.3s;
        }

        .security-stage:hover {
            border-color: var(--primary);
            background: #fff;
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .question-text {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 12px;
            display: block;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.6);
            border: 1.5px solid rgba(226, 232, 240, 0.5);
            border-radius: 16px;
            padding: 12px 18px;
            font-size: 0.95rem;
            color: var(--text-main);
            transition: all 0.3s;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
            outline: none;
        }

        .btn-action {
            background: linear-gradient(135deg, var(--primary), #1565C0);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 24px;
            box-shadow: 0 10px 24px var(--primary-glow);
            transition: all 0.3s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px var(--primary-glow);
        }

        .btn-cancel-custom {
            display: block;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .btn-cancel-custom:hover {
            color: var(--primary);
        }

        @media (max-width: 580px) {
            .glass-card {
                padding: 40px 24px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-mesh"></div>

    <div class="auth-container">
        <div class="glass-card text-center">
            <!-- Language Switcher -->
            <div class="lang-switch">
                @if(app()->getLocale() == 'ar')
                    <a href="{{ url('/change-language/en') }}" class="lang-link">
                        <i class="bi bi-translate"></i> EN
                    </a>
                @else
                    <a href="{{ url('/change-language/ar') }}" class="lang-link">
                        <i class="bi bi-translate"></i> AR
                    </a>
                @endif
            </div>

            <!-- Branding -->
            <div class="branding">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo-img" onerror="this.src='https://ui-avatars.com/api/?name=DA&background=1E88E5&color=fff&size=200'">
                <h1 class="brand-title">
                    <span style="color: #46bfa3;">{{ __('massage.company_name_1') }}</span><span style="color: #c21460;">{{ __('massage.company_name_2') }}</span>
                </h1>
                <div class="system-tag">{{ __('massage.reset_password') }}</div>
            </div>

            <div class="user-badge shadow-sm">
                <i class="bi bi-person-check-fill"></i> {{ $user->username }}
            </div>

            @if(session('error'))
                <div class="alert alert-danger border-0 rounded-4 mb-4 small text-center" style="background: rgba(239, 68, 68, 0.1); color: #dc2626;">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset.update') }}" class="text-start">
                @csrf
                
                <div class="alert alert-info border-0 rounded-4 py-2 px-3 small mb-4 text-center" style="background: rgba(30, 136, 229, 0.1); color: #0d47a1; font-size: 0.85rem;">
                    <i class="bi bi-shield-lock-fill me-1"></i> يرجى إدخال كلمة المرور الجديدة أدناه.
                </div>

                <div class="mt-4">
                    <label class="form-label">{{ __('massage.form_new_password') }}</label>
                    <input type="password" name="password" class="form-control w-100 mb-1" required placeholder="********" autofocus>
                    <div class="text-muted small mb-3 d-flex align-items-center gap-1" style="font-size: 0.78rem; opacity: 0.85;">
                        <i class="bi bi-info-circle"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.' : 'Password must be at least 8 characters long.' }}</span>
                    </div>
                    
                    <label class="form-label">{{ __('massage.form_confirm_password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control w-100" required placeholder="********">
                </div>

                <button type="submit" class="btn-action">
                    <i class="bi bi-unlock-fill me-1"></i> {{ __('massage.reset_password') }}
                </button>
                <a href="{{ route('login') }}" class="btn-cancel-custom">
                    {{ __('massage.form_cancel') }}
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
