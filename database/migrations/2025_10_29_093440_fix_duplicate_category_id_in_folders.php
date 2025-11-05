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
        // Check if category_id column already exists in folders table
        if (Schema::hasColumn('folders', 'category_id')) {
            // If it exists, we'll just make sure it's properly configured
            Schema::table('folders', function (Blueprint $table) {
                // Make sure the foreign key constraint exists
                if (!Schema::hasColumn('folders', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
                }
            });
        } else {
            // If it doesn't exist, create it
            Schema::table('folders', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the column in down() to avoid data loss
    }
};
