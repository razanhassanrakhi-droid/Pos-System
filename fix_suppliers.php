<?php
$latest = \App\Models\Supplier::where('supplier_number', 'like', 'SUP-%')
    ->whereRaw('LENGTH(supplier_number) = 9')
    ->orderBy('supplier_number', 'desc')
    ->first();

$bad = \App\Models\Supplier::where('supplier_number', 'like', 'SUP-2026%')->get();

foreach($bad as $b) {
    $seq = 1;
    if ($latest) {
        $seq = (int)str_replace('SUP-', '', $latest->supplier_number) + 1;
    }
    $b->supplier_number = sprintf('SUP-%05d', $seq);
    $b->save();
    $latest = $b;
    echo 'Fixed: ' . $b->supplier_number . "\n";
}
