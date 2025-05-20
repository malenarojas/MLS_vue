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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('MLSID')->unique(); // RegionOficinaAgente-Numero
            $table->date('date_of_listing')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('reference')->nullable();
            $table->string('property_registration_number')->nullable();
            $table->string('title_catchment')->nullable();
            $table->string('description_website')->nullable();
            $table->string('marketing_description')->nullable();
            $table->string('location_neigthborhood_information')->nullable();
            $table->string('financial_note')->nullable();
            $table->date('first_upload_date')->nullable();
            // $table->string('market_segment')->nullable();
            $table->boolean('is_published')->default(false);

            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('contract_type_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('status_listing_id')->nullable();
            $table->unsignedBigInteger('price_type_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable(); // Agente principal

            // Columna para indicar si el registro está en estado de borrador
            $table->boolean('is_draft')->default(true);

            $table->foreign('project_id')->references('id')->on('projects')
                ->restrictOnDelete();

            $table->foreign('contract_type_id')->references('id')->on('contract_types')
                ->restrictOnDelete();

            $table->foreign('area_id')->references('id')->on('areas')
                ->restrictOnDelete();

            $table->foreign('status_listing_id')->references('id')->on('status_listings')
                ->restrictOnDelete();

            $table->foreign('price_type_id')->references('id')->on('price_types')
                ->restrictOnDelete();

            $table->foreign('agent_id')->references('id')->on('agents')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
