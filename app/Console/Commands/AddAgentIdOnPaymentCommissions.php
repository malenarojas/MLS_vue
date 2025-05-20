<?php

namespace App\Console\Commands;

use App\Models\Commission;
use App\Models\Office;
use App\Models\Payment;
use Illuminate\Console\Command;

class AddAgentIdOnPaymentCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-agent-id-on-payment-commissions';

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
        $payments = Payment::all();
        foreach($payments as $payment)
        {
            $agentInternalId = $payment->agent_internal_id;

            if($agentInternalId)
            {
                $officeId = substr($agentInternalId, 0, 6);
                if($officeId == '120099')
                {
                    $officeId = '125001';
                }
                $office = Office::where('office_id', $officeId)->first();
                if($office)
                {
                    $payment->office_id = $office->id;
                    $payment->save();
                }
            }
        }
        echo('Done payments');

        $commissions = Commission::all();
        foreach($commissions as $commission)
        {
            $agentInternalId = $commission->agent_internal_id;

            if($agentInternalId)
            {
                $officeId = substr($agentInternalId, 0, 6);
                if($officeId == '120099')
                {
                    $officeId = '125001';
                }
                $office = Office::where('office_id', $officeId)->first();
                if($office)
                {
                    $commission->office_id = $office->id;
                    $commission->save();
                }
            }
        }
    }
}
