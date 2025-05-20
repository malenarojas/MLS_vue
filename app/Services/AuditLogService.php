<?php

namespace App\Services;

use App\Dtos\AuditLogDto;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AuditLogService
{


    public function getAll(): Collection
    {
        return AuditLog::all();
    }

    public function findById(string $id): AuditLog
    {
        return AuditLog::findOrFail($id);
    }

	 // Método para crear un log de auditoría
	 public function createAuditLog(array $data)
	 {
		 // Creamos el log usando los datos proporcionados
		 return AuditLog::create([
			 'agent_id' => $data['agent_id'],
			 'user_id' => $data['user_id'],
			 'field_name' => $data['field_name'],
			 'old_value' => $data['old_value'] ?? null,
			 'new_value' => $data['new_value'] ?? null,
			 'notes' => $data['notes'] ?? null,
		 ]);
	 }

	 // Método para obtener los logs de un agente específico
	 public function getAuditLogsByAgent($agentId)
	{
		return AuditLog::with('user') // Asegúrate de que el modelo tiene una relación 'user'
			->where('agent_id', $agentId)
			->get()
			->map(function ($log) {
				return [
					'id' => $log->id,
					'agent_id' => $log->agent_id,
					'user_id' => $log->user_id,
					'field_name' => $log->field_name,
					'old_value' => $log->old_value,
					'new_value' => $log->new_value,
					'user_name' => $log->user ? $log->user->first_name : 'Desconocido', // Usa 'name_to_show' para mostrar el nombre
					'created_at' => $log->created_at->toDateTimeString(),
					'updated_at' => $log->updated_at->toDateTimeString()
				];
			});
	}

}
