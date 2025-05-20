<?php

namespace App\Services\Agents;

use App\Dtos\Agents\OfficeDto;
use App\Models\Agent;
use App\Models\Office;
use App\Models\Payment;
use App\Models\Province;
use App\Models\Transaction;
use App\Services\Location\ProvinciaService;
use App\Services\Location\StateService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\select;

class OfficeService
{
    // laravel > node

    public function __construct(
        private RegionService $regionService,
        private LanguageService $LanguageService,
        private CityService $CityService,
        private ProvinciaService $provinciaService,
        private StateService $stateService,

    ) {}

    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Office::paginate($perPage);
    }
    public function getAll(): Collection
    {
        return Office::orderBy('name')->get();
    }
    public function getOfficesWithFilters(array $data)
    {
        return Office::with(['city', 'province'])
        ->when(isset($data['active_office']) && $data['active_office'] !== 'all', function ($query) use ($data) {
            $query->where('active_office', $data['active_office']);
        })
            ->when(!empty($data['search']), function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['search'] . '%');
            })
            ->when(isset($data['is_external']) && $data['is_external'] !== 'all', function ($query) use ($data) {
                $query->where('is_external', $data['is_external']);
            })
            ->orderBy('name')
            ->get();
    }

    public function findById(string $id): Office
    {
        return Office::findOrFail($id);
    }

    public function countOfficesInRegion(int $regionId): int
    {
        return Office::where('region_id', $regionId)->count();
    }


    public function create(OfficeDto $dto): Office
{
    return DB::transaction(function () use ($dto): Office {
        $regionId = 120; // Región fija
        $dto->region_id = $regionId;

        if (!empty($regionId)) {
            $regionCode = str_pad($regionId, 3, '0', STR_PAD_LEFT); // Aseguramos formato tipo 120

            $maxId = Office::where('office_id', 'LIKE', $regionCode . '%')
                ->selectRaw("MAX(CAST(SUBSTRING(office_id, LENGTH(?) + 1) AS UNSIGNED)) as max_suffix", [$regionCode])
                ->value('max_suffix');

            $nextSuffix = ($maxId ?? 0) + 1;
            $dto->office_id = intval($regionCode . str_pad($nextSuffix, 3, '0', STR_PAD_LEFT));
        }

        // 🔥 Log para verificar el office_id generado
        Log::info('Creando nueva oficina', [
            'region_id' => $regionId,
            'max_office_id_existente' => $maxId ?? 0,
            'office_id_generado' => $dto->office_id,
        ]);

        // 🔥 Guardar directamente el DTO como antes
        return Office::create($dto->toArray());
    });
}

    public function update(string $id, OfficeDto $dto): Office
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
    public function delete(int $id): bool
    {
        $office = Office::findOrFail($id);
        return $office->delete();
    }

    public function getOficinasPorUbicacion($data)
    {

        $offices = Office::select('offices.*')
            ->where(function ($query) use ($data) {

                if (isset($data['province_ids']) && $data['province_ids'] != []) {
                    $query->whereIn('province_id', $data['province_ids']);
                }

                if (isset($data['city_ids']) && $data['city_ids'] != []) {
                    $query->whereIn('city_id', $data['city_ids']);
                }
            });

        if (isset($data['state_id']) && $data['state_id'] != '') {

            $offices->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id')
                ->leftJoin('states', 'states.id', '=', 'provinces.state_id');

            if ($data['state_id'] == 'other') {
                $offices->whereNotIn('states.id', [1, 2, 3]);
            } else {
                $offices->where('states.id', $data['state_id']);
            }
        }

        return $offices->where('active_office', 1)->orderBy('name')->get();
    }

    public function getRanking($data)
    {

        if ($data['mensual'] == 1) {

            $rankingGlobal = [];

            foreach ($data['meses'] as $mes) {
                $fechaInicio = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                $fechaFinal = $fechaInicio->copy()->endOfMonth();

                $query = Payment::join('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
                    ->join('transactions', 'transactions.id', '=', 'payments.transaction_id')
                    ->join('offices', 'offices.office_id', '=', 'agents.office_id')
                    ->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id')
                    ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'payments.exchange_rate_id')
                    ->whereBetween(DB::raw('DATE(payments.received_date)'), [$fechaInicio->format('Y-m-d'), $fechaFinal])
                    ->whereIn('transactions.transaction_status_id', [2, 3, 4, 5]);

                if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                    if ($data['state_id'] == 'other') {
                        $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                    } else {
                        $query->where('provinces.state_id', $data['state_id']);
                    }
                }

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $query->leftJoin('users', 'users.id', '=', 'agents.user_id')
                        ->where('offices.id', $data['office_id'])
                        ->selectRaw('
                            users.name_to_show as name,
                            agents.id,
                            SUM(payments.amount_expected) as total,
                            SUM(IFNULL((payments.amount_expected / exchange_rates.amount), (payments.amount_expected / 6.96))) as total_usd
                        ')
                        ->groupBy('name', 'agents.id');
                } else {
                    $query->selectRaw('
                    offices.id,
                    offices.name,
                    offices.image,
                    offices.city,
                    offices.country,
                    SUM(payments.amount_expected) as total,
                    SUM(IFNULL((payments.amount_expected / exchange_rates.amount), (payments.amount_expected / 6.96))) as total_usd
                ')
                        ->groupBy('offices.name', 'offices.id', 'offices.image', 'offices.city', 'offices.country');
                }
                $rankingMes = $query->get();

                foreach ($rankingMes as $item) {
                    $index = array_search($item->name, array_column($rankingGlobal, 'name'));

                    if ($index === false) {

                        $rankingGlobal[] = [
                            'id' => $item->id,
                            'name' => $item->name,
                            'city' => $item->city ?? '',
                            'country' => $item->country ?? '',
                            'total' => $data['inDollars'] ? $item->total_usd : $item->total,
                            'image' => $item->image ?? '',
                        ];
                    } else {
                        $rankingGlobal[$index]['total'] += $data['inDollars'] ? $item->total_usd : $item->total;
                    }
                }
            }

            usort($rankingGlobal, function ($a, $b) {
                return $b['total'] <=> $a['total'];
            });

            foreach ($rankingGlobal as &$item) {

                if (isset($data['office_id']) && $data['office_id'] != '') {

                    $agenteFinded = Agent::find($item['id']);

                    if ($agenteFinded) {
                        $item['captaciones'] = $agenteFinded->listings->count();
                        $item['transacciones'] = $agenteFinded->transactions()
                            ->where('office_id', $data['office_id'])->count();

                        $mesSeleccionado = $data['meses'][count($data['meses']) - 1];
                        $anioSeleccionado = $data['anio'];

                        $fechaCreacion = new DateTime($agenteFinded->date_joined);
                        $fechaSeleccionada = new DateTime("{$anioSeleccionado}-{$mesSeleccionado}-01");

                        $diferencia = $fechaCreacion->diff($fechaSeleccionada);

                        $anios = $diferencia->y;
                        $meses = $diferencia->m;

                        $item['tiempo_activa'] = "{$anios} Años, {$meses} Meses";
                    }
                } else {

                    $oficinaFinded = Office::where('name', $item['name'])->first();

                    if ($oficinaFinded) {
                        $agentes = Agent::where('office_id', $oficinaFinded->office_id)
                            ->where('agent_status_id', 1)->get();
                        $totalCaptaciones = 0;
                        foreach ($agentes as $agente) {
                            $totalCaptaciones += $agente->listings()->count();
                        }
                        $item['transacciones'] = $oficinaFinded->transactions
                            ->whereBetween('sold_date', [$fechaInicio->format('Y-m-d'), $fechaFinal->toString()])
                            ->count();
                        $item['captaciones'] = $totalCaptaciones;
                        $item['agentes_activos'] = count($agentes);

                        $mesSeleccionado = $data['meses'][count($data['meses']) - 1];
                        $anioSeleccionado = $data['anio'];

                        $fechaCreacion = new DateTime($oficinaFinded->first_updated_to_web);
                        $fechaSeleccionada = new DateTime("{$anioSeleccionado}-{$mesSeleccionado}-01");

                        $diferencia = $fechaCreacion->diff($fechaSeleccionada);

                        $anios = $diferencia->y;
                        $meses = $diferencia->m;

                        $item['tiempo_activa'] = "{$anios} Años, {$meses} Meses";
                    }
                }
            }

            return $rankingGlobal;
        } else {

            $fechaInicio = Carbon::createFromFormat('Y-m-d', '2000-01-01');
            $fechaFinal = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-12-31');

            $query = Transaction::join('offices', 'offices.id', '=', 'transactions.office_id')
                ->whereBetween('transactions.sold_date', [$fechaInicio->format('Y-m-d'), $fechaFinal->toDateString()])
                ->orderBy('total', 'DESC');

            if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                $query->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id')
                    ->leftJoin('states', 'states.id', '=', 'provinces.state_id');
                if ($data['state_id'] == 'other') {
                    $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                } else {
                    $query->where('provinces.state_id', $data['state_id']);
                }
            }

            if (isset($data['office_id']) && $data['office_id'] != '') {
                $query->leftJoin('agents', 'agents.id', '=', 'transactions.agent_id')
                    ->leftJoin('users', 'users.id', '=', 'agents.user_id')
                    ->where('offices.id', $data['office_id'])
                    ->selectRaw('
                users.name_to_show as name,
                agents.id,
                SUM(transactions.payments_amount_received) as total')
                    ->groupBy('name', 'agents.id');
            } else {
                $query->selectRaw('
                offices.id,
                offices.name,
                offices.image,
                offices.city,
                offices.country,
                SUM(transactions.payments_amount_received) as total')
                    ->groupBy('offices.name', 'offices.id', 'offices.image', 'offices.city', 'offices.country');
            }

            $ranking = $query->get();

            return $ranking;
        }
    }
}
