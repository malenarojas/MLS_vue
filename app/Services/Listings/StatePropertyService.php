<?php

namespace App\Services\Listings;

use App\Dtos\Listings\StatePropertyDto;
use App\Models\StateProperty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StatePropertyService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return StateProperty::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return StateProperty::all();
    }

    public function findById(string $id): StateProperty
    {
        return StateProperty::findOrFail($id);
    }

    public function create(StatePropertyDto $dto): StateProperty
    {
        return DB::transaction(function () use ($dto) {
            return StateProperty::create($dto->toArray());
        });
    }

    public function update(string $id, StatePropertyDto $dto): StateProperty
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
