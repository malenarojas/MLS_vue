<?php

namespace App\Console\Commands;

use App\Models\Commission;
use App\Models\Payment;
use Illuminate\Console\Command;

class ModifyPaymentsOfficeNull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:modify-payments-office-null';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payments = Payment::whereNull('office_id')->get();
        foreach($payments as $payment)
        {
            $agent = $payment->agent;
            if($agent)
            {
                $payment->office_id = $agent->office->id;
                $payment->save();
            }
        }

        $commissions = Commission::whereNull('office_id')->get();
        foreach($commissions as $commission)
        {
            $agent = $commission->agent;
            if($agent){
                $commission->office_id = $agent->office->id;
                $commission->save();
            }
        }
    }
}
