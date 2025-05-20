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
        Schema::table('languages_users', function (Blueprint $table) {
            $table->boolean('is_preferred')->default(false); // 1 = preferred, 0 = not preferred
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('languages_users', function (Blueprint $table) {
            $table->dropColumn('is_preferred');
        });
    }
};
