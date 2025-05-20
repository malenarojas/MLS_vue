<?php

namespace App\Services\Agents;

use App\Dtos\Agents\SpecialityDto;
use App\Models\Speciality;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SpecialityService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Speciality::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Speciality::all();
    }

    public function findById(string $id): Speciality
    {
        return Speciality::findOrFail($id);
    }

    public function create(SpecialityDto $dto): Speciality
    {
        return DB::transaction(function () use ($dto) {
            return Speciality::create($dto->toArray());
        });
    }

    public function update(string $id, SpecialityDto $dto): Speciality
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }

       /**
     * Eliminar una especialidad por su ID.
     */
    public function delete(int $id): bool
    {
        $speciality = Speciality::findOrFail($id);
        return $speciality->delete();
    }
}
