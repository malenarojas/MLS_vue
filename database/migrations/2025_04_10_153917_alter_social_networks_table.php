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
       /* Schema::table('social_networks', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id')->nullable()->change();
            $table->unsignedBigInteger('office_id')->nullable()->after('agent_id');
            $table->unsignedBigInteger('team_management_id')->nullable()->after('office_id');

            $table->foreign('office_id')
                  ->references('id')->on('offices')
                  ->onDelete('cascade');
            $table->foreign('team_management_id')
                  ->references('id')->on('team_management')
                  ->onDelete('cascade');
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            /*Schema::table('social_networks', function (Blueprint $table) {
                $table->dropForeign(['office_id']);
                $table->dropForeign(['team_management_id']);
                $table->dropColumn(['office_id', 'team_management_id']);
                $table->unsignedBigInteger('agent_id')->nullable(false)->change();
            });*/
    }
};
