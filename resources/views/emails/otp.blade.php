<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود التحقق</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1E88E5;
            text-decoration: none;
        }
        .otp-box {
            background-color: #f1f8ff;
            border: 2px dashed #1E88E5;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 15px;
            margin: 20px 0;
            border-radius: 8px;
            color: #0d47a1;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
        .warning {
            color: #d32f2f;
            font-size: 13px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">POS System</div>
        </div>
        <h2>مرحباً، {{ $userName }}</h2>
        <p>نحن بصدد معالجة طلب استعادة كلمة المرور الخاص بك. كود التحقق الخاص بك هو:</p>
        <div class="otp-box">
            {{ $otp }}
        </div>
        <p>يرجى إدخال هذا الكود في صفحة التحقق لإكمال العملية.</p>
        <div class="warning">
            * تنبيه: هذا الكود صالح لمدة 15 دقيقة فقط. إذا لم تطلب استعادة كلمة المرور، يرجى تجاهل هذا الإيميل.
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} POS System. جميع الحقوق محفوظة.
        </div>
    </div>
</body>
</html>
