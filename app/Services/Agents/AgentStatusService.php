<?php

namespace App\Services\Agents;

use App\Dtos\Agents\AgentStatusDto;
use App\Models\AgentStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AgentStatusService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return AgentStatus::paginate($perPage);
    }
    public function getAll(): Collection
    {
        return AgentStatus::all();
    }

    public function findById(string $id): AgentStatus
    {
        return AgentStatus::findOrFail($id);
    }

    public function create(AgentStatusDto $dto): AgentStatusService
    {
        return DB::transaction(function () use ($dto) {
            return AgentStatus::create($dto->toArray());
        });
    }

    public function update(string $id, AgentStatusDto $dto): AgentStatus
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
