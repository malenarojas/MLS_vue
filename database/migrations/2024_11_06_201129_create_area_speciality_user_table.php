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
		Schema::create('area_speciality_user', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('area_speciality_id');
			$table->unsignedBigInteger('user_id');
			$table->timestamps();

			$table->foreign('area_speciality_id')->references('id')
				->on('area_specialities')->onDelete('restrict');

			$table->foreign('user_id')->references('id')
				->on('users')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('area_speciality_user');
	}
};
