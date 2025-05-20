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
			// $table->uuid('contact_key')->after('id')->nullable();
			// $table->string('prospect_id')->nullable();
			// $table->string('stage_id')->nullable();
			// $table->string('profile_type_id')->nullable();
			// $table->string('title_id')->nullable();
			// $table->string('birthday_reminder_id')->nullable();
			// $table->string('birthday_template_id')->nullable();
			// $table->string('gender_id')->nullable();
			// $table->string('nationalitie_id')->nullable();
			// $table->string('preferred_language_id')->nullable();
			// $table->string('display_name')->nullable();
			// $table->string('salutation')->nullable();
			// $table->integer('sell')->nullable();
			// $table->string('fax')->nullable();
			// $table->mediumText('quick_note')->nullable();
			// $table->text('chat_telegram')->nullable();
			// $table->text('chat_viber')->nullable();
			// $table->text('chat_messenger')->nullable();
			// $table->text('chat_whatsapp')->nullable();
			$table->string('address_type_id')->nullable();
			// $table->unsignedBigInteger('state_id')->nullable();
			// $table->unsignedBigInteger('province_id')->nullable();
			// $table->unsignedBigInteger('city_id')->nullable();
			// $table->unsignedBigInteger('zone_id')->nullable();
			// $table->integer('number')->nullable();
			// $table->integer('unit')->nullable();
			// $table->integer('zip_code')->nullable();
			// $table->integer('floor_number')->nullable();
			// $table->unsignedBigInteger('floor_type_id')->nullable();
			// $table->string('marital_statu_id')->nullable();
			// $table->string('address')->nullable();
			// $table->string('address2')->nullable();
			// $table->text('red_facebook')->nullable();
			// $table->text('red_twitter')->nullable();
			// $table->text('red_youtube')->nullable();
			// $table->text('red_linkedin')->nullable();
			// $table->text('red_instagram')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('contacts', function (Blueprint $table) {
			$table->dropColumn([
				'contact_key',
				'prospect_id',
				'stage_id',
				'profile_type_id',
				'title_id',
				'birthday_reminder_id',
				'birthday_template_id',
				'gender_id',
				'nationalitie_id',
				'marital_statu_id',
				'preferred_language_id',
				'salutation',
				'sell',
				'fax',
				'display_name',
				'quick_note',
				'chat_telegram',
				'chat_viber',
				'chat_messenger',
				'chat_whatsapp',
				'address_type_id',
				'state_id',
				'province_id',
				'city_id',
				'zone_id',
				'number',
				'unit',
				'zip_code',
				'floor_number',
				'floor_type_id',
				'address',
				'address2',
				'red_facebook',
				'red_twitter',
				'red_youtube',
				'red_linkedin',
				'red_instagram'
			]);
		});
	}
};
