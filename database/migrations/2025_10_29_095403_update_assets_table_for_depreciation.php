<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Add new columns for the comprehensive asset management
            $table->string('type_make')->nullable()->after('name');
            $table->decimal('cost', 15, 2)->nullable()->after('type_make');
            $table->date('date_of_purchase')->nullable()->after('cost');
            $table->string('chasis_no')->nullable()->after('serial_number');
            $table->string('ctpd_asset_code')->nullable()->after('chasis_no');
            $table->decimal('depreciation_rate', 5, 2)->nullable()->after('status');
            $table->string('depreciation_method')->default('straight_line')->after('depreciation_rate');
            $table->decimal('accumulated_depreciation', 15, 2)->default(0)->after('depreciation_method');
            $table->decimal('net_book_value', 15, 2)->nullable()->after('accumulated_depreciation');
            $table->text('remark')->nullable()->after('location');
            $table->decimal('cost_of_disposal', 15, 2)->nullable()->after('remark');
            $table->date('disposal_date')->nullable()->after('cost_of_disposal');
            $table->string('disposal_reason')->nullable()->after('disposal_date');

            // Remove the old purchase_price and current_value columns
            $table->dropColumn(['purchase_price', 'current_value']);
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'type_make',
                'cost',
                'date_of_purchase',
                'chasis_no',
                'ctpd_asset_code',
                'depreciation_rate',
                'depreciation_method',
                'accumulated_depreciation',
                'net_book_value',
                'remark',
                'cost_of_disposal',
                'disposal_date',
                'disposal_reason'
            ]);

            // Restore old columns
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('current_value', 10, 2)->nullable();
        });
    }
};
