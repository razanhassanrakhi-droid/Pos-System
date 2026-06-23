<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use App\Models\Branch;
use App\Models\Setting;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useBootstrapFive();
        // Pre-flight DB check to avoid 30s hangs on Windows
        $dbConnected = true;
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3307');
        
        $connection = @fsockopen($host, $port, $errno, $errstr, 0.2); // 200ms timeout
        if (!$connection) {
            $dbConnected = false;
        } else {
            fclose($connection);
        }

        config(['database.db_is_up' => $dbConnected]);

        // Fetch settings and branches once per request lifecycle to prevent N+1 queries during view rendering
        $branches = null;
        $setting = null;

        // Use view composer to share data only when views are rendered
        View::composer('*', function ($view) use ($dbConnected, &$branches, &$setting) {
            if (!$dbConnected) {
                $view->with('branches', collect([]));
                return;
            }
            
            try {
                if ($branches === null) {
                    $branches = Branch::all();
                }
                if ($setting === null) {
                    $setting = Setting::first();
                }
                $view->with('branches', $branches);
                $view->with('setting', $setting);
            } catch (\Throwable $e) {
                $view->with('branches', collect([]));
                $view->with('setting', null);
            }
        });

        // Implicitly grant "admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user()->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->isAdmin() ? true : null;
        });
    }
}