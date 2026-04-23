<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('institutional_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('category');
            $table->string('file_path');
            $table->string('file_extension');
            $table->integer('file_size');
            $table->string('version')->default('1.0');
            $table->boolean('is_public')->default(true);
            $table->integer('download_count')->default(0);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('institutional_documents');
    }
};
