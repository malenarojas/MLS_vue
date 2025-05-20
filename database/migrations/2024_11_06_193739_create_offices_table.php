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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('office_id');
            $table->string('image')->nullable();
            $table->string('name');
            $table->string('city');
            $table->string('province');
            $table->string('country');
            $table->decimal('latitude', 10, 6)->nullable(); // Para la latitud
            $table->decimal('longitude', 10, 6)->nullable(); // Para la longitud
            $table->timestamp('first_updated_to_web')->nullable();
            $table->boolean('access_ilist_net')->default(0);
            $table->boolean('succeed_certified')->default(0);
            $table->integer('is_regional_office')->nullable();
            $table->boolean('is_satellite_office')->default(0);
            $table->timestamp('first_year_licensed')->nullable(); // Asegúrate de que el año es correcto
            $table->boolean('is_commercial')->default(0);
            $table->boolean('is_collection')->default(0);
            $table->timestamp('date_time_stamp')->nullable();
            $table->boolean('active_office')->default(1);
            $table->string('office_iconnect_id')->nullable();
            $table->string('office_intl_id')->nullable();
            $table->string('macro_office')->nullable();
            $table->string('office_type')->nullable();

            $table->index('region_id');
            $table->index('office_id');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('SET NULL');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
