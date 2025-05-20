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
        Schema::create('feature_listing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feature_id')->nullable();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->foreign('feature_id')->references('id')->on('features')
                ->restrictOnDelete();
            $table->foreign('listing_id')->references('id')->on('listings')
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_listing');
    }
};
