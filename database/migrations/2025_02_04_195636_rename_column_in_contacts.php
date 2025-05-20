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
        Schema::table('contacts', function (Blueprint $table) {
			//$table->renameColumn('name', 'first_name');
			$table->renameColumn('qualification', 'rating');
			$table->renameColumn('birthdate', 'birth_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            //$table->renameColumn('first_name', 'name');
            $table->renameColumn('rating', 'qualification');
            $table->renameColumn('birth_day', 'birthdate');
        });
    }
};
