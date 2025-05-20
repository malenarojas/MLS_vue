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
        Schema::create('listing_migration_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->string('key')->nullable()->unique();
            $table->string('MLSID')->nullable()->uniqid();
            $table->unsignedTinyInteger('status')
                ->default(0); //  0: Pending 1: Success, 2: No Return from API, 3: Error DB
            $table->string('message')->nullable();
            $table->string('message_error')->nullable();
            $table->json('data')->nullable();
            $table->json('errors')->nullable();
            $table->json('warnings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_migration_logs');
    }
};
