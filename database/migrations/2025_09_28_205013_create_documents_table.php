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
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('title'); // Name of the document
            $table->text('description')->nullable(); // Optional description
            $table->string('file_path'); // Path to the uploaded file
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Who uploaded it
            $table->timestamps(); // Created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
