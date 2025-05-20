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
        Schema::table('locations', function (Blueprint $table) {

            $table->unsignedBigInteger('type_floor_id')->nullable()->after('listing_id');

            $table->foreign('type_floor_id')->references('id')->on('type_floors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // $table->dropForeign(['type_floor_id']);
            // $table->dropColumn('type_floor_id');
        });
    }
};