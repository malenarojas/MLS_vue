<?php

namespace App\Services\Agents;

use App\Dtos\Agents\AchievementDto;
use App\Models\Achievement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Achievement::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Achievement::all();
    }

    public function findById(string $id): Achievement
    {
        return Achievement::findOrFail($id);
    }

    public function create(AchievementDto $dto): Achievement
    {
        return DB::transaction(function () use ($dto) {
            return Achievement::create($dto->toArray());
        });
    }

    public function update(string $id, AchievementDto $dto): Achievement
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
      /**
     * Eliminar un logro por ID.
     */
    public function delete(int $id): bool
    {
        $achievement = Achievement::findOrFail($id);
        return $achievement->delete();
    }
}
