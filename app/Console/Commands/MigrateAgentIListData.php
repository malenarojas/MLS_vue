<?php

namespace App\Console\Commands;

use App\Http\Controllers\AgentController;
use App\Jobs\MigrateIListAgentData;
use Illuminate\Console\Command;

class MigrateAgentIListData extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-agent-i-list-data';

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
        dispatch(new MigrateIListAgentData());
    }
}
