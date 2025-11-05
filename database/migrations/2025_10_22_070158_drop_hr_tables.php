<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the HR tables in correct order to handle foreign key constraints
        $tablesToDrop = ['hr_documents', 'hr_categories', 'hr_folders'];

        foreach ($tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
                echo "Dropped table: {$table}\n";
            }
        }
    }

    public function down(): void
    {
        // Note: We cannot easily recreate these tables since we don't know their original structure
        // This is a destructive migration, so use with caution
    }
};
