<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('massage.register') }} - DigitalAge</title>
    
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
            max-width: 680px;
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

        .brand-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .section-header {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
            margin: 32px 0 20px;
            padding-bottom: 12px;
            border-bottom: 1.5px solid rgba(226, 232, 240, 0.5);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.6);
            border: 1.5px solid rgba(226, 232, 240, 0.5);
            border-radius: 16px;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: var(--text-main);
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
            outline: none;
        }

        .security-stage {
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .security-stage:hover {
            background: rgba(255, 255, 255, 0.7);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-register {
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

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px var(--primary-glow);
        }

        .btn-cancel {
            background: rgba(226, 232, 240, 0.5);
            color: var(--text-muted);
            border: none;
            padding: 14px;
            border-radius: 18px;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            margin-top: 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: rgba(226, 232, 240, 0.8);
            color: var(--text-main);
        }

        .footer-links {
            margin-top: 32px;
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .glass-card {
                padding: 40px 24px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-mesh"></div>

    <div class="auth-container">
        <div class="glass-card">
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
                <div class="system-tag">{{ __('massage.register') }}</div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-4 mb-4 small" style="background: rgba(239, 68, 68, 0.1); color: #dc2626;">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                
                <!-- Personal Info -->
                <div class="section-header">
                    <i class="bi bi-person-badge-fill"></i> {{ __('massage.form_user_details') }}
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('pos.full_name') }} (AR) <span class="text-danger">*</span></label>
                        <input type="text" name="full_name_ar" class="form-control" value="{{ old('full_name_ar') }}" required placeholder="{{ __('massage.contact.placeholder.name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('pos.full_name') }} (EN) <span class="text-danger">*</span></label>
                        <input type="text" name="full_name_en" class="form-control" value="{{ old('full_name_en') }}" required placeholder="{{ __('massage.contact.placeholder.name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('massage.username') }}</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="jane_pos">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('massage.email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="jane@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('massage.phone_number') }}</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+123 456 789">
                    </div>
                </div>

                <!-- Password -->
                <div class="section-header">
                    <i class="bi bi-shield-lock-fill"></i> {{ __('massage.form_password_section') }}
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('massage.password') }}</label>
                        <input type="password" name="password" class="form-control" required placeholder="********">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('massage.form_confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="********">
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    {{ __('massage.create_account') }} <i class="bi bi-person-check ms-1"></i>
                </button>

                <div class="footer-links">
                    <span class="text-muted small">{{ __('massage.already_have_account') }}</span><br>
                    <a href="{{ route('login') }}" class="footer-action mt-2">{{ __('login.login_btn') }}</a>
                    <a href="{{ route('login') }}" class="btn-cancel shadow-sm">{{ __('massage.form_cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
