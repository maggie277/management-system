<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('budgets', function (Blueprint $table) {
            if (!Schema::hasColumn('budgets', 'total_expenses_zmw')) {
                $table->decimal('total_expenses_zmw', 15, 2)->default(0)->after('total_budget_usd');
            }
            if (!Schema::hasColumn('budgets', 'total_expenses_usd')) {
                $table->decimal('total_expenses_usd', 15, 2)->default(0)->after('total_expenses_zmw');
            }
            if (!Schema::hasColumn('budgets', 'remaining_budget_zmw')) {
                $table->decimal('remaining_budget_zmw', 15, 2)->default(0)->after('total_expenses_usd');
            }
            if (!Schema::hasColumn('budgets', 'remaining_budget_usd')) {
                $table->decimal('remaining_budget_usd', 15, 2)->default(0)->after('remaining_budget_zmw');
            }
        });
    }

    public function down()
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn([
                'total_expenses_zmw',
                'total_expenses_usd',
                'remaining_budget_zmw',
                'remaining_budget_usd'
            ]);
        });
    }
};
