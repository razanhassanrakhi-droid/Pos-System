<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$customers = \App\Models\Customer::all();
foreach ($customers as $c) {
    $salesCount = \App\Models\Sale::where('customer_id', $c->id)->count();
    echo $c->name . " -> Sales: " . $salesCount . " - Visits: " . $c->visit_count . "\n";
}
