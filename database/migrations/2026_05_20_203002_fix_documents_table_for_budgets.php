<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Make file_path and file_name nullable (budget docs have no physical file)
        Schema::table('documents', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
            $table->string('file_name')->nullable()->change();
        });

        // 2. Expand the status enum to include budget/donor statuses
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'active','expired','draft','archived','pending','approved','expiring_soon'
        ) NOT NULL DEFAULT 'active'");

        // 3. Add missing columns (only if they don't already exist)
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'fiscal_year')) {
                $table->string('fiscal_year', 4)->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('documents', 'budget_type')) {
                $table->string('budget_type')->nullable()->after('fiscal_year');
            }
            if (!Schema::hasColumn('documents', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->nullable()->after('budget_type');
            }
            if (!Schema::hasColumn('documents', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_public');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->string('file_name')->nullable(false)->change();
        });

        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'active','expired','draft','archived'
        ) NOT NULL DEFAULT 'active'");

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['fiscal_year', 'budget_type', 'total_amount', 'is_active']);
        });
    }
};
