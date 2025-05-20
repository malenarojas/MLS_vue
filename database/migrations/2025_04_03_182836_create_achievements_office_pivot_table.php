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
        /*Schema::create('achievements_office_pivot', function (Blueprint $table) {
            $table->id();
            $table->date('achievement_date')->nullable();
            $table->boolean('enable_achievement')->default(false);
            $table->unsignedBigInteger('achievement_id');
            $table->unsignedBigInteger('office_id');
            $table->foreign('achievement_id')->references('id')->on('achievements_offices')->cascadeOnDelete();
            $table->foreign('office_id')->references('id')->on('offices')->cascadeOnDelete();
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements_office');
    }
};
