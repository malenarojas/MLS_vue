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
        Schema::table('listings', function (Blueprint $table) {
            $table->foreignId('cancellation_reason_id')
                ->nullable()
                ->constrained('cancellation_reasons')
                ->onDelete('cascade')
                ->after('status_listing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['cancellation_reason_id']);
            $table->dropColumn('cancellation_reason_id');
        });
    }
};
