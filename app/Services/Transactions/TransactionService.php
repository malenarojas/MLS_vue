<?php

namespace App\Services\Transactions;

use App\Dtos\Excels\ListingMigrationDto;
use App\Models\Agent;
use App\Models\Commission;
use App\Models\ExchangeRate;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\SubtypeProperty;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TypeProperty;
use App\Services\CommissionService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Assert;

class TransactionService
{
	protected $user_id;
	protected $commissionService;
	protected $paymentService;

	public function __construct(CommissionService $commissionService, PaymentService $paymentService)
	{
		$this->commissionService = $commissionService;
		$this->paymentService = $paymentService;
	}

	public function create($data)
	{

		$transaction = Transaction::where('listing_id', $data['listing_id'])->first();

		$agent = Agent::find($data['agent_id']);
		$listing = Listing::find($data['listing_id']);

		$isExternal = $listing->is_external;

		if ($listing->transaction_type_id == 2) {
			$listing->status_listing_id = 7;
		} else {
			$listing->status_listing_id = 8;
		}
		$listing->save();

		$exchange_rate = ExchangeRate::orderBy('id', 'DESC')->first();

		if (!$transaction) {
			if (!$isExternal) {
				$new_transaction_listing = Transaction::create([
					'agent_id' => $agent->id,
					'office_id' => $agent->office->id,
					'transaction_status_id' => 3,
					'trr_report_id' => $this->getNextTrrId(),
					'listing_id' => $listing->id,
					'current_listing_price' => $listing->listing_prices
						? optional($listing->listing_prices->where('currency_id', 1)->first())->amount ?? 0
						: 0,
					'transaction_type_id' => 1,
					'internal_id' => (string) Str::uuid(),
					'mls_id' => $listing?->MLSID,
					'exchange_rate_id' => $exchange_rate?->id ?? null,
				]);
			} else {
				$new_transaction_listing = Transaction::create([
					'transaction_status_id' => 3,
					'trr_report_id' => $this->getNextTrrId(),
					'listing_id' => $listing->id,
					'current_listing_price' => $listing->listing_prices
						? optional($listing->listing_prices->where('currency_id', 1)->first())->amount ?? 0
						: 0,
					'transaction_type_id' => 1,
					'internal_id' => (string) Str::uuid(),
					'exchange_rate_id' => $exchange_rate?->id ?? null,
				]);
			}

			$new_transaction_sell = Transaction::create([
				'transaction_status_id' => 3,
				'trr_report_id' => $this->getNextTrrId(),
				'listing_id' => $listing->id,
				'current_listing_price' => $listing->listing_prices
					? optional($listing->listing_prices->where('currency_id', 1)->first())->amount ?? 0
					: 0,
				'transaction_type_id' => 2,
				'internal_id' => (string) Str::uuid(),
				'mls_id' => $listing?->MLSID,
				'agent_id' => $isExternal ? $agent->id : null,
				'office_id' => $isExternal ? $agent->office->id : null,
				'exchange_rate_id' => $exchange_rate?->id ?? null,
			]);

			//Caso sea externo se trabaja con el de venta
			$transaction = $isExternal ? $new_transaction_sell : $new_transaction_listing;
		}

		return $transaction;
	}

	public function getStepData($data)
	{
		if (
			isset($data['step']) && !empty($data['step']) &&
			isset($data['internal_id']) && !empty($data['internal_id'])
		) {
			$transaction = Transaction::where('internal_id', $data['internal_id'])->first();

			switch ($data['step']) {
				case 1:
					/**
					 * Calcula los dias en el mercado en base al dia de hoy
					 * en caso no exista el sold_date
					 */
					//return $transaction->listing->date_of_listing;
					$days_on_market = $transaction->listing->date_of_listing ? Carbon::createFromFormat(
						'Y-m-d',
						$transaction->listing->date_of_listing
					)->diffInDays($transaction->sold_date ?
						Carbon::createFromFormat('Y-m-d', $transaction->sold_date) :
						Carbon::today()) : 0;

					$location = $transaction->listing->location;

					if ($location) {
						$location_name = $location->second_address ?? $location->first_address;
						$location_name .= $location->zone?->name ? ', ' . $location->zone->name . ', ' : ', ';
						$location_name .= $location->city?->name ? $location->city->name . ', ' : '';
						$location_name .= $location->city?->province?->name ? $location->city->province->name . ', ' : '';
						$location_name .= $location->city?->province?->state?->name ?? '';
					} else {
						$location_name = 'Sin dirección';
					}

					$image_url = $transaction->listing?->multimedias?->first()?->getUrlAttribute() ?? null;



					return [
						'current_listing_price' => $transaction->current_listing_price,
						'date_of_listing' => $transaction->listing?->date_of_listing ? Carbon::createFromFormat('Y-m-d', $transaction->listing->date_of_listing)->format('d/m/Y') : null,
						'days_on_market' => $days_on_market,
						'transaction_status_id' => $transaction->transaction_status_id,
						'sold_price' => $transaction->current_listing_price_usd,
						'sold_date' => $transaction->sold_date ? Carbon::createFromFormat('Y-m-d', $transaction->sold_date)->format('d/m/Y') : null,
						'possession_date' => $transaction->possession_date ? Carbon::createFromFormat('Y-m-d', $transaction->possession_date)->format('d/m/Y') : null,
						'listing_name' => $location_name,
						'owner' => '-', //Hacer cuando haya owners
						'listing_id' => $transaction->listing->MLSID,
						'listing_transaction_type' => $transaction->listing->transaction_type->name,
						'type_property' => $transaction->listing->listing_information?->subtype_property?->name ?
							$transaction->listing->listing_information->subtype_property->name : '-',
						'side' => $transaction->both == 1 ? 'both' : ($transaction->transaction_type_id == 1 ? 'listing' : 'selling'),
						'restricted_side_selection' => $transaction->transaction_type_id == 2 && !$transaction->both == 1,
						'transaction_type_id' => $transaction->transaction_type_id,
						'is_external' => $transaction->listing && $transaction->listing->is_external,
						'image_url' => $image_url,
					];
					break;

				case 2:

					return $this->commissionService->getCommissionsOrCreate($transaction->id);
					break;

				case 3:

					return $this->paymentService->getPayments($transaction->id);
					break;

				case 4:

					$another_transaction = Transaction::where('listing_id', $transaction->listing_id)
						->whereNot('id', $transaction->id)->first();

					if (!$another_transaction) {
						$listing = Listing::find($transaction->listing_id);
						$exchange_rate = ExchangeRate::orderBy('id', 'DESC')->first();

						$another_transaction = Transaction::create([
							'transaction_status_id' => 3,
							'trr_report_id' => $this->getNextTrrId(),
							'listing_id' => $listing->id,
							'current_listing_price' => $listing->listing_prices
								? optional($listing->listing_prices->where('currency_id', 2)->first())->amount ?? 0
								: 0,
							'transaction_type_id' => 2,
							'exchange_rate_id' => $exchange_rate?->id ?? null,
							'internal_id' => (string) Str::uuid(),
						]);
					}
					return $this->commissionService->getCommissionsOrCreate($another_transaction->id, false);

					break;

				case 5:

					$another_transaction = Transaction::where('listing_id', $transaction->listing_id)
						->whereNot('id', $transaction->id)->first();
					return $this->paymentService->getPayments($another_transaction->id);
					break;

				case 6:

					return $transaction->listing->buyers;
					break;
				case 7:
					return [
						'bank_id' => $transaction->bank_id,
						'amount_financed' => $transaction->amount_financed,
					];
			}
		}
	}

	public function update($data)
	{

		$transaction = Transaction::where('internal_id', $data['transaction_id'])->first();
		$transaction->current_listing_price_usd = $data['sold_price'];
		$transaction->current_listing_price = $data['sold_price'];
		$transaction->sold_date = $data['sold_date'] ? Carbon::createFromFormat('d/m/Y', $data['sold_date'])->format('Y-m-d') : null;
		$transaction->possession_date = $data['possession_date'] ? Carbon::createFromFormat('d/m/Y', $data['possession_date'])->format('Y-m-d') : null;

		if ($transaction->getAttributes() != $transaction->getOriginal()) {
			if ($transaction->transaction_status_id != 3) {
				$transaction->transaction_status_id = 4;
			}
		}

		$transaction->save();

		//dd($transaction, $data);

		if ($transaction->transaction_type_id == 1) {
			$transaction_selling = Transaction::where('listing_id', $transaction->listing_id)
				->whereNot('transaction_type_id', 1)->first();

			$transaction_selling->current_listing_price_usd = $data['sold_price'];
			$transaction_selling->current_listing_price = $data['sold_price'];
			$transaction_selling->sold_date = $data['sold_date'] ? Carbon::createFromFormat('d/m/Y', $data['sold_date'])->format('Y-m-d') : null;
			$transaction_selling->possession_date = $data['possession_date'] ? Carbon::createFromFormat('d/m/Y', $data['possession_date'])->format('Y-m-d') : null;

			if ($data['side'] == 'both') {
				$transaction_selling->agent_id = $transaction->agent_id;
				$transaction_selling->office_id = $transaction->office_id;
				$transaction_selling->both = 1;

				$transaction->both = 1;
				$transaction->save();
			} else {
				//Cas ode que antes esta transaccion era both
				echo ('entra a no both');
				if ($transaction_selling->both == 1) {
					echo ('detecta otro trr como both');
					//Borrar el agente anterior (Mismo agente que la transaccion de listing)
					$transaction_selling->both = 0;
					$transaction_selling->agent_id = null;
					$transaction_selling->office_id = null;

					//Asignamos como gente principal del trr al agente de la primera comision (caso existiera comisiones)
					$transaction_selling_commissions = $transaction_selling->commissions;
					if (count($transaction_selling_commissions) > 0) {
						echo ('cambia de dueño de trr');
						$firstCommission = $transaction_selling_commissions->first();
						$transaction_selling->agent_id = $firstCommission->agent->id;
						$transaction_selling->office_id = $firstCommission->agent->office->id;
					}

					$transaction_selling->save();

					$transaction->both = 0;
					$transaction->save();
				}
			}

			if ($transaction_selling->getAttributes() != $transaction_selling->getOriginal()) {
				if ($transaction_selling->transaction_status_id != 3) {
					$transaction_selling->transaction_status_id = 4;
				}
			}

			$transaction_selling->save();
		}

		return $transaction;
	}

	private function getNextTrrId()
	{
		$lastTrr = Transaction::orderBy('trr_report_id', 'DESC')->first();
		return $lastTrr ? $lastTrr->trr_report_id + 1 : 1;
	}

	public function getDatosTransaccionesPorTipos($data)
	{
		$meses = $data['mes'];
		$anios = $data['anio'];
		$tipoTransaccion = $data['type'];

		$transacciones = Transaction::select('transactions.*')
			->leftJoin('listings', 'listings.id', '=', 'transactions.listing_id')
			->leftJoin('listings_information', 'listings.id', '=', 'listings_information.listing_id');

		$transacciones->where(function ($query) use ($meses, $anios) {
			foreach ($anios as $anio) {
				foreach ($meses as $mes) {
					$fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
					$fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();
					$query->orWhereBetween('transactions.sold_date', [$fechaInicio->format('Y-m-d'), $fechaFinal]);
				}
			}
		});

		if (isset($data['state_id']) && $data['state_id'] != 0) {
			$transacciones->leftJoin('locations', 'locations.listing_id', 'listings.id')
				->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
				->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id');
		}

		if (isset($data['province_id']) && $data['province_id'] != []) {
			$transacciones->whereIn('provinces.id', $data['province_id']);
		}

		if (isset($data['state_id']) && $data['state_id'] != []) {
			$transacciones->whereIn('provinces.state_id', $data['state_id']);
		}

		if (isset($data['city_id']) && $data['city_id'] != []) {
			$transacciones->whereIn('cities.id', $data['city_id']);
		}

		if (isset($data['zone_id']) && $data['zone_id'] != []) {
			$transacciones->whereIn('locations.zone_id', $data['zone_id']);
		}

		if (isset($data['office_id']) && $data['office_id'] != []) {
			$transacciones->whereIn('transactions.office_id', $data['office_id']);
		}

		if (isset($data['agent_id']) && $data['agent_id'] != []) {
			$transacciones->whereIn('transactions.agent_id', $data['agent_id']);
		}

		if (!empty($tipoTransaccion)) {

			$transacciones->where(function ($query) use ($tipoTransaccion) {

				if (in_array('venta', $tipoTransaccion)) {
					$query->orWhere('listings.transaction_type_id', 1);
				}

				if (in_array('alquiler', $tipoTransaccion)) {
					$query->orWhere('listings.transaction_type_id', 2);
				}

				if (in_array('anticretico', $tipoTransaccion)) {
					$query->orWhere('listings.transaction_type_id', 3);
				}
			});
		}

		$transacciones = $transacciones->get();

		$transacciones = $this->filtroBothOneSide($transacciones, 0);

		$transaccionesFormateadas = [];


		foreach ($transacciones as $transaccion) {
			if (!isset($data['porpertyTypename'])) {

				$tipoPropiedad = $transaccion->listing->listing_information->subtype_property->type_property->name ?? null;
				if ($tipoPropiedad) {
					if (!isset($transaccionesFormateadas[$tipoPropiedad])) {
						$transaccionesFormateadas[$tipoPropiedad] = [
							'subtype_name' => $tipoPropiedad,
							'cantidad' => 0,
						];
					}
					$transaccionesFormateadas[$tipoPropiedad]['cantidad']++;
				} else {
					if (!isset($transaccionesFormateadas['Sin categoría'])) {
						$transaccionesFormateadas['Sin categoría'] = [
							'subtype_name' => 'Sin categoría',
							'cantidad' => 0,
						];
					}
					$transaccionesFormateadas['Sin categoría']['cantidad']++;
				}
			} else {

				$subTipoPropiedad = $transaccion->listing->listing_information->subtype_property ?? null;
				$typeProperty = TypeProperty::where('name', $data['porpertyTypename'])->first();

				if (!$typeProperty) {
					if (!$transaccion->listing && !$subTipoPropiedad) {
						if (!isset($transaccionesFormateadas['Sin categoría'])) {

							$transaccionesFormateadas['Sin categoría'] = [
								'subtype_name' => 'Sin categoría',
								'cantidad' => 1,
								'transaction_id' => $transaccion->id,
							];
						} else {
							$transaccionesFormateadas['Sin categoría']['cantidad']++;
						}
					}
				} else {
					if ($subTipoPropiedad && $subTipoPropiedad->type_property_id == $typeProperty->id) {

						if (!isset($transaccionesFormateadas[$subTipoPropiedad->name])) {

							$transaccionesFormateadas[$subTipoPropiedad->name] = [
								'subtype_name' => $subTipoPropiedad->name,
								'cantidad' => 1,
								'transaction_id' => $transaccion->id,
							];
						} else {
							$transaccionesFormateadas[$subTipoPropiedad->name]['cantidad']++;
						}
					}
				}
			}
		}

		usort($transaccionesFormateadas, function ($a, $b) {
			return $b['cantidad'] <=> $a['cantidad'];
		});

		return $transaccionesFormateadas;
	}

	public function getTransactionsByType($data)
	{
		$typeName = $data['propertyTypeName'] ?? "";

		$query = Transaction::selectRaw('
			COUNT(transactions.id) as transactions,
			IF( ? != "",
				IFNULL(subtype_properties.name, "Sin Categoria"),
				IFNULL(type_properties.name, "Sin Categoria")
			) as property_type
		', [$typeName])
			->join('listings', 'listings.id', '=', 'transactions.listing_id')
			->leftJoin('listings_information', 'listings.id', '=', 'listings_information.listing_id')
			->leftJoin('subtype_properties', 'listings_information.subtype_property_id', '=', 'subtype_properties.id')
			->leftJoin('type_properties', 'subtype_properties.type_property_id', '=', 'type_properties.id')
			->whereIn('transactions.transaction_status_id', [2, 4, 5])
			->where(function ($q) {
				$q->whereNull('listings.is_external')
					->orWhere('listings.is_external', 0);
			});

		if ($typeName) {
			$query->where('type_properties.name', $typeName);
		}

		// Tipo de transaccion de la propiedad (Venta, alquiler o anticretico)
		if (!empty($data['types'])) {
			$query->whereIn('listings.transaction_type_id', $data['types']);
		}

		if (!empty($data['office_ids'])) {
			$query->whereIn('transactions.office_id', $data['office_ids']);
		}

		if (!empty($data['agent_ids'])) {
			$query->whereIn('transactions.agent_id', $data['agent_ids']);
		}

		if (!empty($data['months']) && !empty($data['years'])) {
			$query->whereIn(DB::raw('MONTH(transactions.sold_date)'), $data['months'])
				->whereIn(DB::raw('YEAR(transactions.sold_date)'), $data['years']);
		}

		if (!empty($data['state_ids'])) {
			$query->join('locations', 'locations.listing_id', '=', 'listings.id')
				->join('cities', 'cities.id', '=', 'locations.city_id')
				->join('provinces', 'provinces.id', '=', 'cities.province_id')
				->whereIn('provinces.state_id', $data['state_ids']);
		}
		if (!empty($data['province_ids'])) {
			$query->whereIn('cities.province_id', $data['province_ids']);
		}
		if (!empty($data['city_ids'])) {
			$query->whereIn('locations.city_id', $data['city_ids']);
		}
		if (!empty($data['zone_ids'])) {
			$query->whereIn('locations.zone_id', $data['zone_ids']);
		}



		$result = $query->groupBy('property_type')->orderBy('transactions', 'DESC')->get();

		return $result;
	}

	public function getDatosTransacciones($data)
	{

		$transacciones = [];

		foreach ($data['meses'] as $mes) {

			$fechaInicio = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
			$fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();
			$fechaInicioComprar = Carbon::createFromFormat('Y-m-d', $data['anio'] - 1 . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
			$fechaFinalComparar = $fechaInicioComprar->copy()->endOfMonth()->toDateString();

			$queryTransaccionesVentas = $this->filtroTipoTransaccion($fechaInicio->format('Y-m-d'), $fechaFinal, 1, $data);

			$queryTransaccionesAlquileres = $this->filtroTipoTransaccion($fechaInicio->format('Y-m-d'), $fechaFinal, 2, $data);

			$queryTransaccionesAnticreticos = $this->filtroTipoTransaccion($fechaInicio->format('Y-m-d'), $fechaFinal, 3, $data);

			$queryTransaccionesExternas =
				Transaction::selectRaw('
                    DATE_FORMAT(transactions.sold_date, "%Y-%m") as mes,
                    COUNT(transactions.id) as total')
				->join('offices', 'offices.id', '=', 'transactions.office_id')
				->leftJoin('listings', 'listings.id', '=', 'transactions.listing_id')
				->whereBetween('transactions.sold_date', [$fechaInicio->format('Y-m-d'), $fechaFinal])
				->whereIn('transactions.transaction_status_id', [2, 4, 5])
				->where(function ($query) {
					$query
						->where('listings.is_external', 1);
				})
				->groupBy('mes')
				->orderBy('mes');

			if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
				$queryTransaccionesExternas->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');
				if ($data['state_id'] == 'other') {
					$queryTransaccionesExternas->whereNotIn('provinces.state_id', [1, 2, 3]);
				} else {
					$queryTransaccionesExternas->where('provinces.state_id', $data['state_id']);
				}
			}

			if (isset($data['office_id']) && $data['office_id'] != '') {
				$queryTransaccionesExternas->where('transactions.office_id', $data['office_id']);
			}
			if (isset($data['agent_id']) && $data['agent_id'] != '') {
				$queryTransaccionesExternas->where('transactions.agent_id', $data['agent_id']);
			}

			$queryTransaccionesVentasComprar = $this->filtroTipoTransaccion($fechaInicioComprar->format('Y-m-d'), $fechaFinalComparar, 1, $data);

			$queryTransaccionesAlquileresComparar = $this->filtroTipoTransaccion($fechaInicioComprar->format('Y-m-d'), $fechaFinalComparar, 2, $data);

			$queryTransaccionesAnticreticosComparar = $this->filtroTipoTransaccion($fechaInicioComprar->format('Y-m-d'), $fechaFinalComparar, 3, $data);

			$queryTransaccionesExternasComparar =
				Transaction::selectRaw('
                    DATE_FORMAT(transactions.sold_date, "%Y-%m") as mes,
                    COUNT(transactions.id) as total')
				->leftJoin('offices', 'offices.id', '=', 'transactions.office_id')
				->whereBetween('transactions.sold_date', [$fechaInicioComprar->format('Y-m-d'), $fechaFinalComparar])
				->whereNull('transactions.listing_id')
				->whereIn('transactions.transaction_status_id', [2, 4, 5])
				->groupBy('mes')
				->orderBy('mes');

			if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
				$queryTransaccionesExternasComparar->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');
				if ($data['state_id'] == 'other') {
					$queryTransaccionesExternasComparar->whereNotIn('provinces.state_id', [1, 2, 3]);
				} else {
					$queryTransaccionesExternasComparar->where('provinces.state_id', $data['state_id']);
				}
			}

			if (isset($data['office_id']) && $data['office_id'] != '') {
				$queryTransaccionesExternasComparar->where('transactions.office_id', $data['office_id']);
			}
			if (isset($data['agent_id']) && $data['agent_id'] != '') {
				$queryTransaccionesExternasComparar->where('transactions.agent_id', $data['agent_id']);
			}

			$transaccionesVentas = $queryTransaccionesVentas->get();
			$transaccionesAlquileres = $queryTransaccionesAlquileres->get();
			$transaccionesAnticreticos = $queryTransaccionesAnticreticos->get();
			$transaccionesExternas = $queryTransaccionesExternas->get();

			$transaccionesVentasComparar = $queryTransaccionesVentasComprar->get();
			$transaccionesAlquileresComparar = $queryTransaccionesAlquileresComparar->get();
			$transaccionesAnticreticosComparar = $queryTransaccionesAnticreticosComparar->get();
			$transaccionesExternasComparar = $queryTransaccionesExternasComparar->get();

			if (isset($data['side_type']) && $data['side_type'] != 0 && $data['side_type'] != '') {
				$side = $data['side_type'];
				if ($side == 2) {
					$transaccionesExternas = [];
					$transaccionesExternasComparar = [];
				}
			} else {
				$side = 0;
			}

			$transaccionesVentas = $this->filtroBothOneSide($transaccionesVentas, $side);
			$transaccionesAlquileres = $this->filtroBothOneSide($transaccionesAlquileres, $side);
			$transaccionesAnticreticos = $this->filtroBothOneSide($transaccionesAnticreticos, $side);
			// $transaccionesExternas = $this->filtroBothOneSide($transaccionesExternas, $isBoth);

			$transaccionesVentasComparar = $this->filtroBothOneSide($transaccionesVentasComparar, $side);
			$transaccionesAlquileresComparar = $this->filtroBothOneSide($transaccionesAlquileresComparar, $side);
			$transaccionesAnticreticosComparar = $this->filtroBothOneSide($transaccionesAnticreticosComparar, $side);
			//  $transaccionesExternasComparar = $this->filtroBothOneSide($transaccionesExternasComparar, $isBoth);




			$transaccionesVentas = $this->agruparTransaccionesMes($transaccionesVentas);
			$transaccionesAlquileres = $this->agruparTransaccionesMes($transaccionesAlquileres);
			$transaccionesAnticreticos = $this->agruparTransaccionesMes($transaccionesAnticreticos);
			//$transaccionesExternas = $this->agruparTransaccionesMes($transaccionesExternas);

			$transaccionesVentasComparar = $this->agruparTransaccionesMes($transaccionesVentasComparar);
			$transaccionesAlquileresComparar = $this->agruparTransaccionesMes($transaccionesAlquileresComparar);
			$transaccionesAnticreticosComparar = $this->agruparTransaccionesMes($transaccionesAnticreticosComparar);
			//$transaccionesExternasComparar = $this->agruparTransaccionesMes($transaccionesExternasComparar);



			/**
			 * TODO:\
			 *  -   Procesar las transacciones para que agrupe las cantidades como
			 *      lo hacia en las consultas
			 *  -   Filtrar dependiendo de si es both, one o two sides
			 */

			$transacciones[] = [
				'transaccionesVentas' => $transaccionesVentas,
				'transaccionesAlquileres' => $transaccionesAlquileres,
				'transaccionesAnticreticos' => $transaccionesAnticreticos,
				'transaccionesExternas' => $transaccionesExternas,
				'transaccionesVentasComparar' => $transaccionesVentasComparar,
				'transaccionesAlquileresComparar' => $transaccionesAlquileresComparar,
				'transaccionesAnticreticosComparar' => $transaccionesAnticreticosComparar,
				'transaccionesExternasComparar' => $transaccionesExternasComparar,
			];
		}

		return $transacciones;
	}

	private function agruparTransaccionesMes($transactions)
	{
		$agrupadoMes = [];

		foreach ($transactions as $transaction) {
			$mes = $transaction->mes;
			if (!isset($agrupadoMes[$mes])) {
				$agrupadoMes[$mes] = [
					'mes' => $mes,
					'total' => 0,
				];
			}
			$agrupadoMes[$mes]['total'] += 1;
		}

		ksort($agrupadoMes);

		$noKeyArray = [];

		foreach ($agrupadoMes as $agrupado) {
			$noKeyArray[] = $agrupado;
		}

		return $noKeyArray;
	}

	//Side 0 para obtener toda la lista filtrada, 1 para 1 side, 2 para 2 side

	public function filtroBothOneSide($transactions, $side)
	{
		$transaction_ids = [];
		// foreach ($transactions as $key => $transaction) {

		// 	// Deprecated 12/03/2025
		// 	// if($side == 0){
		// 	// 	if(($transaction->both == 1 && !in_array($transaction->listing_id, $transaction_ids)) || ($transaction->both == 0)){
		// 	// 		$transaction_ids[] = $transaction->listing_id;
		// 	// 	}else{
		// 	// 		unset($transactions[$key]);
		// 	// 	}
		// 	// }

		// 	if($side == 1) {
		// 		if($transaction->both == 0){
		// 			$transaction_ids[] = $transaction->id;
		// 		}else{
		// 			unset($transactions[$key]);
		// 		}
		// 	}

		// 	if($side == 2) {
		// 		if($transaction->both == 1 && !in_array($transaction->listing_id, $transaction_ids)){
		// 			$transaction_ids[] = $transaction->listing_id;
		// 		}else{
		// 			unset($transactions[$key]);
		// 		}
		// 	}
		// }

		if ($side == 2) {
			$transactions = $transactions->filter(function ($transaction) {
				return $transaction->both == 1 &&
					($transaction->is_external == 0 || is_null($transaction->is_external));
			});
		}
		if ($side == 1) {
			$transactions = $transactions->filter(function ($transaction) {
				return $transaction->both == 0;
			});
		}

		return $transactions;
	}

	private function filtroTipoTransaccion($fechaInicio, $fechaFinal, $tipo, $data)
	{
		$query = Transaction::selectRaw('
                transactions.*,
				listings.is_external,
                DATE_FORMAT(transactions.sold_date, "%Y-%m") as mes
            ')
			->whereBetween('transactions.sold_date', [$fechaInicio, $fechaFinal])
			->join('listings', 'listings.id', '=', 'transactions.listing_id')
			->leftJoin('offices', 'offices.id', '=', 'transactions.office_id')
			->whereIn('transactions.transaction_status_id', [2, 4, 5])
			->where('listings.transaction_type_id', $tipo)
			->where(function ($query) {
				$query->whereNull('listings.is_external')
					->orWhere('listings.is_external', 0);
			});

		if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
			$query->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');
			if ($data['state_id'] == 'other') {
				$query->whereNotIn('provinces.state_id', [1, 2, 3]);
			} else {
				$query->where('provinces.state_id', $data['state_id']);
			}
		}

		if (isset($data['office_id']) && $data['office_id'] != '') {
			$query->where('transactions.office_id', $data['office_id']);
		}
		if (isset($data['agent_id']) && $data['agent_id'] != '') {
			$query->where('transactions.agent_id', $data['agent_id']);
		}
		return $query;
	}

	public function getDetallesTransacciones($data)
	{

		//return $data;
		$query = Transaction::selectRaw('
            transactions.id,
			transactions.trr_report_id as trr_id,
			transactions.transaction_status_id,
			transactions.id as transaction_id,
			listings.MLSID,
			listings.is_external,
			transactions.transaction_type_id,
			transactions.both,
			IF(' . $data['inDollars'] . ' = 1 ,
				IFNULL(transactions.current_listing_price / exchange_rates.amount, transactions.current_listing_price / 6.96),
				transactions.current_listing_price
				) as current_listing_price,
			listings_information.land_m2,
			listings_information.number_bedrooms,
			listings_information.number_bathrooms,
			transactions.transaction_type_id as listing_transaction_type_id,
			listing_transaction_types.name as listing_transaction_type,
			transactions.sold_date,
			users.name_to_show as agent,
			listings.area_id,
			transactions.internal_id
        ')->leftJoin('listings', 'listings.id', '=', 'transactions.listing_id')
			->leftJoin('agents', 'agents.id', '=', 'transactions.agent_id')
			->leftJoin('users', 'users.id', '=', 'agents.user_id')
			->leftJoin('listings_information', 'listings_information.listing_id', '=', 'listings.id')
			->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'transactions.exchange_rate_id')
			->leftJoin('listing_transaction_types', 'listing_transaction_types.id', '=', 'listings.transaction_type_id')
			->groupBy(
				'transactions.id',
				'transactions.trr_report_id',
				'transactions.transaction_status_id',
				'listings.MLSID',
				'listings.is_external',
				'transactions.transaction_type_id',
				'transactions.both',
				'transactions.current_listing_price',
				'listings_information.land_m2',
				'listings_information.number_bedrooms',
				'listings_information.number_bathrooms',
				'listing_transaction_types.id',
				'listing_transaction_types.name',
				'transactions.sold_date',
				'users.id',
				'users.name_to_show',
				'listings.area_id',
				'transactions.internal_id',
				'exchange_rates.amount'
			);

		$this->filtroFechas($query, $data['mes'] ?? [], $data['anio'] ?? []);
		$this->filtroUbicaciones($query, $data);

		if (isset($data['agent_id']) && $data['agent_id'] != []) {
			$query->whereIn('transactions.agent_id', $data['agent_id']);
		}

		if (isset($data['office_id']) && $data['office_id'] != []) {
			$query->whereIn('transactions.office_id', $data['office_id']);
		}

		$tipoTransaccion = $data['type'] ?? [];

		if (!empty($tipoTransaccion)) {

			$query->where(function ($subQuery) use ($tipoTransaccion) {

				if (in_array('venta', $tipoTransaccion)) {
					$subQuery->orWhere('listings.transaction_type_id', 1);
				}

				if (in_array('alquiler', $tipoTransaccion)) {
					$subQuery->orWhere('listings.transaction_type_id', 2);
				}

				if (in_array('anticretico', $tipoTransaccion)) {
					$subQuery->orWhere('listings.transaction_type_id', 3);
				}
			});
		}

		if (isset($data['porpertySubtypename']) && $data['porpertySubtypename'] != []) {

			$subType = SubtypeProperty::where('name', $data['porpertySubtypename'])->first();
			if ($subType) {
				$query
					->where('listings_information.subtype_property_id', $subType->id);
			} else {
				if ($data['porpertySubtypename'] == "Sin categoría") {
					$query->leftJoin('subtype_properties', 'subtype_properties.id', '=', 'listings_information.subtype_property_id')
						->whereNull('subtype_properties.type_property_id');
				}
			}
		}

		if (isset($data['transaction_status_id']) && $data['transaction_status_id'] != []) {
			$query->whereIn('transactions.transaction_status_id', $data['transaction_status_id']);
		}

		if (isset($data['transaction_type_id']) && $data['transaction_type_id'] != [] && $data['transaction_type_id'] != [3]) {
			$query->whereIn('transactions.transaction_type_id', $data['transaction_type_id'])
				->where('transactions.both', 0);
		} else {
			if ($data['transaction_type_id'] == [3]) {
				$query->where('transactions.both', 1);
			}
		}

		if (isset($data['paginated']) && $data['paginated'] != '') {
			$query->orderBy('transactions.updated_at', 'DESC')
				->orderBy('transactions.sold_date', 'DESC');
			// ->limit(5);
		}

		//Caso de que se este buscando por trr_id quita la restriccion de los ya cargados
		if (
			isset($data['transactions_loaded']) && !empty($data['transactions_loaded']) &&
			(!isset($data['trr_id']) || empty($data['trr_id']))
		) {
			$query->whereNotIn('transactions.id', $data['transactions_loaded']);
		}

		if (isset($data['trr_id']) && !empty($data['trr_id'])) {
			$query->where(function ($query) use ($data) {
				$query->whereIn('transactions.trr_report_id', $data['trr_id'])
					->orWhere('transactions.mls_id', $data['trr_id'])
					->orWhere('listings.MLSID', $data['trr_id']);
			});
		}



		$this->filterBothSidesTransactions($query);
		$this->filterExternalTransactions($query);

		$result = $query->get();

		foreach ($result as $item) {
			$item['pending_payments'] = $this->getPendingPayments($item->id);
		}

		return $result;
	}

	private function getPendingPayments($transaction_id)
	{
		return Payment::where('transaction_id', $transaction_id)
			->whereNull('received_date')
			->count();
	}

	private function filtroFechas(&$query, $meses, $anios)
	{
		$query->where(function ($subQuery) use ($meses, $anios) {
			foreach ($anios as $anio) {
				foreach ($meses as $mes) {
					$fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
					$fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

					$subQuery->orWhereBetween('transactions.sold_date', [$fechaInicio->format('Y-m-d'), $fechaFinal]);
				}
			}
		});
	}

	private function filtroUbicaciones(&$query, $data)
	{

		if (isset($data['state_id']) && $data['state_id'] != 0) {
			$query->leftJoin('locations', 'locations.listing_id', 'listings.id')
				->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
				->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id');
		}

		if (isset($data['province_id']) && $data['province_id'] != []) {
			$query->whereIn('provinces.id', $data['province_id']);
		}

		if (isset($data['state_id']) && $data['state_id'] != []) {
			$query->whereIn('provinces.state_id', $data['state_id']);
		}

		if (isset($data['city_id']) && $data['city_id'] != []) {
			$query->whereIn('cities.id', $data['city_id']);
		}

		if (isset($data['zone_id']) && $data['zone_id'] != []) {
			$query->whereIn('locations.zone_id', $data['zone_id']);
		}
	}

	public function detalleTransaccion($id)
	{
		$query_tr = Transaction::where('internal_id', $id)
			->select(
				'id',
				'internal_id',
				'mls_id',
				'both',
				'listing_id',
			)->first();
		$com1 = [];
		$com2 = [];
		$pay1 = [];
		$pay2 = [];

		$listing = Listing::find($query_tr->listing_id);
		$is_external = $listing?->is_external == 1 ?? 0;

		if ($query_tr->both == 1) {
			$transaccion = $this->getTr('mls_id', $query_tr->mls_id);
		} else {
			$transaccion = $this->getTr('internal_id', $id);
		}
		if (!$listing) {

			$transa = $this->getTr('internal_id', $id);
		} else {
			if ($is_external) {
				$transa = $this->getTr('listing_id', $query_tr->listing->id);
			} else {
				$transa = $this->getTr('mls_id', $query_tr->mls_id);
			}
		}


		foreach ($transa as $key => $value) {
			array_push($com1, $this->getCommisions($value->id));
		}

		$new_array = Arr::collapse($com1);
		$pay1 = $this->getPayments($transaccion[0]->id);

		if ($query_tr->both == 1) {
			$pay2 = $this->getPayments($transaccion[1]->id);
		}

		//Get image URL
		$imageUrl = $listing?->multimedias?->first()?->getUrlAttribute() ?? null;

		return  [
			"tr" => $transaccion,
			'com1' => $new_array,
			'com2' => $com2,
			'pay1' => $pay1,
			'pay2' => $pay2,
			'image_url' => $imageUrl
		];
	}

	public function setFinantiation($data)
	{
		$transaction = Transaction::where('internal_id', $data['transaction_id'])->first();
		$transaction->bank_id = $data['bank_id'];
		$transaction->amount_financed = $data['amount_financed'];

		if (($transaction->getAttributes() != $transaction->getOriginal()) || $transaction->transaction_status_id == 3) {
			$transaction->transaction_status_id = 4;
		}

		$transaction->save();

		// $otherTransaction = Transaction::where('listing_id', $transaction->listing_id)
		// 	->whereNot('id', $transaction->id)->first();

		// if($otherTransaction) {
		// 	$otherTransaction->bank_id = $data['bank_id'];
		// 	$otherTransaction->amount_financed = $data['amount_financed'];

		// 	$otherTransaction->save();
		// }

		return $transaction;
	}

	public function filterBothSidesTransactions(&$queryTransactions)
	{

		$queryTransactions->where(function ($subQuery) {
			$subQuery->whereNot('transactions.both', 1)
				->orWhere('transactions.transaction_type_id', 1);
		});
	}

	public function filterExternalTransactions(&$queryTransactions)
	{
		$queryTransactions->whereNot(function ($subQuery) {
			$subQuery->where('transactions.transaction_type_id', 1)
				->where('listings.is_external', 1);
		});
	}

	private function getPayments($tr_id)
	{
		return Payment::where('transaction_id', $tr_id)
			->with(['agent' => function ($query) {
				$query->select(
					"id",
					"agent_internal_id",
					"office_id",
					"region_id",
					"user_id",
					"date_joined",
					"agent_status_id",
					"license_type",
					"commission_percentage",
				)
					->with(['user' => function ($query) {
						$query->select(
							"id",
							"first_name",
							"middle_name",
							"last_name",
							"name_to_show",
							"gender",
							"phone_number",
							"email"
						);
					}]);
			}])->get() ?? [];
	}

	private function getCommisions($tr_id)
	{
		return Commission::where('transaction_id', $tr_id)
			->with(['transaction' => function ($query) {
				$query->select('id', 'internal_id', 'transaction_status_id', 'transaction_type_id');
			}])
			->with(['agent' => function ($query) {
				$query->select(
					"id",
					"agent_internal_id",
					"office_id",
					"region_id",
					"user_id",
					"date_joined",
					"agent_status_id",
					"license_type",
					"commission_percentage",
				)
					->with(['user' => function ($query) {
						$query->select(
							"id",
							"first_name",
							"middle_name",
							"last_name",
							"name_to_show",
							"gender",
							"phone_number",
							"email"
						);
					}]);
			}])->select('*')->orderBy('commissions.commission_type_id', 'asc')->get();
	}

	private function getTr($colum, $value)
	{
		return Transaction::where($colum, $value)
			->select('*')
			->with(
				[
					'bank' => function ($query) {
						$query->select('id', 'name');
					},
					'listing' =>
					function ($query) {
						$query->select(
							"id",
							"key",
							"MLSID",
							"date_of_listing",
							"contract_end_date",
							"cancellation_date",
							//"cancellation_reason",
							"first_upload_date",
							"area_id",
							"is_external",
							"status_listing_id",
							"agent_id",
							"transaction_type_id"
						)
							->with(['buyers:id,name,mobile'])
							->with(['location' => function ($query) {
								$query->select(
									"id",
									"zone_id",
									"first_address",
									"number",
									"city_id",
									"listing_id"
								)
									->with(['zone' => function ($query) {
										$query->select('id', 'name', 'city_id');
									}])
									->with(['city' => function ($query) {
										$query->select('*')
											->with(['province' => function ($query) {
												$query->select('id', 'name', 'state_id');
											}]);
									}]);
							}])
							->with(['listing_information' => function ($query) {
								$query->select(
									"id",
									"subtype_property_id",
									"state_property_id",
									"listing_id"
								)
									->with(['subtype_property' => function ($query) {
										$query->select('id', 'name');
									}]);
							}])
							->with(['transaction_type' => function ($query) {
								$query->select('id', 'name');
							}])
							->with(['listing_prices' => function ($query) {
								$query->select('*')->latest('id')->first();
							}])
							->with(['owners' => function ($query) {
								$query->select('owner.id as owner_id', 'name', 'last_name', 'listing_id');
							}]);
					}
				]
			)->get();
	}
	/*
		Cuando tenemos dos listings registrados y necesitamos transferirlos
		Cambiamos el agent id
		Cambiar la relacion, debe pertener al nuevo listings
	*/
	public function transferListingTransaction(Listing $transferredListing, ?Listing $currentListing, string $agentId)
	{
		$this->updateAgentIdInPaymentsAndCommission($transferredListing->id, $agentId);

		Transaction::where('listing_id', $transferredListing->id)
			->update([
				'listing_id' => $currentListing->id,
				// 'listing_uuid' => $currentListing->key,
				'mls_id' => $currentListing->MLSID,
			]);
	}
	/*
		Cambiar la key y mls de un listing transferido, por otro listing
	*/
	public function updateMlsid(int $transferredListingId, ListingMigrationDto $dto)
	{
		Transaction::where('listing_id', $transferredListingId)
			->update([
				// 'listing_uuid' => $dto->key,
				'mls_id' => $dto->mlsId,
			]);
	}
	/*
		Cambiar el agent id en pagos y commisiones
	*/
	public function updateAgentIdInPaymentsAndCommission(int $listingId, string $agentId)
	{
		$transactionIds = Transaction::where('listing_id', $listingId)
			->pluck('id');

		if ($transactionIds->isEmpty()) {
			Log::info("No tiene transactions");
			return;
		}

		Payment::whereIn('transaction_id', $transactionIds)->update([
			'agent_internal_id' => $agentId,
		]);

		Commission::whereIn('transaction_id', $transactionIds)->update([
			'agent_internal_id' => $agentId,
		]);
	}

	public function updateStatus ($transaction_id, $status_id) 
	{
		$it = Transaction::where('id', $transaction_id)->first();

		if ($it && !$this->matchExpectedPaymentAndCommission($transaction_id) && in_array($status_id, [2, 5])) {
			return [
				'message' => 'No se puede cambiar el estado de la transacción porque tiene pagos pendientes.',
				'status' => 201
			];
		}

        if ($it->both === 1) {
            $item = Transaction::where('mls_id', $it->mls_id)->update([
                "transaction_status_id" => $status_id
            ]);
        } else {
            $it->update([
                "transaction_status_id" => $status_id
            ]);
        }

        if ($status_id == 1) {
            $listing = Listing::find($it->listing_id);
            if ($listing) {
                $listing->update([
                    'status_listing_id' => 2
                ]);
            }
        }

		return [
			'message' => 'Estado actualizado correctamente',
			'status' => 200
		];
	}

	private function matchExpectedPaymentAndCommission($transaction_id)
	{
		$transaction = Transaction::find($transaction_id);
		$payments = $transaction->both ? Payment::join('transactions', 'transactions.id', '=', 'payments.transaction_id')
			->where('transactions.mls_id', $transaction->mls_id)
			->where('payments.amount_expected', '>', 0)
			->get() : 
			Payment::where('transaction_id', $transaction_id)
			->where('payments.amount_expected', '>', 0)
			->get();

		$commissions = $transaction->both ? Commission::join('transactions', 'transactions.id', '=', 'commissions.transaction_id')
			->where('transactions.mls_id', $transaction->mls_id)
			->get() : 
			Commission::where('transaction_id', $transaction_id)
			->get();

		if(!$commissions || !$payments) {
			return false;
		}

		$totalPaymentAmount = $payments->sum('amount_expected');
		$totalCommissionAmount = $commissions->sum('total_commission_amount');

		return $commissions && $payments && $totalPaymentAmount == $totalCommissionAmount;
	}
}
