<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->foreignId('budget_item_id')->nullable()->constrained()->onDelete('set null');
            $table->string('expense_number', 50);
            $table->string('description');
            $table->text('purpose')->nullable();
            $table->decimal('amount_zmw', 15, 2);
            $table->decimal('amount_usd', 15, 2);
            $table->date('expense_date');
            $table->string('category');
            $table->string('payment_method')->nullable();
            $table->string('vendor_payee')->nullable();
            $table->string('status')->default('pending');
            $table->string('receipt_number')->nullable();
            $table->string('receipt_file')->nullable();
            $table->text('notes')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
