<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\OtpCode;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    // عرض صفحة اللوقن
    public function showLogin()
    {
        return view('auth.login');
    }

    // عرض صفحة التسجيل
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // معالجة التسجيل
    public function register(Request $request)
    {
        $request->validate([
            'full_name_ar' => 'required|string|max:150',
            'full_name_en' => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:150|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'full_name' => [
                'ar' => $request->full_name_ar,
                'en' => $request->full_name_en,
            ],
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password, // setPasswordAttribute handles hashing
            'role' => 'employee',
            'is_active' => true,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', __('massage.user_created_successfully'));
    }

    // معالجة اللوقن
public function login(Request $request)
{
    // Validate form input
    $request->validate([
        'login' => 'required|string',
        'password' => 'required|string',
    ]);

    // Check if database is reachable
    if (!config('database.db_is_up')) {
        return back()->with('error', __('pos.database_connection_failed') ?? 'خطأ في الاتصال بقاعدة البيانات. يرجى التأكد من تشغيل الخادم.');
    }

    // جلب بيانات المستخدم بناء على username أو email
    $login = $request->input('login'); // حقل login في الفورم

    $user = User::where('username', $login)
                ->orWhere('email', $login)
                ->first();

    // تحقق من كلمة المرور
    if ($user && Hash::check($request->password, $user->password)) {
        Auth::login($user); // تسجيل دخول المستخدم
        $request->session()->regenerate();

        // Dispatch login notification
        \App\Services\NotificationService::send(
            'Security',
            'login_success',
            'Activity',
            'تسجيل دخول ناجح',
            'Successful Login',
            'قام المستخدم "' . $user->full_name . '" بتسجيل الدخول إلى النظام.',
            'User "' . $user->full_name . '" logged in successfully.',
            User::class,
            $user->id,
            null,
            $user->id
        );

        // توجه للداشبورد
        return redirect()->route('dashboard');
    }

    // إذا فشلت عملية تسجيل الدخول
    return back()->with('error', 'بيانات الدخول غير صحيحة');
}
    public function logout()
    {
        $locale = request()->session()->get('locale'); // Preserve locale before clearing
        
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if ($locale) {
            request()->session()->put('locale', $locale); // Restore locale
        }

        return redirect()->route('login');
    }

    // Password Recovery - Step 1: Show Forgot Form
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Password Recovery - Step 2: Verify User & Send OTP
    public function verifyUser(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        // Check if database is reachable
        if (!config('database.db_is_up')) {
            return back()->with('error', __('pos.database_connection_failed') ?? 'خطأ في الاتصال بقاعدة البيانات. يرجى التأكد من تشغيل الخادم.');
        }

        $user = User::where('username', $request->login)
                    ->orWhere('email', $request->login)
                    ->first();

        if (!$user) {
            return back()->with('error', __('pos.user_not_found') ?? 'User not found.');
        }

        if (!$user->email) {
            return back()->with('error', 'هذا الحساب لا يمتلك بريداً إلكترونياً مسجلاً لإرسال الكود.');
        }

        // Generate 6-digit OTP
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete any existing codes for this user
        OtpCode::where('user_id', $user->id)->delete();

        // Save new OTP
        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Send Email
        try {
            Mail::to($user->email)->send(new OtpMail($code, $user->getTranslation('full_name')));
        } catch (\Exception $e) {
            // If mail fails, we still keep the OTP in DB for manual check if needed, 
            // but return error to user.
            return back()->with('error', 'فشل في إرسال البريد الإلكتروني. يرجى المحاولة لاحقاً.');
        }

        return redirect()->route('password.otp.verify', ['user_id' => $user->id])
                        ->with('success', 'تم إرسال كود التحقق إلى بريدك الإلكتروني.');
    }

    // Password Recovery - Step 3: Show OTP Verification Form
    public function showVerifyOtp(Request $request)
    {
        $user_id = $request->query('user_id');
        if (!$user_id) return redirect()->route('password.request');

        return view('auth.verify-otp', compact('user_id'));
    }

    // Password Recovery - Step 4: Process OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|size:6',
        ]);

        $otp = OtpCode::where('user_id', $request->user_id)
                      ->where('code', $request->code)
                      ->first();

        if (!$otp || !$otp->isValid()) {
            return back()->with('error', 'كود التحقق غير صحيح أو انتهت صلاحيته.');
        }

        // Mark as used
        $otp->used_at = Carbon::now();
        $otp->save();

        // Store verification in session to allow access to reset page
        session(['otp_verified_user_id' => $request->user_id]);

        return redirect()->route('password.reset.get');
    }

    // Password Recovery - Step 5: Show Reset Form
    public function showResetForm()
    {
        $user_id = session('otp_verified_user_id');
        if (!$user_id) return redirect()->route('password.request');

        $user = User::find($user_id);
        return view('auth.reset-password', compact('user'));
    }

    // Password Recovery - Step 6: Update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user_id = session('otp_verified_user_id');
        if (!$user_id) return redirect()->route('password.request');

        $user = User::find($user_id);
        $user->password = $request->password;
        $user->save();

        // Clear session
        session()->forget('otp_verified_user_id');

        return redirect()->route('login')->with('success', __('pos.user_updated_successfully') ?? 'Password reset successfully. You can now log in.');
    }
    public function showProfile()
    {
        $user = Auth::user();
        $user->load('branches');
        return view('settings.profile', compact('user'));
    }

    public function showChangePassword()
    {
        return view('settings.password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', __('pos.incorrect_old_password'));
        }

        $user->password = $request->new_password;
        $user->save();

        return back()->with('success', __('pos.password_changed_successfully'));
    }

    public function changeLanguage($lang)
    {
        if (in_array($lang, ['ar', 'en'])) {
            session(['locale' => $lang]);
        }
        return back();
    }
}