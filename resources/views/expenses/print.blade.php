<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ app()->getLocale() == 'ar' ? 'سند صرف مصروفات' : 'Expense Voucher' }} - {{ $expense->expense_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .detail-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .detail-item strong {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 12px;
            text-transform: uppercase;
        }
        .detail-item span {
            font-size: 16px;
            font-weight: bold;
        }
        .description-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 40px;
        }
        .description-box strong {
            display: block;
            margin-bottom: 10px;
            color: #555;
            text-transform: uppercase;
            font-size: 12px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
            height: 50px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-container { border: none; box-shadow: none; padding: 20px; max-width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt-container">
        <div class="header">
            <h1>{{ app()->getLocale() == 'ar' ? 'سند صرف مصروفات' : 'Expense Voucher' }}</h1>
            <p>{{ isset($setting) ? $setting->getTranslation('company_name') : 'POS System' }}</p>
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <strong>{{ app()->getLocale() == 'ar' ? 'رقم السند' : 'Voucher No.' }}</strong>
                <span>{{ $expense->expense_number }}</span>
            </div>
            <div class="detail-item">
                <strong>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</strong>
                <span>{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</span>
            </div>
            <div class="detail-item">
                <strong>{{ __('pos.amount') }}</strong>
                <span>{{ number_format($expense->amount, 2) }} {{ $setting->currency ?? 'SAR' }}</span>
            </div>
            <div class="detail-item">
                <strong>{{ app()->getLocale() == 'ar' ? 'طريقة الدفع' : 'Payment Method' }}</strong>
                @php
                    $paymentMethod = $expense->payment_method;
                    if (app()->getLocale() == 'ar') {
                        $translations = [
                            'cash' => 'نقداً',
                            'bank' => 'بنك',
                            'card' => 'بطاقة',
                        ];
                        $paymentMethod = $translations[strtolower($paymentMethod)] ?? $paymentMethod;
                    }
                @endphp
                <span>{{ $paymentMethod }}</span>
            </div>
            <div class="detail-item">
                <strong>{{ __('pos.expense_type') }}</strong>
                @php
                    $typeObj = \App\Models\ExpenseType::where('name_en', $expense->type)->orWhere('name_ar', $expense->type)->first();
                    $typeName = $typeObj ? (app()->getLocale() == 'ar' ? $typeObj->name_ar : $typeObj->name_en) : $expense->type;
                @endphp
                <span>{{ $typeName }}</span>
            </div>
            <div class="detail-item">
                <strong>{{ __('pos.status') }}</strong>
                @php
                    $status = $expense->status;
                    if (app()->getLocale() == 'ar') {
                        $statusTranslations = [
                            'draft' => 'مسودة',
                            'approved' => 'معتمد',
                            'cancelled' => 'ملغي',
                            'paid' => 'مدفوع',
                            'pending' => 'قيد الانتظار',
                        ];
                        $status = $statusTranslations[strtolower($status)] ?? $status;
                    }
                @endphp
                <span>{{ $status }}</span>
            </div>
        </div>

        <div class="description-box">
            <strong>{{ __('pos.description') }}</strong>
            <p style="margin:0; font-size: 15px; line-height: 1.6;">
                {{ app()->getLocale() == 'ar' ? $expense->description_ar : $expense->description_en }}
            </p>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>{{ app()->getLocale() == 'ar' ? 'توقيع المستلم' : 'Receiver Signature' }}</strong>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>{{ app()->getLocale() == 'ar' ? 'توقيع المحاسب / الإدارة' : 'Accountant / Management Signature' }}</strong><br>
                <small>{{ $expense->user->full_name ?? '' }}</small>
            </div>
        </div>
    </div>
</body>
</html>
