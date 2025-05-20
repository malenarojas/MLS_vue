<?php

namespace App\AuditConfigs;

use App\Models\Room;

class MultimediaAuditConfig
{
  public static function fieldMap(): array
  {
    return [
      'is_default' => '¿Es por defecto?',
      'room_id' => 'ID de Habitación',
    ];
  }

  public static function foreignKeys(): array
  {
    return [
      'room_id' => [Room::class, 'id'],
    ];
  }
}
