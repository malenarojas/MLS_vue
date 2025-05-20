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
		Schema::create('area_specialities', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('area_id');
			$table->unsignedBigInteger('speciality_id')->nullable();
			$table->timestamps();

			$table->foreign('area_id')->references('id')->on('areas')
				->onDelete('restrict');

			/*$table->foreign('speciality_id')->references('id')
				->on('specialities')->onDelete('restrict');*/
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('area_specialities');
	}
};
