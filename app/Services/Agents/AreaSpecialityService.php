<?php

namespace App\Services\Agents;

use App\Dtos\Agents\AreaSpecialityDto;
use App\Models\AreaSpeciality;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AreaSpecialityService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return AreaSpeciality::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return AreaSpeciality::all();
    }

    public function findById(string $id): AreaSpeciality
    {
        return AreaSpeciality::findOrFail($id);
    }

    public function create(AreaSpecialityDto $dto): AreaSpeciality
    {
        return DB::transaction(function () use ($dto) {
            return AreaSpeciality::create($dto->toArray());
        });
    }

    public function update(string $id, AreaSpecialityDto $dto): AreaSpeciality
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }

        // Eliminar una relación específica entre área y especialidad
        public function delete(int $id): bool
        {
            $areaSpeciality = AreaSpeciality::findOrFail($id);
            return $areaSpeciality->delete();
        }
}
