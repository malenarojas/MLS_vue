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
        Schema::create('listing_status_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_status_id')->constrained('status_listings')->onDelete('cascade'); // El estado de origen
            $table->foreignId('to_status_id')->constrained('status_listings')->onDelete('cascade'); // El estado de destino

            $table->timestamps();

            // Asegurar que no haya transiciones duplicadas
            $table->unique(['from_status_id', 'to_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_status_transitions');
    }
};
