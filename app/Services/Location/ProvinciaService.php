<?php

namespace App\Services\Location;

use App\Dtos\Location\ProvinciaDto;
use App\Models\Province;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProvinciaService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Province::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Province::all();
    }

    public function findById(string $id): Province
    {
        return Province::findOrFail($id);
    }

    public function create(ProvinciaDto $dto): Province
    {
        return DB::transaction(function () use ($dto) {
            return Province::create($dto->toArray());
        });
    }

    public function update(string $id, ProvinciaDto $dto): Province
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
