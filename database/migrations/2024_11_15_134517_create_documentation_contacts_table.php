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
        Schema::create('documentation_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('type_documentacion_id');
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->restrictOnDelete();
            $table->foreign('type_documentacion_id')->references('id')->on('type_documentation_contacts')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_contacts');
    }
};
