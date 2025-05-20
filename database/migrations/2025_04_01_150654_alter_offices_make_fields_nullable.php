<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  AlterOfficesMakeFieldsNullable extends Migration
{
    public function up()
    {
       /* Schema::table('offices', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('province')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->boolean('access_ilist_net')->nullable()->change();
            $table->boolean('succeed_certified')->nullable()->change();
            $table->boolean('is_satellite_office')->nullable()->change();
            $table->boolean('is_commercial')->nullable()->change();
            $table->boolean('is_collection')->nullable()->change();
            $table->boolean('active_office')->nullable()->change();
        });*/
    }

    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('province')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();
            $table->boolean('access_ilist_net')->default(0)->nullable(false)->change();
            $table->boolean('succeed_certified')->default(0)->nullable(false)->change();
            $table->boolean('is_satellite_office')->default(0)->nullable(false)->change();
            $table->boolean('is_commercial')->default(0)->nullable(false)->change();
            $table->boolean('is_collection')->default(0)->nullable(false)->change();
            $table->boolean('active_office')->default(1)->nullable(false)->change();
        });
    }
}
