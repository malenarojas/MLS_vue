<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Services\Agents\AgentService;
use App\Services\Agents\OfficeService;
use App\Services\CommissionService;
use App\Services\ExecutiveResumeService;
use App\Services\Listings\ListingService;
use App\Services\LocationService;
use App\Services\MenuService;
use App\Services\Transactions\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MenuController extends Controller
{
    protected $menuService;
    protected $transactionService;
    protected $locationService;
    protected $officeService;
    protected $agentService;
    protected $listingService;
    protected $commissionService;
    protected $executiveResumeService;

    public function __construct(
        MenuService $menuService,
        TransactionService $transactionService,
        LocationService $locationService,
        OfficeService $officeService,
        ListingService $listingService,
        CommissionService $commissionService,
        AgentService $agentService,
        ExecutiveResumeService $executiveResumeService
    ) {

        $this->menuService = $menuService;
        $this->locationService = $locationService;
        $this->transactionService = $transactionService;
        $this->officeService = $officeService;
        $this->agentService = $agentService;
        $this->listingService = $listingService;
        $this->commissionService = $commissionService;
        $this->executiveResumeService = $executiveResumeService;
    }

    public function getMenuItems()
    {
        $menuItems = $this->menuService->getMenuItems();
        return response()->json(['items' => $menuItems]);
    }

    public function getDatosCharts(Request $request)
    {
        // return auth()->user();
        // if(auth()->user()->hasRole('Broker')){
        //     $request->oficina_id = auth()->user()->office_id;
        // }
        // if(auth()->user()->hasRole('Agente')){
        //     $request->oficina_id = auth()->user()->office_id;
        //     $request->agente_id = auth()->user()->agent_id;
        // }
        $data = [
            'meses' => $request->mesesSeleccionados,
            'anio' => $request->anioSeleccionado,
            'mensual' => $request->tipoReporte == 'mensual' ? 1 : 0,
            'office_id' => $request->oficina_id ?? '',
            'agent_id' => $request->agente_id ?? '',
            'state_id' => $request->departamento_id ?? 0,
            'inDollars' => $request->inDollars ?? 0,

        ];

        $datosCharts = $this->menuService->getDatosCharts($data);

        return response()->json([$datosCharts]);
    }

    public function getDatosTransacciones(Request $request)
    {
        // if(auth()->user()->hasRole('Broker')){
        //     $request->oficina_id = auth()->user()->office_id;
        // }
        // if(auth()->user()->hasRole('Agente')){
        //     $request->oficina_id = auth()->user()->office_id;
        //     $request->agente_id = auth()->user()->agent_id;
        // }

        $transacciones = $this->transactionService->getDatosTransacciones([
            'meses' => $request->mesesSeleccionados,
            'anio' => $request->anioSeleccionado,
            'mensual' => $request->tipoReporte == 'mensual' ? 1 : 0,
            'office_id' => $request->oficina_id ?? '',
            'agent_id' => $request->agente_id ?? '',
            'side_type' => $request->tipo_transaccion ?? 0,
        ]);
        return response()->json($transacciones);
    }

    public function getDatosTransaccionesPorTipos(Request $request)
    {
        return $this->transactionService->getDatosTransaccionesPorTipos($request);
    }

    public function getDepartamentos(Request $request)
    {
        return $this->locationService->getDepartamentos($request);
    }

    public function getProvincias(Request $request)
    {
        return $this->locationService->getProvincias($request);
    }

    public function getCiudades(Request $request)
    {
        return $this->locationService->getCiudades($request);
    }

    public function getZonas(Request $request)
    {
        return $this->locationService->getZonas($request);
    }

    public function getOficinasPorUbicacion(Request $request)
    {
        return $this->officeService->getOficinasPorUbicacion($request);
    }

    public function getDatosSecundarios(Request $request)
    {
        // if(auth()->user()->hasRole('Broker')){
        //     $request->oficina_id = [auth()->user()->office_id];
        // }
        // if(auth()->user()->hasRole('Agente')){
        //     $request->oficina_id = [auth()->user()->office_id];
        //     $request->agente_id = [auth()->user()->agent_id];
        // }

        $data = [
            'meses' => $request->mesesSeleccionados,
            'anio' => $request->anioSeleccionado,
            'mensual' => $request->tipoReporte == 'mensual' ? 1 : 0,
            'office_id' => $request->oficina_id ?? [],
            'agent_id' => $request->agente_id ?? [],
            'states_id' => $request->departamento_id ? [$request->departamento_id] : [],
        ];

        $comisionesAgentes = $this->agentService->getComisiones($data);
        $precioPromedio = $this->listingService->getPrecioPromedio($data);
        $tiempoPromedio = $this->listingService->getTiempoPromedio($data);

        $captacionesActivas = $this->listingService->getCaptacionesActivas($data);

        if(!empty($data['agent_id']) && $data['agent_id'][0] ) {
            $agentes = [];
        } else {
            $data['agent_id'] = $data['agent_id'][0];
            $data['office_id'] = $data['office_id'][0];
            $data['anio'] = $data['anio'][0];

            if(isset($data['states_id']) && isset($data['states_id'][0])) {
                $data['state_id'] = $data['states_id'][0];
            }
            
            $agentes = $this->menuService->getDatosAgentes($data);
        }

        return [
            'comisionesAgentes' => $comisionesAgentes,
            'captacionesActivas' => $captacionesActivas,
            'precioPromedio' => $precioPromedio,
            'tiempoPromedio' => $tiempoPromedio,
            'agentesActivos' => $agentes,
        ];
    }

    public function getPromedios(Request $request)
    {
        // if(auth()->user()->hasRole('Broker')){
        //     $request->oficina_id = [auth()->user()->office_id];
        // }
        // if(auth()->user()->hasRole('Agente')){
        //     $request->oficina_id = [auth()->user()->office_id];
        //     $request->agente_id = [auth()->user()->agent_id];
        // }

        $data = [
            'meses' => $request->mesesSeleccionados,
            'anio' => $request->anioSeleccionado,
            'office_id' => $request->oficina_id ?? [],
            'agent_id' => $request->agente_id ?? [],
            'states_id' => $request->states_id ?? [],
            'provinces_id' => $request->provinces_id ?? [],
            'cities_id' => $request->cities_id ?? [],
            'zones_id' => $request->zones_id ?? [],
            'transaction_types_id' => $request->transaction_types_id ?? [],
        ];

        $precioPromedio = $this->listingService->getPrecioPromedio($data);
        $tiempoPromedio = $this->listingService->getTiempoPromedio($data);

        return [
            'precioPromedio' => $precioPromedio,
            'tiempoPromedio' => $tiempoPromedio,
        ];
    }

    public function getInventario(Request $request)
    {
        // if(auth()->user()->hasRole('Broker')){
        //     $request->oficina_id = auth()->user()->office_id;
        // }
        // if(auth()->user()->hasRole('Agente')){
        //     $request->oficina_id = auth()->user()->office_id;
        //     $request->agente_id = auth()->user()->agent_id;
        // }

        $data = [
            'meses' => $request->mesesSeleccionados ? [$request->mesesSeleccionados[count($request->mesesSeleccionados) - 1]] : [],
            'anio' => $request->anioSeleccionado ? [$request->anioSeleccionado[count($request->anioSeleccionado) - 1]] : [],
            'mensual' => $request->tipoReporte == 'mensual' ? 1 : 0,
            'office_id' => $request->oficina_id ?? '',
            'agent_id' => $request->agente_id ?? '',
            'state_id' => $request->departamento_id ? [$request->departamento_id] : [''],
        ];

        return $this->listingService->getInventario($data);
    }

    public function getDatosCaptaciones(Request $request)
    {
        $captaciones = $this->listingService->getDatosCaptaciones($request);
        return response()->json($captaciones);
    }

    public function getAgentes(Request $request)
    {
        $agentes = $this->agentService->getAgentes([
            'office_id' => $request->oficina_id
        ]);
        return response()->json($agentes);
    }

    public function getAgentesPorUbicacion(Request $request)
    {
        $agentes = $this->agentService->getAgentesPorUbicacion($request);
        return $agentes;
    }

    public function getDetallesTransacciones(Request $request)
    {
        return $this->transactionService->getDetallesTransacciones($request);
    }

    public function getComisiones(Request $request)
    {
        return $this->commissionService->getComisiones($request);
    }

    public function test(Request $request)
    {
        $data = [
            'meses' => [1],
            'anio' => [2025],
            'mensual' => 1,
            'office_id' => ['120043'],
            'agent_id' => ['1756'],
            'cantidad' => 'true',
        ];
        return $this->listingService->test($data);
    }

	public function getResumenEjecutivo (Request $request) {

        // if(auth()->user()->hasRole('Broker')){
        //     $request->office_id = auth()->user()->office_id;
        // }
        // if(auth()->user()->hasRole('Agente')){
        //     $request->office_id = auth()->user()->office_id;
        //     $request->agent_id = auth()->user()->agent_id;
        // }

		$data = [
			'months' => $request->mesesSeleccionados,
			'year' => $request->anioSeleccionado,
			'monthly' => $request->mensual ? 1 : 0,
			'office_id' => $request->office_id ?? '',
			'agent_id' => $request->agent_id ?? '',
			'state_id' => $request->departamento_id ?? '',
            'inDollars' => $request->inDollars ?? 0,
		];

		return $this->menuService->getExecutiveReport($data);
	}

    public function executiveResumeDetails(Request $request)
    {
        return response()->json($this->executiveResumeService->getExecutiveResumeDetails($request));
    }

    public function executiveResumeDetailsExport(Request $request)
    {
        $data = $this->executiveResumeService->getExecutiveResumeDetails($request);

        if($request->comparativeType == 'year')
        {
            $excelHeaders = ['Oficina', 'Transacciones Año Pasado' , 'Transacciones', 
            'Volumen Año Pasado', 'Volumen', 'Agentes Activos Año Pasado', 'Agentes Activos', 
            'Comisiones Año Pasado', 'Comisiones', 'Captaciones Activas Año Pasado', 'Captaciones Activas', 
            'Tiempo en el Mercado'];
        }
        else {
            $excelHeaders = ['Oficina','Meta Transacciones', 'Transacciones', 'Meta Volumen', 'Volumen',
            'Meta Agentes Activos', 'Agentes Activos', 'Meta Comisiones', 'Comisiones', 'Meta Captaciones Activas', 'Captaciones Activas',
            'Tiempo en el Mercado'];

        }

        $formattedData = array_map(function ($item) {
            return [
                $item['name'] ?? '',
                $item['comparation_transactions'] ?? 0,
                $item['current_transactions'] ?? 0,
                $item['comparation_transaction_volume'] ?? 0,
                $item['current_transaction_volume'] ?? 0,
                $item['comparation_agents'] ?? 0,
                $item['current_agents'] ?? 0,
                $item['comparation_payment_amount'] ?? 0,
                $item['current_payment_amount'] ?? 0,
                $item['comparation_active_listings'] ?? 0,
                $item['current_active_listings'] ?? 0,
                $this->yearMonthFormat($item['age'] ?? 0)
            ];
        }, $data);

        $excelData = array_merge([$excelHeaders], $formattedData);

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $activeWorksheet
            ->fromArray($excelData);

        $activeWorksheet->getStyle('1:1')->getFont()->setBold(true);

        foreach ($excelData[0] as $columnIndex => $value) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
            $activeWorksheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        $fileName = 'test.xlsx';
        $tempFilePath = storage_path("app/temp/{$fileName}");

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        ob_end_clean();

        return response()->download($tempFilePath, basename($tempFilePath), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . basename($tempFilePath) . '"'
        ])->deleteFileAfterSend(true);

        return response()->json($formatedPayments);

    }

    public function yearMonthFormat($value)
    {
        $years = intval($value / 365);
        $months = ($value % 365) / 30  ;

        return sprintf('%d años %d meses', $years, $months);
    }

    public function transactions (Request $request)
    {
        return Inertia::render('Dashboard/transacciones', [
            'mes' => $request->mes,
            'anio' => $request->anio,
            'office_id' => $request->office_id,
            'agent_id' => $request->agent_id,
            'type' => $request->type
        ]);
    }

    public function getTransactionsByType (Request $request)
    {
        if(auth()->user()->hasRole('Broker')){
            $agent = Agent::where('user_id', auth()->user()->id)->first();
            if($agent)
            {
                $request->office_ids = [$agent->office->id];
            }else{
                return response()->json(['error' => 'No se encontró la oficina del agente'], 404);
            }
        }
        if(auth()->user()->hasRole('Agente')){
            $agent = Agent::where('user_id', auth()->user()->id)->first();
            if($agent)
            { 
                $request->agent_id = [$agent->id];
                $request->office_ids = [$agent->office->id];
            }else{
                return response()->json(['error' => 'No se encontró la oficina del agente'], 404);
            }
        }
        return response()->json($this->transactionService->getTransactionsByType($request));
    }
}
