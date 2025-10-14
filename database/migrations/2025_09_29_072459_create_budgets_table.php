<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('donor_id')->constrained()->cascadeOnDelete();
    $table->decimal('amount', 15, 2);
    $table->decimal('threshold', 15, 2)->nullable();
    $table->string('currency')->default('USD');
    $table->string('description')->nullable();
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('budgets');
    }
}
