<?php

namespace App\Services\Agents;

use App\Dtos\Agents\UserTypeDto;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserTypeService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return UserType::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return UserType::all();
    }

    public function findById(string $id): UserType
    {
        return UserType::findOrFail($id);
    }

    public function create(UserTypeDto $dto): UserType
    {
        return DB::transaction(function () use ($dto) {
            return UserType::create($dto->toArray());
        });
    }

    public function update(string $id, UserTypeDto $dto): UserType
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }

        /**
     * Eliminar un tipo de usuario por su ID.
     */
    public function delete(int $id): bool
    {
        $userType = UserType::findOrFail($id);
        return $userType->delete();
    }
}
