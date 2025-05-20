<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\ExchangeRate;
use App\Models\Office;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    //Funcion para crear, actualizar y borrar pagos en base a un json de datos de pagos
    //Inserta : Los elementos del json que no posean payment_id
    //Actualiza : Los elementos del json que si poseen payment_id (uuid)
    //BORRA : Los elementos de la tabla los cuales NO ESTEN PRESENTES en el json (OJO)
    public function createUpdate ($data) {

        $transaction = Transaction::where('internal_id', $data['transaction_id'])->first();
        $exchange_rate = ExchangeRate::orderBy('id', 'desc')->first();

        if(isset($data['other_transaction']) && $data['other_transaction'] == true){

            $transaction = Transaction::where('listing_id', $transaction->listing_id)
                ->whereNot('id', $transaction->id)->first();

        }

        $incommingPaymentsMapping = array_filter(
            array_map(function ($payment) {
                return $payment['payment_id'] != 'null' ? $payment['payment_id'] : null;
            }, $data['payments']),
            function ($value) {
                return $value !== null;
            }
        );



        $hasChanged = false;

        //Pagos para borrar

        $paymentsToDelete = Payment::where('transaction_id', $transaction->id)
            ->whereNotIn('internal_id', $incommingPaymentsMapping)
            ->where('amount_expected', '>=' , 0)
            ->get();

        foreach($paymentsToDelete as $paymentToDelete){

            //Caso que sea de referido, borrar tambien sus negativos (pagos que no se muestran en el form)
            if($paymentToDelete->payment_type_id == 2) {
                $paymentNegative = Payment::where('transaction_id', $transaction->id)
                    ->where('amount_expected' , $paymentToDelete->amount_expected * -1)
					->where('payment_type_id', 2)
                    ->first();

                if($paymentNegative) {
                    $paymentNegative->delete();
                }
            }
            $hasChanged = true;
            $paymentToDelete->delete();
        }

        //Pagos a actualizar

        $paymentsToUpdate = Payment::whereIn('internal_id', $incommingPaymentsMapping)->get();


        foreach($paymentsToUpdate as $paymentToUpdate) {

            // Encontrar los datos enviados correspondientes a el pago a actualizar
            $paymentReference = array_values(array_filter($data['payments'],
                function($payment) use ($paymentToUpdate) {
                    return $payment['payment_id'] == $paymentToUpdate->internal_id;
                }));
            
            if($paymentReference && isset($paymentReference[0])){

                if(!$paymentToUpdate->received_date && $paymentReference[0]['received'] == 'true') {
                    $paymentToUpdate->exchange_rate_id = $exchange_rate?->id ?? null;
                }

                $paymentReference = $paymentReference[0];
                $agent = Agent::find($paymentReference['agent_id']);

                $lestPaymentType = $paymentToUpdate->payment_type_id;

                $paymentToUpdate->agent_internal_id = $agent->agent_internal_id;

                $paymentToUpdate->office_id = $agent->office->id;

                $paymentToUpdate->amount_expected = $paymentReference['amount_expected']??0;

                $paymentToUpdate->amount_received = $paymentReference['received'] == 'true' ? $paymentReference['amount_received'] : 0;

                $paymentToUpdate->expected_payment_date = $paymentReference['expected_payment_date']
                        ? Carbon::createFromFormat('d/m/Y', $paymentReference['expected_payment_date'])
                            ->setTime(0, 0, 0)
                            ->format('Y-m-d H:i:s')
                        : null;

                $paymentToUpdate->received_date = $paymentReference['received'] && $paymentReference['received'] == 'true'?
                                                Carbon::createFromFormat('d/m/Y',$paymentReference['received_date'])
                                                ->setTime(0, 0, 0)
                                                ->format('Y-m-d H:i:s')
                                                : null;

                $paymentToUpdate->payment_type_id = $paymentReference['type_commission'];

                if($paymentToUpdate->getAttributes() != $paymentToUpdate->getOriginal()) {
                    $hasChanged = true;
                }

                $paymentToUpdate->save();

                //Caso se haya cambiado de 2 a 1 y se tenga que borrar el payment negativo
                if($lestPaymentType == 2 && $paymentToUpdate->payment_type_id == 1) {
                    $paymentNegativeToDelete = Payment::where('transaction_id', $transaction->id)
                        ->where('amount_expected' , $paymentToUpdate->amount_expected * -1)
						->where('payment_type_id', 2)
                        ->first();

                    $paymentNegativeToDelete->delete();
                }

                if($paymentToUpdate->payment_type_id == 2) {

                    //Ya existia el pago negativo paralelo, solo hay que actualizarlo
                    if ($lestPaymentType == 2) {
                        $paymentNegativeToUpdate = Payment::where('transaction_id', $transaction->id)
                        ->where('amount_expected', $paymentToUpdate->amount_expected * -1)
						->where('payment_type_id', 2)
                            ->first();

                        if(!$paymentNegativeToUpdate->received_date && $paymentReference[0]['received'] == 'true') {
                            $paymentNegativeToUpdate->exchange_rate_id = $exchange_rate?->id??null;
                        }

                        $paymentNegativeToUpdate->amount_expected = abs($paymentReference['amount_expected']) * -1;

                        $paymentNegativeToUpdate->amount_received = $paymentReference['received'] == 'true' ? abs($paymentReference['amount_received']) * -1 : 0;

                        $paymentNegativeToUpdate->expected_payment_date = $paymentReference['expected_payment_date'] ?
                            Carbon::createFromFormat('d/m/Y', $paymentReference['expected_payment_date'])->format('Y-m-d')
                            : null;

                        $paymentNegativeToUpdate->received_date = $paymentReference['received_date'] && $paymentReference['received'] == 'true' ?
                            Carbon::createFromFormat('d/m/Y', $paymentReference['received_date'])->format('Y-m-d')
                            : null;

						$paymentNegativeToUpdate->save();
                    }
                    // No existe aun el pago paralelo, hay que crearlo
                    else
                    {
                        $newPaymentNegative = Payment::create([
                            'agent_internal_id' => $transaction->agent->agent_internal_id,
                            'office_id' => $transaction->agent->office->id,
                            'transaction_id' => $paymentToUpdate->transaction_id,
                            'internal_id' => (string) Str::uuid(),
                            'expected_payment_date' => $paymentToUpdate->expected_payment_date,
                            'received_date' => $paymentReference['received'] == 'true' ? $paymentToUpdate->received_date : null,
                            'amount_expected' => abs($paymentReference['amount_expected']) * -1??0,
                            'amount_received' => $paymentReference['received'] == 'true' ? abs($paymentReference['amount_received']) * -1 : 0,
                            'amount_expected_currency' => 'USD',
                            'payment_type_id' => 2,
                            'exchange_rate_id' => $exchange_rate?->id ?? null,
                        ]);
                    }
                }
            }
        }


        //Pagos a insertar

        $dataToInsert = array_filter(
            $data['payments'],
            function ($payment) {
                return $payment['payment_id'] == null;
            }
        );

		//Log::info($dataToInsert);

        foreach($dataToInsert as $newData) {

            $agent = Agent::find($newData['agent_id']);

            Payment::create([

                'agent_internal_id' => $agent->agent_internal_id,

                'office_id' => $agent->office->id,

                'transaction_id' => $transaction->id,

                'amount_expected' => $newData['amount_expected']??0,

                'amount_received' => !empty($newData['received']) && $newData['received'] == 'true' ?  $newData['amount_received'] : 0 ,

                'received_date' => $newData['received_date'] && $newData['received'] == 'true' ?
                                    Carbon::createFromFormat('d/m/Y',$newData['received_date'])->format('Y-m-d')
                                    : null,

                'expected_payment_date' => $newData['expected_payment_date'] ?
                                    Carbon::createFromFormat('d/m/Y',$newData['expected_payment_date'])->format('Y-m-d')
                                    : null,

                'amount_expected_currency' => 'USD',
                'amount_received_currency' => 'USD',
                'internal_id' => (string) Str::uuid(),

                'payment_type_id' => $newData['type_commission'],
                'exchange_rate_id' => $exchange_rate?->id ?? null,
            ]);

            if($newData['type_commission'] == 2) {
                Payment::create([

                    'agent_internal_id' => $transaction->agent->agent_internal_id,

                    'office_id' => $transaction->agent->office->id,

                    'transaction_id' => $transaction->id,

                    'amount_expected' => $newData['amount_expected'] * -1??0,

                    'amount_received' => $newData['amount_received'] * -1??0,

                    'received_date' => $newData['received_date'] ?
                                        Carbon::createFromFormat('d/m/Y',$newData['received_date'])->format('Y-m-d')
                                        : null,

                    'expected_payment_date' => $newData['expected_payment_date'] ?
                                        Carbon::createFromFormat('d/m/Y',$newData['expected_payment_date'])->format('Y-m-d')
                                        : null,

                    'amount_expected_currency' => 'USD',
                    'amount_received_currency' => 'USD',
                    'internal_id' => (string) Str::uuid(),

                    'payment_type_id' => $newData['type_commission'],
                    'exchange_rate_id' => $exchange_rate?->id ?? null,
                ]);
            }
            $hasChanged = true;

        }

        if ($hasChanged && $transaction->transaction_status_id != 3) {
            $transaction->transaction_status_id = 4;
            $transaction->save();
        }

        return 1;
    }

    public function getPayments($transaction_id) {

        $transaction = Transaction::find($transaction_id);

        $payments = $transaction->payments->filter(function($payment) {
            return $payment->amount_expected > 0;
        });

        if($payments->count() > 0) {
            return $payments->map(function($payment) {
                return [
                    'payment_id' =>  $payment->internal_id,
                    'agent_id' =>  $payment->agent->id,
                    'agent_name' => $payment->agent->user->name_to_show,
                    'expected_payment_date' => Carbon::parse($payment->expected_payment_date)->format('d/m/Y'),
                    'amount_expected' => abs($payment->amount_expected)??0,
                    'amount_received' => abs($payment->amount_received)??0,
                    'received_date' => Carbon::parse($payment->received_date)->format('d/m/Y'),
                    'received' => $payment->received_date ? true : false,
                    'type_commission' => $payment->payment_type_id
                ];
            });

        } else {
            // $commissions = $transaction->commissions;
            // $total_amount = $commissions->sum('total_commission_amount');

            // $payment = Payment::create([
            //     'transaction_id' => $transaction->id,
            //     'internal_id' => (string) Str::uuid(),
            //     'agent_internal_id' => $transaction->agent->agent_internal_id,
            //     'expected_payment_date' => Carbon::today()->format('Y-m-d'),
            //     'amount_expected' => $total_amount,
            //     'amount_expected_currency' => 'USD',
            // ]);

            // return [
            //     [
            //         'payment_id' => $payment->internal_id,
            //         'agent_internal_id' => $payment->agent->agent_internal_id,
            //         'amount_expected' => $payment->amount_expected,
            //         'amount_expected_currency' => $payment->amount_expected_currency,
            //         'expected_payment_date' => Carbon::createFromFormat('Y-m-d',$payment->expected_payment_date)->format('d/m/Y'),
            //         'agent_id' => $payment->agent->id,
            //         'agent_name' => $payment->agent->user->name_to_show,
            //         'type_commission' => 1,
            //         'received' => false,
            //     ]
            // ];

            return [];
        }
    }

    public function getFilterPayments ($data) {

        $query = Payment::selectRaw('
            payments.id,
            payments.expected_payment_date,
            payments.amount_expected,
            payments.amount_received,
            payments.received_date,
            users.name_to_show as agent_name,
            transactions.transaction_type_id,
			transactions.transaction_status_id,
            transactions.trr_report_id as trr_id,
            listings.transaction_type_id as listing_transaction_type,
            listings.area_id,
			listings.MLSID,
            listings.is_external
        ')
        ->join('transactions', 'payments.transaction_id', '=', 'transactions.id')
        ->leftJoin('listings', 'listings.id', '=', 'transactions.listing_id')
        ->leftJoin('agents', 'payments.agent_internal_id', '=','agents.agent_internal_id')
        ->leftJoin('users', 'users.id', '=', 'agents.user_id')
		->whereIn('transactions.transaction_status_id', [2,3,4,5]); //Submited, Incompleto, Pendiente y Aceptado

        if(isset($data['not_received']) && $data['not_received'] == 'true'){
            $query->whereNull('payments.received_date');
        }

        if(isset($data['office_id']) && $data['office_id'] != ''){
			$office = Office::find($data['office_id']);
            $query->where('agents.office_id', $office->office_id);
        }

        if(isset($data['agent_id']) && $data['agent_id'] != ''){
            $query->where('agents.id', $data['agent_id']);
        }

        if(isset($data['trr_report_id']) && $data['trr_report_id'] != ''){
            $query->where('transactions.trr_report_id', $data['trr_report_id']);
        }

        if(isset($data['months']) && $data['months'] != ''){
            $query->whereIn(DB::raw('MONTH(payments.expected_payment_date)'), $data['months']);
        }

        if(isset($data['year']) && $data['year'] != ''){
            $query->whereRaw('YEAR(payments.expected_payment_date) = ?', [$data['year']]);
        }

        $result = $query->orderBy('payments.expected_payment_date', 'DESC')->get();

        return $result;
    }

    public function getPaymentsTransaction ($data) {

        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        
        $startDate = Carbon::createFromFormat('Y-m-d', $data['start_date']);
        $endDate = Carbon::createFromFormat('Y-m-d', $data['end_date'])->format('Y-m-d');
        $firstDayOfYear = Carbon::createFromFormat('Y-m-d', $startDate->year. '-01-01')->format('Y-m-d');


        $agents = Agent::select('agents.*', 'users.name_to_show')
            ->join('offices', 'offices.office_id', '=', 'agents.office_id')
            ->join('users', 'users.id', '=', 'agents.user_id')
            ->whereIn('offices.id', $data['office_ids'])
            ->where(function ($subQuery) use ($firstDayOfYear) {
                $subQuery->whereNull('agents.date_termination')
                    ->orWhere('agents.date_termination', '>', $firstDayOfYear);
            })
            ->get();

        $rows = [];
        $startDate = $startDate->format('Y-m-d');

        foreach($agents as $agent) {

            $transactions = $this->getTransactionByArea($agent->id, $startDate, $endDate);

            $payments = $this->getPaymentsByArea($agent->id, $firstDayOfYear, $endDate);

            //RESIDENCIAL

            //Transacciones
            $transactionsRES = $transactions->filter(function ($transaction) {
                return $transaction->area_id == 2;
            });

            $listingSideQuantityRES = count($transactionsRES->filter(function ($transaction) {
                return $transaction->transaction_type_id == 1;
            }));

            $sellingSideQuantityRES = count($transactionsRES->filter(function ($transaction) {
                return $transaction->transaction_type_id == 2;
            }));

            $transactionsListingSideRES = ($transactionsRES->filter(function ($transaction) {
                return $transaction->transaction_type_id == 1;
            }));

            $volumeListingSideRES = $data['inDollars'] ?
                $transactionsListingSideRES->sum('current_listing_price_usd') :
                $transactionsListingSideRES->sum('current_listing_price');

            $transactionsSellingSideRES = ($transactionsRES->filter(function ($transaction) {
                return $transaction->transaction_type_id == 2;
            }));

            $volumeSellingSideRES = $data['inDollars'] ?
                $transactionsSellingSideRES->sum('current_listing_price_usd') :
                $transactionsSellingSideRES->sum('current_listing_price');



            $paymentsRES = $payments->filter(function ($payment) use ($startDate, $endDate) {
                return ($payment->area_id == 2 || $payment->area_id == null) &&
                    (Carbon::parse($payment->expected_payment_date)->toDateString() >= $startDate
                        && Carbon::parse($payment->expected_payment_date)->toDateString() <= $endDate) ;
            });

            $amountPaymentsRES = $data['inDollars'] ? 
                $paymentsRES->sum('amount_expected_usd') : 
                $paymentsRES->sum('amount_expected');

            $paymentsInYearRES = $payments->filter(function ($payment) use ($firstDayOfYear, $endDate) {
                return ($payment->area_id == 2 || $payment->area_id == null) &&
                    (Carbon::parse($payment->expected_payment_date)->toDateString() >= $firstDayOfYear
                        && Carbon::parse($payment->expected_payment_date)->toDateString() <= $endDate) ;
            });

            $amountPaymentsInYearRES = $data['inDollars'] ? 
                $paymentsInYearRES->sum('amount_expected_usd') : 
                $paymentsInYearRES->sum('amount_expected');


            //COMERCIAL

            $transactionsCOM = $transactions->filter(function ($transaction) {
                return $transaction->area_id == 1;
            });

            $listingSideQuantityCOM = count($transactionsCOM->filter(function ($transaction) {
                return $transaction->transaction_type_id == 1;
            }));
            $sellingSideQuantityCOM = count($transactionsCOM->filter(function ($transaction) {
                return $transaction->transaction_type_id == 2;
            }));

            $transactionsListingSideCOM = ($transactionsCOM->filter(function ($transaction) {
                return $transaction->transaction_type_id == 1;
            }));

            $volumeListingSideCOM = $data['inDollars'] ?
                $transactionsListingSideCOM->sum('current_listing_price_usd') :
                $transactionsListingSideCOM->sum('current_listing_price');

            $transactionsSellingSideCOM = ($transactionsCOM->filter(function ($transaction) {
                return $transaction->transaction_type_id == 2;
            }));

            $volumeSellingSideCOM = $data['inDollars'] ?
                $transactionsSellingSideCOM->sum('current_listing_price_usd') :
                $transactionsSellingSideCOM->sum('current_listing_price');

            $paymentsCOM = $payments->filter(function ($payment) use ($startDate, $endDate) {
                return $payment->area_id == 1 &&
                    (Carbon::parse($payment->expected_payment_date)->toDateString() >= $startDate
                        && Carbon::parse($payment->expected_payment_date)->toDateString() <= $endDate) ;
            });

            $amountPaymentsCOM  = $data['inDollars'] ? 
                $paymentsCOM->sum('amount_expected_usd') : 
                $paymentsCOM->sum('amount_expected');

            $paymentsInYearCOM = $payments->filter(function ($payment) use ($firstDayOfYear, $endDate) {
                return $payment->area_id == 1 &&
                    (Carbon::parse($payment->expected_payment_date)->toDateString() >= $firstDayOfYear
                        && Carbon::parse($payment->expected_payment_date)->toDateString() <= $endDate) ;
            });

            $amountPaymentsInYearCOM  = $data['inDollars'] ? 
                $paymentsInYearCOM->sum('amount_expected_usd') : 
                $paymentsInYearCOM->sum('amount_expected');

            //Si es que existen datos

            if (
                count($transactionsRES) > 0 || count($paymentsRES) > 0 ||
                count($transactionsCOM) > 0 || count($paymentsCOM) > 0 ||
                $amountPaymentsInYearCOM > 0 || $amountPaymentsInYearRES > 0
            ) {

                if($data['inDollars']) {
                    
                    $paymentsRES = $paymentsRES->map(function ($payment) {
                        $payment->amount_expected = $payment->amount_expected_usd;
                        $payment->amount_received = $payment->amount_received_usd;
                        return $payment;
                    });

                    $paymentsCOM = $paymentsCOM->map(function ($payment) {
                        $payment->amount_expected = $payment->amount_expected_usd;
                        $payment->amount_received = $payment->amount_received_usd;
                        return $payment;
                    });
                }

                $rows[$agent->id] = [

                    'transactions_listing_res' => $listingSideQuantityRES,
                    'transactions_selling_res' => $sellingSideQuantityRES,
                    'transactions_listing_res_volume' => $volumeListingSideRES,
                    'transactions_selling_res_volume' => $volumeSellingSideRES,
                    'amount_payment_res' => $amountPaymentsRES,
                    'payments_res' => $paymentsRES,
                    'payments_in_year_res' => $amountPaymentsInYearRES,

                    'transactions_listing_com' => $listingSideQuantityCOM,
                    'transactions_selling_com' => $sellingSideQuantityCOM,
                    'transactions_listing_com_volume' => $volumeListingSideCOM,
                    'transactions_selling_com_volume' => $volumeSellingSideCOM,
                    'amount_payment_com' => $amountPaymentsCOM,
                    'payments_com' => $paymentsCOM,
                    'payments_in_year_com' => $amountPaymentsInYearCOM,

                    'agent_name' => $agent->name_to_show,

                    'is_active' => $agent->agent_status_id,

                ];
            }
        }
        usort($rows, function($a, $b) {
            return $a['agent_name'] <=> $b['agent_name'];
        });

        return $rows;
    }

    public function getPaymentsTransactionByOffice ($data)
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $inDollars = $data['inDollars'] == 1;
        $office_id = $data['office_id'];
        $month = $data['month'];
        $year = $data['year'];

        $payments = $this->getPaymentsByOffice($office_id, $year, $month, $inDollars);
        $transactions = $this->getTransactionByOffice($office_id, $year, $month);
        
        $paymentsThisMonth = $payments->filter(function ($p) use ($month){
            return $p->month == $month;
        });

        $agents = [];

        foreach($payments as $payment)
        {
            if(!isset($agents[$payment->agent_id]))
            {
                $transactionListingResVolume =  
                    $transactions->filter(function ($t) use ($payment){
                        return $t->agent_id == $payment->agent_id && $t->area_id == 2 && $t->transaction_type_id == 1;
                    });

                $transactionSellingResVolume =  
                    $transactions->filter(function ($t) use ($payment){
                        return $t->agent_id == $payment->agent_id && $t->area_id == 2 && $t->transaction_type_id == 2;
                    });

                $transactionListingComVolume = 
                    $transactions->filter(function ($t) use ($payment){
                        return $t->agent_id == $payment->agent_id && $t->area_id == 1 && $t->transaction_type_id == 1;
                    });

                $transactionSellingComVolume = 
                    $transactions->filter(function ($t) use ($payment){
                        return $t->agent_id == $payment->agent_id && $t->area_id == 1 && $t->transaction_type_id == 2;
                    });


                $agents[$payment->agent_id] = [

                    'transactions_listing_res_volume' => $inDollars ? 
                        $transactionListingResVolume->sum('current_listing_price_usd') : 
                        $transactionListingResVolume->sum('current_listing_price'),

                    'transactions_selling_res_volume' => $inDollars ? 
                        $transactionSellingResVolume->sum('current_listing_price_usd') : 
                        $transactionSellingResVolume->sum('current_listing_price'),

                    'transactions_listing_com_volume' => $inDollars ? 
                        $transactionListingComVolume->sum('current_listing_price_usd') : 
                        $transactionListingComVolume->sum('current_listing_price'),

                    'transactions_selling_com_volume' => $inDollars ? 
                        $transactionSellingComVolume->sum('current_listing_price_usd') : 
                        $transactionSellingComVolume->sum('current_listing_price'),

                    'transactions_listing_com' => 
                        $transactions->filter(function ($t) use ($payment){
                            return $t->agent_id == $payment->agent_id && $t->area_id == 1 && $t->transaction_type_id == 1;
                        })->count(),

                    'transactions_selling_com' => 
                        $transactions->filter(function ($t) use ($payment){
                            return $t->agent_id == $payment->agent_id && $t->area_id == 1 && $t->transaction_type_id == 2;
                        })->count(),

                    'transactions_listing_res' => 
                        $transactions->filter(function ($t) use ($payment){
                            return $t->agent_id == $payment->agent_id && $t->area_id == 2 && $t->transaction_type_id == 1;
                        })->count(),

                    'transactions_selling_res' => 
                        $transactions->filter(function ($t) use ($payment){
                            return $t->agent_id == $payment->agent_id && $t->area_id == 2 && $t->transaction_type_id == 2;
                        })->count(),

                    'payments_in_year_res' => 
                        $payments->filter(function ($p) use ($payment){
                            return $p->agent_id == $payment->agent_id && $p->area_id == 2;
                        })->sum('amount_expected') ,

                    'payments_in_year_com' => 
                        $payments->filter(function ($p) use ($payment){
                            return $p->agent_id == $payment->agent_id && $p->area_id == 1;
                        })->sum('amount_expected'),

                    'amount_payment_res' => 0,
                    'amount_payment_com' => 0,
                    'payments_res' => [],
                    'payments_com' => [],
                    
    
                    'agent_name' => $payment->agent_name,

                    'is_active' => 1,
                ];  
                
            }
        }


        foreach($paymentsThisMonth as $payment)
        {   
            if($payment->area_id == 2){
                $agents[$payment->agent_id]['amount_payment_res'] += $payment->amount_expected;
            }else{
                $agents[$payment->agent_id]['amount_payment_com'] += $payment->amount_expected;
            }

            if($payment->area_id == 2)
            {
                $agents[$payment->agent_id]['payments_res'][] = $payment;
            }
            else
            {
                $agents[$payment->agent_id]['payments_com'][] = $payment;                
            }

        }

        usort($agents, function($a, $b) {
            return $a['agent_name'] <=> $b['agent_name'];
        });

        return $agents;

    }

    private function getPaymentsByOffice ($office_id, $year, $month, $inDollars) : Collection
    {
        $payments = Payment::selectRaw('
            payments.id,
            payments.expected_payment_date,
            payments.received_date,
            payments.payment_type_id,

            IF(transactions.transaction_type_id = 2 AND transactions.both = 1,
                tr_listing_side.trr_report_id, 
                transactions.trr_report_id) as trr_report_id,

            IF(listings.is_external = 1, "", transactions.mls_id) as mls_id,
            transactions.sold_date, 
            transactions.current_listing_price, 
            transactions.transaction_type_id,
            listings.area_id,
            listings.is_external, 
            transactions.agent_id as transaction_agent_id,
            agents.id as agent_id,
            users.name_to_show as agent_name,

            IF( ? = 1,
                IFNULL(
                    (payments.amount_expected /exchange_rates.amount) ,
                    (payments.amount_expected / 6.96)
                ),
                (payments.amount_expected)
            ) as amount_expected,

            IF( ? = 1,
                IFNULL(
                    (payments.amount_received /exchange_rates.amount) ,
                    (payments.amount_received / 6.96)
                ),
                (payments.amount_received)
            ) as amount_received,

            SUM(
                (commissions.total_commission_amount * 100) / transactions.current_listing_price
                ) as commission_percentage,
            MONTH(payments.expected_payment_date) as month', [$inDollars, $inDollars]
         )
         ->join('transactions', 'transactions.id', '=', 'payments.transaction_id')
         ->leftJoin('exchange_rates', 'payments.exchange_rate_id', '=', 'exchange_rates.id')
         ->leftJoin('listings', 'transactions.listing_id', '=', 'listings.id')
         ->join('agents', 'payments.agent_internal_id', '=', 'agents.agent_internal_id')
         ->join('users', 'agents.user_id', '=', 'users.id')
         ->leftJoin('transactions as tr_for_commissions', 'tr_for_commissions.listing_id', '=', 'listings.id')
         ->leftJoin('commissions', 'commissions.transaction_id', '=', 'tr_for_commissions.id')
         ->where('commissions.commission_type_id', 1)
         ->whereIn('transactions.transaction_status_id', [2,4,5])
 
         ->leftJoin('transactions as tr_listing_side', function ($query) {
             $query->on('tr_listing_side.listing_id', '=', 'listings.id')
                 ->where('tr_listing_side.transaction_type_id', 1);
         })
 
         ->where('payments.office_id', $office_id)
         ->where(function ($query) {
             $query->whereNotNull('payments.amount_received')
                 ->where('payments.amount_received', '!=', 0);
         })
         ->whereYear('payments.expected_payment_date', $year)
         ->whereMonth('payments.expected_payment_date', '<=', $month)
         ->groupBy(
            'month',
            'payments.id',
            // 'payments.expected_payment_date',
            // 'payments.amount_expected',
            // 'payments.amount_received',
            // 'payments.received_date',
            // 'payments.payment_type_id',
            // 'trr_report_id',
            // 'transactions.mls_id',
            // 'transactions.sold_date',
            // 'transactions.current_listing_price',
            // 'transactions.transaction_type_id',
            // 'listings.area_id',
            // 'listings.is_external',
            // 'transactions.agent_id',
         )
         ->get();

        return $payments;
    }

    private function getPaymentsByArea($agent_id, $startDate, $endDate) {

        $agent = Agent::find($agent_id);
        $query = Payment::selectRaw(
           'payments.id,
            payments.expected_payment_date,
            payments.amount_expected,
            payments.amount_received,
            payments.received_date,
            payments.payment_type_id,

            IF(transactions.transaction_type_id = 2 AND transactions.both = 1,
                tr_listing_side.trr_report_id, 
                transactions.trr_report_id) as trr_report_id,

            IF(listings.is_external = 1, "", transactions.mls_id) as mls_id,
            transactions.sold_date, 
            transactions.current_listing_price, 
            transactions.transaction_type_id,
            listings.area_id,
            listings.is_external, 
            transactions.agent_id as transaction_agent_id,
            agents.id as agent_id,
                IFNULL(
                    (payments.amount_expected /exchange_rates.amount) ,
                    (payments.amount_expected / 6.96))
                as amount_expected_usd,
            
                IFNULL(
                    (payments.amount_received /exchange_rates.amount) ,
                    (payments.amount_received / 6.96))
                as amount_received_usd,
            SUM(
                (commissions.total_commission_amount * 100) / transactions.current_listing_price
                ) as commission_percentage'
        )
        ->join('transactions', 'transactions.id', '=', 'payments.transaction_id')
        ->leftJoin('exchange_rates', 'payments.exchange_rate_id', '=', 'exchange_rates.id')
        ->leftJoin('listings', 'transactions.listing_id', '=', 'listings.id')
        ->join('agents', 'payments.agent_internal_id', '=', 'agents.agent_internal_id')
        ->join('transactions as tr_for_commissions', 'tr_for_commissions.listing_id', '=', 'listings.id')
        ->join('commissions', 'commissions.transaction_id', '=', 'tr_for_commissions.id')
        ->where('commissions.commission_type_id', 1)
        ->whereIn('transactions.transaction_status_id', [2,4,5])

        ->leftJoin('transactions as tr_listing_side', function ($query) {
            $query->on('tr_listing_side.listing_id', '=', 'listings.id')
                ->where('tr_listing_side.transaction_type_id', 1);
        })

        ->where('payments.agent_internal_id', $agent->agent_internal_id)
        ->where(function ($query) {
            $query->whereNotNull('payments.amount_received')
                ->where('payments.amount_received', '!=', 0);
        })
        ->whereBetween(DB::raw('DATE(payments.expected_payment_date)'), [$startDate, $endDate])
        ->groupBy(
            'payments.id',
            'payments.expected_payment_date',
            'payments.amount_expected',
            'payments.amount_received',
            'payments.received_date',
            'payments.payment_type_id',
            'trr_report_id',
            'transactions.mls_id',
            'transactions.sold_date',
            'transactions.current_listing_price',
            'transactions.transaction_type_id',
            'listings.area_id',
            'listings.is_external',
            'transactions.agent_id',
            'agents.id'
        )
        ->get();

        return $query;
    }

    private function getTransactionByArea ($agent_id, $startDate, $endDate) : Collection {
        return Transaction::selectRaw(
                'transactions.transaction_type_id, 
                transactions.current_listing_price, 
                transactions.id,
                transactions.agent_id,
                listings.area_id,
                SUM(IFNULL((transactions.current_listing_price /exchange_rates.amount) ,(transactions.current_listing_price / 6.96))) as current_listing_price_usd'
            )
                ->join('listings', 'transactions.listing_id', '=', 'listings.id')
                ->whereIn('transactions.transaction_status_id', [2,4,5])
                ->leftJoin('exchange_rates', 'transactions.exchange_rate_id', '=', 'exchange_rates.id')
                ->where('transactions.agent_id', $agent_id)
                ->whereBetween(DB::raw('DATE(transactions.sold_date)'), [$startDate, $endDate])
                ->groupBy(
                    'transactions.id',
                    'transactions.transaction_type_id',
                    'listings.area_id',
                    'transactions.current_listing_price')
                ->get();
    }
    
    private function getTransactionByOffice ($office_id, $year, $month) : Collection {
        return Transaction::selectRaw(
                'transactions.transaction_type_id, 
                transactions.current_listing_price, 
                transactions.id,
                transactions.agent_id,
                listings.area_id,
                SUM(IFNULL((transactions.current_listing_price /exchange_rates.amount) ,(transactions.current_listing_price / 6.96))) as current_listing_price_usd'
            )
                ->join('listings', 'transactions.listing_id', '=', 'listings.id')
                ->whereIn('transactions.transaction_status_id', [2,4,5])
                ->leftJoin('exchange_rates', 'transactions.exchange_rate_id', '=', 'exchange_rates.id')
                ->where('transactions.office_id', $office_id)
                ->whereMonth('transactions.sold_date', $month)
                ->whereYear('transactions.sold_date', $year)
                ->groupBy(
                    'transactions.id',
                    'transactions.transaction_type_id',
                    'listings.area_id',
                    'transactions.current_listing_price')
                ->get();
    }

    public function getOnlyPayments ($data) {

        $year = $data['year'];
        $month = $data['month'];

        $office_id = $data['office_id'];

        $payments = Payment::selectRaw('
            users.name_to_show as agent_name,
            offices.name as office_name,
            transactions.trr_report_id,
            
            IF(
                listings.is_external = 1, "", transactions.mls_id
            ) as mls_id,

            CASE
                WHEN '. $data['inDollars'].' = 1 THEN
                    IFNULL(
                        (transactions.current_listing_price /exchange_rate_transactions.amount) ,
                        (transactions.current_listing_price / 6.96)
                    )    
                ELSE
                    transactions.current_listing_price
            END as current_listing_price,

            DATE_FORMAT(transactions.sold_date, "%d/%m/%Y") as sold_date,

            IF(
                transactions.transaction_type_id = 1, "L", "S"
                ) 
            as transaction_type,

            IF(
                transactions.agent_id = agents.id, "SI","NO"
                ) 
            as is_pricipal_agent,

            CASE
                WHEN '. $data['inDollars'].' = 1 THEN 
                    IFNULL(
                        (payments.amount_received /exchange_rate_payments.amount) ,
                        (payments.amount_received / 6.96)
                    )    
                ELSE
                    payments.amount_received
            END as amount_received,
                
            DATE_FORMAT(payments.expected_payment_date, "%d/%m/%Y") as expected_payment_date,
            DATE_FORMAT(payments.received_date, "%d/%m/%Y") as received_date,

            IF(transactions.transaction_type_id = 2 AND transactions.both = 1,
                tr_listing_side.trr_report_id, 
                transactions.trr_report_id) as trr_report_id,

            IF(
                '. $data['inDollars'].' = 1, "USD", "Bs"
            ) as currency
            ')
            ->join('transactions', 'transactions.id', '=', 'payments.transaction_id')
            ->leftJoin('listings', 'transactions.listing_id', '=', 'listings.id')
            ->join('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
            ->join('users', 'users.id', '=', 'agents.user_id')
            ->join('offices', 'offices.id', '=', 'payments.office_id')
            ->leftJoin('exchange_rates as exchange_rate_payments', 'payments.exchange_rate_id', '=', 'exchange_rate_payments.id')
            ->leftJoin('exchange_rates as exchange_rate_transactions', 'transactions.exchange_rate_id', '=', 'exchange_rate_transactions.id')
            
            ->leftJoin('transactions as tr_listing_side', function ($query) {
                $query->on('tr_listing_side.listing_id', '=', 'listings.id')
                    ->where('tr_listing_side.transaction_type_id', 1);
            })
            
            ->whereYear('payments.expected_payment_date', $year)
            ->whereMonth('payments.expected_payment_date', $month)
            ->where('offices.id', $office_id)

            ->whereNotNull('payments.amount_received')
            ->where('payments.amount_received', '!=', 0)
            ->whereIn('transactions.transaction_status_id', [2,4,5])
            ->whereNotNull('payments.received_date')

            ->orderBy('users.name_to_show')
            ->get();
        
        return $payments;
    }
    // No se ocupa aun
    public function getOnlyPaymentsYtd ($data)
    {
        $month = $data['month'];
        $year = $data['year'];
        $offices = Office::where('id', $data['office_id'])->get();

        $office_ids = array_map(function($office) {
            return $office['id'];
        }, $offices->toArray());


        $payments = Payment::selectRaw('
            users.name_to_show as agent_name,
            offices.name as office_name,
            transactions.trr_report_id,
            
            IF(
                listings.is_external = 1, "", transactions.mls_id
            ) as mls_id,

            CASE
                WHEN '. $data['inDollars'].' = 1 THEN
                    IFNULL(
                        (transactions.current_listing_price /exchange_rate_transactions.amount) ,
                        (transactions.current_listing_price / 6.96)
                    )    
                ELSE
                    transactions.current_listing_price
            END as current_listing_price,

            DATE_FORMAT(transactions.sold_date, "%d/%m/%Y") as sold_date,

            IF(
                transactions.transaction_type_id = 1, "L", "S"
                ) 
            as transaction_type,

            IF(
                transactions.agent_id = agents.id, "SI","NO"
                ) 
            as is_pricipal_agent,

            CASE
                WHEN '. $data['inDollars'].' = 1 THEN 
                    IFNULL(
                        (payments.amount_received /exchange_rate_payments.amount) ,
                        (payments.amount_received / 6.96)
                    )    
                ELSE
                    payments.amount_received
            END as amount_received,
                
            DATE_FORMAT(payments.expected_payment_date, "%d/%m/%Y") as expected_payment_date,
            DATE_FORMAT(payments.received_date, "%d/%m/%Y") as received_date,

            IF(transactions.transaction_type_id = 2 AND transactions.both = 1,
                tr_listing_side.trr_report_id, 
                transactions.trr_report_id) as trr_report_id,

            IF(
                '. $data['inDollars'].' = 1, "USD", "Bs"
            ) as currency
            ')
            ->join('transactions', 'transactions.id', '=', 'payments.transaction_id')
            ->leftJoin('listings', 'transactions.listing_id', '=', 'listings.id')
            ->join('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
            ->join('users', 'users.id', '=', 'agents.user_id')
            ->join('offices', 'offices.office_id', '=', 'agents.office_id')
            ->leftJoin('exchange_rates as exchange_rate_payments', 'payments.exchange_rate_id', '=', 'exchange_rate_payments.id')
            ->leftJoin('exchange_rates as exchange_rate_transactions', 'transactions.exchange_rate_id', '=', 'exchange_rate_transactions.id')
            
            ->leftJoin('transactions as tr_listing_side', function ($query) {
                $query->on('tr_listing_side.listing_id', '=', 'listings.id')
                    ->where('tr_listing_side.transaction_type_id', 1);
            })
            
            
            ->whereIn('offices.id', $office_ids)

            ->whereNotNull('payments.amount_received')
            ->where('payments.amount_received', '!=', 0)
            ->whereIn('transactions.transaction_status_id', [2,4,5])
            ->whereNotNull('payments.received_date')

            ->orderBy('users.name_to_show')
            ->get();
    }

    public function formatPaymentsToExcel($payments) {
        $formatedPayments = [];

        $titles = [
            'Agente', 'Oficina', 'TRR', 'MLS', 'Precio de captacion', 'Fecha de venta',
            'Tipo', 'Agente Principal', 'Cantidad recibida', 'Dia de pago', 'Recibido el', 'Moneda'
        ];

        $formatedPayments [] = $titles;
        $formatedPayments = array_merge($formatedPayments, array_map('array_values', $payments->toArray()));

        return $formatedPayments;
    }

    public function formatDate ($transactions) {
        $transactions = array_map(function($transaction) {
            $transaction['payments_res'] = array_map(function ($payment) {
                $payment['sold_date'] = Carbon::parse($payment['sold_date'])->format('d/m/Y');
                $payment['expected_payment_date'] = Carbon::parse($payment['expected_payment_date'])->format('d/m/Y');
                $payment['received_date'] = $payment['received_date'] ? Carbon::parse($payment['received_date'])->format('d/m/Y') : null;
                return $payment;
            }, $transaction['payments_res']);

            $transaction['payments_com'] = array_map(function ($payment) {
                $payment['sold_date'] = Carbon::parse($payment['sold_date'])->format('d/m/Y');
                $payment['expected_payment_date'] = Carbon::parse($payment['expected_payment_date'])->format('d/m/Y');
                $payment['received_date'] = $payment['received_date'] ? Carbon::parse($payment['received_date'])->format('d/m/Y') : null;
                return $payment;
            }, $transaction['payments_com']);

            return $transaction;
        }, $transactions);

        return $transactions;
    }

    public function formatTransactionsToExcel($transactions, $year, $month) {

        $formatedTransactions = [];

        $titles = [
            'Last Name', 'First Name', 'Unique ID', 'Master Customer ID',
            'Office Number', 'Master Office ID','Office Name','Month','Year',
            'resListingEnds', 'resSellingEnds', 'resListingVolume', 'resSellingVolume',
            'comListingEnds', 'comSellingEnds', 'comListingVolume', 'comSellingVolume',
            'ytdCommRes', 'ytdCommCom'
        ];

        $formatedTransactions [] = $titles;

        foreach($transactions as $transaction) {
            $agent = User::where('name_to_show', $transaction['agent_name'])->first()?->agent??null;

            $row = [
                $agent?->user?->last_name?? '-',
                $agent?->user?->first_name?? '-',
                '',
                '',
                $agent?->office?->office_id?? '-',
                $agent?->office?->office_id?? '-',
                $agent?->office?->name?? '-',
                $month,
                $year,
                $transaction['transactions_listing_res'],
                $transaction['transactions_selling_res'],
                $transaction['transactions_listing_res_volume'],
                $transaction['transactions_selling_res_volume'],
                $transaction['transactions_listing_com'],
                $transaction['transactions_selling_com'],
                $transaction['transactions_listing_com_volume'],
                $transaction['transactions_selling_com_volume'],
                $transaction['payments_in_year_res'],
                $transaction['payments_in_year_com'],
            ];

            $formatedTransactions [] = $row;
        }

        return $formatedTransactions;
    }

	public function updatePayments ($data) {
        $payment = Payment::find($data['payment_id' ]);
        $exchange_rate = ExchangeRate::orderBy('id', 'desc')->first();

        if($data['received'] == 1) {
            $payment->received_date = Carbon::now();
            $payment->amount_received = $payment->amount_expected??0;
            $payment->amount_received_usd = $payment->amount_expected??0;
            $payment->exchange_rate_id = $exchange_rate?->id ?? null;
        } else {
            $payment->received_date = null;
            $payment->amount_received = 0;
            $payment->amount_received = 0;
        }

        $payment->save();
        return $payment;
    }

}
