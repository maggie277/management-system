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
        if (!Schema::hasTable('tasks')) {
            // Create table if it does not exist
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
                $table->date('due_date')->nullable();
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->enum('assigned_type', ['staff', 'admin'])->default('staff');
                $table->timestamps();
            });
        } else {
            // Table exists, just make sure all columns exist
            Schema::table('tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('tasks', 'title')) {
                    $table->string('title')->after('id');
                }
                if (!Schema::hasColumn('tasks', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
                if (!Schema::hasColumn('tasks', 'status')) {
                    $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->after('description');
                }
                if (!Schema::hasColumn('tasks', 'due_date')) {
                    $table->date('due_date')->nullable()->after('status');
                }
                if (!Schema::hasColumn('tasks', 'assigned_to')) {
                    $table->unsignedBigInteger('assigned_to')->nullable()->after('due_date');
                }
                if (!Schema::hasColumn('tasks', 'assigned_type')) {
                    $table->enum('assigned_type', ['staff', 'admin'])->default('staff')->after('assigned_to');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop columns if table exists
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (Schema::hasColumn('tasks', 'title')) $table->dropColumn('title');
                if (Schema::hasColumn('tasks', 'description')) $table->dropColumn('description');
                if (Schema::hasColumn('tasks', 'status')) $table->dropColumn('status');
                if (Schema::hasColumn('tasks', 'due_date')) $table->dropColumn('due_date');
                if (Schema::hasColumn('tasks', 'assigned_to')) $table->dropColumn('assigned_to');
                if (Schema::hasColumn('tasks', 'assigned_type')) $table->dropColumn('assigned_type');
            });
        }
    }
};
