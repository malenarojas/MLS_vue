
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuditLogController;

Route::middleware([
    'auth:sanctum'
])->prefix('agents')->group(function () {

    //Route::get('/addBrokerRoleAndPassword', [AgentController::class, 'addBrokerRoleAndPassword']);
    Route::get('/create', [AgentController::class, 'create']);
    Route::get('/migrateAgents', [AgentController::class, 'migrateAgents']);
    Route::get('/export-excel', [AgentController::class, 'exportExcel'])->name('agents.export-excel');
    Route::get('/export-exceltoday', [AgentController::class, 'exportExceltoday'])->name('agents.export-exceltoday');


    Route::get('/changePermissionsNew', [AgentController::class, 'changePermissionsNew']);
    Route::get('/create', [AgentController::class, 'create'])->name('agents.create');
    Route::get('/edit', [AgentController::class, 'edit'])->name('agents.edit');
    Route::get('/', [AgentController::class, 'index'])->name('agents.index');
    Route::get('/filter', [AgentController::class, 'filter'])->name('agents.filter');
    Route::get('/search', [AgentController::class, 'search'])->name('agents.search');
    Route::post('/', [AgentController::class, 'store'])->name('agents.store');
    Route::post('/agents_external', [AgentController::class, 'storeExternal'])->name('agents.storeExternal');
    Route::get('/migrateAgentsGriAPI', [AgentController::class, 'migrateAgentsGriAPI']);
    Route::post('/migrate-agent-images', [AgentController::class, 'handleAgentImageMigration']);
    Route::get('/{id}', [AgentController::class, 'show'])->name('agents.show');
    Route::put('/{id}', [AgentController::class, 'update'])->name('agents.update');
    Route::delete('/{id}', [AgentController::class, 'destroy'])->name('agents.destroy');
    Route::get('/agent/{agentId}/logs', [AuditLogController::class, 'index']);
    Route::get('/listings/{id}', [AgentController::class, 'getListings'])->name('agents.getListings');
    Route::post('/by-office', [AgentController::class, 'getAgentsByOffice']);
    Route::post('/by-region', [AgentController::class, 'getAgentsByRegion']);
    Route::post('/filter', [AgentController::class, 'getFilteredAgents']);
    Route::get('/addPermission', [AgentController::class, 'addPermission']);

    Route::get('/agents/{agentId}/logs', [AuditLogController::class, 'index']);
   // Route::delete('/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');



});
