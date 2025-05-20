<?php

namespace App\Traits;

use App\Constants\ListingConstants;
use App\Models\Listing;

trait HandlesDescriptions
{
  private function savePrimaryDescription(Listing $listing, ?string $descType, ?string $descText): void
  {
    switch ($descType) {
      case ListingConstants::DESCRIPTION_TITLE:
        $listing->title = $descText;
        break;
      case ListingConstants::DESCRIPTION_WEB:
        $listing->description_website = $descText;
        break;
      case ListingConstants::DESCRIPTION_LOCATION:
        $listing->location_information = $descText;
        break;
      default:
        $this->assignMarketingOrLocation($listing, $descText);
        break;
    }
  }

  private function saveTranslatedDescription(array &$translations, string $languageCode, ?string $descType, ?string $descText): void
  {
    switch ($descType) {
      case ListingConstants::DESCRIPTION_TITLE:
        $translations[$languageCode][ListingConstants::FIELD_TITLE] = $descText;
        break;
      case ListingConstants::DESCRIPTION_WEB:
        $translations[$languageCode][ListingConstants::FIELD_WEBSITE] = $descText;
        break;
      case ListingConstants::DESCRIPTION_LOCATION:
        $translations[$languageCode][ListingConstants::FIELD_LOCATION] = $descText;
        break;
      default:
        $this->assignTranslatedMarketingOrLocation($translations, $languageCode, $descText);
        break;
    }
  }

  private function assignMarketingOrLocation(Listing $listing, ?string $descText): void
  {
    if (isset($listing->location_information)) {
      $listing->marketing_description = $descText;
    } else {
      if (strlen($descText) <= ListingConstants::MARKETING_DESC_MAX_LENGTH || !isset($listing->marketing_description)) {
        $listing->marketing_description = $descText;
      } else {
        $listing->location_information = $descText;
      }
    }
  }

  private function assignTranslatedMarketingOrLocation(array &$translations, string $languageCode, ?string $descText): void
  {
    if (isset($translations[$languageCode][ListingConstants::FIELD_LOCATION])) {
      $translations[$languageCode][ListingConstants::FIELD_MARKETING] = $descText;
    } else {
      if (strlen($descText) <= ListingConstants::MARKETING_DESC_MAX_LENGTH && !isset($translations[$languageCode][ListingConstants::FIELD_MARKETING])) {
        $translations[$languageCode][ListingConstants::FIELD_MARKETING] = $descText;
      } else {
        $translations[$languageCode][ListingConstants::FIELD_LOCATION] = $descText;
      }
    }
  }
}
