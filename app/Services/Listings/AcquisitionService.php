<?php

namespace App\Services\Listings;

use App\Dtos\Listings\CreateAcquisitionDto;
use App\Models\Agent;
use App\Models\Listing;
use App\Models\ListingPrice;
use App\Models\Location;
use App\Models\Office;
use App\Services\Agents\OfficeService;
use App\Services\Agents\RegionService;
use App\Services\Listings\ListingInformationService;
use App\Services\Listings\StatusListingService;
use App\Traits\AutenticationTrait;
use App\Traits\GenerateMlsid;
use App\Utils\StringGenerateKey;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcquisitionService
{
    use AutenticationTrait, GenerateMlsid;

    public function __construct(
        private OfficeService $officeService,
        private RegionService $regionService,
        private StatusListingService $statusListingService,
        private ListingInformationService $listingInformationService,
        private ListingService $listingService,
    ) {}

    public function storeAcquisition(CreateAcquisitionDto $dto): Listing
    {
        return DB::transaction(function () use ($dto) {
            $key = StringGenerateKey::generateKey();
            $user = $this->getAuthenticate();

            $agentInternalId = null;

            if ($user->hasDirectPermission('listing.create.select_office')) {
                $office = Office::find($dto->office_id);
                $dto->office_id = $office->office_id;
            }

            if ($user->hasDirectPermission('listing.create.select_agent')) {
                $agentInternalId = Agent::find($dto->agent_id)?->agent_internal_id;
            } else {
                $agent = $user->agent;
                $dto->agent_id = $agent->id;
                $dto->office_id = $agent->office_id;
                $agentInternalId = $agent->agent_internal_id;
            }

            // dd($agentInternalId);
            $MLSID = $this->getNextMLSIDByAgentInternalId($agentInternalId);
            $draftStatuId = $this->statusListingService->getDraftStatuId();

            // dd($key, $MLSID, $dto->agent_id, $dto->area_id, $draftStatuId, $dto->office_id, $dto->transaction_type_id);

            $listing = Listing::create([
                'key' => $key,
                'MLSID' => $MLSID,
                'agent_id' => $dto->agent_id,
                'area_id' => $dto->area_id,
                'status_listing_id' => $draftStatuId,
                'office_id' => $dto->office_id,
                'transaction_type_id' => $dto->transaction_type_id,
            ]);

            DB::table('agent_listing')->insert([
                'agent_id' => $dto->agent_id,
                'listing_id' => $listing->id,
            ]);

            $this->listingInformationService->create([
                'subtype_property_id' => $dto->subtype_property_id,
                'listing_id' => $listing->id,
            ]);

            return $listing;
        });
    }

    public function storeAcquisitionExternal(array $data): Listing
    {
        return DB::transaction(function () use ($data) {
            $officeId = $data['office_id'];
            $office = $this->officeService->findById($officeId);
            $region = $this->regionService->findById($office->region_id);

            // Cantidad de lisntg +1, estamos creando una nueva
            $listingCount = $this->listingCountByAgentId($data['agent_id']) + 2;

            Log::info("Cantidad de listings: $listingCount");

            // Generar key
            $key = StringGenerateKey::generateKey();
            $key = "$key$listingCount"; // Key + Cantidad de listings

            // Generar MLSID
            $agent = Agent::find($data['agent_id']);
            $MLSID = $this->getNextMLSIDByAgentInternalId($agent?->agent_internal_id);

            $draftStatuId = $this->statusListingService->getDraftStatuId();

            $da_listing = $data['date_of_listing'] ? Carbon::parse($data['date_of_listing'])->format('Y-m-d') : null;
            $listing = Listing::create([
                'key' => $key,
                'MLSID' => $MLSID,
                'is_external' => 1,
                'agent_id' => $data['agent_id'],
                'area_id' => $data['area_id'],
                'description_website' => $data['description_website'] ?? null,
                'date_of_listing' => $da_listing,
                'status_listing_id' => $data['status_listing_id'],
                'office_id' => $data['office_id'],
                'reference' => $data['reference'] ?? '',
                'contract_type_id' => $data['contract_type_id'] ?? null,
                'area_id' => $data['area_id'] ?? null,
                'price_type_id' => $data['price_type_id'] ?? null,
                'transaction_type_id' => $data['transaction_type_id'],
            ]);

            DB::table('agent_listing')->insert([
                'agent_id' => $data['agent_id'],
                'listing_id' => $listing->id,
            ]);

            $this->listingInformationService->create([
                'subtype_property_id' => $data['subtype_property_id'],
                'number_bathrooms' => $data['number_bathrooms'],
                'total_number_rooms' => $data['total_number_rooms'],
                'number_bedrooms' => $data['number_bedrooms'],
                'unit_department' => $data['unit_department'],
                'cubic_volume' => $data['cubic_volume'],
                'land_m2' => $data['land_m2'],
                'total_area' => $data['total_area'],
                'listing_id' => $listing->id,
            ]);

            /*            if (isset($data['price']) && $data['price'] != null) {
                $newPrice = $data['price'];*/

            $price = ListingPrice::where('listing_id', $listing->id)
                ->where('currency_id', 2) // Default USD
                ->first();

            if ($price && $data['amount']) {
                // Existe un precio anterior y el nuevo precio es diferente
                $price->amount = $data['amount'];
                $price->save();
            } else {
                ListingPrice::create([
                    'listing_id' => $listing->id,
                    'amount' => $data['amount'],
                    'currency_id' => 2, // Default BOB
                ]);
            }

            // Location
            $location = new Location();
            $location->first_address  = $data['first_address'];
            $location->second_address  = $data['second_address'];
            $location->zone_id  = $data['zone_id'];
            $location->number  = $data['number'];
            $location->city_id  = $data['city_id'];
            $location->zip_code  = $data['zip_code'];
            $location->show_addres_on_website  = true;
            $location->listing_id  = $listing->id;
            $location->save();

            return $listing;
        });
    }

    public function listingCountByAgentId(int $agentId): int
    {
        return DB::table('agent_listing')->where('agent_id', $agentId)->count() + 2;
    }
}
