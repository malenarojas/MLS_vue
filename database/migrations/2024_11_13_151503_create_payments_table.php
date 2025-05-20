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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('internal_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('agent_internal_id')->nullable();
            $table->timestamp('expected_payment_date')->nullable();

            $table->decimal('amount_expected', 17, 6)->nullable();
            $table->string('amount_expected_currency')->nullable();
            $table->decimal('amount_expected_euro', 17, 6)->nullable();
            $table->decimal('amount_expected_usd', 17, 6)->nullable();

            $table->decimal('amount_received', 17, 6)->nullable();
            $table->string('amount_received_currency')->nullable();
            $table->decimal('amount_received_euro', 17, 6)->nullable();
            $table->decimal('amount_received_usd', 17, 6)->nullable();
            $table->timestamp('received_date')->nullable();

            $table->decimal('outstanding_amount', 17, 6)->nullable();
            $table->string('outstanding_amount_currency')->nullable();
            $table->decimal('outstanding_amount_euro', 17, 6)->nullable();
            $table->decimal('outstanding_amount_usd', 17, 6)->nullable();
            $table->timestamp('date_submitted')->nullable();

            $table->integer('billing_month')->nullable();
            $table->integer('billing_year')->nullable();

            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')
                ->onDelete('SET NULL')->onUpdate('SET NULL');
                
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
