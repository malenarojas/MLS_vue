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
        Schema::table('listings', function (Blueprint $table) {
            $table->renameColumn('title_catchment', 'title');
            $table->renameColumn('location_neigthborhood_information', 'location_information');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->renameColumn('title', 'title_catchment');
            $table->renameColumn('location_information', 'location_neigthborhood_information');
        });
    }
};
