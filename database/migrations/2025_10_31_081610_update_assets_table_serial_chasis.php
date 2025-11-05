<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            // First, just add the new column
            if (!Schema::hasColumn('assets', 'serial_chasis_no')) {
                $table->string('serial_chasis_no')->nullable()->after('date_of_purchase');
            }
        });

        // Now safely migrate data using Laravel's query builder (not raw SQL)
        // This avoids SQLite issues with COALESCE in schema operations
        $assets = DB::table('assets')->select(['id', 'serial_number', 'chasis_no'])->get();

        foreach ($assets as $asset) {
            $newValue = null;

            // Use serial_number if available, otherwise chasis_no
            if (!empty($asset->serial_number)) {
                $newValue = $asset->serial_number;
            } elseif (!empty($asset->chasis_no)) {
                $newValue = $asset->chasis_no;
            }

            // Update the new field if we have a value
            if ($newValue) {
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->update(['serial_chasis_no' => $newValue]);
            }
        }
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'serial_chasis_no')) {
                $table->dropColumn('serial_chasis_no');
            }
        });
    }
};
