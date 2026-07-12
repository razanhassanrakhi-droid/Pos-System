<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\InventoryAdjustmentController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
Route::middleware([SetLocale::class])->group(function () {
    // Routes عامة
    Route::get('/', function() { return redirect('/login'); })->name('home');
    Route::get('/login', function () { return view('auth.login'); })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/change-language/{lang}', [AuthController::class, 'changeLanguage'])->name('language.switch');

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'verifyUser'])->name('password.verify.user');
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('password.otp.verify');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.otp.process');
    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset.get');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.reset.update');

    // Routes تحتاج تسجيل دخول
    Route::middleware(['auth', 'branch'])->group(function () {
        // Dashboard Route
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Settings Routes
        Route::get('/api/alerts/realtime', [\App\Http\Controllers\AlertController::class, 'getRealtimeAlerts'])->name('alerts.realtime')->middleware('permission:view-alerts');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Notification System Routes
        Route::get('/api/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.api');
        Route::post('/api/notifications/read/{id?}', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/api/notifications/dismiss/{id}', [\App\Http\Controllers\NotificationController::class, 'dismiss'])->name('notifications.dismiss');
        Route::get('/settings/notifications', [\App\Http\Controllers\NotificationController::class, 'showSettings'])->name('settings.notifications');
        Route::post('/settings/notifications', [\App\Http\Controllers\NotificationController::class, 'saveSettings'])->name('settings.notifications.save');
        Route::get('/settings/activity-feed', [\App\Http\Controllers\NotificationController::class, 'activityFeed'])->name('settings.activity_feed');

        // Routes خاصة Admin فقط
        Route::middleware(['admin'])->group(function () {
            Route::resource('users', UserController::class);
            Route::resource('branches', BranchController::class)->middleware('permission:view-branches');
            
            // Permissions Management
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:manage-permissions');
            Route::get('permissions/edit/{user}', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:manage-permissions');
            Route::put('permissions/update/{user}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:manage-permissions');
        });

        // Products Routes
        Route::get('/products', [ProductController::class, 'index'])->name('products.index')->middleware('permission:view-products');
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'getByBarcode'])->name('products.barcode')->middleware('permission:view-products|create-sales');
        Route::get('/products/batches/{product}', [ProductController::class, 'getBatches'])->name('products.batches')->middleware('permission:view-products|create-sales');
        Route::get('/products/units/{product}', [ProductController::class, 'getUnits'])->name('products.units')->middleware('permission:view-products|create-sales');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store')->middleware('permission:create-products');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('permission:edit-products');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware('permission:delete-products');
        Route::post('/products/quick-store', [ProductController::class, 'quickStore'])->name('products.quick_store')->middleware('permission:create-products');
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search')->middleware('permission:view-products|create-sales');
        Route::post('/products/bulk-status', [ProductController::class, 'bulkStatus'])->name('products.bulk_status')->middleware('permission:edit-products');
        Route::get('/products/{product}/movements', [ProductController::class, 'movements'])->name('products.movements')->middleware('permission:view-products');
        Route::put('/products/batches/{batch}', [ProductController::class, 'updateBatch'])->name('products.batches.update')->middleware('permission:edit-products');

        // Categories Routes
        Route::resource('categories', CategoryController::class)->except(['show'])->middleware('permission:view-categories');

        // Branch Switch Route
        Route::get('/branches/switch/{id}', [BranchController::class, 'switch'])->name('branches.switch'); // Publicly accessible to assigned branches

        // Customers Routes
        Route::middleware('permission:view-customers')->group(function () {
            Route::resource('customers', \App\Http\Controllers\CustomerController::class)
                ->only(['index', 'store', 'show', 'update', 'destroy']);
        });

        // Purchases Routes
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index')->middleware('permission:view-purchases');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create')->middleware('permission:create-purchases');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store')->middleware('permission:create-purchases');
        Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show')->middleware('permission:view-purchases');
        Route::get('/purchases/{id}/pdf', [PurchaseController::class, 'downloadPdf'])->name('purchases.pdf')->middleware('permission:view-purchases');
        Route::get('/purchases/{id}/print', [PurchaseController::class, 'print'])->name('purchases.print')->middleware('permission:view-purchases');
        Route::post('/purchases/{id}/payments', [PurchaseController::class, 'addPayment'])->name('purchases.payments.store')->middleware('permission:view-purchases');
        Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy')->middleware('permission:delete-purchases');

        // Sales Routes
        Route::middleware('permission:view-sales|create-sales')->group(function () {
            Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
            Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        });

        Route::middleware('permission:view-sales')->group(function () {
            Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
            Route::get('/sales/{id}', [SaleController::class, 'show'])->name('sales.show');
            Route::get('/sales/{id}/pdf', [SaleController::class, 'downloadPdf'])->name('sales.pdf');
            Route::get('/sales/{id}/print', [SaleController::class, 'print'])->name('sales.print');
            Route::post('/sales/{id}/payments', [SaleController::class, 'addPayment'])->name('sales.payments.store');
            Route::delete('/sales/{id}', [SaleController::class, 'destroy'])->name('sales.destroy')->middleware('permission:delete-sales');

            // Sales Returns
            Route::prefix('sales-returns')->group(function () {
                Route::get('/', [\App\Http\Controllers\SalesReturnController::class, 'index'])->name('sales_returns.index')->middleware('permission:view-sales-returns');
                Route::get('/create', [\App\Http\Controllers\SalesReturnController::class, 'create'])->name('sales_returns.create')->middleware('permission:create-sales-returns');
                Route::post('/store', [\App\Http\Controllers\SalesReturnController::class, 'store'])->name('sales_returns.store')->middleware('permission:create-sales-returns');
                Route::get('/search', [\App\Http\Controllers\SalesReturnController::class, 'searchSale'])->name('sales_returns.search')->middleware('permission:view-sales-returns');
                Route::delete('/{id}', [\App\Http\Controllers\SalesReturnController::class, 'destroy'])->name('sales_returns.destroy')->middleware('permission:delete-sales-returns');
            });
        });

        // Warranty Routes
        Route::middleware('permission:view-warranties')->group(function () {
            Route::get('/warranties', [\App\Http\Controllers\WarrantyController::class, 'index'])->name('warranties.index');
            Route::get('/warranties/{id}', [\App\Http\Controllers\WarrantyController::class, 'show'])->name('warranties.show');
            Route::get('/warranties/{id}/print', [\App\Http\Controllers\WarrantyController::class, 'print'])->name('warranties.print');
        });
        Route::middleware('permission:create-warranties')->group(function () {
            Route::post('/warranties/upsert', [\App\Http\Controllers\WarrantyController::class, 'upsert'])->name('warranties.upsert');
            Route::post('/warranties/{id}/claims', [\App\Http\Controllers\WarrantyController::class, 'storeClaim'])->name('warranties.claims.store');
            Route::put('/warranties/claims/{claim_id}', [\App\Http\Controllers\WarrantyController::class, 'updateClaim'])->name('warranties.claims.update');
            Route::delete('/warranties/claims/{claim_id}', [\App\Http\Controllers\WarrantyController::class, 'destroyClaim'])->name('warranties.claims.destroy');
            Route::put('/warranties/{id}', [\App\Http\Controllers\WarrantyController::class, 'update'])->name('warranties.update');
        });

        // Settings & License Routes
        Route::middleware('permission:manage-settings')->group(function () {
            Route::get('/settings/company', function () { return view('settings.company'); })->name('settings.company');
            Route::post('/settings/company', function () { return redirect()->route('settings.company')->with('success', 'Company settings updated successfully.'); })->name('settings.company.update');
            
            // Notification settings and change password routes remain under settings/
            // (all license request, activation, manager routes removed)
        });
        
        Route::get('/settings/profile', [AuthController::class, 'showProfile'])->name('settings.profile');
        Route::get('/settings/password', [AuthController::class, 'showChangePassword'])->name('settings.password');
        Route::post('/settings/password', [AuthController::class, 'changePassword'])->name('settings.password.update');
        
        // Suppliers Routes
        Route::post('/suppliers/quick-store', [SupplierController::class, 'quickStore'])->name('suppliers.quick_store')->middleware('permission:view-suppliers');
        Route::resource('suppliers', SupplierController::class)->middleware('permission:view-suppliers');

        // Expenses Routes
        Route::middleware(['auth', 'permission:view-expenses'])->group(function () {
            Route::get('/daily-expenses/{expense}/print', [\App\Http\Controllers\ExpenseController::class, 'print'])->name('expenses.print');
            Route::resource('daily-expenses', \App\Http\Controllers\ExpenseController::class)
                ->names('expenses')
                ->parameters(['daily-expenses' => 'expense']);
        });
        // Inventory Adjustments Routes
        Route::resource('adjustments', InventoryAdjustmentController::class)
            ->only(['index', 'show'])
            ->middleware('permission:view-adjustments');
            
        Route::resource('adjustments', InventoryAdjustmentController::class)
            ->only(['store'])
            ->middleware('permission:create-adjustments');
            
        Route::resource('adjustments', InventoryAdjustmentController::class)
            ->only(['destroy'])
            ->middleware('permission:delete-adjustments');
        
        // Reports
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index')->middleware('permission:view-reports');
        Route::get('/reports/export/{type}', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export')->middleware('permission:view-reports');
        Route::get('/reports/top-analytics', [\App\Http\Controllers\ReportController::class, 'apiAnalytics'])->name('reports.top_analytics')->middleware('permission:view-reports');
        Route::get('/reports/detailed', [\App\Http\Controllers\ReportController::class, 'detailedReport'])->name('reports.detailed')->middleware('permission:view-reports');

        // Expense Types
        Route::middleware('permission:manage-expense-types')->group(function () {
            Route::post('/expense-types', [\App\Http\Controllers\ExpenseTypeController::class, 'store'])->name('expense-types.store');
            Route::delete('/expense-types/{id}', [\App\Http\Controllers\ExpenseTypeController::class, 'destroy'])->name('expense-types.destroy');
        });

        // Logout Route
        Route::post('/logout', function () {
            return redirect()->route('login');
        })->name('logout');
        
    });
});
