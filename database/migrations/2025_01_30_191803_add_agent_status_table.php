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
		Schema::table('agents', function (Blueprint $table) {
			$table->dropColumn('is_active');
			$table->unsignedBigInteger('agent_status_id')->nullable();

			$table->foreign('agent_status_id')
				->references('id')
				->on('agents_statuses')
				->restrictOnDelete();  // Acciones en caso de eliminación (por ejemplo, eliminar en cascada)
			});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
