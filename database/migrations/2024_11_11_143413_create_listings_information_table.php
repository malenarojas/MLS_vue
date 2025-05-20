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
        Schema::create('listings_information', function (Blueprint $table) {
            $table->id();
            $table->date('available_date')->nullable();
            $table->string('year_construction')->nullable();
            $table->string('parking_slots')->nullable();
            $table->integer('plant_numbers')->nullable();
            $table->integer('number_departments')->nullable();
            $table->integer('sale_sign')->nullable();
            $table->double('total_area')->nullable();
            $table->double('cubic_volume')->nullable();
            $table->double('land_m2')->nullable();
            $table->double('land_x')->nullable();
            $table->double('land_y')->nullable();
            $table->double('construction_area_m')->nullable();
            $table->integer('total_number_rooms')->nullable();
            $table->integer('number_bathrooms')->nullable();
            $table->integer('number_bedrooms')->nullable();
            $table->integer('number_toiletrooms')->nullable();

            $table->unsignedBigInteger('subtype_property_id')->nullable(); //Esperar
            $table->unsignedBigInteger('market_status_id')->nullable();
            $table->unsignedBigInteger('state_property_id')->nullable();
            $table->unsignedBigInteger('property_category_id')->nullable();
            $table->unsignedBigInteger('land_use_id')->nullable();
            $table->unsignedBigInteger('land_category_id')->nullable();
            $table->unsignedBigInteger('parking_type_id')->nullable();
            $table->unsignedBigInteger('listing_id')->nullable();

            $table->foreign('subtype_property_id')->references('id')
                ->on('subtype_properties')->restrictOnDelete();

            $table->foreign('market_status_id')->references('id')
                ->on('market_status')->restrictOnDelete();

            $table->foreign('state_property_id')->references('id')
                ->on('state_properties')->restrictOnDelete();

            $table->foreign('property_category_id')->references('id')
                ->on('properties_category')->restrictOnDelete();

            $table->foreign('land_use_id')->references('id')
                ->on('land_uses')->restrictOnDelete();

            $table->foreign('land_category_id')->references('id')
                ->on('land_category')->restrictOnDelete();

            $table->foreign('parking_type_id')->references('id')
                ->on('parking_types')->restrictOnDelete();

            $table->foreign('listing_id')->references('id')
                ->on('listings')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings_information');
    }
};
