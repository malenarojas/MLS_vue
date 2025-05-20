<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {
       /* Schema::table('offices', callback: function (Blueprint $table) {
                $table->date('office_start_date')->nullable();
                $table->integer('international_code')->nullable();
                $table->integer('hide_office_from_web')->nullable();
                $table->integer('show_whatsapp')->nullable();
            // --- Contacto ---
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('cell_phone')->nullable();
                $table->string('number')->nullable();

                $table->string('address')->nullable();
                $table->string('address2')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('floor')->nullable();
                $table->string('unit')->nullable();
                $table->unsignedBigInteger('state_id')->nullable();
                $table->unsignedBigInteger('zone_id')->nullable();
                $table->date('expiration_date')->nullable();
                $table->string('license_number')->nullable();
                $table->string('license_department')->nullable();
                $table->string('marketing_slogan')->nullable();
                $table->text('website_description')->nullable()->after('marketing_slogan');
                $table->text('closure')->nullable()->after('website_description');

                // Enlaces
                $table->string('short_link')->nullable()->after('closure');
                $table->string('office_website')->nullable()->after('short_link');
                $table->string('bullet_point_one')->nullable()->after('office_intl_id');
                $table->string('bullet_point_two')->nullable()->after('bullet_point_one');
                $table->string('bullet_point_three')->nullable()->after('bullet_point_two');
                $table->string('bullet_point_four')->nullable()->after('bullet_point_three');

                // SEO fields
                $table->text('meta_tag_keywords')->nullable()->after('bullet_point_four');
                $table->text('meta_tag_description')->nullable()->after('meta_tag_keywords');

                $table->foreign('state_id')->references('id')->on('states')
                ->restrictOnDelete();
                $table->foreign('zone_id')->references('id')->on('zones')
                ->restrictOnDelete();
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['state_id']);

            $table->dropColumn([
                'international_code',
                'hide_office_from_web',
                'show_whatsapp',
                'phone',
                'email',
                'cell_phone',
                'number',
                'address',
                'address2',
                'postal_code',
                'floor',
                'state_id',
                'expiration_date',
                'license_number',
                'license_department',
                'marketing_slogan',
                'website_description',
                'closure',
                'short_link',
                'office_website',
                'bullet_point_one',
                'bullet_point_two',
                'bullet_point_three',
                'bullet_point_four',
                'meta_tag_keywords',
                'meta_tag_description',
            ]);
        });*/
    }
};
