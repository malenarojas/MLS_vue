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
        Schema::create('listing_prices', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();

            $table->foreign('listing_id')->references('id')
                ->on('listings')->restrictOnDelete();

            $table->foreign('currency_id')->references('id')
                ->on('currencies')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_prices');
    }
};
