<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->decimal('amount', 15, 2);
    $table->date('expense_date');
    $table->text('notes')->nullable();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
}
