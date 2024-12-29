<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('lead_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add category_id to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('status')->constrained('lead_categories')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        Schema::dropIfExists('lead_categories');
    }
} 