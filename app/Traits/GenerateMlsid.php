<?php

namespace App\Traits;

use App\Models\Listing;

trait GenerateMlsid
{
  public function getNextMLSIDByAgentInternalId(int $agentInternalId)
  {
    $mlsid = Listing::selectRaw("
            MAX(CAST(SUBSTRING_INDEX(MLSID, '-', -1) AS UNSIGNED)) AS max_mlsid
        ")
      ->whereRaw("SUBSTRING_INDEX(MLSID, '-', 1) = ?", [$agentInternalId])
      ->value('max_mlsid');

    if (!$mlsid) {
      return "$agentInternalId-1";
    }

    return "$agentInternalId-" . ($mlsid + 1);
  }
}
