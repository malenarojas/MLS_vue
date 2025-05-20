<?php

namespace App\Services\Agents;

use App\Dtos\RegionDto;
use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RegionService
{
    // laravel > node

    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Region::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Region::select('id', 'name')->get();
    }

    public function findById(string $id): Region
    {
        return Region::findOrFail($id);
    }
    public function getRegionsWithOfficesAndTeamStatuses()
    {
    return Region::with(['offices'])->get();
    }


    public function create(RegionDto $dto): Region
    {
        return DB::transaction(function () use ($dto) {
            return Region::create($dto->toArray());
        });
    }

    public function update(string $id, RegionDto $dto): Region
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
        $region = Region::findOrFail($id);
        return $region->delete();
    }
}
