<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Str;


class CommissionService
{
    public function getComisiones ($data) {

        $query = Commission::where(function ($subQuery) use ($data) {
            if(isset($data['transaction_id']) && $data['transaction_id'] != '') {
                $subQuery->where('transaction_id', $data['transaction_id']);
            }

            if(isset($data['listing_id']) && $data['listing_id'] != '') {
                $subQuery->where('listing_id', $data['listing_id']);
            }
        });

        return $query->get();
    }

    //Funcion para crear, actualizar y borrar comisiones en base a un json de datos de comisiones
    //Inserta : Los elementos del json que no posean commission_id
    //Actualiza : Los elementos del json que si poseen commission_id (uuid)
    //BORRA : Los elementos de la tabla los cuales NO ESTEN PRESENTES en el json (OJO)
    public function createUpdate($data) {

        $transaction = Transaction::where('internal_id', $data['transaction_id'])->first();
        $hasChanged = false;
        $exchange_rate = ExchangeRate::orderBy('id', 'desc')->first();
        //Aca entra solo si:
        //Se esta realizando el paso 4 y se tiene que actualizar el agent_id y office_id
        //caso no se haya insertado el office_id y agent_id anteriormente
        //Se trabaja con la otra transaccion puesto que los datos a actualizar son esos
        if(isset($data['other_transaction']) && $data['other_transaction'] == true){

            $transaction = Transaction::where('listing_id', $transaction->listing_id)
                ->whereNot('id', $transaction->id)->first();


            if ($data['commissions'][0]) {
                $first_commission = $data['commissions'][0];
                $agent = Agent::find($first_commission['agent_id']);
                if ($agent) {
                    $transaction->agent_id = $agent->id;
                    $transaction->office_id = $agent->office->id;
                    $transaction->save();

                    // - Bandera para saber si se realizo un cambio en la transaccion
                    // - Se actualiza la transaccion a estado 4 (Pendiente a aprovacion)
                    //$hasChanged = true;
                }
            }
        }

        $incomingCommissionsMapping = array_filter(
            array_map(function ($commission) {
                return $commission['commission_id'] != 'null' ? $commission['commission_id'] : null;
            }, $data['commissions']),
            function ($value) {
                return $value !== null;
            }
        );

        //Comisiones para borrar

        $commissionsToDelete = Commission::where('transaction_id', $transaction->id)
            ->whereNotIn('internal_id', $incomingCommissionsMapping)
            ->get();

        foreach($commissionsToDelete as $commissionToDelete){
            $commissionToDelete->delete();
            $hasChanged = true;
        }

        //Comisiones a actualizar

        $commissionsToUpdate = Commission::whereIn('internal_id', $incomingCommissionsMapping)->get();

        foreach($commissionsToUpdate as $commissionToUpdate) {

            $commissionReference = array_values(array_filter($data['commissions'], function($commission) use ($commissionToUpdate) {
                return $commission['commission_id'] == $commissionToUpdate->internal_id;
            }));

            //echo json_encode($commissionReference);

            if($commissionReference && isset($commissionReference[0])){
                $commissionReference = $commissionReference[0];
                $agent = Agent::find($commissionReference['agent_id']);

                $commissionToUpdate->commission_type_id = $commissionReference['commission_type_id'];
                $commissionToUpdate->agent_internal_id = $agent->agent_internal_id;
                $commissionToUpdate->office_id = $agent->office->id;
                $commissionToUpdate->total_commission_amount = $commissionReference['total_commission_amount'];
                $commissionToUpdate->total_commission_percentage = $commissionReference['total_commission_percentage'];


                if($commissionToUpdate->getAttributes() != $commissionToUpdate->getOriginal()) {
                    $hasChanged = true;
                }

                $commissionToUpdate->save();
            }
        }

        //Comisiones a insertar

        $dataToInsert = array_filter(
            $data['commissions'],
            function ($commission) {
                return $commission['commission_id'] == null;
            }
        );

        foreach($dataToInsert as $newData) {
            $agent = Agent::find($newData['agent_id']);
            Commission::create([
                'agent_internal_id' => $agent->agent_internal_id,
                'office_id' => $agent->office->id,
                'transaction_id' => $transaction->id,
                'total_commission_amount' => $newData['total_commission_amount'],
                'total_commission_amount_currency' => 'BOB',
                'total_commission_percentage' => $newData['total_commission_percentage'],
                'commission_type_id' => $newData['commission_type_id'],
                'internal_id' => (string) Str::uuid(),
                'date_created' => Carbon::today(),
                'date_edited' => Carbon::today(),
                'exchange_rate_id' => $exchange_rate?->id ?? null,
            ]);

            $hasChanged = true;

        }

        if ($hasChanged && $transaction->transaction_status_id != 3) {
            $transaction->transaction_status_id = 4;
            $transaction->save();
        }

        return 1;
    }

    public function getCommissionsOrCreate($transaction_id, $create = true)
    {
        $transaction = Transaction::find($transaction_id);
        $exchange_rate = ExchangeRate::orderBy('id', 'desc')->first();

        $commissions_transaction = Commission::where('transaction_id', $transaction->id)
            ->get();

        if (count($commissions_transaction) == 0 && $create) {
            $commission = Commission::create([
                'transaction_id' => $transaction->id,
                'internal_id' => (string) Str::uuid(),
                'commission_type_id' => 1,
                'agent_internal_id' => $transaction->agent->agent_internal_id,
                'office_id' => $transaction->agent->office->id,
                'date_created' => Carbon::today(),
                'date_edited' => Carbon::today(),
                'total_commission_amount' => $this->calculateCommission($transaction),
                'total_commission_amount_currency' => 'BOB',
                'total_commission_amount_usd' => $this->calculateCommission($transaction),
                'total_commission_percentage' => $this->calculateCommission($transaction) * 100 / $transaction->current_listing_price,
                'exchange_rate_id' => $exchange_rate?->id ?? null,

            ]);

            return [[
                'commission_id' => $commission->internal_id,
                'commission_type_id' => $commission->commission_type_id,
                'agent_name' => $commission->agent->user->name_to_show,
                'agent_id' => $commission->agent->id,
                'total_commission_amount' => $commission->total_commission_amount,
                'total_commission_percentage' => $commission->total_commission_percentage,
                'can_delete' => ($transaction->side == 1 && ($commission->agent->id == $transaction->agent->id))
                ? false : true
            ]];
        } else {
            
            //Caso no se ha creado comision, el trr es de selling y both
            //Es necesario agregar a primer agente
            if ($transaction->transaction_type_id == 2 && $transaction->both == 1 && count($transaction->commissions) == 0) {
                Commission::create([
                    'transaction_id' => $transaction->id,
                    'internal_id' => (string) Str::uuid(),
                    'commission_type_id' => 1,
                    'agent_internal_id' => $transaction->agent->agent_internal_id,
                    'office_id' => $transaction->agent->office->id,
                    'date_created' => Carbon::today(),
                    'date_edited' => Carbon::today(),
                    'total_commission_amount' => $this->calculateCommission($transaction),
                    'total_commission_amount_currency' => 'BOB',
                    'total_commission_amount_usd' => $this->calculateCommission($transaction),
                    'exchange_rate_id' => $exchange_rate?->id ?? null,
                    'total_commission_percentage' => $this->calculateCommission($transaction) * 100 / $transaction->current_listing_price,

                ]);
            }
            return $commissions_transaction->map(function ($commission) use ($transaction) {
                return [
                    'commission_id' => $commission->internal_id,
                    'commission_type_id' => $commission->commission_type_id,
                    'agent_name' => $commission->agent->user->name_to_show,
                    'agent_id' => $commission->agent->id,
                    'total_commission_amount' => $commission->total_commission_amount,
                    'total_commission_percentage' => $commission->total_commission_percentage,
                    'can_delete' => ($transaction->side == 1 && ($commission->agent->id == $transaction->agent->id))
                    ? false : true,
                ];
            });
        }
    }

    private function calculateCommission (Transaction $transaction) {
        if($transaction->listing && $transaction->listing->commission_option) {

            if($transaction->transaction_type_id == 1) {

                return $transaction->listing->commission_option->type_recruitment_commission == 'P' ?
                    $transaction->current_listing_price * $transaction->listing->commission_option->recruitment_commission / 100 :
                    $transaction->listing->commission_option->recruitment_commission;
            }else{

                return $transaction->listing->commission_option->sales_commission_type == 'P' ?
                    $transaction->current_listing_price * $transaction->listing->commission_option->sales_commission / 100 :
                    $transaction->listing->commission_option->sales_commission;
            }

        }else {

            return $transaction->current_listing_price * 2.5 / 100;

        }
    }

}
