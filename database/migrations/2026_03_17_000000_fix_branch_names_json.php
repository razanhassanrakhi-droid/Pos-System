<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $branches = DB::table('branches')->get();

        foreach ($branches as $branch) {
            $name = $branch->name;
            
            // Check if it's already a JSON string
            $decoded = json_decode($name, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                // It's a plain string, let's convert it to JSON
                // Assuming it was intended to be Arabic based on the report ("زين")
                $newName = json_encode([
                    'ar' => $name,
                    'en' => $name // Fallback to same name for English if unknown, user can edit later
                ]);
                
                // Specific fix for "زين" if we want to be helpful
                if ($name === 'زين') {
                    $newName = json_encode([
                        'ar' => 'زين',
                        'en' => 'Zain'
                    ]);
                }

                DB::table('branches')
                    ->where('id', $branch->id)
                    ->update(['name' => $newName]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting JSON back to plain string might lose data, so we'll just leave it.
    }
};
