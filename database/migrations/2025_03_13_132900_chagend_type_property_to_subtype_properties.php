<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('subtype_properties', function (Blueprint $table) {
            $table->unsignedBigInteger('type_property_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('subtype_properties', function (Blueprint $table) {
            // $table->dropForeign(['type_property_id']);
            // $table->uuid('type_property_id')->nullable(false)->change();
            // $table->foreign('type_property_id')->references('id')->on('type_properties')->onDelete('cascade');
        });
    }
};
