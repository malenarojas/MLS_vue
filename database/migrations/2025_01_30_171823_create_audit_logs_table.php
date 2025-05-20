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

            Schema::create('audit_logs', function (Blueprint $table) {
				$table->id(); // ID único
				$table->unsignedBigInteger('agent_id')->nullable();
				$table->unsignedBigInteger('user_id')->nullable(); // ID del usuario que realizó el cambio
				$table->string('field_name'); // Nombre del campo editado
				$table->text('old_value')->nullable(); // Valor anterior
				$table->text('new_value')->nullable(); // Nuevo valor
				$table->text('notes')->nullable(); // Notas adicionales
				$table->foreign('agent_id')->references('id')->on('agents')
                ->restrictOnDelete();
				$table->foreign('user_id')->references('id')->on('users')
				->restrictOnDelete();
				$table->timestamps(); // Fecha y hora de creación y actualización
			});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
