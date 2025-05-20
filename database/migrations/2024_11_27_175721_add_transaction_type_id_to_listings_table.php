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
            // Agregar la columna 'transaction_type_id' que referencia 'listing_transaction_types'
            $table->unsignedBigInteger('transaction_type_id')->nullable();

            // Definir la clave foránea
            $table->foreign('transaction_type_id')->references('id')->on('listing_transaction_types')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['transaction_type_id']);
            $table->dropColumn('transaction_type_id');
        });
    }
};
