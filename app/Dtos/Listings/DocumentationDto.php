<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;

class DocumentationDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $original_name,
        public ?string $description,
        public ?UploadedFile $file,
        public int $documentation_type_id,
        public ?int $is_new,
        public ?string $link,
    ) {}
}
