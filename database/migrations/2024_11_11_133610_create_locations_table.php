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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable(); // Verificar si es necesario tratarlo como numerico
            $table->string('unit_department')->nullable();
            $table->string('first_address')->nullable();
            $table->string('second_address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('district')->nullable();
            $table->string('access_number')->nullable();
            $table->boolean('show_addres_on_website');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('listing_id');
            $table->timestamps();

            $table->foreign('zone_id')->references('id')->on('zones')
                ->restrictOnDelete();

            $table->foreign('city_id')->references('id')->on('cities')
                ->restrictOnDelete();

            $table->foreign('listing_id')->references('id')->on('listings')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
