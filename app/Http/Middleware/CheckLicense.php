<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for specific routes to avoid infinite redirection
        $whitelisted = [
            'settings.license',
            'settings.license.activate',
            'settings.license.request',
            'login',
            'login.post',
            'logout',
            'register',
            'register.post',
            'password.request',
            'password.verify.user',
            'password.otp.verify',
            'password.otp.process',
            'password.reset.get',
            'password.reset.update',
            'language.switch',
        ];

        if (in_array($request->route()->getName(), $whitelisted)) {
            return $next($request);
        }

        // Exempt Admins from the license lock so they can always manage settings and licenses
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        $setting = \App\Models\Setting::first();
        if (!$setting || !$setting->isLicenseValid()) {
            return redirect()->route('settings.license')->with('error', __('pos.license_expired_key'));
        }

        return $next($request);
    }
}
