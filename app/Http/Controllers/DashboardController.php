<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Batch;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = session('branch_id');
        
        // Base query for products strictly belonging to the current branch
        $productsQuery = Product::when($branchId, function($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })->where('is_active', true);
        
        $products = $productsQuery->get()->map(function($product) use ($branchId) {
            $product->current_stock = $branchId ? $product->currentBranchStock($branchId) : $product->totalStock();
            return $product;
        });

        // Summary Statistics
        $totalProducts = $products->count();
        $lowStockProducts = $products->filter(function($p) {
            return $p->stock_status !== 'Sufficient';
        });
        
        $expiringSoonProducts = $products->filter(function($p) {
            return $p->expiration_status !== 'Valid';
        });

        // Today Sales Count
        $todaySalesCount = Sale::when($branchId, function($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })
        ->whereDate('created_at', Carbon::today())
        ->count();

        // Expiring Soon Batches (expiring within 90 days, remaining stock > 0)
        $expiringSoonBatches = Batch::with('product')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(90))
            ->orderBy('expiry_date', 'asc')
            ->get();

        // Calculate stats for Low Stock products
        $lowestQuantity = $lowStockProducts->min('current_stock') ?? 0;
        $totalLowStockQuantity = $lowStockProducts->sum('current_stock') ?? 0;

        return view('dashboard', [
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockProducts->count(),
            'expiringSoonCount' => $expiringSoonProducts->count(),
            'lowStockProducts' => $lowStockProducts,
            'lowestQuantity' => $lowestQuantity,
            'totalLowStockQuantity' => $totalLowStockQuantity,
            'expiringSoonProducts' => $expiringSoonProducts->take(5),
            'expiringSoonBatches' => $expiringSoonBatches,
            'allProducts' => $products, // For the main display table
            'todaySalesCount' => $todaySalesCount,
        ]);
    }
}

