<?php

namespace App\Dtos\Iconnect;

use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;

class RoomIcoDto extends Data
{
  public function __construct(
    #[MapInputName('Rooms.TypeID')]
    public ?string $room_type_id,
    #[MapInputName('Rooms.Type')]
    public ?string $room_type,
    #[MapInputName('Rooms.Dimension')]
    public ?string $dimension,
    #[MapInputName('Rooms.Size')]
    public ?string $size,
    #[MapInputName('Rooms.Description')]
    public ?string $description,
    #[Computed]
    public ?string $dimension_x = null,
    #[Computed]
    public ?string $dimension_y = null,
  ) {
    if ($this->dimension) {
      Log::info("RoomIcoDto: Dimension: " . $this->dimension);
      $dimension = explode('x', $this->dimension);
      $this->dimension_y = $dimension[0];
      $this->dimension_x = $dimension[1];
    }
  }
}
