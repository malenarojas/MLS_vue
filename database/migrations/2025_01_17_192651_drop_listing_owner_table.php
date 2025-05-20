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
        Schema::dropIfExists('listing_owner');
        Schema::dropIfExists('owners');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ojo no da
        // Schema::create('listing_owner', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('owner_id');
        //     $table->unsignedBigInteger('listing_id');
        //     $table->foreign('owner_id')->references('id')
        //         ->on('owners')->restrictOnDelete();
        //     $table->foreign('listing_id')->references('id')
        //         ->on('listings')->restrictOnDelete();
        //     $table->timestamps();
        // });
    }
};
