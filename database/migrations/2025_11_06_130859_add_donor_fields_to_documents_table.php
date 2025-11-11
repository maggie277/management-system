<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('donor_name')->nullable()->after('employee_id');
            $table->decimal('contract_value', 15, 2)->nullable()->after('donor_name');
            $table->string('currency', 10)->nullable()->after('contract_value');
            $table->string('project_name')->nullable()->after('currency');
            $table->text('reporting_requirements')->nullable()->after('project_name');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'donor_name',
                'contract_value',
                'currency',
                'project_name',
                'reporting_requirements'
            ]);
        });
    }
};
