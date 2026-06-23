<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // تحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // تحقق من أن المستخدم Admin أو لديه صلاحيات الإدارة
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasAnyPermission(['view-users', 'manage-permissions', 'view-branches', 'manage-settings'])) {
            abort(403, 'غير مسموح بالدخول');
        }

        return $next($request);
    }
}