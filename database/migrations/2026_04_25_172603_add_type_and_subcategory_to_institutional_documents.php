<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('institutional_documents', function (Blueprint $table) {
            $table->enum('document_type', ['policies_manuals', 'templates', 'reports', 'forms'])->default('policies_manuals')->after('category');
            $table->string('subcategory')->nullable()->after('document_type');
        });
    }

    public function down()
    {
        Schema::table('institutional_documents', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'subcategory']);
        });
    }
};
