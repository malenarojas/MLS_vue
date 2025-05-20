<?php

namespace App\Http\Controllers;


use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuditLogController extends Controller
{
	protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    // Método para crear un log de auditoría
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'agent_id' => 'required|exists:agents,id', // Asegúrate que el agente existe
            'user_id' => 'required|exists:users,id', // Asegúrate que el usuario existe
            'field_name' => 'required|string', // El campo editado
            'old_value' => 'nullable|string', // Valor anterior
            'new_value' => 'nullable|string', // Nuevo valor
            'notes' => 'nullable|string', // Notas adicionales
        ]);

        // Llamar al servicio para crear el log de auditoría
        $this->auditLogService->createAuditLog($request->all());

        return response()->json(['message' => 'Audit log created successfully'], 201);
    }

    // Método para obtener los logs de auditoría de un agente específico
    public function index($agentId)
    {
        $logs = $this->auditLogService->getAuditLogsByAgent($agentId);
        return response()->json($logs);
    }
}
