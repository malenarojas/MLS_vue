<?php

namespace App\Traits;

use Carbon\Carbon;

trait FormatsListingData
{
  public function formatListingData($listing)
  {
    return array_merge($listing->toArray(), [
      'date_of_listing' => $listing->date_of_listing ? Carbon::parse($listing->date_of_listing)->format('m/d/Y') : null,
      'contract_end_date' => $listing->contract_end_date ? Carbon::parse($listing->contract_end_date)->format('m/d/Y') : null,
    ]);
  }
}
