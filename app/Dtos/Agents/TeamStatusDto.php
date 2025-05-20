<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class TeamStatusDto extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
       
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
