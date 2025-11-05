<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->enum('department', ['hr', 'finance', 'programs', 'procurement', 'admin', 'general'])->default('general');
            $table->enum('document_type', [
                'staff_contract',
                'donor_contract',
                'budget',
                'policy',
                'report',
                'proposal',
                'agreement',
                'other'
            ])->default('other');
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('status', ['active', 'expired', 'draft', 'archived'])->default('active');
            $table->string('version')->default('1.0');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'department',
                'document_type',
                'effective_date',
                'expiry_date',
                'reference_number',
                'status',
                'version'
            ]);
        });
    }
};
