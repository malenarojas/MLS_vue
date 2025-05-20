<?php

namespace App\Enum;

enum ListingImageType: string
{
  case WEB = 'WebImageURL';
  case THUMBNAIL = 'ThumbnaiImageURL';
  case LARGE = 'LargeImageURL';
  case EXTRA_LARGE = 'ExtraLargeImageURL';
}
