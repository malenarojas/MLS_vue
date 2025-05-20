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
        /*
        Cambios necesarios para migrar listings
        */

        Schema::table('zones', function (Blueprint $table) {
            $table->unsignedBigInteger('zone_id')->nullable();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable();
        });

        Schema::table('multimedias', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->text('description_website')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
