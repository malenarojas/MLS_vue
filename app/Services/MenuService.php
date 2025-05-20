<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\Goal;
use App\Models\Listing;
use App\Models\MenuItem;
use App\Models\Office;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Agents\OfficeService;
use App\Services\Listings\ListingService;
use App\Services\Transactions\TransactionService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuService
{
    // Mis respetos a quien refactorize esta caga***

    protected $transactionService;
    protected $officeService;
    protected $listingService;

    public function __construct(
        TransactionService $transactionService,
        ListingService $listingService,
        OfficeService $officeService
    ) {

        $this->transactionService = $transactionService;
        $this->officeService = $officeService;
        $this->listingService = $listingService;
    }
    public function  getMenuItems(): array
    {
        // $user = auth()->user();

        // $permissions = $user->permissions->pluck('id')->toArray();
        //->whereIn('permission_id', $permissions)

        $menuItems = MenuItem::whereNull('parent_id')
            ->orderBy('order')
            ->get();

        if ($menuItems->isEmpty()) {
            return [];
        }

        $menuItemsFormateados = $this->formatearMenuItems($menuItems);

        return $menuItemsFormateados;
    }

    public function  getMenuItemsByPermissions($permissions): array
    {
        $menuItems = MenuItem::whereNull('parent_id')
            ->whereIn('permission_id', $permissions)
            ->get();

        if ($menuItems->isEmpty()) {
            return [];
        }

        $menuItemsFormateados = $this->formatearMenuItems($menuItems);

        return $menuItemsFormateados;
    }


    public function formatearMenuItems($menuItems): array
    {
        $menuFormateados = [];
        // $user = auth()->user();
        // $permissions = $user->permissions->pluck('id')->toArray();

        foreach ($menuItems as $menuItem) {
            $item = [
                'key' => $menuItem->id . '',
                'label' => $menuItem->name,
                'icon' => $menuItem->icon,
                'route' => $menuItem->route,
                'permission_id' => $menuItem->permission_id,
            ];

            $subItems = MenuItem::where('parent_id', $menuItem->id)
                //->whereIn('permission_id', $permissions)
                ->get();

            if ($subItems->isNotEmpty()) {
                $item['items'] = $this->formatearMenuItems($subItems);
            }

            $menuFormateados[] = $item;
        }

        return $menuFormateados;
    }

    public function getDatosCharts($data)
    {
        $comisiones = $this->getPagos($data);
        $captaciones = $this->getDatosCaptaciones($data);
        $transacciones = $this->transactionService->getDatosTransacciones($data);

        //Si se selecciona agente no trae nada
        $ranking = isset($data['agent_id']) && $data['agent_id'] != '' ? [] : $this->officeService->getRanking($data);

        return [
            'comisiones' => $comisiones,
            'captaciones' => $captaciones,
            'transacciones' => $transacciones,
            'ranking' => $ranking,
        ];
    }

    public function getDatosCaptaciones($data)
    {
        $captacionesVentas = 0;
        $captacionesAlquileres = 0;
        $captacionesAnticreticos = 0;

        if ($data['mensual'] == 1) {

            foreach ($data['meses'] as $mes) {

                $fechaInicio = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                $fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

                $queryVentas = $this->newListingsQuery($fechaInicio, $fechaFinal, 1);
                $queryAlquileres = $this->newListingsQuery($fechaInicio, $fechaFinal, 2);
                $queryAnticreticos = $this->newListingsQuery($fechaInicio, $fechaFinal, 3);

                if (isset($data['agent_id']) && $data['agent_id'] != '') {

                    $queryVentas = $this->applyFilterByAgent($queryVentas, $data['agent_id']);
                    $queryAlquileres = $this->applyFilterByAgent($queryAlquileres, $data['agent_id']);
                    $queryAnticreticos = $this->applyFilterByAgent($queryAnticreticos, $data['agent_id']);
                } else {

                    if ((isset($data['office_id']) && $data['office_id'] != '') ||
                        (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0)
                    ) {

                        $queryVentas = $this->applyOfficeJoinInQuery($queryVentas, $data);
                        $queryAlquileres = $this->applyOfficeJoinInQuery($queryAlquileres, $data);
                        $queryAnticreticos = $this->applyOfficeJoinInQuery($queryAnticreticos, $data);

                        if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                            $queryVentas = $this->applyStateFilter($queryVentas, $data['state_id']);
                            $queryAlquileres = $this->applyStateFilter($queryAlquileres, $data['state_id']);
                            $queryAnticreticos = $this->applyStateFilter($queryAnticreticos, $data['state_id']);
                        }

                        if (isset($data['office_id']) && $data['office_id'] != '') {

                            $queryVentas->where('offices.id', '=', $data['office_id']);
                            $queryAlquileres->where('offices.id', '=', $data['office_id']);
                            $queryAnticreticos->where('offices.id', '=', $data['office_id']);
                        }
                    }
                }

                $captacionesVentas += $queryVentas->count();
                $captacionesAlquileres += $queryAlquileres->count();
                $captacionesAnticreticos += $queryAnticreticos->count();
            }
        } else {

            $fechaInicio = Carbon::createFromFormat('Y-m-d', '2000-01-01');
            $fechaFinal = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-12-31');

            $queryVentas = $this->newListingsQuery($fechaInicio, $fechaFinal, 1);
            $queryAlquileres = $this->newListingsQuery($fechaInicio, $fechaFinal, 2);
            $queryAnticreticos = $this->newListingsQuery($fechaInicio, $fechaFinal, 3);

            if (isset($data['agent_id']) && $data['agent_id'] != '') {

                $queryVentas = $this->applyFilterByAgent($queryVentas, $data['agent_id']);
                $queryAlquileres = $this->applyFilterByAgent($queryAlquileres, $data['agent_id']);
                $queryAnticreticos = $this->applyFilterByAgent($queryAnticreticos, $data['agent_id']);
            } else {

                if ((isset($data['office_id']) && $data['office_id'] != '') ||
                    (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0)
                ) {
                    $queryVentas = $this->applyOfficeJoinInQuery($queryVentas, $data);
                    $queryAlquileres = $this->applyOfficeJoinInQuery($queryAlquileres, $data);
                    $queryAnticreticos = $this->applyOfficeJoinInQuery($queryAnticreticos, $data);

                    if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                        $queryVentas = $this->applyStateFilter($queryVentas, $data['state_id']);
                        $queryAlquileres = $this->applyStateFilter($queryAlquileres, $data['state_id']);
                        $queryAnticreticos = $this->applyStateFilter($queryAnticreticos, $data['state_id']);
                    }

                    if (isset($data['office_id']) && $data['office_id'] != '') {
                        $queryVentas->where('offices.id', '=', $data['office_id']);
                        $queryAlquileres->where('offices.id', '=', $data['office_id']);
                        $queryAnticreticos->where('offices.id', '=', $data['office_id']);
                    }
                }
            }

            $captacionesVentas = $queryVentas->count();
            $captacionesAlquileres = $queryAlquileres->count();
            $captacionesAnticreticos = $queryAnticreticos->count();
        }

        return [
            'captacionesVentas' => $captacionesVentas,
            'captacionesAlquileres' => $captacionesAlquileres,
            'captacionesAnticreticos' => $captacionesAnticreticos,
        ];
    }

    private function applyStateFilter($query, $state_id)
    {
        $query->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');
        if ($state_id != 'other') {
            $query->where('provinces.state_id', '=', $state_id);
        } else {
            $query->whereNotIn('provinces.state_id', [1, 2, 3]);
        }
        return $query;
    }

    private function newListingsQuery($fechaInicio, $fechaFinal, $type)
    {
        return Listing::whereBetween('date_of_listing', [$fechaInicio->format('Y-m-d'), $fechaFinal])
            ->leftJoin('listings_information', 'listings_information.listing_id', '=', 'listings.id')
            ->where('transaction_type_id', $type);
    }

    private function applyFilterByAgent($query, $agent_id)
    {
        return $query->leftJoin('agent_listing', 'agent_listing.listing_id', '=', 'listings.id')
            ->leftJoin('agents', 'agent_listing.agent_id', '=', 'agents.id')
            ->where('agents.id', '=', $agent_id);
    }

    private function applyOfficeJoinInQuery($query, $data)
    {
        $query = $query->leftJoin('agent_listing', 'agent_listing.listing_id', '=', 'listings.id')
            ->leftJoin('agents', 'agent_listing.agent_id', '=', 'agents.id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id');
        return $query;
    }

    public function getDatosAgentes($data)
    {
        if ($data['mensual'] == 1) {

            $today = Carbon::now()->format('Y-m-d');
            $dateToAnalize = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . $data['meses'][count($data['meses']) - 1] . '-01')
                ->lastOfMonth()->format('Y-m-d');

            $queryTotalAgentes = Agent::where('agents.agent_status_id', 1);


            if ((isset($data['office_id']) && $data['office_id'] != '') ||
                (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0)
            ) {
                $queryTotalAgentes->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id');

                if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                    $queryTotalAgentes->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');

                    if ($data['states_id'][0] == 'other') {
                        $queryTotalAgentes->whereNotIn('provinces.state_id', [1, 2, 3]);
                    } else {
                        $queryTotalAgentes->where('provinces.state_id', $data['state_id']);
                    }
                }

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $queryTotalAgentes->where('offices.id', '=', $data['office_id']);
                }
            }

            $totalAgentes = $queryTotalAgentes->count() - $this->getDiferenceAgents($data, $today, $dateToAnalize);

            $agentesRetirados = 0;
            $agentesIngresados = 0;

            foreach ($data['meses'] as $mes) {

                $fechaInicio = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                $fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

                $queryRetirados =  Agent::whereBetween('date_termination', [$fechaInicio, $fechaFinal]);
                $queryIngresados =  Agent::whereBetween('date_joined', [$fechaInicio, $fechaFinal]);

                if ((isset($data['office_id']) && $data['office_id'] != '') ||
                    (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0)
                ) {
                    $queryRetirados->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id');
                    $queryIngresados->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id');

                    if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                        $queryRetirados->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id')
                            ->where('provinces.state_id', $data['state_id']);
                        $queryIngresados->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id')
                            ->where('provinces.state_id', $data['state_id']);
                    }

                    if (isset($data['office_id']) && $data['office_id'] != '') {
                        $queryRetirados->where('offices.id', '=', $data['office_id']);
                        $queryIngresados->where('offices.id', '=', $data['office_id']);
                    }
                }

                $agentesRetirados += $queryRetirados->count();
                $agentesIngresados += $queryIngresados->count();
            }
        } else {

            $totalAgentes = $this->getAgentesActivos($data);

            $queryRetirados = Agent::where('date_termination', '<=', $data['anio'] . '-12-31');
            $queryIngresados = Agent::where('date_joined', '<=', $data['anio'] . '-12-31');

            if ((isset($data['office_id']) && $data['office_id'] != '') ||
                (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0)
            ) {
                $queryRetirados->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id');
                $queryIngresados->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id');

                if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                    $queryRetirados->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id')
                        ->where('provinces.state_id', $data['state_id']);
                    $queryIngresados->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id')
                        ->where('provinces.state_id', $data['state_id']);
                }

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $queryRetirados->where('offices.id', '=', $data['office_id']);
                    $queryIngresados->where('offices.id', '=', $data['office_id']);
                }
            }

            $agentesRetirados = $queryRetirados->count();
            $agentesIngresados = $queryIngresados->count();
        }

        return [
            'totalAgentes' => $totalAgentes,
            'agentesRetirados' => $agentesRetirados,
            'agentesIngresados' => $agentesIngresados,
        ];
    }

    public function getAgentesActivos($data)
    {
        $queryTotalAgentes = Agent::where(function ($query) use ($data) {
            $query->whereNull('date_termination')
                ->orWhere('date_termination', '>=', $data['anio'] . '-12-31');
        })
            ->where('date_joined', '<=', $data['anio'] . '-12-31');

        if (isset($data['office_id']) && $data['office_id'] != '') {
            $queryTotalAgentes->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
                ->where('offices.id', '=', $data['office_id']);
        }

        return $queryTotalAgentes->count();
    }

    public function getDatosComisiones($data)
    {
        $comisiones = [];
        $comisionesComprar = [];


        if ($data['mensual'] == 0) {

            $anioFinal = $data['anio'];
            for ($anio = 2020; $anio <= $anioFinal; $anio++) {
                $fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-01-01');
                $fechaFinal = Carbon::createFromFormat('Y-m-d', $anio . '-12-31');

                $query = Commission::selectRaw('
                DATE_FORMAT(commissions.date_created, "%Y") as mes,
                SUM(commissions.total_commission_amount) as total_comisiones')
                    ->whereBetween('commissions.date_created', [$fechaInicio, $fechaFinal])
                    ->groupBy('mes')
                    ->orderBy('mes');

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $query->leftJoin('transactions', 'transactions.id', '=', 'commissions.transaction_id')
                        ->where('transactions.office_id', $data['office_id']);
                }

                if (isset($data['agent_id']) && $data['agent_id'] != '' && isset($data['office_id']) && $data['office_id'] != '') {
                    $query->where('transactions.agent_id', $data['agent_id']);
                }

                $comision = $query->get();

                $comisiones[] = $comision;
            }
        } else {
            foreach ($data['meses'] as $mes) {
                $fechaInicio = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                $fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

                $query = Commission::selectRaw('
                DATE_FORMAT(commissions.date_created, "%Y-%m") as mes,
                SUM(commissions.total_commission_amount) as total_comisiones')
                    ->whereBetween('commissions.date_created', [$fechaInicio->format('Y-m-d'), $fechaFinal])
                    ->groupBy('mes')
                    ->orderBy('mes');

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $query->join('transactions', 'transactions.id', '=', 'commissions.transaction_id')
                        ->where('transactions.office_id', $data['office_id']);
                }

                if (isset($data['agent_id']) && $data['agent_id'] != '') {
                    $query->where('transactions.agent_id', $data['agent_id']);
                }

                $comision = $query->get();

                $comisiones[] = $comision;
            }

            $data['anio']--;
            $comisionesComprar = [];

            foreach ($data['meses'] as $mes) {
                $fechaInicio = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                $fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

                $query = Commission::selectRaw('
                DATE_FORMAT(commissions.date_created, "%Y-%m") as mes,
                SUM(commissions.total_commission_amount) as total_comisiones')
                    ->whereBetween('commissions.date_created', [$fechaInicio->format('Y-m-d'), $fechaFinal])
                    ->groupBy('mes')
                    ->orderBy('mes');

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $query->leftJoin('transactions', 'transactions.id', '=', 'commissions.transaction_id')
                        ->where('transactions.office_id', $data['office_id']);
                }

                if (isset($data['agent_id']) && $data['agent_id'] != '') {
                    $query->where('transactions.agent_id', $data['agent_id']);
                }
                $comision = $query->get();

                $comisionesComprar[] = $comision;
            }
        }

        $result = [
            'comisiones' => $comisiones,
            'comisionesComprar' => $comisionesComprar
        ];

        return $result;
    }

    public function getPagos($data)
    {
        $comisiones = [];
        $comisionesComprar = [];

        if ($data['mensual'] == 0) {

            $anioFinal = $data['anio'];
            for ($anio = 2020; $anio <= $anioFinal; $anio++) {

                $query = Payment::selectRaw('
                DATE_FORMAT(payments.expected_payment_date, "%Y") as mes,
                SUM(payments.amount_expected) as total_comisiones,
                SUM(IFNULL((payments.amount_expected / exchange_rates.amount), (payments.amount_expected / 6.96))) as total_comisiones_usd
                ')
                    ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'payments.exchange_rate_id')
                    ->leftJoin('transactions', 'transactions.id', '=', 'payments.transaction_id')
                    ->leftJoin('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
                    ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
                    ->whereYear('payments.expected_payment_date', $anio)
                    ->whereIn('transactions.transaction_status_id', [2, 3, 4, 5])
                    ->groupBy('mes')
                    ->orderBy('mes');

                if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {

                    $query
                        ->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');

                    if ($data['state_id'] == 'other') {
                        $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                    } else {
                        $query->where('provinces.state_id', $data['state_id']);
                    }
                }

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $query
                        ->where('offices.id', $data['office_id']);
                }

                if (isset($data['agent_id']) && $data['agent_id'] != '' && isset($data['office_id']) && $data['office_id'] != '') {
                    $query->where('agents.id', $data['agent_id']);
                }

                $query->where(function ($query) {
                    $query->whereNotNull('payments.amount_received')
                        ->where('payments.amount_received', '!=', 0);
                });

                $comision = $query->get();

                if (isset($data['inDollars']) && $data['inDollars'] == 1) {
                    if (count($comision) > 0 && $comision[0]) {
                        $comision[0]->total_comisiones = $comision[0]->total_comisiones_usd;
                    }
                }

                $comisiones[] = $comision;
            }
        } else {

            // $total = 0;
            foreach ($data['meses'] as $mes) {
                $fechaInicio = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                $fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

                $query = Payment::selectRaw('
                    DATE_FORMAT(payments.expected_payment_date, "%Y") as mes,
                    SUM(payments.amount_expected) as total_comisiones,
                    SUM(IFNULL((payments.amount_expected / exchange_rates.amount), (payments.amount_expected / 6.96))) as total_comisiones_usd
                ')
                    ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'payments.exchange_rate_id')
                    ->leftJoin('transactions', 'transactions.id', '=', 'payments.transaction_id')
                    ->leftJoin('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
                    ->leftJoin('offices', 'offices.id', '=', 'payments.office_id')
                    ->whereBetween(DB::raw('DATE(payments.expected_payment_date)'), [$fechaInicio->format('Y-m-d'), $fechaFinal])
                    ->whereIn('transactions.transaction_status_id', [2, 3, 4, 5])
                    ->groupBy('mes')
                    ->orderBy('mes');

                $query->where(function ($query) {
                    $query->whereNotNull('payments.amount_received')
                        ->where('payments.amount_received', '!=', 0);
                });


                if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                    $query
                        ->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');
                    if ($data['state_id'] == 'other') {
                        $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                    } else {
                        $query->where('provinces.state_id', $data['state_id']);
                    }
                }

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $query->where('payments.office_id', $data['office_id']);
                }

                if (isset($data['agent_id']) && $data['agent_id'] != '' && isset($data['office_id']) && $data['office_id'] != '') {
                    $query->where('agents.id', $data['agent_id']);
                }

                $comision = $query->get();

                if (isset($data['inDollars']) && $data['inDollars'] == 1) {
                    if (count($comision) > 0 && $comision[0]) {
                        $comision[0]->total_comisiones = $comision[0]->total_comisiones_usd;
                    }
                }

                $comisiones[] = $comision;
            }

            $data['anio']--;
            $comisionesComprar = [];

            foreach ($data['meses'] as $mes) {
                $fechaInicio = Carbon::createFromFormat('Y-m-d', $data['anio'] . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                $fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

                $query = Payment::selectRaw('
                DATE_FORMAT(payments.expected_payment_date, "%Y") as mes,
                SUM(payments.amount_expected) as total_comisiones,
                SUM(IFNULL((payments.amount_expected / exchange_rates.amount), (payments.amount_expected / 6.96))) as total_comisiones_usd
                ')
                    ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'payments.exchange_rate_id')
                    ->leftJoin('transactions', 'transactions.id', '=', 'payments.transaction_id')
                    ->leftJoin('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
                    ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
                    ->whereBetween(DB::raw('DATE(payments.expected_payment_date)'), [$fechaInicio, $fechaFinal])
                    ->whereIn('transactions.transaction_status_id', [2, 3, 4, 5])
                    ->groupBy('mes')
                    ->orderBy('mes');


                if (isset($data['state_id']) && $data['state_id'] != '' && $data['state_id'] != 0) {
                    $query
                        ->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');
                    if ($data['state_id'] == 'other') {
                        $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                    } else {
                        $query->where('provinces.state_id', $data['state_id']);
                    }
                }

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $query->where('offices.id', $data['office_id']);
                }

                if (isset($data['agent_id']) && $data['agent_id'] != '' && isset($data['office_id']) && $data['office_id'] != '') {
                    $query->where('agents.id', $data['agent_id']);
                }

                $comision = $query->get();

                if (isset($data['inDollars']) && $data['inDollars'] == 1) {
                    if (count($comision) > 0 && $comision[0]) {
                        $comision[0]->total_comisiones = $comision[0]->total_comisiones_usd;
                    }
                }

                $comisionesComprar[] = $comision;
            }
        }

        $result = [
            'comisiones' => $comisiones,
            'comisionesComprar' => $comisionesComprar
        ];

        return $result;
    }

    public function getExecutiveReport($data)
    {

        #Transacciones Both separadas
        $transactions = $this->getVolumeTransactions($data);

        #Transacciones Both juntas
        //$transactionsFilterd  = $this->transactionService->filtroBothOneSide(clone $transactions, 0);

        $modifiedData =    $data;
        $modifiedData['year'] = $modifiedData['year'] - 1;
        $lastYearTransactions = $this->getVolumeTransactions($modifiedData);
        // $lastYearTransactionsFilterd  = $this->transactionService->filtroBothOneSide(clone $lastYearTransactions, 0);

        //Volumen de captaciones
        $totalTransactionsAmount =
            $data['inDollars'] ? $transactions->sum('current_listing_price_usd') :
            $transactions->sum('current_listing_price');

        $totalTransactionsAmountLastYear =
            $data['inDollars'] ? $lastYearTransactions->sum('current_listing_price_usd') :
            $lastYearTransactions->sum('current_listing_price');


        //Pagos
        $totalPaymentAmount = $this->getPaymentsV2($data);
        $totalPaymentAmountLastYear = $this->getPaymentsV2($modifiedData);

        //Total de agentes activos
        if (!empty($data['office_id'])) {
            $office_id = Office::find($data['office_id'])->office_id;
        } else {
            $office_id = null;
        }

        if ($data['monthly']) {
            $today = Carbon::now()->format('Y-m-d');

            $dateToAnalize = Carbon::createFromFormat('Y-m-d', $data['year'] . '-' . $data['months'][count($data['months']) - 1] . '-01')->lastOfMonth()
                ->format('Y-m-d');

            $dateToAnalizeLastYear = Carbon::createFromFormat('Y-m-d', $data['year'] . '-' . $data['months'][count($data['months']) - 1] . '-01')->lastOfMonth()
                ->subYear()->format('Y-m-d');

            $activeAgents = Agent::where('agent_status_id', 1)
                ->join('offices', 'offices.office_id', '=', 'agents.office_id')
                ->where(function ($query) {
                    $query->whereNull('offices.is_external')
                        ->orWhere('offices.is_external', 0);
                        
                })
                ->where('offices.active_office', 1)
                ->when($office_id, function ($query) use ($office_id) {
                    return $query->where('offices.office_id', $office_id);
                })->count() - $this->getDiferenceAgents($data, $today, $dateToAnalize);

            $activeAgentsLastYear = Agent::where('agent_status_id', 1)
                ->when($office_id, function ($query) use ($office_id) {
                    return $query->where('office_id', $office_id);
                })->count() - $this->getDiferenceAgents($data, $today, $dateToAnalizeLastYear);
        } else {
            $activeAgents = Agent::where('agent_status_id', 1)
                ->when($office_id, function ($query) use ($office_id) {
                    return $query->where('office_id', $office_id);
                })->count();

            $activeAgentsLastYear = $activeAgents;
        }

        // Metas
        $goals = $this->getGoals($data);

        // Tiempo Promedio en Mercado
        $timeInMarket = $this->getAverageTimeInMarket($data);

        //Captaciones activas
        if ($data['monthly']) {
            $data['meses'] = [$data['months'][count($data['months']) - 1]];
            $data['anio'] = [$data['year']];
            $data['office_id'] = [$data['office_id']];
            $data['agent_id'] = [$data['agent_id']];
            $data['cantidad'] = true;
            $activeListings = $this->listingService->getInventario($data);

            $data['anio'] = [$data['year'] - 1];
            $activeListingsLastYear = $this->listingService->getInventario($data);
        } else {

            $activeListings = Listing::where('status_listing_id', 1)
                ->when($office_id, function ($query) use ($office_id) {
                    return $query->whereHas('agents', function ($q) use ($office_id) {
                        $q->where('office_id', $office_id);
                    });
                })
                ->when($data['agent_id'], function ($query) use ($data) {
                    return $query->whereHas('agents', function ($q) use ($data) {
                        $q->where('agent_id', $data['agent_id']);
                    });
                })->count();

            $activeListingsLastYear = $activeListings;
        }

        return [
            'transactions' => $transactions->count(),
            'transactionsLastYear' => $lastYearTransactions->count(),
            'transactionsGoal' => $goals?->transactions ?? 0,

            'totalTransactionsAmount' => $totalTransactionsAmount,
            'totalTransactionsAmountLastYear' => $totalTransactionsAmountLastYear,
            'transactionVolumeGoal' => $goals?->transaction_volume ?? 0,

            'totalPaymentAmount' => $data['inDollars'] ?  $totalPaymentAmount->total_usd : $totalPaymentAmount->total,
            'totalPaymentAmountLastYear' => $data['inDollars'] ?  $totalPaymentAmountLastYear->total_usd : $totalPaymentAmountLastYear->total,
            'paymentAmountGoal' => $goals?->payment_amount ?? 0,

            'activeAgents' => $activeAgents,
            'activeAgentsLastYear' => $activeAgentsLastYear,
            'agentsGoal' => $goals?->new_agents ?? 0,

            'activeListings' => $activeListings,
            'activeListingsLastYear' => $activeListingsLastYear,
            'listingsGoal' => $goals?->new_listings ?? 0,
        ];
    }

    private function getAverageTimeInMarket($data)
    {
        $query = Listing::selectRaw(
            'SUM(DATEDIFF(
                LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ),
                listings.date_of_listing
            )) AS dias_en_mercado_total'
        )
            ->leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id');
    }

    private function getGoals($data): ?Goal
    {

        $office_id = $data['office_id'];
        $agent_id = $data['agent_id'];
        $year = $data['year'];
        $months = $data['months'];

        if ($office_id == '' && $agent_id == '') {
            $goals = Goal::selectRaw('
                SUM(goals.transactions) as transactions,

                CASE
                    WHEN ' . $data['inDollars'] . ' = 1 THEN 
                        IFNULL(
                            SUM(payment_amount / exchange_rates.amount),
                            SUM(payment_amount / 6.96)
                        )
                    ELSE SUM(payment_amount)
                END as payment_amount,
                
                SUM(new_agents) as new_agents,
                SUM(new_listings) as new_listings,

                CASE
                    WHEN ' . $data['inDollars'] . ' = 1 THEN 
                        IFNULL(
                            SUM(transaction_volume / exchange_rates.amount),
                            SUM(transaction_volume / 6.96)
                        )
                    ELSE SUM(transaction_volume)
                END as transaction_volume
            ')
                ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'goals.exchange_rate_id')
                ->whereNull('agent_id')
                ->where('year', $year)
                ->whereIn('month', $months)
                ->first();
        } else {
            if ($agent_id != '') {
                $goals = Goal::selectRaw('
                    SUM(goals.transactions) as transactions,

                    CASE
                        WHEN ' . $data['inDollars'] . ' = 1 THEN 
                            IFNULL(
                                SUM(payment_amount / exchange_rates.amount),
                                SUM(payment_amount / 6.96)
                            )
                        ELSE SUM(payment_amount)
                    END as payment_amount,
                    
                    SUM(new_agents) as new_agents,
                    SUM(new_listings) as new_listings,

                    CASE
                        WHEN ' . $data['inDollars'] . ' = 1 THEN 
                            IFNULL(
                                SUM(transaction_volume / exchange_rates.amount),
                                SUM(transaction_volume / 6.96)
                            )
                        ELSE SUM(transaction_volume)
                    END as transaction_volume
                ')
                    ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'goals.exchange_rate_id')
                    ->where('year', $year)
                    ->where('agent_id', $agent_id)
                    ->where('office_id', $office_id)
                    ->whereIn('month', $months)
                    ->first();
            } else {
                $goals = Goal::selectRaw('
                    
                    SUM(goals.transactions) as transactions,

                    CASE
                        WHEN ' . $data['inDollars'] . ' = 1 THEN 
                            IFNULL(
                                SUM(payment_amount / exchange_rates.amount),
                                SUM(payment_amount / 6.96)
                            )
                        ELSE SUM(payment_amount)
                    END as payment_amount,
                    
                    SUM(new_agents) as new_agents,
                    SUM(new_listings) as new_listings,

                    CASE
                        WHEN ' . $data['inDollars'] . ' = 1 THEN 
                            IFNULL(
                                SUM(transaction_volume / exchange_rates.amount),
                                SUM(transaction_volume / 6.96)
                            )
                        ELSE SUM(transaction_volume)
                    END as transaction_volume
                
                ')->where('year', $year)
                    ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'goals.exchange_rate_id')
                    ->where('office_id', $office_id)
                    ->whereNull('agent_id')
                    ->whereIn('month', $months)
                    ->first();
            }
        }


        return $goals;
    }

    /**
     * @param  date $startDate = Fecha la cual queremos conocer cuantos agentes
     * @param  date $endDate = Fecha actual
     * 
     * Calcula la diferencia de agentes entre la fecha de inicio y la fecha final
     */

    public function getDiferenceAgents($data, $endDate = null, $startDate = null)
    {
        if (!$startDate || !$endDate) {
            $month = $data['months'][count($data['months']) - 1];
            $year = $data['year'];
            $lastYear = $year - 1;

            $startDate = Carbon::createFromFormat('Y-m-d', $lastYear . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01')->format('Y-m-d');
            $endDate = Carbon::createFromFormat('Y-m-d', $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01')->endOfMonth()->toDateString();
        }

        if (!empty($data['office_id'])) {
            $office_id = Office::find($data['office_id'])->office_id;
        } else {
            $office_id = null;
        }

        $newAgents = Agent::whereBetween('date_joined', [$startDate, $endDate])
            ->when($data['office_id'], function ($query) use ($office_id) {
                $query->where('office_id', $office_id);
            })->count();

        $terminatedAgents = Agent::whereBetween('date_termination', [$startDate, $endDate])
            ->when($data['office_id'], function ($query) use ($office_id) {
                $query->where('office_id', $office_id);
            })->count();

        return $newAgents - $terminatedAgents;
    }

    public function getPayments($data)
    {
        $query = Payment::selectRaw('
                SUM(payments.amount_expected) as total,
                SUM(
                    IFNULL((payments.amount_expected / exchange_rates.amount), 
                (payments.amount_expected / 6.96))) as total_usd
			')
            ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'payments.exchange_rate_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'payments.transaction_id')
            ->leftJoin('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->whereIn('transactions.transaction_status_id', [2, 4, 5]);

        if (!empty($data['office_id']) || !empty($data['state_id'])) {

            $query->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');

            if (!empty($data['state_id'])) {
                if ($data['state_id'] != 'other') {
                    $query->where('provinces.state_id', $data['state_id']);
                } else {
                    $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                }
            }
            if (!empty($data['office_id'])) {
                $query->where('offices.id', $data['office_id']);
            }
        }

        $query->where(function ($query) {
            $query->whereNotNull('payments.amount_received')
                ->where('payments.amount_received', '!=', 0);
        });

        if (!empty($data['agent_id'])) {
            $query->where('agents.id', $data['agent_id']);
        }

        $query->where(function ($query) use ($data) {
            if ($data['monthly']) {
                foreach ($data['months'] as $month) {
                    $query->orWhere(function ($query) use ($data, $month) {
                        $query->whereMonth('payments.expected_payment_date', $month);
                        $query->whereYear('payments.expected_payment_date', $data['year']);
                    });
                }
                // $startDate = Carbon::createFromFormat('Y-m-d', $data['year'] . '-' . $data['months'][0] . '-01')->format('Y-m-d');
                // $endDate = Carbon::createFromFormat('Y-m-d', $data['year'] . '-' . $data['months'][count($data['months']) - 1] . '-01')->endOfMonth()->toDateString();
                // $query->whereBetween(DB::raw('DATE(payments.expected_payment_date)'), [$startDate, $endDate]);
            } else {
                $query->whereBetween(DB::raw('YEAR(payments.expected_payment_date)'), [2020, $data['year']]);
            }
        });

        return $query->first();
    }

    public function getPaymentsV2 ($data)
    {
        $office_id = $data['office_id'];
        $agent_id = $data['agent_id'];
        $state_id = $data['state_id'];
        $year = $data['year'];
        $months = $data['months'];

        $query = Payment::selectRaw('
                SUM(payments.amount_expected) as total,
                SUM(
                    IFNULL(
                        (payments.amount_expected / exchange_rates.amount), 
                        (payments.amount_expected / 6.96)
                        )
                    ) as total_usd
			')
            ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'payments.exchange_rate_id')
            ->join('transactions', 'transactions.id', '=', 'payments.transaction_id')
            ->join('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
            ->join('offices', 'offices.id', '=', 'payments.office_id')
            ->whereIn('transactions.transaction_status_id', [2, 4, 5]);

        if(!empty($office_id))
        {
            $query->where('payments.office_id', $office_id);
        }

        if(!empty($province_id))
        {
            $query->join('provinces', 'provinces.id', '=', 'offices.province_id');

            if (!empty($state_id)) {
                if ($data['state_id'] != 'other') {
                    $query->where('provinces.state_id', $state_id);
                } else {
                    $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                }
            }
        }

        $query->where(function ($query) {
            $query->whereNotNull('payments.amount_received')
                ->where('payments.amount_received', '!=', 0);
        });

        if (!empty($agent_id)) {
            $query->where('agents.id', $agent_id);
        }

        $query->where(function ($query) use ($months, $year) {
         
            foreach ($months as $month) {
                $query->orWhere(function ($query) use ($year, $month) {
                    $query->whereMonth('payments.expected_payment_date', $month);
                    $query->whereYear('payments.expected_payment_date', $year);
                });
            }
        });

        return $query->first();
    }

    function getVolumeTransactions($data)
    {
        $query = Transaction::selectRaw(
            'transactions.id,
            transactions.listing_id,
            transactions.both,
			transactions.transaction_type_id,
			transactions.current_listing_price,
            IFNULL((transactions.current_listing_price / exchange_rates.amount), (transactions.current_listing_price / 6.96)) as current_listing_price_usd
            '
        )->whereIn('transactions.transaction_status_id', [2, 4, 5])
            ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'transactions.exchange_rate_id');

        if (!empty($data['office_id']) || !empty($data['state_id'])) {

            $query->leftJoin('offices', 'offices.id', '=', 'transactions.office_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');

            if (!empty($data['state_id'])) {
                if ($data['state_id'] != 'other') {
                    $query->where('provinces.state_id', $data['state_id']);
                } else {
                    $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                }
            }
            if (!empty($data['office_id'])) {
                $query->where('transactions.office_id', $data['office_id']);
            }
        }

        if (!empty($data['agent_id'])) {
            $query->where('transactions.agent_id', $data['agent_id']);
        }

        $query->where(function ($query) use ($data) {
            if ($data['monthly']) {
                foreach ($data['months'] as $month) {
                    $query->orWhere(function ($query) use ($data, $month) {
                        $query->whereMonth('transactions.sold_date', $month);
                        $query->whereYear('transactions.sold_date', $data['year']);
                    });
                }
            } else {
                $query->whereBetween(DB::raw('YEAR(transactions.sold_date)'), [2020, $data['year']]);
            }
        });

        $transactions = $query->get();

        return $transactions;
    }
}
