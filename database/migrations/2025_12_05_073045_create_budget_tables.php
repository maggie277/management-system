<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Main budgets table
        if (!Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->string('project_title');
                $table->text('project_goal');
                $table->string('project_code', 50)->unique(); // CHANGED FROM project_id
                $table->string('contact_person');
                $table->string('contact_email');
                $table->string('contact_phone', 20);
                $table->string('duration');
                $table->decimal('exchange_rate', 10, 2)->default(25.00);
                $table->decimal('total_budget_zmw', 15, 2);
                $table->decimal('total_budget_usd', 15, 2);
                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('updated_by')->constrained('users');
                $table->string('status')->default('draft');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Budget items table for detailed breakdown
        if (!Schema::hasTable('budget_items')) {
            Schema::create('budget_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('budget_id')->constrained()->onDelete('cascade');
                $table->string('section');
                $table->string('objective')->nullable();
                $table->string('activity')->nullable();
                $table->string('component')->nullable();
                $table->string('description_cost_category');
                $table->string('description_cost_item');
                $table->integer('number')->default(1);
                $table->string('frequency');
                $table->string('unit');
                $table->decimal('unit_cost', 12, 2);
                $table->decimal('total_amount_zmw', 15, 2);
                $table->decimal('total_amount_usd', 15, 2);
                $table->decimal('revised_year_1', 15, 2)->nullable();
                $table->decimal('revised_year_2', 15, 2)->nullable();
                $table->decimal('revised_year_3', 15, 2)->nullable();
                $table->text('comments')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
    }
};
