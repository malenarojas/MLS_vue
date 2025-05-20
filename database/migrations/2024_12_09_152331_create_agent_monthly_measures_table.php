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
        Schema::create('agent_monthly_measures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->integer('active_listings')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->nullOnDelete();
            $table->foreign('office_id')->references('id')->on('offices')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_monthly_measures');
    }
};
