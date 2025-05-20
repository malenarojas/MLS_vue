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
		Schema::create('agents', function (Blueprint $table) {
            $table->id();
		    $table->string('agent_internal_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
			$table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();

			$table->string('date_joined')->nullable();
			$table->string('date_termination')->nullable();

			$table->integer('is_active')->nullable();
			$table->string('studies')->nullable();
			$table->string('additional_education')->nullable();
			$table->string('image_name')->nullable(); // Null
			$table->string('previous_occupation')->nullable(); //Null - Scrapping
			$table->string('license_type')->nullable();; //BrockerLicenced ? 'Brocker' : (SalesLicedsed ? 'Agente')
			$table->string('license_department')->nullable(); //Null temporal - Scrapping
			$table->string('year_obtained_license')->nullable();
			$table->string('expiration_date_license')->nullable();
			$table->string('license_number')->nullable();
			$table->string('marketing_slogan')->nullable();
			$table->string('website_descripction')->nullable();
			$table->string('countries_interested')->nullable();
			$table->string('meta_tag_description')->nullable();
			$table->string('bullet_point_one')->nullable();
			$table->string('bullet_point_two')->nullable();
			$table->string('bullet_point_three')->nullable();
			$table->string('meta_tag_keywords')->nullable();
			$table->string('deactivation_date')->nullable(); //Termination Date
			$table->decimal('commission_percentage')->nullable();

            $table->string('nro_internacional_remax')->nullable();
            $table->string('id_business_agent')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')
            ->restrictOnDelete();

           $table->foreign('region_id')->references('region_id')->on('offices')
            ->restrictOnDelete();

            $table->foreign('office_id')->references('office_id')->on('offices')
            ->restrictOnDelete();
			/*$table->foreign('qualifications_id')->references('id')->on('qualifications')
				->restrictOnDelete();*/

            $table->foreign('contact_id')->references('id')->on('contacts')->restrictOnDelete();
            $table->timestamps();
            $table->index('region_id');
            $table->index('office_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::dropIfExists('agents');
    }
};
