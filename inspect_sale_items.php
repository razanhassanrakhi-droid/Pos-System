<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = Schema::getColumnListing('sale_items');
print_r($columns);

$sale_items = DB::table('sale_items')->limit(1)->get();
print_r($sale_items);
