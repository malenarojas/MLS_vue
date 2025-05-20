<?php

namespace App\Services\Agents;

use App\Dtos\Agents\AreaDto;
use App\Models\Area;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AreaService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Area::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Area::all();
    }

    public function getAllBase(): Collection
    {
        return Area::select('id', 'name')->where('is_base', true)->get();
    }

    public function findById(string $id): Area
    {
        return Area::findOrFail($id);
    }

    public function create(AreaDto $dto): Area
    {
        return DB::transaction(function () use ($dto) {
            return Area::create($dto->toArray());
        });
    }

    public function update(string $id, AreaDto $dto): Area
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
        $area = Area::findOrFail($id);
        return $area->delete();
    }

    public function getAreasWithSpecialities()
    {
        return Area::with(['specialities' => function ($query) {
            $query->select('specialities.id', 'specialities.name', 'specialities.parent_id')
                ->with('children'); // Cargar subespecialidades (hijas)
        }])->get();
    }
}
