<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createUpdate(Request $request)
    {
        $payments = $this->paymentService->createUpdate($request);
        return $payments;
    }

    public function getPaymentsDetails(Request $request)
    {
        $payments = $this->paymentService->getFilterPayments($request);
        return response()->json($payments);
    }

    public function getPaymentsTransaction(Request $request)
    {
        $transactions = $this->paymentService->getPaymentsTransactionByOffice($request);
        $transactions = $this->paymentService->formatDate($transactions);
        //$data = $request->only(['start_date', 'end_date', 'office_ids']);
        // foreach($transactions as $transaction) {

        //     $transaction['payments_res'] = !is_array($transaction['payments_res']) && !is_null($transaction['payments_res']) ? $transaction['payments_res']->toArray() : $transaction['payments_res'];
        //     $transaction['payments_com'] = !is_array($transaction['payments_com']) && !is_null($transaction['payments_com']) ? $transaction['payments_com']->toArray() : $transaction['payments_com'];

        // }

        // GetPaymentTransactionsWebhoook::dispatch($request->request_id, $data)->onQueue('payment_transactions');
        return response()->json($transactions);
    }


    function getPaymentsTransactionPdfGet($start_date, $end_date, $office_id, $inDollars, $month, $year)
    {

        $data = [
            'office_id' => $office_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'inDollars' => $inDollars,
            'month' => $month,
            'year' => $year
        ];

        $year = explode('-', $start_date)[0];
        $month = explode('-', $start_date)[1];
        $office_name = str_replace('/', '', Office::find($office_id)->name);

        $currency = $inDollars ? 'USD' : 'BOB';

        $transactions = $this->paymentService->getPaymentsTransactionByOffice($data);
        $transactions = $this->paymentService->formatDate($transactions);
        $fileName = $office_name . '_' . $year . '_' . $month . '_' . $currency . '.pdf';

        $pdf = Pdf::loadView('pdf.paymentTrasactionReport', compact('transactions', 'year', 'month', 'office_name', 'currency'))
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0);

        return $pdf->download($fileName);
    }

    public function getOnlyPaymentsExcel($office_id, $start_date, $end_date, $inDollars, $month, $year)
    {

        $data = [
            'office_id' => $office_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'inDollars' => $inDollars,
            'month' => $month,
            'year' => $year
        ];

        $payments = $this->paymentService->getOnlyPayments($data);

        $formatedPayments = $this->paymentService->formatPaymentsToExcel($payments);

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $activeWorksheet
            ->fromArray($formatedPayments);

        $activeWorksheet->getStyle('1:1')->getFont()->setBold(true);

        foreach ($formatedPayments[0] as $columnIndex => $value) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
            $activeWorksheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        $fileName = $office_id . '_' . $year . '_' . $month . '.xlsx';
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

    public function getPaymentsTransactionExcel($office_id, $start_date, $end_date, $inDollars, $month, $year)
    {

        $data = [
            'office_id' => $office_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'inDollars' => $inDollars,
            'month' => $month,
            'year' => $year
        ];

        $transactions = $this->paymentService->getPaymentsTransactionByOffice($data);

        $formatedTransactionsForExcel = $this->paymentService->formatTransactionsToExcel($transactions, $year, $month);

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $activeWorksheet
            ->fromArray($formatedTransactionsForExcel);

        $activeWorksheet->getStyle('1:1')->getFont()->setBold(true);

        foreach ($formatedTransactionsForExcel[0] as $columnIndex => $value) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
            $activeWorksheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        $fileName = $office_id . '_' . $year . '_' . $month . '.xlsx';
        $tempFilePath = storage_path("app/temp/{$fileName}");

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        ob_end_clean();

        return response()->download($tempFilePath, basename($tempFilePath), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . basename($tempFilePath) . '"'
        ])->deleteFileAfterSend(true);
    }

    function updatePayments(Request $request)
    {
        $payment = $this->paymentService->updatePayments($request);
        return response()->json($payment);
    }
}
