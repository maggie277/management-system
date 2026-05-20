<?php
// database/migrations/2026_05_04_210240_add_payment_fields_to_expenses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Add user relation columns if they don't exist
            if (!Schema::hasColumn('expenses', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('notes');
                $table->foreign('created_by')->references('id')->on('users');
            }

            if (!Schema::hasColumn('expenses', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                $table->foreign('updated_by')->references('id')->on('users');
            }

            // Add approved fields if they don't exist
            if (!Schema::hasColumn('expenses', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('updated_by');
                $table->foreign('approved_by')->references('id')->on('users');
            }

            if (!Schema::hasColumn('expenses', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            // Add payment fields
            if (!Schema::hasColumn('expenses', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('approved_at');
            }

            if (!Schema::hasColumn('expenses', 'paid_by')) {
                $table->unsignedBigInteger('paid_by')->nullable()->after('paid_at');
                $table->foreign('paid_by')->references('id')->on('users');
            }

            // Add rejection fields
            if (!Schema::hasColumn('expenses', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('paid_by');
            }

            if (!Schema::hasColumn('expenses', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
                $table->foreign('rejected_by')->references('id')->on('users');
            }

            if (!Schema::hasColumn('expenses', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_by');
            }
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['paid_by']);
            $table->dropForeign(['rejected_by']);

            // Drop columns
            $table->dropColumn([
                'created_by',
                'updated_by',
                'approved_by',
                'approved_at',
                'paid_at',
                'paid_by',
                'rejected_at',
                'rejected_by',
                'rejection_reason'
            ]);
        });
    }
};
