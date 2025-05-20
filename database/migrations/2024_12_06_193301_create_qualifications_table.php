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
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
			$table->float('qualification');
            $table->string('comment');
            $table->string('reference_type');
            $table->unsignedBigInteger('agent_id')->nullable(); // Si las calificaciones pueden no estar asociadas inicialmente
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualifications');
    }
};
