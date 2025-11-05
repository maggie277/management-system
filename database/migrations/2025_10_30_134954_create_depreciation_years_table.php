<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('depreciation_years', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique();
            $table->integer('position')->default(0);
            $table->string('background_color')->default('#f8f9fa');
            $table->string('text_color')->default('#000000');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('depreciation_years');
    }
};
