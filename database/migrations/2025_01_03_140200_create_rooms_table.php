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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->unsignedInteger('dimension_x')->nullable();
            $table->unsignedInteger('dimension_y')->nullable();
            $table->foreignId('room_type_id')->nullable()->constrained();
            $table->unsignedBigInteger('information_id')->nullable();
            $table->foreign('information_id')->references('id')->on('listings_information');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
