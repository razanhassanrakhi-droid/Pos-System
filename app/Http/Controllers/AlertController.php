<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Batch;
use Carbon\Carbon;

class AlertController extends Controller
{
    /**
     * Fetch realtime alerts for the current logged-in user.
     */
    public function getRealtimeAlerts(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['alerts' => []]);
        }

        $branchId = session('branch_id'); // Or use your branch logic
        $alerts = [];

        // Determine branch filter for products (if they use standard inventory logic)
        // If admin and branch_id is null/all, check all. Else, check specific branch.
        $isAdmin = $user->isAdmin();
        
        // 1. Out of Stock (Critical)
        // Adjust this query based on how your system determines "current_stock"
        $products = Product::with(['batches' => function($q) use ($branchId, $isAdmin) {
            if (!$isAdmin || $branchId) {
                $q->where('branch_id', $branchId);
            }
        }])->get();

        foreach ($products as $p) {
            $stock = (!$isAdmin || $branchId) ? $p->currentBranchStock($branchId) : $p->totalStock();
            
            if ($stock <= 0) {
                $alerts[] = [
                    'id' => 'oos_' . $p->id,
                    'title' => 'Out of Stock',
                    'message' => $p->name . ' is out of stock.',
                    'type' => 'danger',   // Critical
                    'sound' => 'alert'
                ];
            } elseif ($stock <= $p->minimum_stock) {
                $alerts[] = [
                    'id' => 'low_' . $p->id,
                    'title' => 'Low Stock',
                    'message' => $p->name . ' is running low (' . $stock . ' left).',
                    'type' => 'warning',  // Medium
                    'sound' => 'warning'
                ];
            }
        }

        // 2. Expired & Expiring Soon
        $now = Carbon::now();
        $soon = Carbon::now()->addDays(7);
        
        $batches = Batch::where('quantity', '>', 0)
            ->when((!$isAdmin || $branchId), function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with('product')
            ->get();

        foreach ($batches as $batch) {
            if (!$batch->expiry_date) continue;
            
            if ($batch->expiry_date < $now) {
                $alerts[] = [
                    'id' => 'exp_' . $batch->id,
                    'title' => 'Expired Product',
                    'message' => ($batch->product->name ?? 'Product') . ' has expired!',
                    'type' => 'danger',   // Critical
                    'sound' => 'alert'
                ];
            } elseif ($batch->expiry_date <= $soon) {
                $alerts[] = [
                    'id' => 'exp_soon_' . $batch->id,
                    'title' => 'Expiring Soon',
                    'message' => ($batch->product->name ?? 'Product') . ' expires in ' . max(0, $now->diffInDays($batch->expiry_date)) . ' days.',
                    'type' => 'warning',  // Medium
                    'sound' => 'warning'
                ];
            }
        }

        return response()->json([
            'alerts' => $alerts
        ]);
    }
}
