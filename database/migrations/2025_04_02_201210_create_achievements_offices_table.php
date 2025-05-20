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
       /* Schema::create('achievements_offices', function (Blueprint $table) {
            $table->id();
            $table->string('name_achievements'); // Nombre visible del logro
            $table->string('achievement_description'); // Descripción visible
            $table->string('image')->nullable(); // Imagen opcional
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements_offices');
    }
};
