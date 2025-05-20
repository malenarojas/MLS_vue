<?php

namespace App\Services\Agents;

use App\Dtos\Agents\TeamStatusDto;
use App\Models\TeamStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TeamStatusService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return TeamStatus::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return TeamStatus::all();
    }

    public function findById(string $id): TeamStatus
    {
        return TeamStatus::findOrFail($id);
    }

    public function create(TeamStatusDto $dto): TeamStatus
    {
        return DB::transaction(function () use ($dto) {
            return TeamStatus::create($dto->toArray());
        });
    }

    public function update(string $id, TeamStatusDto $dto): TeamStatus
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
      /**
     * Elimina un TeamStatus por su ID.
     */
    public function delete(int $id): bool
    {
        $teamStatus = $this->findById($id);
        return $teamStatus->delete();
    }
}
