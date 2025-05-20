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
        Schema::create('agent_listing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('listing_id');
            $table->unsignedTinyInteger('type')->default(1); // 1: Agente secundario, 2: Agente terciario, ...
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')
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
        Schema::dropIfExists('agent_listing');
    }
};
