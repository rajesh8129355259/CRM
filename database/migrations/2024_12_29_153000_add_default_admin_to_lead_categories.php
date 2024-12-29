<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultAdminToLeadCategories extends Migration
{
    public function up()
    {
        Schema::table('lead_categories', function (Blueprint $table) {
            $table->foreignId('default_admin_id')->nullable()->after('is_active')
                ->constrained('admins')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('lead_categories', function (Blueprint $table) {
            $table->dropForeign(['default_admin_id']);
            $table->dropColumn('default_admin_id');
        });
    }
} 