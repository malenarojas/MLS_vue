<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentListings;
use App\Models\Commission;
use App\Models\CommissionOption;
use App\Models\Listing;
use App\Models\ListingInformation;
use App\Models\ListingPrice;
use App\Models\Office;
use App\Models\StatusListing;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\Payment;
use App\Models\User;
use App\Services\Listings\ListingService;
use App\Services\Transactions\TransactionService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PdfDom;
use Inertia\Inertia;
use Spatie\LaravelPdf\Facades\Pdf;
use function Spatie\LaravelPdf\Support\pdf;
use Spatie\LaravelPdf\Enums\Format;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{

    protected $transaccionService;
    protected $listingService;

    public function __construct(
        TransactionService $transaccionService,
        ListingService $listingService,
    ) {
        $this->transaccionService = $transaccionService;
        $this->listingService = $listingService;
    }

    public function index()
    {
        if (!auth()->user()->can('list transactions')) {
            abort(403, 'No tienes permiso para ver esta página');
        }
        return Inertia::render('Transactions/index');
    }

    public function getTransactionStatues()
    {
        return response()->json(TransactionStatus::all());
    }

    public function getTransacciones(Request $request)
    {
        if (!auth()->user()->can('list transactions')) {
            abort(403, 'No tienes permiso para ver esta página');
        }

        $data = [
            'transaction_status_id' => $request->transaction_status_id ?? [],
            'transaction_type_id' => $request->transaction_type_id ?? [],
            'office_id' => $request->office_id ?? [],
            'agent_id' => $request->agent_id ?? [],
            'transactions_loaded' => $request->transacciones_cargadas ?? [],
            'paginated' => true,
            'trr_id' => $request->trr_id ?? [],
            'mes' => $request->months,
            'anio' => [$request->year],
            'internal_id' => $request->internal_id ?? [],
            'inDollars' => $request->inDollars
        ];

        $transacciones = $this->transaccionService->getDetallesTransacciones($data);

        return response()->json($transacciones);
    }

    public function create(Request $request)
    {
        return Inertia::render('Transactions/create', [
            'transaction_id' => $request->transaction_id ?? null
        ]);
    }

    public function store(Request $request)
    {
        $transaction = $this->transaccionService->create($request);
        return response()->json($transaction);
    }

    public function getStepData(Request $request)
    {
        $stepData = $this->transaccionService->getStepData($request);
        return response()->json($stepData);
    }

    public function update(Request $request)
    {
        $transaction = $this->transaccionService->update($request);
        return response()->json($transaction);
    }

    public function getDetalle($id)
    {
        //return $request;
        $item = $this->transaccionService->detalleTransaccion($id);
        return response()->json($item);
    }

    public function show($transaction_id)
    {
        //dd($transaction_id);
        return Inertia::render('Transactions/show', [
            'transaction_id' => $transaction_id ?? null
        ]);
    }
    public function generatePdf(Request $request, $id)
    {
        $dato = $this->transaccionService->detalleTransaccion($id);

        // print_r($dato);

        $tr = $dato['tr'][0];
        $com1 = $dato['com1'];
        $com2 = $dato['com2'];
        $pay1 = $dato['pay1'];
        $pay2 = $dato['pay2'];
        $queryestado = TransactionStatus::where('id', $tr->transaction_status_id)->first();
        $estado = $queryestado->name ?? '';

        return pdf()
            ->view('pdf.transaction', compact('dato', 'tr', 'com1', 'com2', 'pay1', 'pay2', 'estado'))
            ->name('transaction.pdf');
    }

    public function generatePdfDownload(Request $request, $id)
    {
        $dato = $this->transaccionService->detalleTransaccion($id);
        $tr = $dato['tr'][0];
        $com1 = $dato['com1'];
        $com2 = $dato['com2'];
        $pay1 = $dato['pay1'];
        $pay2 = $dato['pay2'];
        $queryestado = TransactionStatus::where('id', $tr->transaction_status_id)->first();
        $estado = $queryestado->name ?? '';

        return pdf()
            ->view('pdf.transaction', compact('dato', 'tr', 'com1', 'com2', 'pay1', 'pay2', 'estado'))
            ->name('transaction.pdf')
            ->download()
        ;
    }

    public function updateStatus($transaction_id, $status_id)
    {
        $result = $this->transaccionService->updateStatus($transaction_id, $status_id);
        return response()->json($result, $result['status']);
    }
    public function setFinantiation(Request $request)
    {
        return $this->transaccionService->setFinantiation($request);
    }
    protected function _getArrayValue($array, $key, $default = null)
    {
        if (isset($array[$key])) return $array[$key];
        return $default;
    }

    public function processExcelFile($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheetCount = $spreadsheet->getSheetCount();

        for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
            $sheet = $spreadsheet->getSheet($sheetIndex);
            $sheetData = $sheet->toArray();

            switch ($sheetIndex) {
                case 0:
                    $this->processTransactions($sheetData);
                    break;
                case 1:
                    $this->processCommissions($sheetData);
                    break;
                case 2:

                    $this->processPayments($sheetData);
                    break;
                default:
                    break;
            }
        }
    }

    protected function processTransactions($data)
    {
        if ($data[0] && $data[0] != '') {
            $listing = Listing::where('MLSID', $data[0])->first();

            if ($listing) {

                $status = StatusListing::where('name', $data[2])
                    ->first();

                $agent = Agent::where('agent_internal_id', $data[3])
                    ->first();

                $office = Office::where('office_id', $data[4])
                    ->first();

                $trr = $data[5];

                $transaction_type_id = TransactionType::where('name', $data[6])
                    ->first();

                $mlsid = $data[9];

                $sold_price = $data[10] * 6.96;


                $transaction = Transaction::create([
                    'internal_id' => $data[1],
                    'listing_id' => $listing->id,
                    'transaction_status_id' => $status->id,
                    'agent_id' => $agent->id,
                    'office_id' => $office->id,
                    'trr_report_id' => $trr,
                    'transaction_type_id' => $transaction_type_id->id,
                    'mls_id' => $mlsid,
                    'current_listing_price' => $sold_price,
                    'sold_date' => '2025-01-15'
                ]);
            }
        }
    }

    protected function processCommissions($data)
    {
        $internal_id = $data[0];

        if ($data[2] == 'L') {
            $side = 1;
        } else {
            $side = 2;
        }

        $transaction = Transaction::where('internal_id', $internal_id)
            ->first();

        if (!$transaction) {

            $transaction = Transaction::where('mls_id', $data[3])
                ->where('transaction_type_id', $side)
                ->first();
        }

        if ($transaction) {

            $agent = Agent::where('agent_internal_id', $data[1])
                ->first();

            $office = Office::where('office_id', $data[4])
                ->first();

            $amount = $data[4] * 6.96;

            $commission = Commission::create([
                'transaction_id' => $transaction->id,
                'agent_id' => $agent->agent_internal_id,
                'amount' => $amount,
            ]);
        }
    }

    protected function processPayments($data) {}


    public function importFromExcel()
    {
        $filePath = storage_path('app/public/paraMigraTRR202501+b.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        $transactions_external_january = Transaction::select('transactions.id')
            ->leftJoin('listings', 'transactions.listing_id', '=', 'listings.id')
            ->where('transactions.sold_date', '>=', '2025-01-01')
            ->where('transactions.sold_date', '<=', '2025-01-31')
            ->where('listings.is_external', 1)
            ->get();

        foreach ($transactions_external_january as $transaction) {
            Commission::where('transaction_id', $transaction->id)->delete();
            Payment::where('transaction_id', $transaction->id)->delete();
            Transaction::where('id', $transaction->id)->delete();

            if ($transaction->listing_id) {
                Listing::where('id', $transaction->listing_id)->delete();
                ListingInformation::where('listing_id', $transaction->listing_id)->delete();
                AgentListings::where('listing_id', $transaction->listing_id)->delete();
                ListingPrice::where('listing_id', $transaction->listing_id)->delete();
                CommissionOption::where('listing_id', $transaction->listing_id)->delete();
            }
            $transaction->delete();
        }

        $transactionMap = [];

        DB::transaction(function () use ($filePath) {
            $spreadsheet = IOFactory::load($filePath);
            $sheets = $spreadsheet->getAllSheets();

            $transactions = $sheets[0]->toArray();
            $commissions = $sheets[1]->toArray();
            $payments = $sheets[2]->toArray();



            $insertedTransactions = [];


            foreach (array_slice($transactions, 1) as $row) {

                // $transactionTrr = Transaction::where('trr_report_id', $row[5])->first();
                if ($row[9] == '') {
                    $transactionMls = null;
                } else {
                    $transactionType = $row[6] == 'L' ? 1 : 2;
                    $transactionMls = Transaction::where('mls_id', $row[9])
                        ->where('transaction_type_id', $transactionType)
                        ->first();
                }

                if ($row[9] == '' || !$transactionMls) {

                    if ($row[9] != '') {
                        $listing = Listing::where('MLSID', $row[0])->first();
                    } else {

                        $agent = Agent::where('agent_internal_id', $row[3])->first();
                        if (!$agent) {
                            Log::info('Agent not found: ' . $row[3]);
                            continue;
                        }

                        $subfixListingId = $this->listingService->getLasMLSID($agent->id) + 1;
                        while (Listing::where('MLSID', $agent->agent_internal_id . '-' . $subfixListingId)->first()) {
                            $subfixListingId++;
                        }
                        $mlsid = $agent->agent_internal_id . '-' . $subfixListingId;

                        $listing = Listing::create([
                            'key' => (string) Str::uuid(),
                            'status_listing_id' => 8,
                            'is_external' => 1,
                            'MLSID' => $mlsid,
                            'created_at' => Carbon::today(),
                            'updated_at' => Carbon::today(),
                        ]);

                        $listingPrice = ListingPrice::create([
                            'listing_id' => $listing->id,
                            'amount' => $row[11] == 'BOB' ? $row[10] : $row[10] * 6.96,
                            'currency_id' => 1,
                            'created_at' => Carbon::today(),
                            'updated_at' => Carbon::today(),
                        ]);

                        DB::table('agent_listing')->insert([
                            'agent_id' => $agent->id,
                            'listing_id' => $listing->id,
                        ]);

                        ListingInformation::create([
                            'subtype_property_id' => 1,
                            'number_bathrooms' => 0,
                            'total_number_rooms' => 0,
                            'number_bedrooms' => 0,
                            'unit_department' => 0,
                            'cubic_volume' => 0,
                            'land_m2' => 0,
                            'total_area' => 0,
                            'listing_id' => $listing->id,
                        ]);
                    }

                    $statusTrr = TransactionStatus::where('name', $row[2])->first();
                    $agent = Agent::where('agent_internal_id', $row[3])->first();
                    $office = Office::where('office_id', $row[4])->first();
                    $transactionType = $row[6] == 'L' ? 1 : 2;
                    $soldPrice = $row[10] * 6.96;
                    $transaction = Transaction::create([
                        'internal_id' => $row[1],
                        'listing_id' => $listing ? $listing->id : null,
                        'transaction_status_id' => $statusTrr ? $statusTrr->id : null,
                        'agent_id' => $agent ? $agent->id : null,
                        'office_id' => $office ? $office->id : null,
                        'trr_report_id' => $row[5],
                        'transaction_type_id' => $transactionType,
                        'mls_id' => $row[9],
                        'current_listing_price' => $soldPrice,
                        'sold_date' => $row[20],
                        'created_at' => Carbon::today(),
                        'updated_at' => Carbon::today(),
                    ]);

                    Log::info('Transaction created: ' . $transaction->id);

                    $insertedTransactions[] = $transaction->toArray();
                    $transactionMap[$row[1]] = $transaction->id;
                }
            }
            //dd($insertedTransactions);

            foreach (array_slice($commissions, 1) as $row) {
                if (isset($transactionMap[$row[0]])) {
                    $agent = Agent::where('agent_internal_id', $row[5])->first();
                    $transaction = Transaction::where('internal_id', $row[0])->first();
                    $commission = Commission::create([
                        'agent_internal_id' => $row[5],
                        'transaction_id' => $transaction->id,
                        'total_commission_amount' => $row[4] * 6.96,
                        'total_commission_amount_currency' => 'BOB',
                        'commission_type_id' => 1,
                        'internal_id' => $row[1],
                        'created_at' => Carbon::today(),
                        'updated_at' => Carbon::today(),
                    ]);
                    Log::info('Commission created: ');
                    Log::info($commission->toArray());
                }
            }

            foreach (array_slice($payments, 1) as $row) {
                if (isset($transactionMap[$row[0]])) {
                    $agent = Agent::where('agent_internal_id', $row[1])->first();
                    $office = Office::where('office_id', $row[2])->first();

                    $transaction = Transaction::where('internal_id', $row[0])->first();

                    $payment = Payment::create([
                        'agent_internal_id' => $row[1],
                        'transaction_id' => $transaction->id,
                        'amount_expected' => $row[4] * 6.96,
                        'amount_received' => $row[6] * 6.96,
                        'expected_payment_date' => $row[3],
                        'received_date' => $row[5],
                        'amount_expected_currency' => 'BOB',
                        'amount_received_currency' => 'BOB',
                        'internal_id' => (string) Str::uuid(),
                        'billing_mount' => 1,
                        'billing_year' => 2025,
                        'payment_type_id' => 1,
                        'created_at' => Carbon::today(),
                        'updated_at' => Carbon::today(),
                    ]);

                    Log::info('Payment created: ');
                    Log::info($payment->toArray());
                }
            }
        });
        DB::commit();
        dd($transactionMap);
    }

    public function fixExcelImportation()
    {
        $listing_id = 32588;

        $transactions = Transaction::where('listing_id', $listing_id)->get();

        foreach ($transactions as $transaction) {
            $listing = Listing::where('MLSID', $transaction->mls_id)->first();

            if ($listing) {
                $transaction->update([
                    'listing_id' => $listing->id
                ]);
            }
        }

        $filePath = storage_path('app/public/paraMigraTRR202501+b.xlsx');

        $spreadsheet = IOFactory::load($filePath);
        $sheets = $spreadsheet->getAllSheets();

        $transactions = $sheets[0]->toArray();

        foreach ($transactions as $item) {
            $transaction = Transaction::where('internal_id', $item[1])->first();
            if ($transaction) {
                $transaction->sold_date = $item[20];
                $transaction->save();
            }
        }
    }
}
