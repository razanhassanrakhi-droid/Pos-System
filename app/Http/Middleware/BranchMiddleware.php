<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class BranchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Illuminate\Support\Facades\Log::info('BranchMiddleware started');

        // Check pre-flight status to avoid hangs
        if (!config('database.db_is_up')) {
            \Illuminate\Support\Facades\Log::info('BranchMiddleware: Skipping DB logic as pre-flight check failed');
            return $next($request);
        }

        try {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Get all branches for admin, or assigned branches for employees
                $userBranches = $user->accessibleBranches();

                if ($userBranches->isNotEmpty()) {
                    if (!session()->exists('branch_id')) {
                        session(['branch_id' => $userBranches->first()->id]);
                    }

                    $currentBranchId = session('branch_id');
                    
                    // Special case for "All Branches" view (branch_id is null in session)
                    if (is_null($currentBranchId) && $user->isAdmin()) {
                        $currentBranch = new class {
                            public $id = null;
                            public function getTranslation($field) {
                                return __('pos.all_branches') ?? 'All Branches';
                            }
                        };
                    } else {
                        $currentBranch = $userBranches->where('id', $currentBranchId)->first();
                        
                        // If the session branch is not in user's accessible branches, reset to first
                        if (!$currentBranch) {
                            $currentBranch = $userBranches->first();
                            session(['branch_id' => $currentBranch->id]);
                        }
                    }

                    View::share('current_branch', $currentBranch);
                    View::share('user_branches', $userBranches);
                }
            }
        } catch (\Throwable $e) {
            // Log the error or ignore if DB is down
            \Illuminate\Support\Facades\Log::warning('Database connection failed in BranchMiddleware: ' . $e->getMessage());
        }

        return $next($request);
    }
}
