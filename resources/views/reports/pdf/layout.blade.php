<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #46bfa3;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #46bfa3;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
            width: 100%;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            font-weight: bold;
        }
        .report-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .fw-bold { font-weight: bold; }
        .rtl { direction: rtl; }
        .ltr { direction: ltr; }
        
        /* Arabic Support */
        [dir="rtl"] .report-table th, [dir="rtl"] .report-table td {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $setting->name ?? config('app.name') }}</h1>
        <p>{{ __('pos.reports') }} - @yield('title')</p>
        <p class="small text-muted">{{ __('pos.date') }}: {{ $filters['from_date'] }} - {{ $filters['to_date'] }}</p>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        <p>{{ __('pos.generated_on') ?? 'Generated on' }}: {{ now()->format('Y-m-d H:i') }}</p>
        <p>&copy; {{ date('Y') }} {{ $setting->name ?? config('app.name') }}</p>
    </div>
</body>
</html>
