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
        Schema::create('non_depreciable_assets', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('name');
            $table->string('type_make')->nullable();
            $table->decimal('cost', 15, 2);
            $table->date('date_of_purchase');
            $table->string('serial_chasis_no');
            $table->string('ctpd_asset_code')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'maintenance', 'retired', 'lost', 'disposed']);
            $table->string('location')->nullable();
            $table->text('remark')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->decimal('net_book_value', 15, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_depreciable_assets');
    }
};
