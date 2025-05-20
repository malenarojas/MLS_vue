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
        Schema::create('sold_prices', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')
                ->on('transactions')->restrictOnDelete();

            $table->foreign('currency_id')->references('id')
                ->on('currencies')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sold_prices');
    }
};
