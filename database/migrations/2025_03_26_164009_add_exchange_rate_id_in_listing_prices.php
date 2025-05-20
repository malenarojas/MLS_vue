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
        Schema::table('listing_prices', function (Blueprint $table) {
            $table->foreignId('exchange_rate_id')->nullable()->after('currency_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_prices', function (Blueprint $table) {
            $table->dropForeign(['exchange_rate_id']);
            $table->dropColumn('exchange_rate_id');
        });
    }
};
