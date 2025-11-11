<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_donors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->enum('contract_type', ['monthly', 'quarterly', 'yearly']);
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->decimal('total_budget', 15, 2);
            $table->decimal('remaining_budget', 15, 2);
            $table->decimal('budget_threshold', 15, 2)->default(0);
            $table->boolean('is_budget_low')->default(false);
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('donor_responsible_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('donor_budget_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense']);
            $table->date('transaction_date');
            $table->foreignId('created_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donor_budget_transactions');
        Schema::dropIfExists('donor_responsible_persons');
        Schema::dropIfExists('donors');
    }
};
