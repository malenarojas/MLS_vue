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
        Schema::table('multimedias', function (Blueprint $table) {
            $table->integer('defaulf')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->foreign('room_id')->references('id')
                ->on('rooms')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multimedias', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['room_id']);
            // Eliminar las columnas añadidas
            $table->dropColumn(['defaulf', 'room_id']);
        });
    }
};
