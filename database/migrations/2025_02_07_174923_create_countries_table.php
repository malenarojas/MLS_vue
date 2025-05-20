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
		Schema::create('countries', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('name'); // Nombre del país
            $table->string('code', 3)->unique(); // Código ISO Alpha-2 (ej: BO, US, MX)
            $table->string('phone_code')->nullable(); // Código de teléfono (+591, +1, etc.)
            $table->text('flag_svg')->nullable(); // Código SVG de la bandera
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
