<?php

namespace App\Enum;

enum ListingMigrationStatus: int
{
  case PENDING = 0;
  case SUCCESS = 1;
  case NOT_FOUND = 2;
  case ERROR = 3;
}
