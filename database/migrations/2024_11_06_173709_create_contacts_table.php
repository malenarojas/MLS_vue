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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('qualification')->nullable();
            $table->string('greeting')->nullable();
            $table->integer('company')->nullable();
            $table->integer('department_name')->nullable();
            $table->integer('identification_number')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('sex')->nullable();
            $table->string('nationality')->nullable();
            $table->string('preferred_language')->nullable();
            $table->string('motivation')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('children')->nullable();
            $table->string('first_time_buyer')->nullable();
            $table->string('buyer_commission')->nullable();
            $table->string('type_buyer_commission')->nullable();
            $table->string('mail')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('cellular')->nullable();
            $table->string('other_phone')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('time_frame_id')->nullable();
            $table->unsignedBigInteger('preferred_communication_method_id')->nullable();
            $table->unsignedBigInteger('category_contact_id')->nullable();


            $table->foreign('preferred_communication_method_id')->references('id')->on('preferend_comunication_methods')->restrictOnDelete();

            $table->foreign('time_frame_id')->references('id')->on('time_frames')->restrictOnDelete();

            $table->foreign('category_contact_id')->references('id')->on('category_contacts')->restrictOnDelete();

            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
