<?php

namespace App\Dtos\Listings;

use Illuminate\Http\File;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class MultimediaDto extends Data
{
    public function __construct(
        public int|Optional|null $id,
        public string|Optional|null $description,
        public File|Optional|null $file,
        public int|Optional|null $is_default,
        public int|Optional|null $is_new,
        public string|Optional|null $link,
        public int|Optional|null $multimedia_type_id,
        public int|Optional|null $room_id,
        // Relations
        public int|Optional|null $listing_id,
    ) {}
}
