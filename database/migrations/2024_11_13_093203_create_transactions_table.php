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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('internal_id')->nullable();
            $table->unsignedBigInteger('transaction_status_id')->nullable();

            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();

            $table->date('status_date')->nullable();
            $table->integer('trr_report_id')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->unsignedBigInteger('transaction_type_id')->nullable();
            $table->date('possession_date')->nullable();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->uuid('listing_uuid')->nullable();
            $table->date('sold_date')->nullable();
            $table->string('is_remax_listing')->default('0');
            $table->decimal('current_listing_price', 17, 6)->nullable();
            $table->string('current_listing_currency')->nullable();
            $table->decimal('current_listing_price_local', 17, 6)->nullable();
            $table->decimal('current_listing_price_usd', 17, 6)->nullable();
            $table->decimal('current_listing_price_euro', 17, 6)->nullable();
            $table->decimal('amount_financed', 17, 6)->nullable();
            $table->date('date_trr_sent')->nullable();
            $table->string('both_sides_commission')->default('0');
            $table->string('transactioning_co_operation')->nullable();
            $table->decimal('payments_amount_expected', 17, 6)->nullable();
            $table->decimal('payments_amount_received', 17, 6)->nullable();
            $table->decimal('payments_amount_outstanding', 17, 6)->nullable();
            $table->unsignedBigInteger('number_of_payments_expected')->nullable();
            $table->unsignedBigInteger('number_of_payments_received')->nullable();
            $table->unsignedBigInteger('number_of_payments_outstanding')->nullable();
            $table->string('mls_id')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')
                ->restrictOnDelete();
            $table->foreign('office_id')->references('id')->on('offices')
                ->restrictOnDelete();
            $table->foreign('bank_id')->references('id')->on('banks')
                ->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')
                ->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->foreign('transaction_status_id')->references('id')->on('transaction_statuses')
                ->onDelete('SET NULL')->onUpdate('SET NULL');

            $table->foreign('listing_id')->references('id')->on('listings')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
