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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('internal_id')->nullable();
            $table->unsignedBigInteger('commission_type_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('agent_internal_id')->nullable();

            $table->date('date_created')->nullable();
            $table->date('date_edited')->nullable();

            $table->decimal('total_commission_amount', 17, 6)->nullable();
            $table->string('total_commission_amount_currency')->nullable();
            $table->decimal('total_commission_amount_usd', 17, 6)->nullable();
            $table->decimal('total_commission_amount_euro', 17, 6)->nullable();
            $table->float('total_commission_percentage')->nullable();

            $table->decimal('transaction_commission_amount', 17, 6)->nullable();
            $table->string('transaction_commission_amount_currency')->nullable();
            $table->decimal('transaction_commission_amount_usd', 17, 6)->nullable();
            $table->decimal('transaction_commission_amount_euro', 17, 6)->nullable();
            $table->float('transaction_commission_percentage')->nullable();

            $table->decimal('referral_commission_amount', 17, 6)->nullable();
            $table->string('referral_commission_amount_currency')->nullable();
            $table->decimal('referral_commission_amount_usd', 17, 6)->nullable();
            $table->decimal('referral_commission_amount_euro', 17, 6)->nullable();
            $table->float('referral_commission_percentage')->nullable();
            $table->timestamps();

            $table->foreign('commission_type_id')->references('id')->on('commission_types')
                ->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->foreign('transaction_id')->references('id')->on('transactions')
                ->onDelete('SET NULL')->onUpdate('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
