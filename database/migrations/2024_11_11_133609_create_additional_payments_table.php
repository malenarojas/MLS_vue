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
        Schema::create('additional_payments', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->string('payment_term');
            $table->string('note');
            $table->unsignedBigInteger('type_additional_payment_id');
            $table->unsignedBigInteger('listing_id');
            $table->timestamps();

            $table->foreign('type_additional_payment_id')->references('id')
                ->on('type_additional_payments')->restrictOnDelete();

            $table->foreign('listing_id')->references('id')
                ->on('listings')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_payments');
    }
};
