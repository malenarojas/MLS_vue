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
        Schema::create('parkings', function (Blueprint $table) {
            $table->id();
            $table->integer('nro_spaces');

            $table->unsignedBigInteger('parking_type_id');
            $table->unsignedBigInteger('listing_information_id');
            
            $table->foreign('parking_type_id')->references('id')
            ->on('parking_types')->restrictOnDelete();
            $table->foreign('listing_information_id')->references('id')
            ->on('listings_information')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};
