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
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('first_name')->nullable();
			$table->string('middle_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('name_to_show')->nullable();
			$table->string('ci')->nullable();
			$table->string('gender')->nullable();
			$table->string('phone_number')->nullable(); //Ask format
			$table->string('email');
			$table->string('url')->nullable(); //Ask
			$table->string('remax_start_date')->nullable();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password')->nullable();
			$table->string('username');
            $table->string('birthdate')->nullable();
			$table->unsignedBigInteger('user_type_id')->nullable();
			$table->unsignedBigInteger('remax_title_id')->nullable();
			$table->unsignedBigInteger('remax_title_to_show_id')->nullable();
			$table->unsignedBigInteger('team_status_id')->nullable();
			$table->unsignedBigInteger('customer_preference_id')->nullable();

			$table->rememberToken();
			$table->timestamps();
			 // Foreign keys with ON DELETE SET NULL
            $table->foreign('user_type_id')
                ->references('id')
                ->on('user_types')
                ->onDelete('set null');

            $table->foreign('remax_title_id')
                ->references('id')
                ->on('remax_titles')
                ->onDelete('set null');

            $table->foreign('remax_title_to_show_id')
                ->references('id')
                ->on('remax_title_to_shows')
                ->onDelete('set null');

            $table->foreign('team_status_id')
                ->references('id')
                ->on('team_statuses')
                ->onDelete('set null');

            $table->foreign('customer_preference_id')
                ->references('id')
                ->on('customer_preferences')
                ->onDelete('set null');
		});

		Schema::create('password_reset_tokens', function (Blueprint $table) {
			$table->string('email')->primary();
			$table->string('token');
			$table->timestamp('created_at')->nullable();
		});

		Schema::create('sessions', function (Blueprint $table) {
			$table->string('id')->primary();
			$table->foreignId('user_id')->nullable()->index();
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent')->nullable();
			$table->longText('payload');
			$table->integer('last_activity')->index();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users');
		Schema::dropIfExists('password_reset_tokens');
		Schema::dropIfExists('sessions');
	}
};
