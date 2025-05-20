<?php

namespace App\Services\Location;

use App\Dtos\Location\ZoneDto;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ZoneService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Zone::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Zone::all();
    }

    public function findById(string $id): Zone
    {
        return Zone::findOrFail($id);
    }

    public function create(ZoneDto $dto): Zone
    {
        return DB::transaction(function () use ($dto) {
            return Zone::create($dto->toArray());
        });
    }

    public function update(string $id, ZoneDto $dto): Zone
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
