<?php

namespace App\Services\Agents;

use App\Dtos\Agents\AreaSpecialityUserDto;
use App\Models\AreaSpecialityUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AreaSpecialityUserService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return AreaSpecialityUser::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return AreaSpecialityUser::all();
    }

    public function findById(string $id): AreaSpecialityUser
    {
        return AreaSpecialityUser::findOrFail($id);
    }

    public function create(AreaSpecialityUserDto $dto): AreaSpecialityUser
    {
        return DB::transaction(function () use ($dto) {
            return AreaSpecialityUser::create($dto->toArray());
        });
    }

    public function update(string $id, AreaSpecialityUserDto $dto): AreaSpecialityUser
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
        /**
     * Eliminar una relación.
     */
    public function delete(int $id): bool
    {
        $model = $this->findById($id);
        return $model->delete();
    }

    public function getAllByAgentId(int $agentId): Collection
    {
        return AreaSpecialityUser::where('user_id', function ($query) use ($agentId) {
            $query->select('user_id')
                  ->from('agents')
                  ->where('id', $agentId)
                  ->limit(1);
        })->get();
    }
}
