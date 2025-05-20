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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('affair');
            $table->text('description')->nullable();
            $table->string('grades')->nullable();
            $table->string('start_date');
            $table->string('start_date_time');
            $table->string('end_date')->nullable();
            $table->string('end_date_time')->nullable();
            $table->boolean('all_day_event')->default(false);
            $table->unsignedBigInteger('type_actividad_id');
            $table->unsignedBigInteger('agenda_legends_id');

            $table->foreign('type_actividad_id')->references('id')->on('type_activities')->restrictOnDelete();
            $table->foreign('agenda_legends_id')->references('id')->on('agenda_legends')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
