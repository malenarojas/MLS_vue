<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\Payment;
use Illuminate\Console\Command;

class FixAgentIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-agent-ids';

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
        $agentIds = 
        [
            [
                'oldId' => '1200421690',
                'newId' => '120042001'
            ],
            [
                'oldId' => '1200305086',
                'newId' => '120030072'
            ],
            [
                'oldId' => '1200225048',
                'newId' => '120022155'
            ],
            [
                'oldId' => '1200341128',
                'newId' => '120034003'
            ],
            [
                'oldId' => '1200344886',
                'newId' => '120034169'
            ],
            [
                'oldId' => '1200562414',
                'newId' => '120078014'
            ],
            [
                'oldId' => '1200421745',
                'newId' => '120042056'
            ],
            [
                'oldId' => '1200472031',
                'newId' => '120047028'
            ],
            [
                'oldId' => '1200682859',
                'newId' => '120068013'
            ],
            ['oldId' => '1200165057', 'newId' => '120016154'],
            ['oldId' => '1200165058', 'newId' => '120016155'],
            ['oldId' => '1200165059', 'newId' => '120016156'],
            ['oldId' => '1200165060', 'newId' => '120016157'],
            // ['oldId' => '1200365065', 'newId' => ''],
            ['oldId' => '1200431818', 'newId' => '120043067'],
            ['oldId' => '1200435063', 'newId' => '120043101'],
            ['oldId' => '1200435080', 'newId' => '120043102'],
            ['oldId' => '1200451875', 'newId' => '120045001'],
            ['oldId' => '1200455062', 'newId' => '120045120'],
            ['oldId' => '1200472004', 'newId' => '120047001'],
            ['oldId' => '1200472005', 'newId' => '120047002'],
            ['oldId' => '1200472018', 'newId' => '120047015'],
            ['oldId' => '1200535066', 'newId' => '120053058'],
            ['oldId' => '1200575072', 'newId' => '120057014'],
            ['oldId' => '1200642601', 'newId' => '120064006'],
            ['oldId' => '1200773004', 'newId' => '120077004'],
            ['oldId' => '1200774925', 'newId' => '120077021'],
            ['oldId' => '1200793052', 'newId' => '120079001'],
            ['oldId' => '1200883223', 'newId' => '120088021'],
            ['oldId' => '1200953341', 'newId' => '120095015'],
            ['oldId' => '1250015055', 'newId' => '125001362'],
            ['oldId' => '1250015069', 'newId' => '125001363'],
            ['oldId' => '1250045081', 'newId' => '125004119'],
        ];
        
        

        foreach ($agentIds as $agentId)
        {
            $agent = Agent::where('agent_internal_id', $agentId['oldId'])->first();

            if($agent)
            {
                $commissions = Commission::where('agent_internal_id', $agentId['oldId'])->get();
                foreach ($commissions as $commission)
                {
                    $commission->agent_internal_id = $agentId['newId'];
                    $commission->save();
                }
                $payments = Payment::where('agent_internal_id', $agentId['oldId'])->get();
                foreach ($payments as $payment)
                {
                    $payment->agent_internal_id = $agentId['newId'];
                    $payment->save();
                }
                $agent->agent_internal_id = $agentId['newId'];
                $agent->save();
            }else{
                $this->info('Agente no encontrado: ' . $agentId['oldId']);
            }
        }
    }
}
