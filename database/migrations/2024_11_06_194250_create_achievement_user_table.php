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
        Schema::create('achievement_user', function (Blueprint $table) {
            $table->id();
            $table->string('achievement_date');
            $table->smallInteger('enable_achievement');
			$table->unsignedBigInteger('achievement_id');
			$table->unsignedBigInteger('user_id');
			$table->foreign('achievement_id')->references('id')->on('achievements')
				->cascadeOnDelete();
			$table->foreign('user_id')->references('id')
				->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_user');
    }
};
