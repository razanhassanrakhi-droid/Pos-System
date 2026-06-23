<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$purchases = \App\Models\Purchase::orderBy('id')->get();
foreach($purchases as $index => $purchase) {
    $purchase->invoice_number = 'PUR-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
    $purchase->save();
}
echo "Updated " . count($purchases) . " purchases.\n";

$sales = \App\Models\Sale::orderBy('id')->get();
foreach($sales as $index => $sale) {
    $sale->invoice_number = 'INV-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
    $sale->save();
}
echo "Updated " . count($sales) . " sales.\n";
