<?php
// database/migrations/2026_04_26_150143_modify_budget_items_for_new_structure.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('budget_items', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('budget_items', 'currency')) {
                $table->string('currency', 3)->nullable()->after('unit_cost');
            }

            if (!Schema::hasColumn('budget_items', 'year_1')) {
                $table->decimal('year_1', 15, 2)->default(0)->after('total_amount_zmw');
            }

            if (!Schema::hasColumn('budget_items', 'year_2')) {
                $table->decimal('year_2', 15, 2)->default(0)->after('year_1');
            }

            if (!Schema::hasColumn('budget_items', 'year_3')) {
                $table->decimal('year_3', 15, 2)->default(0)->after('year_2');
            }

            if (!Schema::hasColumn('budget_items', 'exchange_rate_at_creation')) {
                $table->decimal('exchange_rate_at_creation', 10, 2)->nullable()->after('year_3');
            }
        });
    }

    public function down()
    {
        Schema::table('budget_items', function (Blueprint $table) {
            $table->dropColumn([
                'currency',
                'year_1',
                'year_2',
                'year_3',
                'exchange_rate_at_creation'
            ]);
        });
    }
};
