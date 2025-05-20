<?php

namespace App\Jobs;

use App\Http\Controllers\AgentController;
use App\Services\Agents\AgentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MigrateIListAgentData implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $agentController = app(AgentController::class);
        $agentController->migrateAgents();
        
    }
}
