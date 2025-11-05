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
            // First, add the new column
            if (!Schema::hasColumn('assets', 'serial_chasis_no')) {
                $table->string('serial_chasis_no')->nullable()->after('date_of_purchase');
            }
        });

        // Now migrate data from old columns to the new one
        // Use separate statements to avoid SQLite issues
        $assets = DB::table('assets')->get();

        foreach ($assets as $asset) {
            $serialChasisNo = null;

            // Use serial_number if available, otherwise use chasis_no
            if (!empty($asset->serial_number)) {
                $serialChasisNo = $asset->serial_number;
            } elseif (!empty($asset->chasis_no)) {
                $serialChasisNo = $asset->chasis_no;
            }

            if ($serialChasisNo) {
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->update(['serial_chasis_no' => $serialChasisNo]);
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
