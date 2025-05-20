<?php

namespace App\Services\Listings;

use App\Dtos\Listings\SubtypePropertyDto;
use App\Models\SubtypeProperty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SubtypePropertyService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return SubtypeProperty::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return SubtypeProperty::all();
    }

    public function getAllByid(string $id): Collection
    {
        return SubtypeProperty::where('id', $id)->get();
    }

    public function findById(string $id): SubtypeProperty
    {
        return SubtypeProperty::findOrFail($id);
    }

    public function create(SubtypePropertyDto $dto): SubtypeProperty
    {
        return DB::transaction(function () use ($dto) {
            return SubtypeProperty::create($dto->toArray());
        });
    }

    public function update(string $id, SubtypePropertyDto $dto): SubtypeProperty
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
