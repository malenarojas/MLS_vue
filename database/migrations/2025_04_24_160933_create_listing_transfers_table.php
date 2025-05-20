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
        Schema::create('listing_transfers', function (Blueprint $table) {
            $table->id();

            // Agente anterior
            $table->string('previous_key')->nullable();
            $table->string('previous_mlsid')->nullable();
            $table->string('previous_agent_id')->nullable();
            $table->string('previous_agent_name')->nullable();
            $table->string('previous_office_id')->nullable();
            $table->string('previous_office_name')->nullable();

            // Agente nuevo
            $table->string('new_key')->nullable();
            $table->string('new_mlsid')->nullable();
            $table->string('new_agent_id')->nullable();
            $table->string('new_agent_name')->nullable();
            $table->string('new_office_id')->nullable();
            $table->string('new_office_name')->nullable();

            $table->timestamp('transferred_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_transfers');
    }
};
