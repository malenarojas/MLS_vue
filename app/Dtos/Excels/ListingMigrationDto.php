<?php

namespace App\Dtos\Excels;

use App\Models\{
  Agent,
  CancellationReason,
  Contact,
  ContractType,
  PropertyCategory,
  StatusListing,
  SubtypeProperty,
  Zone,
  City,
  RemaxTitleToShow
};
use App\Utils\StringUtil;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;

class ListingMigrationDto extends Data
{
  public function __construct(
    // Columnas desde excel
    #[MapInputName('Departamento')]
    public ?string $departament,
    #[MapInputName('Nombre_Oficina')]
    public ?string $officeName,
    #[MapInputName('Nombre_Agente_Asociado')]
    public ?string $agentName,
    #[MapInputName('Titulo_a_Enseñar')]
    public ?string $agentTitle,
    #[MapInputName('MLS_ID')]
    public ?string $mlsId,
    #[MapInputName('Integrador_de_ID')]
    public ?int $integradorId,
    #[MapInputName('Moneda')]
    public ?string $currency,
    #[MapInputName('Precio')]
    public ?int $price,
    #[MapInputName('Ciudad')]
    public ?string $city,
    #[MapInputName('Hora_Local')]
    public ?string $zone,
    #[MapInputName('Dirección')]
    public ?string $address,
    #[MapInputName('Numero_en_la_Calle')]
    public ?string $number,
    #[MapInputName('Código_Postal')]
    public ?string $zipCode,
    #[MapInputName('Status_de_Captación')]
    public ?string $statusName,
    #[MapInputName('Tipo_de_Transacción')]
    public ?string $transactionName,
    #[MapInputName('Tipo_de_Transacción.1')]
    public ?string $contractName,
    #[MapInputName('Categoría_de_propiedad')]
    public ?string $categoryName,
    #[MapInputName('Tipo_de_Propiedad')]
    public ?string $subTypePropertyName,
    #[MapInputName('Borrar')]
    public ?string $isDelete,
    #[MapInputName('Fecha_Captación')]
    public ?string $listingDate,
    #[MapInputName('Fecha_Cargado_a_la_Web')]
    public ?string $uplodaDateInWeb,
    #[MapInputName('Ultima_Actualización')]
    public ?string $updatedAt,
    #[MapInputName('Fecha_Cancelado')]
    public ?string $cancellationDate,
    #[MapInputName('CancellationReason')]
    public ?string $cancellationReason,
    #[MapInputName('Fecha_de_Venta/Alquiler')]
    public ?string $soldDate,
    #[MapInputName('Fecha_de_Vencimiento')]
    public ?string $contractEndDate,
    #[MapInputName('Listing_Percentage')]
    public ?int $listingPercentage,
    #[MapInputName('Porcentaje_de_Venta')]
    public ?int $soldPercentage,
    #[MapInputName('Precio_Venta/Alquiler')]
    public ?int $soldPrice,
    #[MapInputName('Comisión_Total')]
    public ?int $totalComission,
    #[MapInputName('Nombre_del_dueño')]
    public ?string $ownerName,
    #[MapInputName('Id_del_Contacto')]
    public ?int $ownerId,
    #[MapInputName('Email_del_dueño')]
    public ?string $ownerEmail,
    #[MapInputName('Cell_del_dueño')]
    public ?string $ownerCell,
    #[MapInputName('Casa_del_dueño')]
    public ?string $ownerPhone,
    #[MapInputName('Días_en_el_Mercado')]
    public ?string $marketDay,
    #[MapInputName('Transferir_Nombre_Agente_Asociado')]
    public ?string $transferedAgentName = null,
    #[MapInputName('Transferir_MLSID')]
    public ?string $transferedMlsid = null,
    #[MapInputName('Transferir_Date')]
    public ?string $transferedDate = null,
    #[MapInputName('key')]
    public ?string $key = null,

    #[Computed]
    public ?int $transactionId = null,
    #[Computed]
    public ?int $areaId = null,
    #[Computed]
    public ?string $officeId = null,
    #[Computed]
    public ?string $agentId = null,
    #[Computed]
    public ?int $statusId = null,
    #[Computed]
    public ?int $categoryId = null,
    #[Computed]
    public ?int $contractId = null,
    #[Computed]
    public ?int $subTypePropertyId = null,
    #[Computed]
    public ?int $cancellationReasonId = null,
    #[Computed]
    public ?int $cityId = null,
    #[Computed]
    public ?int $zoneId = null,
    #[Computed]
    public ?int $remaxTitleToShowId  = null,
    #[Computed]
    public ?string $transferredOfficeId = null,
    #[Computed]
    public ?string $transferredAgentId = null,
  ) {
    $status = StatusListing::firstOrCreate([
      'name' => $this->statusName,
    ]);
    $this->statusId = $status->id;

    $this->transactionId = $this->transactionName === 'For Sale' ? 1 : 2;

    if (isset($this->categoryName)) {
      $category = PropertyCategory::firstOrCreate([
        'name_properties_categories' => $this->categoryName,
      ]);
      $this->categoryId = $category->id;

      if ($this->categoryName === 'Anticrético') {
        $this->transactionId = 3; // Es anticrético
      }
    }

    $contract = ContractType::firstOrCreate([
      'name' => $this->contractName,
    ]);
    $this->contractId = $contract->id;

    $subTypeProperty = SubtypeProperty::firstOrCreate([
      'name' => $this->subTypePropertyName,
    ]);
    $this->subTypePropertyId = $subTypeProperty->id;
    $this->areaId = $subTypeProperty->area_id;

    // Formatear fechas
    $this->listingDate = $this->normalizarDate($this->listingDate);
    $this->uplodaDateInWeb = $this->normalizarDate($this->uplodaDateInWeb);
    $this->updatedAt = $this->normalizarDate($this->updatedAt);
    $this->contractEndDate = $this->normalizarDate($this->contractEndDate);
    $this->cancellationDate = $this->normalizarDate($this->cancellationDate);
    $this->soldDate = $this->normalizarDate($this->soldDate);

    // Formatear numero
    $this->integradorId = $this->normalizarInt($this->integradorId);
    $this->price = $this->price ?? 0;
    $this->listingPercentage = $this->normalizarInt($this->listingPercentage);
    $this->soldPercentage = $this->normalizarInt($this->soldPercentage);
    $this->soldPrice = $this->normalizarInt($this->soldPrice);
    $this->totalComission = $this->normalizarInt($this->totalComission);
    $this->ownerId = $this->normalizarInt($this->ownerId);

    if (!empty($this->ownerName)) {
      Contact::updateOrCreate([
        'name' => $this->ownerName,
      ], [
        'email' => $this->ownerEmail,
        'mobile' => $this->ownerCell,
        'cellular' => $this->ownerPhone,
      ]);
    }

    // Ids de tablas
    $cancellationReason = CancellationReason::where('name', 'like', "%$this->cancellationReason%")->first();
    $this->cancellationReasonId = $cancellationReason->id;

    $city = City::where('name', $this->city)->first();
    $this->cityId = $city?->id;
    $zone = Zone::where('name', $this->zone)->first();
    $this->zoneId = $zone?->id;

    $remaxTitleToShow = RemaxTitleToShow::firstOrCreate([
      'name' => $this->agentTitle,
    ]);
    $this->remaxTitleToShowId = $remaxTitleToShow->id;

    $this->agentId = StringUtil::getFirstPart($this->mlsId);
    $this->officeId = StringUtil::getOfficeIdfromAgentId($this->agentId);

    if ($this->transferedMlsid) {
      $this->transferredAgentId = StringUtil::getFirstPart($this->transferedMlsid);
      $this->transferredOfficeId = StringUtil::getOfficeIdfromAgentId($this->transferredAgentId);
    }

    if (strlen($this->key) < 6) {
      $this->key = null;
    }
  }

  private function normalizarDate(?string $date): ?string
  {
    if ($date === '0000-00-00 00:00:00' || empty($date)) {
      return null;
    }

    return $date;
  }

  private function normalizarInt(?int $value): ?int
  {
    return ($value === 0) ? null : $value;
  }
}
