<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\InventoryAdjustment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get Comprehensive Sales Report
     */
    public function getSalesReport($filters)
    {
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

        $invoices = $query->with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.total) as total_revenue'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('sales.branch_id', $filters['branch_id']))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $decoded = json_decode($item->name, true);
                if (is_array($decoded)) {
                    $item->name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? array_shift($decoded);
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
            ->limit(10)
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
            'invoices' => $invoices,
            'top_products' => $topProducts,
            'top_customers' => $topCustomers,
            'sales_by_day' => $salesByDay,
        ];
    }

    /**
     * Get Purchase Report
     */
    public function getPurchaseReport($filters)
    {
        $query = Purchase::query();
        $this->applyFilters($query, $filters);

        $stats = (clone $query)->selectRaw('
            SUM(total_amount) as total_purchases,
            SUM(paid_amount) as total_paid,
            SUM(remaining_amount) as total_remaining,
            SUM(discount) as total_discount,
            COUNT(id) as invoice_count
        ')->first();

        $purchases = $query->with('supplier')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return [
            'total_purchases' => $stats->total_purchases ?? 0,
            'total_paid' => $stats->total_paid ?? 0,
            'total_remaining' => $stats->total_remaining ?? 0,
            'total_discount' => $stats->total_discount ?? 0,
            'invoice_count' => $stats->invoice_count ?? 0,
            'purchases' => $purchases,
        ];
    }

    /**
     * Get Inventory Report
     */
    public function getInventoryReport($filters)
    {
        $branchId = $filters['branch_id'] ?? null;

        // Current Stock Table
        $products = Product::with(['batches' => function($q) use ($branchId) {
            if ($branchId) $q->where('branch_id', $branchId);
        }])->get()->map(function($p) use ($branchId) {
            $p->current_stock = $branchId ? $p->currentBranchStock($branchId) : $p->totalStock();
            // Calculate stock value = SUM(batch.quantity × batch.purchase_price)
            $p->stock_value = $p->batches->sum(fn($b) => $b->quantity * $b->purchase_price);
            return $p;
        });

        $lowStock = $products->filter(fn($p) => $p->current_stock <= $p->minimum_stock);

        // Expired Products from Batches
        $expired = Batch::where('expiry_date', '<', now())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('quantity', '>', 0)
            ->with('product')
            ->get();

        // Waste / Adjustments
        $adjustments = InventoryAdjustment::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $inventoryValue = $products->sum('stock_value');

        // KPI Counts
        $totalProducts = Product::count();
        $outOfStockCount = $products->filter(fn($p) => $p->current_stock <= 0)->count();
        $lowStockCount = $products->filter(fn($p) => $p->current_stock > 0 && $p->current_stock <= $p->minimum_stock)->count();
        $expiredCount = Batch::where('expiry_date', '<', now())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('quantity', '>', 0)
            ->count();

        // Total remaining stock (sum of all active batch quantities)
        $totalRemainingStock = Batch::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('quantity', '>', 0)
            ->sum('quantity');

        // Total sold quantity (from sale_items within the filter period)
        $totalSoldQty = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->whereBetween('sales.created_at', [$filters['from_date'] . ' 00:00:00', $filters['to_date'] . ' 23:59:59'])
            ->sum('sale_items.quantity');

        // معدل الدوران = المباع ÷ المتبقي
        $turnoverRate = $totalRemainingStock > 0
            ? round($totalSoldQty / $totalRemainingStock, 2)
            : 0;

        // نسبة المبيعات = المباع ÷ (المباع + المتبقي) × 100
        $totalFlow = $totalSoldQty + $totalRemainingStock;
        $salesToStockRatio = $totalFlow > 0
            ? round(($totalSoldQty / $totalFlow) * 100, 1)
            : 0;

        return [
            'products' => $products->sortBy('current_stock')->take(50),
            'low_stock' => $lowStock->sortBy('current_stock')->take(50),
            'expired' => $expired,
            'adjustments' => $adjustments,
            'inventory_value' => $inventoryValue,
            'total_products' => $totalProducts,
            'out_of_stock_count' => $outOfStockCount,
            'low_stock_count' => $lowStockCount,
            'expired_count' => $expiredCount,
            'total_remaining_stock' => $totalRemainingStock,
            'total_sold_qty' => $totalSoldQty,
            'turnover_rate' => $turnoverRate,
            'sales_to_stock_ratio' => $salesToStockRatio,
        ];
    }

    /**
     * Get Supplier Report
     */
    public function getSupplierReport($filters)
    {
        $branchId = $filters['branch_id'] ?? null;

        $suppliers = \App\Models\Supplier::select('suppliers.id', 'suppliers.supplier_number', 'suppliers.name', 'suppliers.email', 'suppliers.address')
            ->selectRaw('COUNT(purchases.id) as invoice_count')
            ->selectRaw('SUM(purchases.total_amount) as total_purchases')
            ->selectRaw('SUM(purchases.paid_amount) as total_paid')
            ->selectRaw('SUM(purchases.remaining_amount) as total_remaining')
            ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
            ->when($branchId, fn($q) => $q->where('purchases.branch_id', $branchId))
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
            'total_suppliers' => \App\Models\Supplier::count(),
            'total_purchases' => $suppliers->sum('total_purchases'),
            'total_paid' => $suppliers->sum('total_paid'),
            'total_remaining' => $suppliers->sum('total_remaining'),
        ];

        return array_merge($stats, ['suppliers' => $suppliers]);
    }

    /**
     * Get Customer Report
     */
    public function getCustomerReport($filters)
    {
        $branchId = $filters['branch_id'] ?? null;

        $customers = Customer::select('customers.id', 'customers.name', 'customers.phone', 'customers.email', 'customers.address')
            ->selectRaw('COUNT(sales.id) as visits')
            ->selectRaw('SUM(sales.total) as total_purchases')
            ->selectRaw('SUM(sales.paid_amount) as total_paid')
            ->selectRaw('SUM(sales.total - sales.paid_amount) as balance')
            ->selectRaw('(SELECT users.full_name FROM sales s JOIN users ON s.user_id = users.id WHERE s.customer_id = customers.id ORDER BY s.created_at DESC LIMIT 1) as responsible_user')
            ->leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
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

        return [
            'customers' => $customers,
            'total_customers' => Customer::count(),
            'total_purchases' => $totalPurchases,
            'total_paid' => $totalPaid,
            'total_remaining' => $customers->sum('balance'),
            'collection_rate' => $collectionRate,
        ];
    }
    public function getExpensesReport($filters)
    {
        $query = Expense::query();
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
        ];
    }

    /**
     * Get Financial Report
     */
    public function getFinancialReport($filters)
    {
        $sales = Sale::query();
        $this->applyFilters($sales, $filters);
        $totalSales = $sales->sum('total');

        $purchases = Purchase::query();
        $this->applyFilters($purchases, $filters);
        $totalPurchases = $purchases->sum('total_amount');

        $expenses = Expense::query();
        $this->applyFilters($expenses, $filters);
        $totalExpenses = $expenses->sum('amount');

        $expenseTypes = \App\Models\ExpenseType::all();
        // Detailed Expense Breakdown
        $expenseBreakdown = DB::table('expenses')
            ->select('type', DB::raw('SUM(amount) as total'))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->whereBetween('expense_date', [$filters['from_date'], $filters['to_date']])
            ->groupBy('type')
            ->get()
            ->map(function ($item) use ($expenseTypes) {
                $et = $expenseTypes->first(fn($t) => $t->name_en == $item->type || $t->name_ar == $item->type);
                $item->type = $et ? $et->getTranslation('name') : $item->type;
                return $item;
            });

        $netProfit = $totalSales - $totalPurchases - $totalExpenses;

        return [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'total_expenses' => $totalExpenses,
            'expense_breakdown' => $expenseBreakdown,
            'net_profit' => $netProfit,
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

        return [
            'total_sales'       => $totalSales,
            'vat_collected'     => $vatCollected,
            'net_sales'         => $netSales,
            'vat_rate'          => $effectiveVatRate,
            'total_purchases'   => $totalPurchases,
            'vat_paid'          => $vatPaid,
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
        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
    }
}
