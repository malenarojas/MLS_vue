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
        Schema::table('listings_information', function (Blueprint $table) {
            $table->string('youtube_link')->nullable();
            $table->string('virtual_link')->nullable();
            $table->string('virtual_viewer')->nullable();
            $table->string('external_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings_information', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_link',
                'external_link',
                // 'virtual_link',
                // 'virtual_viewer'
            ]);
        });
    }
};
