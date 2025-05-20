<?php

namespace App\Dtos\Listings;

use App\Dtos\AuditLogDto;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ListingDto extends Data
{
    public function __construct(
        public string|Optional|null $key,
        public string|Optional|null $MLSID,
        public string|Optional|null $date_of_listing,
        public string|Optional|null $contract_end_date,
        public string|Optional|null $cancellation_date,
        public string|Optional|null $cancellation_reason,
        public string|Optional|null $reference,
        public string|Optional|null $property_registration_number,
        public string|Optional|null $financial_note,
        public string|Optional|null $title,
        public string|Optional|null $description_website,
        public string|Optional|null $marketing_description,
        public string|Optional|null $location_information,
        public string|Optional|null $rent_timeframe_id,
        public array|Optional|null $translations,
        public int|Optional|null $is_published,
        public string|Optional|null $market_segment,
        public int|Optional|null $project_id,
        public int|Optional|null $contract_type_id,
        public int|Optional|null $area_id,
        public int|Optional|null $status_listing_id,
        public int|Optional|null $cancellation_reason_id,
        public int|Optional|null $price_type_id,
        public int|Optional|null $agent_id,
        public int|Optional|null $transaction_type_id,
        public int|Optional|null $office_id,
        #[MapInputName('price.amount')]
        public float|Optional|null $amount,

        public ?int $is_sent_to_review,
        public ?int $is_draft,
        public ?int $is_new,

        // Features
        public array|Optional|null $features,
        // Dtos
        public ?CommissionOptionDto $commission_option,
        public ?ListingInformationDto $listing_information,
        public ?LocationDto $location,
        /** @var RoomDto[] */
        public array|Optional|null $rooms,
        /** @var MultimediaDto */
        public ?array $multimedias,
        /** @var AuditLogsDto */
        public ?array $logs,
        /** @var DocumentationDto */
        public ?array $private_documentation,
        /** @var DocumentationDto */
        public ?array $public_documentation,
        /** @var OwnerDto */
        public ?array $owners,
    ) {}
}
