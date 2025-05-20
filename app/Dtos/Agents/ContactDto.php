<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class ContactDto extends Data
{

	/**
	* $table->string('nationality')->nullable();
    *      $table->string('preferred_language')->nullable();
    *        $table->string('motivation')->nullable();
    *        $table->string('marital_status')->nullable();
    *        $table->string('children')->nullable();
    *        $table->string('first_time_buyer')->nullable();
    *        $table->string('buyer_commission')->nullable();
    *        $table->string('type_buyer_commission')->nullable();
    *        $table->string('mail')->nullable();
    *        $table->string('home_phone')->nullable();
    *        $table->string('cellular')->nullable();
    *        $table->string('other_phone')->nullable();
    *        $table->unsignedBigInteger('grade_id')->nullable();
    *        $table->unsignedBigInteger('time_frame_id')->nullable();
    *        $table->unsignedBigInteger('preferred_communication_method_id')->nullable();
    *        $table->unsignedBigInteger('category_contact_id')->nullable();
	 */
    public function __construct(
        public ?string $name,
		public ?string $last_name,
		public ?string $email,
		public ?string $mobile,
		public ?string $qualification,
		public ?string $greeting,
		public ?string $company,
		public ?string $department_name,
		public ?string $identification_number,
		public ?string $birthdate,
		public ?string $sex,
		public ?string $nationality,
		public ?string $preferred_language,
		public ?string $motivation,
		public ?string $marital_status,
		public ?string $children,
		public ?string $first_time_buyer,
		public ?string $buyer_commission,
		public ?string $type_buyer_commission,
		public ?string $mail,
		public ?string $home_phone,
		public ?string $cellular,
		public ?string $other_phone,
		public ?string $grade_id,
		public ?string $time_frame_id,
		public ?string $preferred_communication_method_id,
		public ?string $category_contact_id,
		public ?string $agent_id,
    ) {}
	public function toArray(): array
	{
		return get_object_vars($this);
	}
}
