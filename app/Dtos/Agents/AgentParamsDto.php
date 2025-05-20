<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class AgentParamsDto extends Data
{
    public function __construct(
        public ?string $office_id,
    ) {}
}