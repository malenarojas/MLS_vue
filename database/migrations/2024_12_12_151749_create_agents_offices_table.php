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
        Schema::create('agent_offices', function (Blueprint $table) {
            $table->id();
            $table->string('date_transferred');
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->timestamps();

            // Definir la llave foránea hacia la tabla offices
            $table->foreign('agent_id')->references('id')->on('agents')
                ->onDelete('restrict'); // Puedes ajustar el comportamiento onDelete si es necesario
            // Definir la llave foránea hacia la tabla offices
            $table->foreign('office_id')->references('id')->on('offices')
                ->onDelete('restrict'); // Puedes ajustar el comportamiento onDelete si es necesario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents_offices');
    }
};
