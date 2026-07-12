<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\Branch;
use Carbon\Carbon;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Initial dates (default to current month, persisted in session)
        if ($request->has('from_date')) {
            $fromDate = $request->get('from_date');
            session(['report_from_date' => $fromDate]);
        } else {
            $fromDate = session('report_from_date', now()->startOfMonth()->format('Y-m-d'));
        }

        if ($request->has('to_date')) {
            $toDate = $request->get('to_date');
            session(['report_to_date' => $toDate]);
        } else {
            $toDate = session('report_to_date', now()->format('Y-m-d'));
        }
        
        // Branch filter handle
        $branchId = session('branch_id');
        if ($user->isAdmin() && $request->has('branch_id')) {
            $branchId = $request->get('branch_id');
            if ($branchId === 'all') $branchId = null;
        }

        $filters = [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'branch_id' => $branchId,
            'customer_id' => $request->get('customer_id'),
            'payment_method' => $request->get('payment_method'),
            'status' => $request->get('status'),
            'category_id' => $request->get('category_id'),
        ];

        // Fetch all report sections
        $salesKPIs = $this->reportService->getSalesKPIs($filters);
        $salesReport = $this->reportService->getSalesReport($filters);
        $purchaseReport = $this->reportService->getPurchaseReport($filters);
        $inventoryReport = $this->reportService->getInventoryReport($filters);
        $customerReport = $this->reportService->getCustomerReport($filters);
        $supplierReport = $this->reportService->getSupplierReport($filters);
        $financialReport = $this->reportService->getFinancialReport($filters);
        $vatReport = $this->reportService->getVATReport($filters);
        $expensesReport = $this->reportService->getExpensesReport($filters);

        $branches = $user->isAdmin() ? Branch::all() : null;
        $setting = \App\Models\Setting::first();

        // Fetch categories & customers for the filters
        $filterCategories = \App\Models\Category::all();
        $filterCustomers = \App\Models\Customer::all();

        return view('reports.index', compact(
            'salesKPIs',
            'salesReport', 
            'purchaseReport',
            'inventoryReport', 
            'customerReport',
            'supplierReport',
            'financialReport', 
            'vatReport',
            'expensesReport',
            'filters', 
            'branches',
            'setting',
            'filterCategories',
            'filterCustomers'
        ));
    }

    /**
     * Export report data to CSV (Simple implementation)
     */
    public function export(Request $request, $type)
    {
        $filters = $request->only(['from_date', 'to_date', 'branch_id']);
        if (isset($filters['branch_id']) && $filters['branch_id'] === 'all') {
            $filters['branch_id'] = null;
        }
        $format = $request->get('format', 'excel');

        $setting = \App\Models\Setting::first();
        $currency = $setting->currency ?? '$';

        $data = [];
        $filename = "report_{$type}_" . now()->format('YmdHis');
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        switch ($type) {
            case 'sales':
                $report = $this->reportService->getSalesReport($filters);
                if ($format === 'pdf') {
                    $pdf = Pdf::loadView('reports.pdf.sales', compact('report', 'filters', 'setting'));
                    return $pdf->download($filename . ".pdf");
                }
                $columns = [__('pos.invoice_number'), __('pos.customer'), __('pos.total') . " ($currency)", __('pos.vat') . " ($currency)", __('pos.paid') . " ($currency)", __('pos.remaining') . " ($currency)", __('pos.date')];
                $callback = function() use ($report, $columns) {
                    $file = fopen('php://output', 'w');
                    fputs($file, "\xEF\xBB\xBF");
                    fputcsv($file, $columns);
                    foreach ($report['invoices'] as $invoice) {
                        $remaining = $invoice->total - $invoice->paid_amount;
                        fputcsv($file, [$invoice->invoice_number, $invoice->customer->name ?? __('pos.walk_in_customer'), $invoice->total, $invoice->tax, $invoice->paid_amount, $remaining, $invoice->created_at]);
                    }
                    fclose($file);
                };
                $filename .= ".csv";
                return response()->stream($callback, 200, array_merge($headers, ["Content-Disposition" => "attachment; filename=$filename"]));
            
            case 'purchases':
                $report = $this->reportService->getPurchaseReport($filters);
                if ($format === 'pdf') {
                    $pdf = Pdf::loadView('reports.pdf.purchases', compact('report', 'filters', 'setting'));
                    return $pdf->download($filename . ".pdf");
                }
                $columns = [__('pos.invoice_number'), __('pos.supplier'), __('pos.total') . " ($currency)", __('pos.paid') . " ($currency)", __('pos.remaining') . " ($currency)", __('pos.date')];
                $callback = function() use ($report, $columns) {
                    $file = fopen('php://output', 'w');
                    fputs($file, "\xEF\xBB\xBF");
                    fputcsv($file, $columns);
                    foreach ($report['purchases'] as $p) {
                        fputcsv($file, [$p->invoice_number, $p->supplier->name ?? '-', $p->total_amount, $p->paid_amount, $p->remaining_amount, $p->created_at]);
                    }
                    fclose($file);
                };
                $filename .= ".csv";
                return response()->stream($callback, 200, array_merge($headers, ["Content-Disposition" => "attachment; filename=$filename"]));

            case 'inventory':
                $report = $this->reportService->getInventoryReport($filters);
                if ($format === 'pdf') {
                    $pdf = Pdf::loadView('reports.pdf.inventory', compact('report', 'filters', 'setting'));
                    return $pdf->download($filename . ".pdf");
                }
                $columns = [__('pos.product'), __('pos.stock_quantity'), __('pos.minimum_stock')];
                $callback = function() use ($report, $columns) {
                    $file = fopen('php://output', 'w');
                    fputs($file, "\xEF\xBB\xBF");
                    fputcsv($file, $columns);
                    foreach ($report['products'] as $p) {
                        fputcsv($file, [$p->name, $p->current_stock, $p->minimum_stock]);
                    }
                    fclose($file);
                };
                $filename .= ".csv";
                return response()->stream($callback, 200, array_merge($headers, ["Content-Disposition" => "attachment; filename=$filename"]));

            case 'customers':
                $report = $this->reportService->getCustomerReport($filters);
                if ($format === 'pdf') {
                    $pdf = Pdf::loadView('reports.pdf.customers', compact('report', 'filters', 'setting'));
                    return $pdf->download($filename . ".pdf");
                }
                $columns = [__('pos.customer_id'), __('pos.customer_name'), __('pos.phone'), __('pos.email'), __('pos.address'), __('pos.visits'), __('pos.total_purchases') . " ($currency)", __('pos.total_paid') . " ($currency)", __('pos.balance') . " ($currency)", __('pos.responsible_user')];
                $callback = function() use ($report, $columns) {
                    $file = fopen('php://output', 'w');
                    fputs($file, "\xEF\xBB\xBF");
                    fputcsv($file, $columns);
                    foreach ($report['customers'] as $c) {
                        fputcsv($file, [$c->id, $c->name, $c->phone, $c->email, $c->address, $c->visits, $c->total_purchases, $c->total_paid, $c->balance, $c->responsible_user]);
                    }
                    fclose($file);
                };
                $filename .= ".csv";
                return response()->stream($callback, 200, array_merge($headers, ["Content-Disposition" => "attachment; filename=$filename"]));

            case 'suppliers':
                $report = $this->reportService->getSupplierReport($filters);
                if ($format === 'pdf') {
                    $pdf = Pdf::loadView('reports.pdf.suppliers', compact('report', 'filters', 'setting'));
                    return $pdf->download($filename . ".pdf");
                }
                $columns = [__('pos.supplier_number'), __('pos.supplier_name'), __('pos.email'), __('pos.address'), __('pos.total_purchases') . " ($currency)", __('pos.paid') . " ($currency)", __('pos.remaining') . " ($currency)"];
                $callback = function() use ($report, $columns) {
                    $file = fopen('php://output', 'w');
                    fputs($file, "\xEF\xBB\xBF");
                    fputcsv($file, $columns);
                    foreach ($report['suppliers'] as $s) {
                        fputcsv($file, [($s->supplier_number ?? '#'.$s->id), $s->name, $s->email, $s->address, $s->total_purchases, $s->total_paid, $s->total_remaining]);
                    }
                    fclose($file);
                };
                $filename .= ".csv";
                return response()->stream($callback, 200, array_merge($headers, ["Content-Disposition" => "attachment; filename=$filename"]));

            case 'expenses':
                $report = $this->reportService->getExpensesReport($filters);
                if ($format === 'pdf') {
                    $pdf = Pdf::loadView('reports.pdf.expenses', compact('report', 'filters', 'setting'));
                    return $pdf->download($filename . ".pdf");
                }
                $columns = [__('pos.date'), __('pos.category'), __('pos.amount') . " ($currency)", __('pos.description'), __('pos.branch')];
                $callback = function() use ($report, $columns) {
                    $file = fopen('php://output', 'w');
                    fputs($file, "\xEF\xBB\xBF");
                    fputcsv($file, $columns);
                    foreach ($report['expenses'] as $e) {
                        fputcsv($file, [$e->expense_date, $e->type, $e->amount, $e->description_en ?: $e->description_ar, $e->branch->name ?? '-']);
                    }
                    fclose($file);
                };
                $filename .= ".csv";
                return response()->stream($callback, 200, array_merge($headers, ["Content-Disposition" => "attachment; filename=$filename"]));

            case 'financial':
                $report = $this->reportService->getFinancialReport($filters);
                if ($format === 'pdf') {
                    $pdf = Pdf::loadView('reports.pdf.financial', compact('report', 'filters', 'setting'));
                    return $pdf->download($filename . ".pdf");
                }
                // Financial summary CSV is simple
                $columns = [__('pos.metric'), __('pos.total_amount') . " ($currency)"];
                $callback = function() use ($report, $columns) {
                    $file = fopen('php://output', 'w');
                    fputs($file, "\xEF\xBB\xBF");
                    fputcsv($file, $columns);
                    fputcsv($file, [__('pos.total_sales'), $report['total_sales']]);
                    fputcsv($file, [__('pos.total_purchases'), $report['total_purchases']]);
                    fputcsv($file, [__('pos.total_expenses'), $report['total_expenses']]);
                    fputcsv($file, [__('pos.net_profit'), $report['net_profit']]);
                    fclose($file);
                };
                $filename .= ".csv";
                return response()->stream($callback, 200, array_merge($headers, ["Content-Disposition" => "attachment; filename=$filename"]));
        }

        return redirect()->back()->with('error', 'Invalid export type.');
    }

    public function apiAnalytics(Request $request)
    {
        $type = $request->get('type');
        $limit = $request->get('limit', 5);
        
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $branchId = session('branch_id');
        if (auth()->user()->isAdmin() && $request->has('branch_id')) {
            $branchId = $request->get('branch_id');
            if ($branchId === 'all') $branchId = null;
        }

        $filters = [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'branch_id' => $branchId
        ];

        $data = $this->reportService->getTopAnalyticsData($type, $filters, $limit);
        return response()->json($data);
    }

    public function detailedReport(Request $request)
    {
        $type = $request->get('type', 'top-selling');
        if ($request->has('from_date')) {
            $fromDate = $request->get('from_date');
            session(['report_from_date' => $fromDate]);
        } else {
            $fromDate = session('report_from_date', now()->startOfMonth()->format('Y-m-d'));
        }

        if ($request->has('to_date')) {
            $toDate = $request->get('to_date');
            session(['report_to_date' => $toDate]);
        } else {
            $toDate = session('report_to_date', now()->format('Y-m-d'));
        }
        $branchId = session('branch_id');
        if (auth()->user()->isAdmin() && $request->has('branch_id')) {
            $branchId = $request->get('branch_id');
            if ($branchId === 'all') $branchId = null;
        }

        $filters = [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'branch_id' => $branchId
        ];

        $data = $this->reportService->getTopAnalyticsData($type, $filters, 'all');
        $setting = \App\Models\Setting::first();
        $branches = auth()->user()->isAdmin() ? Branch::all() : null;

        return view('reports.detailed', compact('data', 'type', 'filters', 'setting', 'branches'));
    }
}
