<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PurchaseItem;

try {
    $items = PurchaseItem::whereIn('purchase_id', [45, 46])->get();
    foreach ($items as $item) {
        echo "Purchase ID: " . $item->purchase_id . " | Product ID: " . $item->product_id . " | Qty: " . $item->quantity . " | Unit: " . $item->unit_name . " | Factor: " . $item->conversion_factor . " | Price: " . $item->purchase_price . " | Expiry: " . $item->expiry_date . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
