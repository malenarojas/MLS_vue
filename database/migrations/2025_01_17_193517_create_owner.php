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
        // Lo vuelvo a crear, porque al eliminar la tabla intermedia se borra en cascada
        Schema::create('owner', function (Blueprint $table) {
            $table->id();
            // Crear columnas de llave foránea
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')
                ->on('contacts')->restrictOnDelete();

            $table->unsignedBigInteger('listing_id')->nullable();
            $table->foreign('listing_id')->references('id')
                ->on('listings')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner');
    }
};
