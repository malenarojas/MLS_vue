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
        Schema::create('commissions_option', function (Blueprint $table) {
            $table->id();
            $table->double('recruitment_commission')->nullable();
            $table->string('type_recruitment_commission')->nullable();
            $table->double('sales_commission')->nullable();
            $table->string('sales_commission_type')->nullable();
            $table->foreignId('listing_id')->nullable()->references('id')->on('listings')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions_option');
    }
};
