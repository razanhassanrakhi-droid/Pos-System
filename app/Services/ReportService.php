<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\InventoryAdjustment;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get Comprehensive Sales Report
     */
    public function getSalesReport($filters)
    {
        if (isset($filters['branch_id']) && ($filters['branch_id'] === 'all' || $filters['branch_id'] === '')) {
            $filters['branch_id'] = null;
        }
        $query = Sale::query();
        $this->applyFilters($query, $filters);

        $stats = (clone $query)->selectRaw('
            SUM(total) as total_sales,
            SUM(tax) as total_tax,
            SUM(discount) as total_discount,
            COUNT(id) as invoice_count,
            AVG(total) as average_invoice,
            SUM(paid_amount) as total_paid,
            SUM(total - paid_amount) as total_remaining
        ')->first();

        // Calculate Total Profit based on batch cost (cost of batches, not current product price)
        $salesIdsQuery = (clone $query)->select('id');
        $profitStats = DB::table('sale_items')
            ->leftJoin('batches', 'sale_items.batch_id', '=', 'batches.id')
            ->whereIn('sale_items.sale_id', $salesIdsQuery)
            ->selectRaw('SUM(sale_items.total - (sale_items.quantity * COALESCE(batches.purchase_price, 0))) as total_profit')
            ->first();
        
        $totalProfit = $profitStats->total_profit ?? 0;

        $invoices = (clone $query)->with(['customer', 'branch', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', 'products.image', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.total) as total_revenue'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('sales.branch_id', $filters['branch_id']))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        // Top Profitable and Least Profitable Products
        $productProfits = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('batches', 'sale_items.batch_id', '=', 'batches.id')
            ->select(
                'products.id',
                'products.name',
                'products.image',
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('SUM(sale_items.total - (sale_items.quantity * COALESCE(batches.purchase_price, 0))) as total_profit')
            )
            ->when(isset($filters['branch_id']), fn($q) => $q->where('sales.branch_id', $filters['branch_id']))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('products.id', 'products.name', 'products.image')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                $item->profit_margin = $item->total_revenue > 0 ? round(($item->total_profit / $item->total_revenue) * 100, 2) : 0;
                return $item;
            });

        $productCount = $productProfits->count();
        $takeLimit = $productCount < 10 ? (int)max(1, floor($productCount / 2)) : 5;
        $topProfitableProducts = $productProfits->sortByDesc('total_profit')->take($takeLimit)->values();
        $leastProfitableProducts = $productProfits->sortBy('total_profit')->take($takeLimit)->values();

        $productMovements = DB::table('products')
            ->when(isset($filters['branch_id']) && $filters['branch_id'] !== 'all' && $filters['branch_id'] !== '', fn($q) => $q->where('products.branch_id', $filters['branch_id']))
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('sales', function($join) use ($filters) {
                $join->on('sale_items.sale_id', '=', 'sales.id')
                     ->where('sales.status', '!=', 'cancelled')
                     ->when(isset($filters['branch_id']) && $filters['branch_id'] !== 'all' && $filters['branch_id'] !== '', fn($q) => $q->where('sales.branch_id', $filters['branch_id']))
                     ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59']);
            })
            ->select(
                'products.id',
                'products.name',
                'products.image',
                DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_quantity')
            )
            ->groupBy('products.id', 'products.name', 'products.image')
            ->get()
            ->map(function($item) use ($filters) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                $p = Product::find($item->id);
                $item->current_stock = $p ? ($filters['branch_id'] ? $p->currentBranchStock($filters['branch_id']) : $p->totalStock()) : 0;
                if ($item->total_quantity >= 15) {
                    $item->movement_status = app()->getLocale() == 'ar' ? 'سريع الحركة' : 'Fast Moving';
                    $item->movement_class = 'success';
                } else {
                    $item->movement_status = app()->getLocale() == 'ar' ? 'بطيء الحركة' : 'Slow Moving';
                    $item->movement_class = 'warning';
                }
                return $item;
            });

        $fastMovingProducts = $productMovements->sortByDesc('total_quantity')->take(5)->values()->map(function($item) {
            $item->movement_status = app()->getLocale() == 'ar' ? 'سريع الحركة' : 'Fast Moving';
            $item->movement_class = 'success';
            return $item;
        });
        $slowMovingProducts = $productMovements->sortBy('total_quantity')->take(5)->values()->map(function($item) {
            $item->movement_status = app()->getLocale() == 'ar' ? 'بطيء الحركة' : 'Slow Moving';
            $item->movement_class = 'warning';
            return $item;
        });

        // Sales Distribution by Category
        $salesByCategory = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(sale_items.total) as total'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('sales.branch_id', $filters['branch_id']))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        // Sales Distribution by Payment Method
        $salesByPaymentMethod = DB::table('sales')
            ->select('payment_method', DB::raw('SUM(total) as total'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->whereBetween('created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('payment_method')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $item->payment_method_label = __('pos.' . $item->payment_method) ?? ucfirst($item->payment_method);
                return $item;
            });

        // Sales Distribution by Branch
        $salesByBranch = DB::table('sales')
            ->join('branches', 'sales.branch_id', '=', 'branches.id')
            ->select('branches.name', DB::raw('SUM(sales.total) as total'))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        // Customer Analytics (Advanced)
        $customerAnalytics = DB::table('sales')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                DB::raw("COALESCE(customers.name, 'Walk-in Customer') as customer_name"),
                DB::raw('COUNT(sales.id) as invoices_count'),
                DB::raw('SUM(sales.total) as total_spent'),
                DB::raw('AVG(sales.total) as average_order_value')
            )
            ->when(isset($filters['branch_id']), fn($q) => $q->where('sales.branch_id', $filters['branch_id']))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('sales.customer_id', 'customers.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                if ($item->customer_name === 'Walk-in Customer') {
                    $item->customer_name = __('pos.walk_in_customer');
                } else {
                    $decoded = json_decode($item->customer_name, true);
                    if (is_array($decoded)) {
                        $item->customer_name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                }
                return $item;
            });

        $topCustomers = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('SUM(sales.total) as total_spent'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('sales.branch_id', $filters['branch_id']))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('customers.id', 'customers.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });
            
        // Group by day for charts
        $salesByDay = $query->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        return [
            'total_sales' => $stats->total_sales ?? 0,
            'total_tax' => $stats->total_tax ?? 0,
            'total_discount' => $stats->total_discount ?? 0,
            'invoice_count' => $stats->invoice_count ?? 0,
            'average_invoice' => $stats->average_invoice ?? 0,
            'total_paid' => $stats->total_paid ?? 0,
            'total_remaining' => $stats->total_remaining ?? 0,
            'total_profit' => $totalProfit,
            'invoices' => $invoices,
            'top_products' => $topProducts,
            'top_profitable_products' => $topProfitableProducts,
            'least_profitable_products' => $leastProfitableProducts,
            'fast_moving_products' => $fastMovingProducts,
            'slow_moving_products' => $slowMovingProducts,
            'sales_by_category' => $salesByCategory,
            'sales_by_payment_method' => $salesByPaymentMethod,
            'sales_by_branch' => $salesByBranch,
            'customer_analytics' => $customerAnalytics,
            'top_customers' => $topCustomers,
            'sales_by_day' => $salesByDay,
        ];
    }

    /**
     * Get dynamic Top Analytics Data for Limit select / Detailed Report
     */
    public function getTopAnalyticsData($type, $filters, $limit = 5)
    {
        $branchId = isset($filters['branch_id']) && $filters['branch_id'] !== '' ? $filters['branch_id'] : null;

        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $filters['to_date'] ?? now()->format('Y-m-d');

        switch ($type) {
            case 'top-selling':
                $query = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->join('products', 'sale_items.product_id', '=', 'products.id')
                    ->select('products.id', 'products.name', 'products.image', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.total) as total_revenue'))
                    ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
                    ->whereBetween('sales.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->groupBy('products.id', 'products.name', 'products.image')
                    ->orderBy('total_quantity', 'desc');
                if ($limit !== 'all') {
                    $query->limit((int)$limit);
                }
                return $query->get()->map(function($item) {
                    $decoded = json_decode($item->name, true);
                    if (is_array($decoded)) {
                        $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    return $item;
                });

            case 'top-profitable':
            case 'least-profitable':
                $query = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->join('products', 'sale_items.product_id', '=', 'products.id')
                    ->leftJoin('batches', 'sale_items.batch_id', '=', 'batches.id')
                    ->select(
                        'products.id',
                        'products.name',
                        'products.image',
                        DB::raw('SUM(sale_items.total) as total_revenue'),
                        DB::raw('SUM(sale_items.total - (sale_items.quantity * COALESCE(batches.purchase_price, 0))) as total_profit')
                    )
                    ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
                    ->whereBetween('sales.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->groupBy('products.id', 'products.name', 'products.image');
                
                if ($type === 'top-profitable') {
                    $query->orderBy('total_profit', 'desc');
                } else {
                    $query->orderBy('total_profit', 'asc');
                }
                if ($limit !== 'all') {
                    $query->limit((int)$limit);
                }
                return $query->get()->map(function($item) {
                    $decoded = json_decode($item->name, true);
                    if (is_array($decoded)) {
                        $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    $item->profit_margin = $item->total_revenue > 0 ? round(($item->total_profit / $item->total_revenue) * 100, 2) : 0;
                    return $item;
                });

            case 'fast-moving':
            case 'slow-moving':
                $query = DB::table('products')
                    ->when($branchId, fn($q) => $q->where('products.branch_id', $branchId))
                    ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
                    ->leftJoin('sales', function($join) use ($branchId, $fromDate, $toDate) {
                        $join->on('sale_items.sale_id', '=', 'sales.id')
                             ->where('sales.status', '!=', 'cancelled')
                             ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
                             ->whereBetween('sales.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
                    })
                    ->select(
                        'products.id',
                        'products.name',
                        'products.image',
                        DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_quantity')
                    )
                    ->groupBy('products.id', 'products.name', 'products.image');

                if ($type === 'fast-moving') {
                    $query->orderBy('total_quantity', 'desc');
                } else {
                    $query->orderBy('total_quantity', 'asc');
                }
                if ($limit !== 'all') {
                    $query->limit((int)$limit);
                }
                return $query->get()->map(function($item) use ($branchId, $type) {
                    $decoded = json_decode($item->name, true);
                    if (is_array($decoded)) {
                        $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    $p = Product::find($item->id);
                    $item->current_stock = $p ? ($branchId ? $p->currentBranchStock($branchId) : $p->totalStock()) : 0;
                    if ($type === 'fast-moving') {
                        $item->movement_status = app()->getLocale() == 'ar' ? 'سريع الحركة' : 'Fast Moving';
                    } else {
                        $item->movement_status = app()->getLocale() == 'ar' ? 'بطيء الحركة' : 'Slow Moving';
                    }
                    return $item;
                });

            case 'top-customers':
                $query = DB::table('sales')
                    ->join('customers', 'sales.customer_id', '=', 'customers.id')
                    ->select('customers.id', 'customers.name', DB::raw('SUM(sales.total) as total_spent'), DB::raw('COUNT(sales.id) as invoices_count'), DB::raw('AVG(sales.total) as average_order_value'))
                    ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId)->where('customers.branch_id', $branchId))
                    ->whereBetween('sales.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->groupBy('customers.id', 'customers.name')
                    ->orderBy('total_spent', 'desc');
                if ($limit !== 'all') {
                    $query->limit((int)$limit);
                }
            case 'suppliers':
                $query = DB::table('purchases')
                    ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                    ->select(
                        'suppliers.id',
                        'suppliers.name',
                        DB::raw('COUNT(purchases.id) as orders_count'),
                        DB::raw('SUM(purchases.total_amount) as total_amount')
                    )
                    ->when($branchId, fn($q) => $q->where('purchases.branch_id', $branchId)->where('suppliers.branch_id', $branchId))
                    ->whereBetween('purchases.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->groupBy('suppliers.id', 'suppliers.name')
                    ->orderBy('total_amount', 'desc');
                if ($limit !== 'all') {
                    $query->limit((int)$limit);
                }
                $totalPurchases = DB::table('purchases')
                    ->when($branchId, fn($q) => $q->where('purchases.branch_id', $branchId))
                    ->whereBetween('purchases.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->sum('total_amount') ?: 1;
                return $query->get()->map(function($item) use ($totalPurchases) {
                    $decoded = json_decode($item->name, true);
                    if (is_array($decoded)) {
                        $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    $item->percentage = round(($item->total_amount / $totalPurchases) * 100, 2);
                    return $item;
                });

            case 'purchase-products':
                $query = DB::table('purchase_items')
                    ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                    ->join('products', 'purchase_items.product_id', '=', 'products.id')
                    ->select(
                        'products.id',
                        'products.name',
                        'products.image',
                        DB::raw('SUM(purchase_items.quantity) as total_quantity'),
                        DB::raw('SUM(purchase_items.total) as total_amount')
                    )
                    ->when($branchId, fn($q) => $q->where('purchases.branch_id', $branchId))
                    ->whereBetween('purchases.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->groupBy('products.id', 'products.name', 'products.image')
                    ->orderBy('total_quantity', 'desc');
                if ($limit !== 'all') {
                    $query->limit((int)$limit);
                }
                return $query->get()->map(function($item) {
                    $decoded = json_decode($item->name, true);
                    if (is_array($decoded)) {
                        $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    return $item;
                });

            case 'low-stock':
                $products = Product::when($branchId, fn($q) => $q->where('branch_id', $branchId))->get()->map(function($p) use ($branchId) {
                    $p->current_stock = $branchId ? $p->currentBranchStock($branchId) : $p->totalStock();
                    $decoded = json_decode($p->name, true);
                    if (is_array($decoded)) {
                        $p->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    return $p;
                })->filter(fn($p) => $p->current_stock > 0 && $p->current_stock <= $p->minimum_stock)->sortBy('current_stock');
                return $limit === 'all' ? $products : $products->take($limit);

            case 'out-of-stock':
                $products = Product::when($branchId, fn($q) => $q->where('branch_id', $branchId))->get()->map(function($p) use ($branchId) {
                    $p->current_stock = $branchId ? $p->currentBranchStock($branchId) : $p->totalStock();
                    $decoded = json_decode($p->name, true);
                    if (is_array($decoded)) {
                        $p->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    return $p;
                })->filter(fn($p) => $p->current_stock <= 0)->sortBy('name');
                return $limit === 'all' ? $products : $products->take($limit);

            case 'overstock':
                $products = Product::when($branchId, fn($q) => $q->where('branch_id', $branchId))->get()->map(function($p) use ($branchId) {
                    $p->current_stock = $branchId ? $p->currentBranchStock($branchId) : $p->totalStock();
                    $p->recommended_stock = $p->minimum_stock * 2;
                    $decoded = json_decode($p->name, true);
                    if (is_array($decoded)) {
                        $p->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    return $p;
                })->filter(fn($p) => $p->minimum_stock > 0 && $p->current_stock > $p->minimum_stock * 3)->sortByDesc('current_stock');
                return $limit === 'all' ? $products : $products->take($limit);

            case 'healthy-stock':
                $products = Product::when($branchId, fn($q) => $q->where('branch_id', $branchId))->get()->map(function($p) use ($branchId) {
                    $p->current_stock = $branchId ? $p->currentBranchStock($branchId) : $p->totalStock();
                    $decoded = json_decode($p->name, true);
                    if (is_array($decoded)) {
                        $p->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    return $p;
                })->filter(fn($p) => $p->current_stock > $p->minimum_stock && ($p->minimum_stock == 0 || $p->current_stock <= $p->minimum_stock * 3))->sortByDesc('current_stock');
                return $limit === 'all' ? $products : $products->take($limit);

            case 'expiring-soon':
                $query = Batch::where('expiry_date', '>=', now())
                    ->where('expiry_date', '<=', now()->addDays(30))
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->whereHas('product', function($q) use ($branchId) {
                        if ($branchId) $q->where('branch_id', $branchId);
                    })
                    ->where('quantity', '>', 0)
                    ->with('product')
                    ->orderBy('expiry_date', 'asc');
                if ($limit !== 'all') $query->limit($limit);
                return $query->get()->map(function($b) {
                    if ($b->product) {
                        $decoded = json_decode($b->product->name, true);
                        if (is_array($decoded)) {
                            $b->product->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                        }
                    }
                    $b->days_remaining = max(0, now()->diffInDays(\Carbon\Carbon::parse($b->expiry_date), false));
                    return $b;
                });

            case 'expired':
                $query = Batch::where('expiry_date', '<', now())
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->whereHas('product', function($q) use ($branchId) {
                        if ($branchId) $q->where('branch_id', $branchId);
                    })
                    ->where('quantity', '>', 0)
                    ->with('product')
                    ->orderBy('expiry_date', 'asc');
                if ($limit !== 'all') $query->limit($limit);
                return $query->get()->map(function($b) {
                    if ($b->product) {
                        $decoded = json_decode($b->product->name, true);
                        if (is_array($decoded)) {
                            $b->product->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                        }
                    }
                    return $b;
                });

            case 'inventory-batches':
                $query = DB::table('batches')
                    ->join('products', 'batches.product_id', '=', 'products.id')
                    ->leftJoin('purchase_items', 'batches.purchase_item_id', '=', 'purchase_items.id')
                    ->leftJoin('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                    ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                    ->select(
                        'batches.batch_number',
                        'products.name as product_name',
                        'products.image as product_image',
                        'suppliers.name as supplier_name',
                        'purchases.created_at as purchase_date',
                        'batches.expiry_date',
                        'batches.purchased_quantity',
                        'batches.quantity as remaining_quantity',
                        'batches.purchase_price',
                        'batches.status'
                    )
                    ->when($branchId, fn($q) => $q->where('batches.branch_id', $branchId))
                    ->whereExists(function($q) use ($branchId) {
                        $q->select(DB::raw(1))
                          ->from('products')
                          ->whereColumn('products.id', '=', 'batches.product_id')
                          ->when($branchId, fn($inner) => $inner->where('products.branch_id', $branchId));
                    })
                    ->whereBetween('batches.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->orderBy('batches.created_at', 'desc');
                if ($limit !== 'all') $query->limit($limit);
                return $query->get()->map(function($item) {
                    $decoded = json_decode($item->product_name, true);
                    if (is_array($decoded)) {
                        $item->product_name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                    if ($item->supplier_name) {
                        $decodedSup = json_decode($item->supplier_name, true);
                        if (is_array($decodedSup)) {
                            $item->supplier_name = $decodedSup[app()->getLocale()] ?? $decodedSup['en'] ?? $decodedSup['ar'] ?? array_shift($decodedSup);
                        }
                    } else {
                        $item->supplier_name = '-';
                    }
                    $expiry = $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date) : null;
                    if ($item->remaining_quantity <= 0) {
                        $item->computed_status = 'completed';
                    } elseif ($expiry && $expiry->isPast()) {
                        $item->computed_status = 'expired';
                    } elseif ($expiry && $expiry->diffInDays(now()) < 30) {
                        $item->computed_status = 'nearly_expired';
                    } else {
                        $item->computed_status = 'active';
                    }
                    return $item;
                });

            case 'inventory-transactions':
                $query = StockMovement::with(['product', 'batch', 'creator'])
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->whereHas('product', function($q) use ($branchId) {
                        if ($branchId) $q->where('branch_id', $branchId);
                    })
                    ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->orderBy('created_at', 'desc');
                if ($limit !== 'all') $query->limit($limit);
                return $query->get()->map(function($t) {
                    if ($t->product) {
                        $decoded = json_decode($t->product->name, true);
                        if (is_array($decoded)) {
                            $t->product->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                        }
                    }
                    return $t;
                });
        }
        return collect();
    }

    /**
     * Get Purchase Report
     */
    public function getPurchaseReport($filters)
    {
        if (isset($filters['branch_id']) && ($filters['branch_id'] === 'all' || $filters['branch_id'] === '')) {
            $filters['branch_id'] = null;
        }

        $query = Purchase::query();
        $this->applyFilters($query, $filters);

        $stats = (clone $query)->selectRaw('
            SUM(total_amount) as total_purchases,
            SUM(paid_amount) as total_paid,
            SUM(remaining_amount) as total_remaining,
            SUM(discount) as total_discount,
            SUM(tax_amount) as total_tax,
            COUNT(id) as invoice_count
        ')->first();

        $totalQty = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->sum('purchase_items.quantity') ?? 0;

        $purchasesByDay = (clone $query)->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $purchaseOrdersByDay = (clone $query)->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $totalPurchases = $stats->total_purchases ?? 0;

        $topSuppliers = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select(
                'suppliers.name',
                DB::raw('COUNT(purchases.id) as orders_count'),
                DB::raw('SUM(purchases.total_amount) as total_amount')
            )
            ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) use ($totalPurchases) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                $item->percentage = $totalPurchases > 0 ? round(($item->total_amount / $totalPurchases) * 100, 2) : 0;
                return $item;
            });

        $purchasesBySupplier = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select('suppliers.name', DB::raw('SUM(purchases.total_amount) as total'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        $supplierTrends = [];
        $top3SupplierIds = DB::table('purchases')
            ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('supplier_id')
            ->orderBy(DB::raw('SUM(total_amount)'), 'desc')
            ->limit(3)
            ->pluck('supplier_id');

        foreach ($top3SupplierIds as $supId) {
            $supplierName = DB::table('suppliers')->where('id', $supId)->value('name');
            $decoded = json_decode($supplierName, true);
            if (is_array($decoded)) {
                $supplierName = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
            }
            
            $trends = DB::table('purchases')
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->where('supplier_id', $supId)
                ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
                ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date');
                
            $supplierTrends[] = [
                'name' => $supplierName,
                'data' => $trends
            ];
        }

        $topPurchasedProducts = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.image',
                DB::raw('SUM(purchase_items.quantity) as total_quantity'),
                DB::raw('SUM(purchase_items.total) as total_amount')
            )
            ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        $mostExpensiveProducts = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('MAX(purchase_items.purchase_price) as max_cost'),
                DB::raw('AVG(purchase_items.purchase_price) as avg_cost'),
                DB::raw('(SELECT pi.purchase_price FROM purchase_items pi JOIN purchases p ON pi.purchase_id = p.id WHERE pi.product_id = products.id ORDER BY p.created_at DESC LIMIT 1) as latest_cost')
            )
            ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('products.id', 'products.name')
            ->orderBy('max_cost', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        $costTrends = [];
        $top3ProductIds = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('purchase_items.product_id')
            ->orderBy(DB::raw('SUM(purchase_items.quantity)'), 'desc')
            ->limit(3)
            ->pluck('purchase_items.product_id');

        foreach ($top3ProductIds as $prodId) {
            $prodName = DB::table('products')->where('id', $prodId)->value('name');
            $decoded = json_decode($prodName, true);
            if (is_array($decoded)) {
                $prodName = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
            }
            
            $costs = DB::table('purchase_items')
                ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->selectRaw('DATE(purchases.created_at) as date, AVG(purchase_items.purchase_price) as avg_price')
                ->where('purchase_items.product_id', $prodId)
                ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
                ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('avg_price', 'date');
                
            $costTrends[] = [
                'name' => $prodName,
                'data' => $costs
            ];
        }

        $purchasesByCategory = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(purchase_items.total) as total'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('purchases.branch_id', $filters['branch_id']))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        $purchasesByPaymentMethod = DB::table('purchases')
            ->select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->whereBetween('created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('payment_method')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $key = strtolower(str_replace(' ', '_', $item->payment_method));
                $translated = __("pos.{$key}");
                if ($translated === "pos.{$key}") {
                    $item->payment_method_label = $item->payment_method;
                } else {
                    $item->payment_method_label = $translated;
                }
                return $item;
            });

        $purchasesByBranch = DB::table('purchases')
            ->join('branches', 'purchases.branch_id', '=', 'branches.id')
            ->select('branches.name', DB::raw('SUM(purchases.total_amount) as total'))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        $batches = DB::table('batches')
            ->join('products', 'batches.product_id', '=', 'products.id')
            ->leftJoin('purchase_items', 'batches.purchase_item_id', '=', 'purchase_items.id')
            ->leftJoin('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select(
                'batches.batch_number',
                'products.name as product_name',
                'products.image as product_image',
                'suppliers.name as supplier_name',
                'purchases.created_at as purchase_date',
                'batches.expiry_date',
                'batches.purchased_quantity',
                'batches.quantity as remaining_quantity',
                'batches.purchase_price',
                'batches.status'
            )
            ->when(isset($filters['branch_id']), fn($q) => $q->where('batches.branch_id', $filters['branch_id']))
            ->whereBetween('batches.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->orderBy('batches.created_at', 'desc')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->product_name, true);
                if (is_array($decoded)) {
                    $item->product_name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                if ($item->supplier_name) {
                    $decodedSup = json_decode($item->supplier_name, true);
                    if (is_array($decodedSup)) {
                        $item->supplier_name = $decodedSup[app()->getLocale()] ?? $decodedSup['en'] ?? $decodedSup['ar'] ?? array_shift($decodedSup);
                    }
                } else {
                    $item->supplier_name = '-';
                }
                
                $expiry = $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date) : null;
                if ($item->remaining_quantity <= 0) {
                    $item->computed_status = 'completed';
                } elseif ($expiry && $expiry->isPast()) {
                    $item->computed_status = 'expired';
                } elseif ($expiry && $expiry->diffInDays(now()) < 30) {
                    $item->computed_status = 'nearly_expired';
                } else {
                    $item->computed_status = 'active';
                }
                return $item;
            });

        $purchases = $query->with('supplier')
            ->orderBy('created_at', 'desc')
            ->get(); // Load all purchases in filtered range for JS data-tables/history

        $branchId = $filters['branch_id'] ?? null;
        $totalInventoryValue = Batch::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('quantity', '>', 0)
            ->sum(DB::raw('quantity * purchase_price'));

        $totalInventoryQty = Batch::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('quantity', '>', 0)
            ->sum('quantity');

        return [
            'total_purchases' => $totalPurchases,
            'total_inventory_value' => $totalInventoryValue,
            'total_inventory_qty' => $totalInventoryQty,
            'total_paid' => $stats->total_paid ?? 0,
            'total_remaining' => $stats->total_remaining ?? 0,
            'total_discount' => $stats->total_discount ?? 0,
            'total_tax' => $stats->total_tax ?? 0,
            'invoice_count' => $stats->invoice_count ?? 0,
            'total_qty' => $totalQty,
            'purchases' => $purchases,
            'purchases_by_day' => $purchasesByDay,
            'purchase_orders_by_day' => $purchaseOrdersByDay,
            'top_suppliers' => $topSuppliers,
            'purchases_by_supplier' => $purchasesBySupplier,
            'supplier_trends' => $supplierTrends,
            'top_purchased_products' => $topPurchasedProducts,
            'most_expensive_products' => $mostExpensiveProducts,
            'cost_trends' => $costTrends,
            'purchases_by_category' => $purchasesByCategory,
            'purchases_by_payment_method' => $purchasesByPaymentMethod,
            'purchases_by_branch' => $purchasesByBranch,
            'batches' => $batches,
        ];
    }

    /**
     * Get Inventory Report
     */
    public function getInventoryReport($filters)
    {
        $branchId = isset($filters['branch_id']) && $filters['branch_id'] !== '' ? $filters['branch_id'] : null;
        // Current Stock Table
        $products = Product::with(['batches' => function($q) use ($branchId) {
            if ($branchId) $q->where('branch_id', $branchId);
        }])
        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
        ->get()->map(function($p) use ($branchId) {
            $p->current_stock = $branchId ? $p->currentBranchStock($branchId) : $p->totalStock();
            // Calculate stock value = SUM(batch.quantity × batch.purchase_price)
            $p->stock_value = $p->batches->sum(fn($b) => $b->quantity * $b->purchase_price);
            $decodedName = json_decode($p->name, true);
            if (is_array($decodedName)) {
                $p->name = $decodedName[app()->getLocale()] ?? $decodedName['en'] ?? $decodedName['ar'] ?? array_shift($decodedName);
            }
            return $p;
        });

        // 1. Stock Status Analysis Categories
        $lowStock = $products->filter(fn($p) => $p->current_stock > 0 && $p->current_stock <= $p->minimum_stock);
        $outOfStock = $products->filter(fn($p) => $p->current_stock <= 0);
        $overstock = $products->filter(fn($p) => $p->minimum_stock > 0 && $p->current_stock > $p->minimum_stock * 3)->map(function($p) {
            $p->recommended_stock = $p->minimum_stock * 2;
            return $p;
        });
        $healthyStock = $products->filter(fn($p) => $p->current_stock > $p->minimum_stock && ($p->minimum_stock == 0 || $p->current_stock <= $p->minimum_stock * 3));

        // 2. Expiry Analysis
        $expired = Batch::where('expiry_date', '<', now())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereHas('product', function($q) use ($branchId) {
                if ($branchId) $q->where('branch_id', $branchId);
            })
            ->where('quantity', '>', 0)
            ->with('product')
            ->get()->map(function($b) {
                if ($b->product) {
                    $decodedName = json_decode($b->product->name, true);
                    if (is_array($decodedName)) {
                        $b->product->name = $decodedName[app()->getLocale()] ?? $decodedName['en'] ?? $decodedName['ar'] ?? array_shift($decodedName);
                    }
                }
                return $b;
            });

        $expiringSoon = Batch::where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereHas('product', function($q) use ($branchId) {
                if ($branchId) $q->where('branch_id', $branchId);
            })
            ->where('quantity', '>', 0)
            ->with('product')
            ->get()->map(function($b) {
                if ($b->product) {
                    $decodedName = json_decode($b->product->name, true);
                    if (is_array($decodedName)) {
                        $b->product->name = $decodedName[app()->getLocale()] ?? $decodedName['en'] ?? $decodedName['ar'] ?? array_shift($decodedName);
                    }
                }
                $b->days_remaining = max(0, now()->diffInDays(\Carbon\Carbon::parse($b->expiry_date), false));
                return $b;
            });

        // 3. Product Movements (Fast / Slow)
        $movementData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select('sale_items.product_id', DB::raw('SUM(sale_items.quantity) as qty_sold'))
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('sale_items.product_id')
            ->get()
            ->keyBy('product_id');

        $productsEnriched = $products->map(function($p) use ($movementData) {
            $p->qty_sold = isset($movementData[$p->id]) ? (float)$movementData[$p->id]->qty_sold : 0;
            return $p;
        });

        $fastMoving = $productsEnriched->filter(fn($p) => $p->qty_sold > 0)->sortByDesc('qty_sold');
        $slowMoving = $productsEnriched->sortBy('qty_sold');

        // 4. Batch Analysis (latest 5 batches)
        $batches = DB::table('batches')
            ->join('products', 'batches.product_id', '=', 'products.id')
            ->leftJoin('purchase_items', 'batches.purchase_item_id', '=', 'purchase_items.id')
            ->leftJoin('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select(
                'batches.batch_number',
                'products.name as product_name',
                'products.image as product_image',
                'suppliers.name as supplier_name',
                'purchases.created_at as purchase_date',
                'batches.expiry_date',
                'batches.purchased_quantity',
                'batches.quantity as remaining_quantity',
                'batches.purchase_price',
                'batches.status'
            )
            ->when($branchId, fn($q) => $q->where('batches.branch_id', $branchId))
            ->whereBetween('batches.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->orderBy('batches.created_at', 'desc')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->product_name, true);
                if (is_array($decoded)) {
                    $item->product_name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                if ($item->supplier_name) {
                    $decodedSup = json_decode($item->supplier_name, true);
                    if (is_array($decodedSup)) {
                        $item->supplier_name = $decodedSup[app()->getLocale()] ?? $decodedSup['en'] ?? $decodedSup['ar'] ?? array_shift($decodedSup);
                    }
                } else {
                    $item->supplier_name = '-';
                }
                
                $expiry = $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date) : null;
                if ($item->remaining_quantity <= 0) {
                    $item->computed_status = 'completed';
                } elseif ($expiry && $expiry->isPast()) {
                    $item->computed_status = 'expired';
                } elseif ($expiry && $expiry->diffInDays(now()) < 30) {
                    $item->computed_status = 'nearly_expired';
                } else {
                    $item->computed_status = 'active';
                }
                return $item;
            });

        // 5. Inventory Transactions
        $transactions = StockMovement::with(['product', 'batch', 'creator'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get()->map(function($t) {
                if ($t->product) {
                    $decoded = json_decode($t->product->name, true);
                    if (is_array($decoded)) {
                        $t->product->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                }
                return $t;
            });

        // 6. Value and Quantity Trend over Time
        // Aggregate active batches value/qty grouped by created date
        $trendData = Batch::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('quantity', '>', 0)
            ->select(
                DB::raw('DATE(created_at) as trend_date'),
                DB::raw('SUM(quantity * purchase_price) as value'),
                DB::raw('SUM(quantity) as qty')
            )
            ->groupBy('trend_date')
            ->orderBy('trend_date', 'asc')
            ->get();

        $inventoryValue = $products->sum('stock_value');
        $totalProducts = Product::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        $outOfStockCount = $products->filter(fn($p) => $p->current_stock <= 0)->count();
        $lowStockCount = $products->filter(fn($p) => $p->current_stock > 0 && $p->current_stock <= $p->minimum_stock)->count();
        $expiredCount = Batch::where('expiry_date', '<', now())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereHas('product', function($q) use ($branchId) {
                if ($branchId) $q->where('branch_id', $branchId);
            })
            ->where('quantity', '>', 0)
            ->count();

        $totalRemainingStock = Batch::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('quantity', '>', 0)
            ->sum('quantity');

        $totalSoldQty = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->sum('sale_items.quantity');

        $turnoverRate = $totalRemainingStock > 0 ? round($totalSoldQty / $totalRemainingStock, 2) : 0;

        // Inventory by Category Distribution
        $purchasesByCategory = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(purchase_items.total) as total'))
            ->when($branchId, fn($q) => $q->where('purchases.branch_id', $branchId))
            ->whereBetween('purchases.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $item;
            });

        return [
            'products' => $products->sortBy('current_stock')->take(50),
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'overstock' => $overstock,
            'healthy_stock' => $healthyStock,
            'fast_moving' => $fastMoving,
            'slow_moving' => $slowMoving,
            'expiring_soon' => $expiringSoon,
            'expired_batches' => $expired,
            'transactions' => $transactions,
            'trend_data' => $trendData,
            'inventory_value' => $inventoryValue,
            'total_products' => $totalProducts,
            'out_of_stock_count' => $outOfStockCount,
            'low_stock_count' => $lowStockCount,
            'expired_count' => $expiredCount,
            'total_remaining_stock' => $totalRemainingStock,
            'total_sold_qty' => $totalSoldQty,
            'turnover_rate' => $turnoverRate,
            'purchases_by_category' => $purchasesByCategory
        ];
    }

    /**
     * Get Supplier Report
     */
    public function getSupplierReport($filters)
    {
        $branchId = isset($filters['branch_id']) && $filters['branch_id'] !== '' ? $filters['branch_id'] : null;

        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;

        $suppliers = \App\Models\Supplier::select('suppliers.id', 'suppliers.supplier_number', 'suppliers.name', 'suppliers.email', 'suppliers.address')
            ->when($branchId, fn($q) => $q->where('suppliers.branch_id', $branchId))
            ->selectRaw('COALESCE(COUNT(purchases.id), 0) as invoice_count')
            ->selectRaw('COALESCE(SUM(purchases.total_amount), 0) as total_purchases')
            ->selectRaw('COALESCE(SUM(purchases.paid_amount), 0) as total_paid')
            ->selectRaw('COALESCE(SUM(purchases.remaining_amount), 0) as total_remaining')
            ->leftJoin('purchases', function($join) use ($branchId, $fromDate, $toDate) {
                $join->on('suppliers.id', '=', 'purchases.supplier_id');
                if ($branchId) {
                    $join->where('purchases.branch_id', $branchId);
                }
                if ($fromDate && $toDate) {
                    $join->whereBetween('purchases.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
                }
            })
            ->groupBy('suppliers.id', 'suppliers.supplier_number', 'suppliers.name', 'suppliers.email', 'suppliers.address')
            ->orderBy('total_purchases', 'desc')
            ->limit(50)
            ->get()
            ->map(function($item) {
                foreach (['name', 'address'] as $field) {
                    if (isset($item->$field)) {
                        $decoded = is_string($item->$field) ? json_decode($item->$field, true) : $item->$field;
                        if (is_array($decoded)) {
                            $item->$field = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                        }
                    }
                }
                return $item;
            });

        $stats = [
            'total_suppliers' => \App\Models\Supplier::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count(),
            'total_purchases' => $suppliers->sum('total_purchases'),
            'total_paid' => $suppliers->sum('total_paid'),
            'total_remaining' => $suppliers->sum('total_remaining'),
        ];

        // 1. Purchase Trends
        $dailyPurchaseAmount = \App\Models\Purchase::selectRaw("DATE(created_at) as period, SUM(total_amount) as amount")
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->get()->pluck('amount', 'period');

        $dailyPurchaseCount = \App\Models\Purchase::selectRaw("DATE(created_at) as period, COUNT(id) as count")
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->get()->pluck('count', 'period');

        $monthlyPurchaseAmount = \App\Models\Purchase::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(total_amount) as amount")
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->get()->pluck('amount', 'period');

        $monthlyPurchaseCount = \App\Models\Purchase::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(id) as count")
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->get()->pluck('count', 'period');

        // 2. Purchases by Category
        $purchasesByCategory = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(purchase_items.total) as total'))
            ->when($branchId, fn($q) => $q->where('purchases.branch_id', $branchId))
            ->whereBetween('purchases.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('categories.id', 'categories.name')
            ->get()->map(function($c) {
                $decoded = json_decode($c->name, true);
                if (is_array($decoded)) {
                    $c->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $c;
            });

        // 3. Top Products by Supplier
        $topProductsBySupplier = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->select('suppliers.name as supplier_name', 'products.name as product_name', DB::raw('SUM(purchase_items.quantity) as quantity'), DB::raw('SUM(purchase_items.total) as total'))
            ->when($branchId, fn($q) => $q->where('purchases.branch_id', $branchId))
            ->whereBetween('purchases.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('suppliers.id', 'suppliers.name', 'products.id', 'products.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()->map(function($item) {
                foreach (['supplier_name', 'product_name'] as $f) {
                    $decoded = json_decode($item->$f, true);
                    if (is_array($decoded)) {
                        $item->$f = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                    }
                }
                return $item;
            });

        // 4. Supplier Categories supplied
        $supplierCategories = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(purchase_items.total) as total'))
            ->when($branchId, fn($q) => $q->where('purchases.branch_id', $branchId))
            ->whereBetween('purchases.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('categories.id', 'categories.name')
            ->get()->map(function($c) {
                $decoded = json_decode($c->name, true);
                if (is_array($decoded)) {
                    $c->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $c;
            });

        // 5. Payment Methods
        $paymentMethods = DB::table('purchases')
            ->select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('payment_method')
            ->get();

        return array_merge($stats, [
            'suppliers' => $suppliers,
            'trends' => [
                'dailyPurchaseAmount' => $dailyPurchaseAmount,
                'dailyPurchaseCount' => $dailyPurchaseCount,
                'monthlyPurchaseAmount' => $monthlyPurchaseAmount,
                'monthlyPurchaseCount' => $monthlyPurchaseCount,
            ],
            'purchasesByCategory' => $purchasesByCategory,
            'topProductsBySupplier' => $topProductsBySupplier,
            'supplierCategories' => $supplierCategories,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Get Customer Report
     */
    public function getCustomerReport($filters)
    {
        $branchId = isset($filters['branch_id']) && $filters['branch_id'] !== '' ? $filters['branch_id'] : null;

        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;

        $customers = Customer::select('customers.id', 'customers.name', 'customers.phone', 'customers.email', 'customers.address')
            ->when($branchId, fn($q) => $q->where('customers.branch_id', $branchId))
            ->selectRaw('COALESCE(COUNT(sales.id), 0) as visits')
            ->selectRaw('COALESCE(SUM(sales.total), 0) as total_purchases')
            ->selectRaw('COALESCE(SUM(sales.paid_amount), 0) as total_paid')
            ->selectRaw('COALESCE(SUM(sales.total - sales.paid_amount), 0) as balance')
            ->selectRaw('(SELECT users.full_name FROM sales s JOIN users ON s.user_id = users.id WHERE s.customer_id = customers.id ORDER BY s.created_at DESC LIMIT 1) as responsible_user')
            ->leftJoin('sales', function($join) use ($branchId, $fromDate, $toDate) {
                $join->on('customers.id', '=', 'sales.customer_id');
                if ($branchId) {
                    $join->where('sales.branch_id', $branchId);
                }
                if ($fromDate && $toDate) {
                    $join->whereBetween('sales.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
                }
            })
            ->groupBy('customers.id', 'customers.name', 'customers.phone', 'customers.email', 'customers.address')
            ->orderBy('total_purchases', 'desc')
            ->limit(50)
            ->get()
            ->map(function($item) {
                foreach (['name', 'address', 'responsible_user'] as $field) {
                    if (isset($item->$field)) {
                        $decoded = is_string($item->$field) ? json_decode($item->$field, true) : $item->$field;
                        if (is_array($decoded)) {
                            $item->$field = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                        }
                    }
                }
                return $item;
            });

        $totalPurchases = $customers->sum('total_purchases');
        $totalPaid = $customers->sum('total_paid');
        $collectionRate = $totalPurchases > 0 ? ($totalPaid / $totalPurchases) * 100 : 0;

        $activeCustomers = Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'Active')->count();

        $newCustomers = Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [
                ($filters['from_date'] ?? now()->startOfMonth()->format('Y-m-d')) . ' 00:00:00',
                ($filters['to_date'] ?? now()->format('Y-m-d')) . ' 23:59:59'
            ])->count();

        $returningCustomers = Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereHas('sales', function($q) use ($branchId, $filters) {
            if ($branchId) $q->where('branch_id', $branchId);
            if (isset($filters['from_date']) && isset($filters['to_date'])) {
                $q->whereBetween('created_at', [$filters['from_date'].' 00:00:00', $filters['to_date'].' 23:59:59']);
            }
        }, '>', 1)->count();

        // Growth Data
        $dailyNew = Customer::selectRaw("DATE(created_at) as period, COUNT(id) as count")
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->get()->pluck('count', 'period');

        $dailyReturning = Sale::selectRaw("DATE(created_at) as period, COUNT(DISTINCT customer_id) as count")
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->get()->pluck('count', 'period');

        $monthlyNew = Customer::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(id) as count")
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->get()->pluck('count', 'period');

        $monthlyReturning = Sale::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(DISTINCT customer_id) as count")
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->get()->pluck('count', 'period');

        // Customer Distributions
        $byType = Customer::selectRaw("customer_type, COUNT(id) as count")
            ->groupBy('customer_type')
            ->get();

        $byCity = Customer::selectRaw("address as city, COUNT(id) as count")
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->groupBy('address')
            ->get();

        // Inactive customers (lifetime last sale)
        $lastSales = Sale::select('customer_id', DB::raw('MAX(created_at) as last_purchase_date'))
            ->groupBy('customer_id')
            ->get()->pluck('last_purchase_date', 'customer_id');

        $inactiveCustomers = Customer::all()->map(function($c) use ($lastSales) {
            $lastSaleDate = $lastSales[$c->id] ?? null;
            $c->last_purchase_date = $lastSaleDate ? \Carbon\Carbon::parse($lastSaleDate)->format('Y-m-d H:i') : null;
            $c->days_since_last_purchase = $lastSaleDate ? now()->diffInDays(\Carbon\Carbon::parse($lastSaleDate)) : null;
            
            $decodedName = json_decode($c->getRawOriginal('name'), true);
            if (is_array($decodedName)) {
                $c->name = $decodedName[app()->getLocale()] ?? $decodedName['en'] ?? $decodedName['ar'] ?? array_shift($decodedName);
            } else {
                $c->name = $c->getRawOriginal('name');
            }
            return $c;
        })->filter(fn($c) => !$c->last_purchase_date || $c->days_since_last_purchase > 30)
          ->sortByDesc('days_since_last_purchase')
          ->take(50);

        // Buying Trends
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('COUNT(DISTINCT sales.customer_id) as customer_count'), DB::raw('SUM(sale_items.quantity) as purchase_count'))
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->whereBetween('sales.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('products.id', 'products.name')
            ->orderBy('purchase_count', 'desc')
            ->limit(10)
            ->get()->map(function($p) {
                $decoded = json_decode($p->name, true);
                if (is_array($decoded)) {
                    $p->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $p;
            });

        $topCategories = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(DISTINCT sales.customer_id) as customer_count'), DB::raw('SUM(sale_items.quantity) as purchase_count'))
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->whereBetween('sales.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('purchase_count', 'desc')
            ->limit(10)
            ->get()->map(function($c) {
                $decoded = json_decode($c->name, true);
                if (is_array($decoded)) {
                    $c->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                }
                return $c;
            });

        return [
            'customers' => $customers,
            'total_customers' => Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count(),
            'total_purchases' => $totalPurchases,
            'total_paid' => $totalPaid,
            'total_remaining' => $customers->sum('balance'),
            'collection_rate' => $collectionRate,
            'active_customers' => $activeCustomers,
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'growth' => [
                'dailyNew' => $dailyNew,
                'dailyReturning' => $dailyReturning,
                'monthlyNew' => $monthlyNew,
                'monthlyReturning' => $monthlyReturning,
            ],
            'byType' => $byType,
            'byCity' => $byCity,
            'inactiveCustomers' => $inactiveCustomers,
            'topProducts' => $topProducts,
            'topCategories' => $topCategories,
        ];
    }
    public function getExpensesReport($filters)
    {
        $branchId = isset($filters['branch_id']) && $filters['branch_id'] !== '' ? $filters['branch_id'] : null;
        $query = Expense::query()->where('status', 'Approved');
        $this->applyFilters($query, $filters);

        $stats = (clone $query)->selectRaw('
            SUM(amount) as total_expenses,
            COUNT(id) as expense_count,
            AVG(amount) as average_expense
        ')->first();

        $expenseTypes = \App\Models\ExpenseType::all();
        $expenses = $query->with(['branch', 'user'])
            ->orderBy('expense_date', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($e) use ($expenseTypes) {
                $et = $expenseTypes->first(fn($t) => $t->name_en == $e->type || $t->name_ar == $e->type);
                $e->type = $et ? $et->getTranslation('name') : $e->type;
                return $e;
            });

        // Highest single expense
        $highestExpenseRecord = (clone $query)->orderBy('amount', 'desc')->first();
        $highestExpenseType = null;
        if ($highestExpenseRecord) {
            $et = $expenseTypes->first(
                fn($t) => $t->name_en == $highestExpenseRecord->type || $t->name_ar == $highestExpenseRecord->type
            );
            $highestExpenseType = $et ? $et->getTranslation('name') : $highestExpenseRecord->type;
        }

        // Lowest single expense
        $lowestExpenseRecord = (clone $query)->orderBy('amount', 'asc')->first();
        $lowestExpenseType = null;
        if ($lowestExpenseRecord) {
            $et = $expenseTypes->first(
                fn($t) => $t->name_en == $lowestExpenseRecord->type || $t->name_ar == $lowestExpenseRecord->type
            );
            $lowestExpenseType = $et ? $et->getTranslation('name') : $lowestExpenseRecord->type;
        }

        // This Month vs Last Month Expenses

        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $filters['to_date'] ?? now()->endOfMonth()->format('Y-m-d');

        $filterDate = Carbon::parse($fromDate);
        $startOfFilterMonth = $filterDate->copy()->startOfMonth()->format('Y-m-d');
        $endOfFilterMonth = $filterDate->copy()->endOfMonth()->format('Y-m-d');

        // Last month of the filtered month
        $startOfLastMonth = $filterDate->copy()->subMonth()->startOfMonth()->format('Y-m-d');
        $endOfLastMonth = $filterDate->copy()->subMonth()->endOfMonth()->format('Y-m-d');

        $thisMonthExpenses = Expense::where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$startOfFilterMonth . ' 00:00:00', $endOfFilterMonth . ' 23:59:59'])
            ->sum('amount');

        $lastMonthExpenses = Expense::where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$startOfLastMonth . ' 00:00:00', $endOfLastMonth . ' 23:59:59'])
            ->sum('amount');

        $percentageDiff = 0;
        if ($lastMonthExpenses > 0) {
            $percentageDiff = (($thisMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100;
        } elseif ($thisMonthExpenses > 0) {
            $percentageDiff = 100;
        }
        $toDate = $filters['to_date'] ?? now()->endOfMonth()->format('Y-m-d');

        // 1. Daily Trend
        $dailyTrend = DB::table('expenses')
            ->selectRaw("DATE(created_at) as period, SUM(amount) as amount, COUNT(id) as count")
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // 2. Monthly Trend
        $monthlyTrend = DB::table('expenses')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(amount) as amount, COUNT(id) as count")
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // 3. Highest Expense Categories
        $highestCategories = DB::table('expenses')
            ->selectRaw("type as category, SUM(amount) as total_amount, COUNT(id) as count")
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('type')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($item) use ($expenseTypes) {
                $et = $expenseTypes->first(fn($t) => $t->name_en == $item->category || $t->name_ar == $item->category);
                $item->category_label = $et ? $et->getTranslation('name') : $item->category;
                return $item;
            });

        // 4. Largest Expenses
        $largestExpenses = Expense::where('status', 'Approved')
            ->with(['branch', 'user'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->orderByDesc('amount')
            ->get()
            ->map(function ($e) use ($expenseTypes) {
                $et = $expenseTypes->first(fn($t) => $t->name_en == $e->type || $t->name_ar == $e->type);
                $e->type_label = $et ? $et->getTranslation('name') : $e->type;
                return $e;
            });

        // 5. Frequent Categories
        $frequentCategories = DB::table('expenses')
            ->selectRaw("type as category, COUNT(id) as transaction_count, SUM(amount) as total_amount")
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('type')
            ->orderByDesc('transaction_count')
            ->get()
            ->map(function ($item) use ($expenseTypes) {
                $et = $expenseTypes->first(fn($t) => $t->name_en == $item->category || $t->name_ar == $item->category);
                $item->category_label = $et ? $et->getTranslation('name') : $item->category;
                return $item;
            });

        // 6. Payment Method Distribution
        $paymentMethods = DB::table('expenses')
            ->selectRaw("payment_method, SUM(amount) as total, COUNT(id) as count")
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('payment_method')
            ->get();

        return [
            'total_expenses' => $stats->total_expenses ?? 0,
            'expense_count' => $stats->expense_count ?? 0,
            'average_expense' => $stats->average_expense ?? 0,
            'expenses' => $expenses,
            'highest_expense' => [
                'amount' => $highestExpenseRecord->amount ?? 0,
                'type'   => $highestExpenseType ?? '-',
                'date'   => $highestExpenseRecord->expense_date ?? null,
            ],
            'lowest_expense' => [
                'amount' => $lowestExpenseRecord->amount ?? 0,
                'type'   => $lowestExpenseType ?? '-',
                'date'   => $lowestExpenseRecord->expense_date ?? null,
            ],
            'this_month_expenses' => $thisMonthExpenses,
            'last_month_comparison' => $percentageDiff,
            'daily_trend' => $dailyTrend,
            'monthly_trend' => $monthlyTrend,
            'highest_categories' => $highestCategories,
            'largest_expenses' => $largestExpenses,
            'frequent_categories' => $frequentCategories,
            'payment_methods' => $paymentMethods,
        ];
    }

    /**
     * Get Financial Report
     */
    public function getFinancialReport($filters)
    {
        $branchId = isset($filters['branch_id']) && $filters['branch_id'] !== '' ? $filters['branch_id'] : null;


        $sales = Sale::query();
        $this->applyFilters($sales, $filters);
        $totalSales = $sales->sum('total');

        $purchases = Purchase::query();
        $this->applyFilters($purchases, $filters);
        $totalPurchases = $purchases->sum('total_amount');

        $expenses = Expense::query()->where('status', 'Approved');
        $this->applyFilters($expenses, $filters);
        $totalExpenses = $expenses->sum('amount');

        $expenseTypes = \App\Models\ExpenseType::all();
        // Detailed Expense Breakdown
        $expenseBreakdown = DB::table('expenses')
            ->select('type', DB::raw('SUM(amount) as total'))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('expense_date', [$filters['from_date'], $filters['to_date']])
            ->where('status', 'Approved')
            ->groupBy('type')
            ->get()
            ->map(function ($item) use ($expenseTypes) {
                $et = $expenseTypes->first(fn($t) => $t->name_en == $item->type || $t->name_ar == $item->type);
                $item->type = $et ? $et->getTranslation('name') : $item->type;
                return $item;
            });

        // COGS
        $cogsQuery = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('batches', 'sale_items.batch_id', '=', 'batches.id')
            ->where('sales.status', '!=', 'cancelled');
        if (!empty($filters['from_date'])) {
            $cogsQuery->where('sales.created_at', '>=', $filters['from_date'] . ' 00:00:00');
        }
        if (!empty($filters['to_date'])) {
            $cogsQuery->where('sales.created_at', '<=', $filters['to_date'] . ' 23:59:59');
        }
        if (isset($filters['branch_id']) && $filters['branch_id'] !== 'all' && $filters['branch_id'] !== '') {
            $cogsQuery->where('sales.branch_id', $filters['branch_id']);
        }
        $totalCOGS = $cogsQuery->sum(DB::raw('sale_items.quantity * COALESCE(batches.purchase_price, 0)'));

        $netProfit = $totalSales - $totalPurchases - $totalExpenses;



        // Total Returns Value
        $totalReturns = DB::table('sales_returns')
            ->join('sale_items', function($join) {
                $join->on('sales_returns.sale_id', '=', 'sale_items.sale_id')
                     ->on('sales_returns.product_id', '=', 'sale_items.product_id');
            })
            ->when($branchId, fn($q) => $q->where('sales_returns.branch_id', $branchId))
            ->whereBetween('sales_returns.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->sum(DB::raw('sales_returns.quantity * sale_items.price'));

        // Total Waste Value
        $totalWaste = DB::table('inventory_adjustments')
            ->join('products', 'inventory_adjustments.product_id', '=', 'products.id')
            ->when($branchId, fn($q) => $q->where('inventory_adjustments.branch_id', $branchId))
            ->whereIn('inventory_adjustments.adjustment_type', ['EXPIRED', 'DAMAGED', 'LOST'])
            ->whereBetween('inventory_adjustments.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->sum(DB::raw('ABS(inventory_adjustments.quantity) * products.sale_price'));

        $wasteBreakdown = DB::table('inventory_adjustments')
            ->select('adjustment_type', DB::raw('SUM(ABS(quantity) * products.sale_price) as total'))
            ->join('products', 'inventory_adjustments.product_id', '=', 'products.id')
            ->when($branchId, fn($q) => $q->where('inventory_adjustments.branch_id', $branchId))
            ->whereIn('inventory_adjustments.adjustment_type', ['EXPIRED', 'DAMAGED', 'LOST'])
            ->whereBetween('inventory_adjustments.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('adjustment_type')
            ->get();

        $netRevenue = $totalSales - $totalReturns;

        $topExpenseCategory = collect($expenseBreakdown)->sortByDesc('total')->first();
        $mostFrequentCategoryName = $topExpenseCategory ? $topExpenseCategory->type : '-';

        // Top Revenue Categories
        $topRevenueCategories = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.id', 'categories.name', DB::raw('SUM(sale_items.total) as total_revenue'))
            ->where('sales.status', '!=', 'cancelled')
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(function ($c) {
                $decoded = json_decode($c->name, true);
                if (is_array($decoded)) {
                    $c->category_label = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
                } else {
                    $c->category_label = $c->name;
                }
                return $c;
            });

        // Payment Methods Distribution in Sales
        $paymentMethods = DB::table('sale_payments')
            ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
            ->select('sale_payments.payment_method', DB::raw('SUM(sale_payments.amount) as total'))
            ->where('sales.status', '!=', 'cancelled')
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('sale_payments.payment_method')
            ->get();

        // Daily Trend Comparison
        $dailySales = DB::table('sales')
            ->selectRaw('DATE(created_at) as period, SUM(total) as amount')
            ->where('status', '!=', 'cancelled')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('period')
            ->get();

        $dailyExpenses = DB::table('expenses')
            ->selectRaw('DATE(created_at) as period, SUM(amount) as amount')
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('period')
            ->get();

        // Monthly Trend Comparison
        $monthlySales = DB::table('sales')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(total) as amount")
            ->where('status', '!=', 'cancelled')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('period')
            ->get();

        $monthlyExpenses = DB::table('expenses')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(amount) as amount")
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('period')
            ->get();

        return [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'total_expenses' => $totalExpenses,
            'expense_breakdown' => $expenseBreakdown,
            'net_profit' => $netProfit,
            'total_returns' => $totalReturns,
            'total_waste' => $totalWaste,
            'waste_breakdown' => $wasteBreakdown,
            'net_revenue' => $netRevenue,
            'most_frequent_category' => $mostFrequentCategoryName,
            'total_cogs' => $totalCOGS,
            'top_revenue_categories' => $topRevenueCategories,
            'payment_methods' => $paymentMethods,
            'daily_sales' => $dailySales,
            'daily_expenses' => $dailyExpenses,
            'monthly_sales' => $monthlySales,
            'monthly_expenses' => $monthlyExpenses,
        ];
    }

    /**
     * Get VAT Report (Dynamic - based on actual tax in sales)
     */
    public function getVATReport($filters)
    {
        $query = Sale::query();
        $this->applyFilters($query, $filters);

        $stats = $query->selectRaw('SUM(total) as total_sales, SUM(tax) as vat_collected, SUM(total - tax - discount) as net_before_tax')->first();
        $totalSales    = $stats->total_sales ?? 0;
        $vatCollected  = $stats->vat_collected ?? 0;
        $netSales      = $totalSales - $vatCollected;

        // Compute effective VAT rate dynamically
        $netBeforeTax = $stats->net_before_tax ?? 0;
        $effectiveVatRate = ($netBeforeTax > 0) ? round(($vatCollected / $netBeforeTax) * 100, 2) : 0;

        // Total purchases in same period
        $purchaseQuery = Purchase::query();
        $this->applyFilters($purchaseQuery, $filters);
        $purchaseStats = $purchaseQuery->selectRaw('SUM(total_amount) as total_purchases, SUM(subtotal) as total_subtotal')->first();
        $totalPurchases = $purchaseStats->total_purchases ?? 0;
        $purchaseSubtotal = $purchaseStats->total_subtotal ?? 0;

        // Estimated VAT paid on purchases = subtotal × effective_vat_rate / 100
        $vatPaid = ($effectiveVatRate > 0)
            ? round($purchaseSubtotal * ($effectiveVatRate / 100), 2)
            : 0;

        $taxableSales = $netSales;
        $taxablePurchases = $totalPurchases - $vatPaid;
        $netTaxPayable = $vatCollected - $vatPaid;

        return [
            'total_sales'       => $totalSales,
            'vat_collected'     => $vatCollected, // Output Tax
            'net_sales'         => $netSales,
            'vat_rate'          => $effectiveVatRate,
            'total_purchases'   => $totalPurchases,
            'vat_paid'          => $vatPaid, // Input Tax
            'taxable_sales'     => $taxableSales,
            'taxable_purchases' => $taxablePurchases,
            'net_tax_payable'   => $netTaxPayable,
        ];
    }

    /**
     * Helper to apply common filters
     */
    protected function applyFilters($query, $filters)
    {
        if (isset($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date'] . ' 00:00:00');
        }
        if (isset($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date'] . ' 23:59:59');
        }
        if (isset($filters['branch_id']) && $filters['branch_id'] !== 'all' && $filters['branch_id'] !== '') {
            $query->where('branch_id', $filters['branch_id']);
        }
    }

    /**
     * Calculate core sales KPI statistics
     */
    public function calculateKPIStats($filters)
    {
        $query = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('batches', 'sale_items.batch_id', '=', 'batches.id')
            ->where('sales.status', '!=', 'cancelled');

        if (!empty($filters['from_date'])) {
            $query->where('sales.created_at', '>=', $filters['from_date'] . ' 00:00:00');
        }
        if (!empty($filters['to_date'])) {
            $query->where('sales.created_at', '<=', $filters['to_date'] . ' 23:59:59');
        }
        if (isset($filters['branch_id']) && $filters['branch_id'] !== 'all' && $filters['branch_id'] !== '') {
            $query->where('sales.branch_id', $filters['branch_id']);
        }
        if (isset($filters['customer_id']) && $filters['customer_id'] !== 'all' && $filters['customer_id'] !== '') {
            $query->where('sales.customer_id', $filters['customer_id']);
        }
        if (isset($filters['payment_method']) && $filters['payment_method'] !== 'all' && $filters['payment_method'] !== '') {
            $query->where('sales.payment_method', $filters['payment_method']);
        }
        if (isset($filters['status']) && $filters['status'] !== 'all' && $filters['status'] !== '') {
            $query->where('sales.status', $filters['status']);
        }
        if (isset($filters['category_id']) && $filters['category_id'] !== 'all' && $filters['category_id'] !== '') {
            $query->where('products.category_id', $filters['category_id']);
        }

        // Query basic financial stats directly from the sales table
        $salesQuery = DB::table('sales')->where('status', '!=', 'cancelled');
        if (!empty($filters['from_date'])) {
            $salesQuery->where('created_at', '>=', $filters['from_date'] . ' 00:00:00');
        }
        if (!empty($filters['to_date'])) {
            $salesQuery->where('created_at', '<=', $filters['to_date'] . ' 23:59:59');
        }
        if (isset($filters['branch_id']) && $filters['branch_id'] !== 'all' && $filters['branch_id'] !== '') {
            $salesQuery->where('branch_id', $filters['branch_id']);
        }
        if (isset($filters['customer_id']) && $filters['customer_id'] !== 'all' && $filters['customer_id'] !== '') {
            $salesQuery->where('customer_id', $filters['customer_id']);
        }
        if (isset($filters['payment_method']) && $filters['payment_method'] !== 'all' && $filters['payment_method'] !== '') {
            $salesQuery->where('payment_method', $filters['payment_method']);
        }
        if (isset($filters['status']) && $filters['status'] !== 'all' && $filters['status'] !== '') {
            $salesQuery->where('status', $filters['status']);
        }
        
        $salesStats = $salesQuery->selectRaw('
            SUM(total) as total_sales,
            SUM(tax) as total_tax,
            SUM(discount) as total_discount,
            COUNT(id) as total_orders,
            SUM(paid_amount) as total_paid,
            SUM(total - paid_amount) as total_remaining
        ')->first();

        $totalSales = $salesStats->total_sales ?? 0;
        $totalTax = $salesStats->total_tax ?? 0;
        $totalDiscount = $salesStats->total_discount ?? 0;
        $totalOrders = $salesStats->total_orders ?? 0;
        $totalPaid = $salesStats->total_paid ?? 0;
        $totalRemaining = $salesStats->total_remaining ?? 0;

        $totalItemsSold = (clone $query)->sum('sale_items.quantity');

        // 6. Cost of Goods Sold (COGS)
        $totalCOGS = (clone $query)->selectRaw('SUM(sale_items.quantity * COALESCE(batches.purchase_price, 0)) as cogs')->first()->cogs ?? 0;

        // 7. Returns (value & count)
        $returnQuery = DB::table('sales_returns')
            ->join('sales', 'sales_returns.sale_id', '=', 'sales.id')
            ->join('products', 'sales_returns.product_id', '=', 'products.id')
            ->join('sale_items', function($join) {
                $join->on('sales_returns.sale_id', '=', 'sale_items.sale_id')
                     ->on('sales_returns.product_id', '=', 'sale_items.product_id')
                     ->on('sales_returns.batch_id', '=', 'sale_items.batch_id');
            })
            ->where('sales.status', '!=', 'cancelled');

        if (!empty($filters['from_date'])) {
            $returnQuery->where('sales.created_at', '>=', $filters['from_date'] . ' 00:00:00');
        }
        if (!empty($filters['to_date'])) {
            $returnQuery->where('sales.created_at', '<=', $filters['to_date'] . ' 23:59:59');
        }
        if (isset($filters['branch_id']) && $filters['branch_id'] !== 'all' && $filters['branch_id'] !== '') {
            $returnQuery->where('sales.branch_id', $filters['branch_id']);
        }
        if (isset($filters['customer_id']) && $filters['customer_id'] !== 'all' && $filters['customer_id'] !== '') {
            $returnQuery->where('sales.customer_id', $filters['customer_id']);
        }
        if (isset($filters['payment_method']) && $filters['payment_method'] !== 'all' && $filters['payment_method'] !== '') {
            $returnQuery->where('sales.payment_method', $filters['payment_method']);
        }
        if (isset($filters['status']) && $filters['status'] !== 'all' && $filters['status'] !== '') {
            $returnQuery->where('sales.status', $filters['status']);
        }
        if (isset($filters['category_id']) && $filters['category_id'] !== 'all' && $filters['category_id'] !== '') {
            $returnQuery->where('products.category_id', $filters['category_id']);
        }

        $returnsAmount = $returnQuery->selectRaw('SUM(sales_returns.quantity * (sale_items.total / COALESCE(sale_items.quantity, 1))) as amount')->first()->amount ?? 0;
        $returnsCount = (clone $returnQuery)->distinct('sales_returns.sale_id')->count('sales_returns.sale_id');

        // 8. Calculated Metrics
        $netSales = $totalSales - $returnsAmount;
        $grossProfit = $netSales - $totalCOGS;
        $avgOrderValue = $totalOrders > 0 ? ($netSales / $totalOrders) : 0;
        $profitMargin = $netSales > 0 ? (($grossProfit / $netSales) * 100) : 0;

        // 9. Best Selling Category
        $bestCategory = (clone $query)
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                DB::raw('SUM(sale_items.quantity) as items_sold'),
                DB::raw('SUM(sale_items.total) as sales_amount')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('items_sold', 'desc')
            ->first();

        // 10. Sparkline data (last 7 data points)
        $dailyPoints = (clone $query)
            ->select(DB::raw('DATE(sales.created_at) as date'), DB::raw('SUM(sale_items.total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->take(-7)
            ->toArray();

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_items_sold' => $totalItemsSold,
            'total_discount' => $totalDiscount,
            'total_tax' => $totalTax,
            'total_cogs' => $totalCOGS,
            'returns_amount' => $returnsAmount,
            'returns_count' => $returnsCount,
            'net_sales' => $netSales,
            'gross_profit' => $grossProfit,
            'avg_order_value' => $avgOrderValue,
            'profit_margin' => $profitMargin,
            'total_paid' => $totalPaid,
            'total_remaining' => $totalRemaining,
            'best_category' => $bestCategory,
            'sparkline' => implode(',', $dailyPoints) ?: '0,0,0,0,0'
        ];
    }

    /**
     * Compute Period-over-Period filters
     */
    protected function getPreviousPeriodFilters($filters)
    {
        $fromDate = Carbon::parse($filters['from_date']);
        $toDate = Carbon::parse($filters['to_date']);
        $diff = $fromDate->diffInDays($toDate) + 1;

        $prevFilters = $filters;
        $prevFilters['from_date'] = $fromDate->copy()->subDays($diff)->format('Y-m-d');
        $prevFilters['to_date'] = $fromDate->copy()->subDays(1)->format('Y-m-d');

        return $prevFilters;
    }

    /**
     * Return enriched KPI metrics
     */
    public function getSalesKPIs($filters)
    {
        $current = $this->calculateKPIStats($filters);
        $prev = $this->calculateKPIStats($this->getPreviousPeriodFilters($filters));

        $metrics = [];
        $kpis = [
            'total_sales' => 'Total Sales',
            'net_sales' => 'Net Sales',
            'gross_profit' => 'Gross Profit',
            'total_orders' => 'Total Orders',
            'avg_order_value' => 'Average Order Value',
            'total_items_sold' => 'Total Items Sold',
            'returns_amount' => 'Sales Returns',
            'total_discount' => 'Discounts Given',
            'total_tax' => 'VAT Collected',
            'total_cogs' => 'Cost of Goods Sold (COGS)',
            'profit_margin' => 'Profit Margin',
            'total_paid' => 'Total Paid',
            'total_remaining' => 'Total Remaining',
        ];

        foreach ($kpis as $key => $title) {
            $currVal = (float)$current[$key];
            $prevVal = (float)$prev[$key];
            $change = $prevVal > 0 ? round((($currVal - $prevVal) / $prevVal) * 100, 2) : 0;
            
            $trend = 'no_change';
            if ($currVal > $prevVal) $trend = 'up';
            elseif ($currVal < $prevVal) $trend = 'down';

            $metrics[$key] = [
                'title' => $title,
                'value' => $currVal,
                'prev' => $prevVal,
                'change' => $change,
                'trend' => $trend,
                'sparkline' => $current['sparkline']
            ];
        }

        // Add special category KPI
        $bestCatName = 'N/A';
        if (isset($current['best_category']->name)) {
            $decoded = json_decode($current['best_category']->name, true);
            if (is_array($decoded)) {
                $bestCatName = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
            } else {
                $bestCatName = $current['best_category']->name;
            }
        }

        $metrics['best_category'] = [
            'title' => 'Best Selling Category',
            'value' => $bestCatName,
            'items_sold' => $current['best_category']->items_sold ?? 0,
            'sales_amount' => $current['best_category']->sales_amount ?? 0,
            'trend' => 'no_change',
            'change' => 0,
            'sparkline' => '0,0,0,0,0'
        ];

        // Attach returns count to returns amount
        $metrics['returns_amount']['returns_count'] = $current['returns_count'];

        return $metrics;
    }
}
