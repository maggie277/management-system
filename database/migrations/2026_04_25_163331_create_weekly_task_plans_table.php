<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weekly_task_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('week_start');
            $table->json('planned_tasks');
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned');
            $table->json('completed_tasks')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'week_start']);
            $table->index('week_start');
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_task_plans');
    }
};
