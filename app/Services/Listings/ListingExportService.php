<?php

namespace App\Services\Listings;

use App\Models\Listing;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ListingExportService
{
    private array $headers = [
        'Departamento',
        'Nombre Oficina',
        'Nombre Agente Asociado',
        'Titulo a Enseñar',
        'MLS ID',
        'Integrador de ID',
        'Moneda',
        'Precio',
        'Ciudad',
        'Hora Local',
        'Dirección',
        'Numero en la Calle',
        'Código Postal',
        'Status de Captación',
        'Tipo de Transacción',
        'Tipo de Contrato',
        'Categoría de propiedad',
        'Tipo de Propiedad',
        'Borrar',
        'Fecha Captación',
        'Fecha Cargado a la Web',
        'Ultima Actualización',
        'Fecha Cancelado',
        'CancellationReason',
        'Fecha de Venta/Alquiler',
        'Fecha de Vencimiento',
        'Listing Percentage',
        'Porcentaje de Venta',
        'Precio Venta/Alquiler',
        'Comisión Total',
        'Nombre del dueño',
        'Id del Contacto',
        'Email del dueño',
        'Cell del dueño',
        'Casa del dueño',
        'Días en el Mercado',
        'Transferir Nombre Agente Asociado',
        'Transferir MLSID',
        'Transferir Date'
    ];

    public function getSelectColumns(): array
    {
        return [
            'listings.id',
            'listings.MLSID',
            'listings.status_listing_id',
            'listings.contract_type_id',
            'listings.transaction_type_id',
            'listings.cancellation_reason_id',
            'listings.cancellation_date',
            'listings.date_of_listing',
            'listings.updated_at',
            'listings.contract_end_date',
            'regions.name as region_name',
            'agents.office_id',
            'users.name_to_show',
            'users.remax_title_to_show_id',
            'offices.name as office_name',
            'remax_titles.name as remax_title_name',
            'cities.name as city_name',
            'zones.name as zone_name',
            'locations.first_address',
            'locations.number',
            'locations.zip_code',
            'currencies.symbol as currency_symbol',
            'listing_prices.amount as price_amount',
            'status_listings.name as status_listing_name',
            'transaction_types.name as transaction_type_name',
            'contract_types.name as contract_type_name',
            'properties_category.name_properties_categories',
            'subtype_properties.name as subtype_property_name',
            'cancellation_reasons.name as cancellation_reason_name',
            'transactions.sold_date',
            'transactions.current_listing_price',
            'commissions_option.recruitment_commission',
            'commissions_option.type_recruitment_commission',
            'commissions_option.sales_commission',
            'commissions_option.sales_commission_type',
            'contacts.id as owner_id',
            'contacts.name as owner_name',
            'contacts.last_name as owner_last_name',
            'contacts.email as owner_email',
            'contacts.mobile as owner_mobile',
            'contacts.home_phone as owner_home_phone',
        ];
    }

    public function getListingSelectFields(): array
    {
        return [
            'listings.id',
            'listings.MLSID',
            'listings.status_listing_id',
            'listings.contract_type_id',
            'listings.transaction_type_id',
            'listings.cancellation_reason_id',
            'listings.cancellation_date',
            'listings.date_of_listing',
            'listings.updated_at',
            'listings.contract_end_date',
            'regions.name as region_name',
            'agents.office_id',
            'users.name_to_show',
            'users.remax_title_to_show_id',
            'offices.name as office_name',
            'remax_titles.name as remax_title_name',
            'cities.name as city_name',
            'zones.name as zone_name',
            'locations.first_address',
            'locations.number',
            'locations.zip_code',
            'currencies.symbol as currency_symbol',
            'listing_prices.amount as price_amount',
            'status_listings.name as status_listing_name',
            'transaction_types.name as transaction_type_name',
            'contract_types.name as contract_type_name',
            'properties_category.name_properties_categories',
            'subtype_properties.name as subtype_property_name',
            'cancellation_reasons.name as cancellation_reason_name',
            'transactions.sold_date',
            'transactions.current_listing_price',
            'commissions_option.recruitment_commission',
            'commissions_option.type_recruitment_commission',
            'commissions_option.sales_commission',
            'commissions_option.sales_commission_type',
            'contacts.id as owner_id',
            'contacts.name as owner_name',
            'contacts.last_name as owner_last_name',
            'contacts.email as owner_email',
            'contacts.mobile as owner_mobile',
            'contacts.home_phone as owner_home_phone',
        ];
    }

    public function getListingsQuery(array $filters)
    {
        return Listing::query()
            ->from('listings')
            ->join('agent_listing', fn($join) =>
            $join->on('agent_listing.listing_id', '=', 'listings.id')
                ->where('agent_listing.type', 1))
            ->join('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->join('users', 'users.id', '=', 'agents.user_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->join('regions', 'regions.id', '=', 'offices.region_id')
            ->leftJoin('remax_titles', 'remax_titles.id', '=', 'users.remax_title_to_show_id')
            ->leftJoin('locations', 'locations.listing_id', '=', 'listings.id')
            ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
            ->leftJoin('zones', 'zones.id', '=', 'locations.zone_id')
            ->leftJoin('listing_prices', function ($join) {
                $join->on('listing_prices.listing_id', '=', 'listings.id')
                    ->where('listing_prices.currency_id', 1); // Preferencia
            })
            ->leftJoin('currencies', 'currencies.id', '=', 'listing_prices.currency_id')
            ->leftJoin('status_listings', 'status_listings.id', '=', 'listings.status_listing_id')
            ->leftJoin('transaction_types', 'transaction_types.id', '=', 'listings.transaction_type_id')
            ->leftJoin('contract_types', 'contract_types.id', '=', 'listings.contract_type_id')
            ->leftJoin('cancellation_reasons', 'cancellation_reasons.id', '=', 'listings.cancellation_reason_id')
            ->leftJoin('commissions_option', 'commissions_option.listing_id', '=', 'listings.id')
            ->leftJoin('transactions', function ($join) {
                $join->on('transactions.listing_id', '=', 'listings.id')
                    ->where('transactions.transaction_status_id', 5);
            })
            ->leftJoin('owner', 'owner.listing_id', '=', 'listings.id')
            ->leftJoin('contacts', 'contacts.id', '=', 'owner.contact_id')
            ->leftJoin('listings_information', 'listings_information.listing_id', '=', 'listings.id')
            ->leftJoin('properties_category', 'properties_category.id', '=', 'listings_information.property_category_id')
            ->leftJoin('subtype_properties', 'subtype_properties.id', '=', 'listings_information.subtype_property_id')
            ->where('listings.is_external', '!=', 1)
            ->where('offices.active_office', '=', 1)
            ->when($filters['status_id'] ?? null, fn($q, $v) => $q->where('listings.status_listing_id', $v))
            ->when($filters['office_id'] ?? null, fn($q, $v) => $q->where('agents.office_id', $v))
            ->distinct()
            ->orderBy('offices.name')
            ->orderBy('users.name_to_show')
            ->orderBy('listings.date_of_listing', 'asc');
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function mapListingToRow($listing, bool $formatDates = false): array
    {
        $formatDate = fn($date) => $formatDates && $date
            ? Date::PHPToExcel(new \DateTime($date))
            : ($date ?? null);

        return [
            $listing->region_name,
            $listing->office_name,
            $listing->name_to_show,
            $listing->remax_title_name,
            $listing->MLSID,
            '',
            $listing->currency_symbol,
            $listing->price_amount,
            $listing->city_name,
            $listing->zone_name,
            $listing->first_address,
            $listing->number,
            $listing->zip_code,
            $listing->status_listing_name,
            $listing->transaction_type_name,
            $listing->contract_type_name,
            $listing->name_properties_categories,
            $listing->subtype_property_name,
            'No',
            $formatDate($listing->date_of_listing),
            $formatDate($listing->date_of_listing),
            $formatDate($listing->updated_at),
            $formatDate($listing->cancellation_date),
            $listing->cancellation_reason_name,
            $formatDate($listing->sold_date),
            $formatDate($listing->contract_end_date),
            $listing->recruitment_commission,
            $listing->sales_commission,
            $listing->current_listing_price,
            $listing->current_listing_price
                ? $listing->current_listing_price * (($listing->recruitment_commission + $listing->sales_commission) / 100)
                : null,
            trim(($listing->owner_name ?? '') . ' ' . ($listing->owner_last_name ?? '')),
            $listing->owner_id,
            $listing->owner_email,
            $listing->owner_mobile,
            $listing->owner_home_phone,
            $this->calculateDaysInMarket($listing),
            '',
            '',
            '',
        ];
    }

    public function calculateDaysInMarket($listing): ?int
    {
        // implementar lógica real si se desea calcular días desde captación
        return $listing->date_of_listing ? now()->diffInDays($listing->date_of_listing) : null;
    }
}
