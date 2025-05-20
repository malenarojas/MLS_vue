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
        Schema::create('social_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->smallInteger('state')->nullable();;
			$table->string('url')->nullable();
            $table->unsignedBigInteger('agent_id'); // Llave foránea
            $table->timestamps();

            // Definimos la relación con agents
            $table->foreign('agent_id')
                  ->references('id')->on('agents')
                  ->onDelete('cascade'); // Elimina redes sociales si el agente se elimina
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_networks');
    }
};
