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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('affair');
            $table->string('date_task');
            $table->date('reminder_date')->nullable();
            $table->string('listing')->nullable();
            $table->text('description')->nullable();
            $table->string('filetxt')->nullable();
            $table->unsignedBigInteger('priority_id'); // Relación con la tabla priority
            $table->unsignedBigInteger('activity_listing_id'); // Relación con la tabla activity_listing
            $table->timestamps();

            $table->foreign('priority_id')->references('id')->on('priorities')->restrictOnDelete();
            $table->foreign('activity_listing_id')->references('id')->on('activity_listings')->restrictOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
