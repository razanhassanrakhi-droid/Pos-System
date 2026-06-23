<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('pos.warranty_card') }} - {{ $warranty->serial_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .warranty-card {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 2px solid #0d6efd;
        }
        .header {
            background: #0d6efd;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px dashed #dee2e6;
            padding-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #6c757d;
        }
        .value {
            font-weight: 600;
            color: #212529;
        }
        .serial-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin-top: 30px;
        }
        .serial-number {
            font-size: 1.5rem;
            letter-spacing: 2px;
            font-family: 'Courier New', Courier, monospace;
            color: #0d6efd;
            font-weight: bold;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
            background: #f8f9fa;
        }
        @media print {
            body { background: white; margin: 0; }
            .warranty-card { margin: 0; box-shadow: none; border: 1px solid #000; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container text-center mt-4 btn-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
            <i class="bi bi-printer"></i> {{ __('pos.print') }}
        </button>
    </div>

    <div class="warranty-card">
        <div class="header">
            <h3>{{ __('pos.warranty_card') }}</h3>
            <p class="mb-0">{{ $warranty->branch->name }}</p>
        </div>
        <div class="content">
            <div class="info-row">
                <span class="label">{{ __('pos.product_name') }}</span>
                <span class="value">{{ $warranty->product->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">{{ __('pos.customer_name') }}</span>
                <span class="value">{{ $warranty->customer->name ?? __('pos.walk_in_customer') }}</span>
            </div>
            <div class="info-row">
                <span class="label">{{ __('pos.invoice_number') }}</span>
                <span class="value">{{ $warranty->sale->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="label">{{ __('pos.warranty_start_date') }}</span>
                <span class="value">{{ $warranty->warranty_start_date->format('Y-m-d') }}</span>
            </div>
            <div class="info-row">
                <span class="label">{{ __('pos.warranty_end_date') }}</span>
                <span class="value">{{ $warranty->warranty_end_date->format('Y-m-d') }}</span>
            </div>
            <div class="info-row">
                <span class="label">{{ __('pos.warranty_period_months') }}</span>
                <span class="value">{{ $warranty->warranty_period_months }} {{ __('pos.months') ?? 'Months' }}</span>
            </div>

            <div class="serial-box">
                <div class="label mb-2">{{ __('pos.serial_number') }}</div>
                <div class="serial-number">{{ $warranty->serial_number ?: 'N/A' }}</div>
            </div>
        </div>
        <div class="footer">
            <p class="mb-1 text-uppercase fw-bold">{{ __('pos.terms_applied') ?? 'Terms & Conditions Apply' }}</p>
            <p class="mb-0">{{ date('Y-m-d H:i') }}</p>
        </div>
    </div>
</body>
</html>
