<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->string('return_number')->nullable()->unique()->after('id');
            $table->index('return_number');
        });
        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->string('adjustment_number')->nullable()->unique()->after('id');
            $table->index('adjustment_number');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_number')->nullable()->unique()->after('id');
            $table->index('customer_number');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->string('category_number')->nullable()->unique()->after('id');
            $table->index('category_number');
        });
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('movement_number')->nullable()->unique()->after('id');
            $table->index('movement_number');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_number')->nullable()->unique()->after('id');
            $table->index('product_number');
        });

        // Populate existing customers
        $customers = DB::table('customers')->orderBy('id', 'asc')->get();
        $seq = 1;
        foreach ($customers as $c) {
            DB::table('customers')->where('id', $c->id)->update([
                'customer_number' => 'CUS-' . str_pad($seq++, 5, '0', STR_PAD_LEFT)
            ]);
        }

        // Populate/standardize existing suppliers
        $suppliers = DB::table('suppliers')->orderBy('id', 'asc')->get();
        $seq = 1;
        foreach ($suppliers as $s) {
            DB::table('suppliers')->where('id', $s->id)->update([
                'supplier_number' => 'SUP-' . str_pad($seq++, 5, '0', STR_PAD_LEFT)
            ]);
        }

        // Populate existing products
        $products = DB::table('products')->orderBy('id', 'asc')->get();
        $seq = 1;
        foreach ($products as $p) {
            DB::table('products')->where('id', $p->id)->update([
                'product_number' => 'PRD-' . str_pad($seq++, 5, '0', STR_PAD_LEFT)
            ]);
        }

        // Populate existing categories
        $categories = DB::table('categories')->orderBy('id', 'asc')->get();
        $seq = 1;
        foreach ($categories as $cat) {
            DB::table('categories')->where('id', $cat->id)->update([
                'category_number' => 'CAT-' . str_pad($seq++, 5, '0', STR_PAD_LEFT)
            ]);
        }

        // Populate existing batches
        $batches = DB::table('batches')->orderBy('id', 'asc')->get();
        $dailySeqs = [];
        foreach ($batches as $b) {
            $date = date('Ymd', strtotime($b->created_at ?: now()));
            if (!isset($dailySeqs[$date])) {
                $dailySeqs[$date] = 1;
            }
            $num = 'BAT-' . $date . '-' . str_pad($dailySeqs[$date]++, 5, '0', STR_PAD_LEFT);
            DB::table('batches')->where('id', $b->id)->update([
                'batch_number' => $num
            ]);
        }

        // Populate existing sales
        $sales = DB::table('sales')->orderBy('id', 'asc')->get();
        $dailySeqs = [];
        foreach ($sales as $s) {
            $date = date('Ymd', strtotime($s->created_at ?: now()));
            if (!isset($dailySeqs[$date])) {
                $dailySeqs[$date] = 1;
            }
            $num = 'INV-' . $date . '-' . str_pad($dailySeqs[$date]++, 5, '0', STR_PAD_LEFT);
            DB::table('sales')->where('id', $s->id)->update([
                'invoice_number' => $num
            ]);
        }

        // Populate existing purchases
        $purchases = DB::table('purchases')->orderBy('id', 'asc')->get();
        $dailySeqs = [];
        foreach ($purchases as $p) {
            $date = date('Ymd', strtotime($p->created_at ?: now()));
            if (!isset($dailySeqs[$date])) {
                $dailySeqs[$date] = 1;
            }
            $num = 'PUR-' . $date . '-' . str_pad($dailySeqs[$date]++, 5, '0', STR_PAD_LEFT);
            DB::table('purchases')->where('id', $p->id)->update([
                'invoice_number' => $num
            ]);
        }

        // Populate existing sales_returns
        $returns = DB::table('sales_returns')->orderBy('id', 'asc')->get();
        $dailySeqs = [];
        foreach ($returns as $r) {
            $date = date('Ymd', strtotime($r->created_at ?: now()));
            if (!isset($dailySeqs[$date])) {
                $dailySeqs[$date] = 1;
            }
            $num = 'RET-' . $date . '-' . str_pad($dailySeqs[$date]++, 5, '0', STR_PAD_LEFT);
            DB::table('sales_returns')->where('id', $r->id)->update([
                'return_number' => $num
            ]);
        }

        // Populate existing inventory_adjustments
        $adjustments = DB::table('inventory_adjustments')->orderBy('id', 'asc')->get();
        $dailySeqs = [];
        foreach ($adjustments as $adj) {
            $date = date('Ymd', strtotime($adj->created_at ?: now()));
            if (!isset($dailySeqs[$date])) {
                $dailySeqs[$date] = 1;
            }
            $num = 'ADJ-' . $date . '-' . str_pad($dailySeqs[$date]++, 5, '0', STR_PAD_LEFT);
            DB::table('inventory_adjustments')->where('id', $adj->id)->update([
                'adjustment_number' => $num
            ]);
        }

        // Populate existing stock_movements
        $movements = DB::table('stock_movements')->orderBy('id', 'asc')->get();
        $dailySeqs = [];
        foreach ($movements as $m) {
            $date = date('Ymd', strtotime($m->created_at ?: now()));
            $prefix = $m->type === 'transfer' ? 'TRF' : 'MOV';
            $key = $prefix . '-' . $date;
            if (!isset($dailySeqs[$key])) {
                $dailySeqs[$key] = 1;
            }
            $num = $prefix . '-' . $date . '-' . str_pad($dailySeqs[$key]++, 5, '0', STR_PAD_LEFT);
            DB::table('stock_movements')->where('id', $m->id)->update([
                'movement_number' => $num
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->dropColumn('return_number');
        });
        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->dropColumn('adjustment_number');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_number');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('category_number');
        });
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('movement_number');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_number');
        });
    }
};
