<?php

namespace App\Services\Agents;

use App\Dtos\Location\CityDto;
use App\Models\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CityService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return City::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return City::all();
    }

    public function findById(string $id): City
    {
        return City::findOrFail($id);
    }

    public function create(CityDto $dto): City
    {
        return DB::transaction(function () use ($dto) {
            return City::create($dto->toArray());
        });
    }

    public function update(string $id, CityDto $dto): City
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}