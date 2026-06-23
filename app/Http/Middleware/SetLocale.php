<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        \Illuminate\Support\Facades\Log::info('SetLocale middleware started');
        if (session()->has('locale')) {
            app()->setLocale(session('locale')); // صح
        }

        \Illuminate\Support\Facades\Log::info('SetLocale middleware finished');
        return $next($request);
    }
}