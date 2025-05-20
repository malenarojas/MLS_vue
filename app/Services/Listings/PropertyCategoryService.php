<?php

namespace App\Services\Listings;

use App\Dtos\Listings\PropertyCategoryDto;
use App\Models\PropertyCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PropertyCategoryService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return PropertyCategory::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return PropertyCategory::all();
    }

    public function findById(string $id): PropertyCategory
    {
        return PropertyCategory::findOrFail($id);
    }

    public function create(PropertyCategoryDto $dto): PropertyCategory
    {
        return DB::transaction(function () use ($dto) {
            return PropertyCategory::create($dto->toArray());
        });
    }

    public function update(string $id, PropertyCategoryDto $dto): PropertyCategory
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
