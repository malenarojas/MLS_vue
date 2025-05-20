<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

class AuditLogDto extends Data
{

	public $agent_id;
    public $user_id;
    public $field_name;
    public $old_value;
    public $new_value;
    public $notes;

    public function __construct($agent_id, $user_id, $field_name, $old_value, $new_value, $notes)
    {
        $this->agent_id = $agent_id;
        $this->user_id = $user_id;
        $this->field_name = $field_name;
        $this->old_value = $old_value;
        $this->new_value = $new_value;
        $this->notes = $notes;
    }
}
