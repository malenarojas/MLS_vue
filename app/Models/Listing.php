<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Listing extends Model
{
    use Translatable;

    protected $fillable = [
        'key',
        'MLSID',
        'transaction_type',
        'cancellation_date',
        'contract_end_date',
        'date_of_listing',
        'description_website',
        'is_published',
        'reference',
        'property_registration_number',
        'financial_note',
        'title',
        'description_website',
        'marketing_description',
        'location_information',
        'translations',
        // Relación con otras tablas
        'agent_id', // Agente que publica la propiedad
        'area_id',
        'contract_type_id',
        'price_type_id',
        'project_id',
        'status_listing_id',
        'cancellation_reason_id',
        'is_external',
        'transaction_type_id', // Tipo de transacción
        'rent_timeframe_id', // Periodo de renta
    ];

    protected $casts = [
        'translations' => 'array',
    ];

    protected $edit = [
        'title' => [
            'label' => 'Titulo',
        ],
    ];

    // protected $appends = ['price'];

    // Indica los campos que se pueden traducir
    protected $translatable = [
        'title',
        'description_website',
        'marketing_description',
        'location_information',
    ];

    // Relations
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'agent_listing', 'listing_id', 'agent_id')->withPivot('type');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function listing_prices(): HasMany
    {
        return $this->hasMany(ListingPrice::class, 'listing_id');
    }

    public function price()
    {
        // Para obtener solo un precio por defecto
        return $this->hasOne(ListingPrice::class);
    }

    public function price_type(): BelongsTo
    {
        return $this->belongsTo(PriceType::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function listing_information(): HasOne
    {
        return $this->hasOne(ListingInformation::class)->withDefault();
    }

    public function commission_option(): HasOne
    {
        return $this->hasOne(CommissionOption::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feacture_listing', 'listing_id', 'feature_id');
    }

    // Imagen por default
    public function default_imagen()
    {
        return $this->hasOne(Multimedia::class)->where('is_default', 1);
    }

    public function multimedias(): HasMany
    {
        return $this->hasMany(Multimedia::class);
    }

    public function documentation(): BelongsToMany
    {
        return $this->belongsToMany(Documentation::class, 'documentation_listing', 'listing_id', 'documentation_id');
    }

    public function status_listing(): BelongsTo
    {
        return $this->belongsTo(StatusListing::class, 'status_listing_id');
    }

    public function addition_payments(): HasMany
    {
        return $this->hasMany(AdditionalPayments::class);
    }

    public function contract_type(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id', 'id');
    }

    // public function owners(): BelongsToMany
    // {
    //     return $this->belongsToMany(Owner::class, 'listing_owner', 'listing_id', 'owner_id');
    // }

    // Relacion tipo de transacción
    public function transaction_type(): BelongsTo
    {
        return $this->belongsTo(ListingTransactionType::class);
    }

    public function buyers(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'buyer', 'listing_id', 'contact_id');
    }

    public function owners(): BelongsToMany
    {
        // Listing -> Owner <- Contact
        return $this->belongsToMany(Contact::class, 'owner', 'listing_id', 'contact_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'listing_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function quality_control()
    {
        return $this->hasMany(ListingQualityControl::class);
    }

    public function rent_timeframe(): BelongsTo
    {
        return $this->belongsTo(RentTimeframe::class);
    }

    public function cancellation_reason(): BelongsTo
    {
        return $this->belongsTo(CancellationReason::class);
    }
}
