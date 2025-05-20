<?php

namespace App\Services\Listings;

use App\Models\Listing;

class ListingTransferedService
{
  public function __construct() {}

  public function transferedListing(int $listingId, int $preAgentId, int $newAgentId)
  {
    // $listing = Listing::select('id', 'MLSID')->;
  }
}
