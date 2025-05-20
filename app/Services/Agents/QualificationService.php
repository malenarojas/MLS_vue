<?php

namespace App\Services\Agents;

use App\Dtos\Agents\QualificationDto;
use App\Models\Qualification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class QualificationService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Qualification::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Qualification::all();
    }

    public function findById(string $id): Qualification
    {
        return Qualification::findOrFail($id);
    }

    public function create(QualificationDto $dto): Qualification
    {
        return DB::transaction(function () use ($dto) {
            return Qualification::create($dto->toArray());
        });
    }

    public function update(string $id, QualificationDto $dto): Qualification
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
    public function delete(int $id): bool
    {
        $qualification = Qualification::findOrFail($id);
        return $qualification->delete();
    }
    public function getAllByAgentId(int $agentId)
    {
        return Qualification::where('agent_id', $agentId)->get();
    }
}
