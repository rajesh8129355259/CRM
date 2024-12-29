<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('lead_activities', 'description')) {
            Schema::table('lead_activities', function (Blueprint $table) {
                $table->text('description')->nullable()->after('activity_type');
            });
        }

        if (!Schema::hasColumn('lead_activities', 'changes')) {
            Schema::table('lead_activities', function (Blueprint $table) {
                $table->json('changes')->nullable()->after('description');
            });
        }
    }

    public function down()
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            if (Schema::hasColumn('lead_activities', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('lead_activities', 'changes')) {
                $table->dropColumn('changes');
            }
        });
    }
}; 