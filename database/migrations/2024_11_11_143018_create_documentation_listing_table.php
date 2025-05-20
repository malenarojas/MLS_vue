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
        Schema::create('documentation_listing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documentation_id');
            $table->unsignedBigInteger('listing_id');
            $table->timestamps();

            $table->foreign('documentation_id')->references('id')
                ->on('documentations')->onDelete('cascade');

            $table->foreign('listing_id')->references('id')
                ->on('listings')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_listing');
    }
};
