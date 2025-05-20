<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /*public function up(): void
    {
        Schema::table('multimedias', function (Blueprint $table) {
            // Modificar la relación para permitir NULL en caso de eliminación de una habitación
            $table->dropForeign(['room_id']);
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    /*public function down(): void
    {
        Schema::table('multimedias', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->restrictOnDelete();
        });
    }*/
};
