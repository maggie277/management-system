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
        // Check if category_id column already exists
        if (!Schema::hasColumn('folders', 'category_id')) {
            Schema::table('folders', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
        // If column already exists, do nothing
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the column to avoid data loss in rollback
        // Schema::table('folders', function (Blueprint $table) {
        //     $table->dropColumn('category_id');
        // });
    }
};
