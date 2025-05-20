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
            /*Schema::table('offices', function (Blueprint $table) {
                $table->string('schedule_weekdays')->nullable(); // Ej: "Lunes a Viernes 08:30 - 18:00"
                $table->string(column: 'schedule_saturday')->nullable(); // Ej: "Sábado 09:00 - 13:00"
            });*/

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn([
                'schedule_weekdays',
                'schedule_saturday',
            ]);
        });
    }
};
