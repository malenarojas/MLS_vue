<?php

namespace App\Services\Listings;

use App\Dtos\Listings\FeactureDto;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FeatureService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Feature::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Feature::select('id', 'name')
            ->whereNull('feature_id')
            ->with('children:id,name,feature_id')
            ->get();
    }

    public function findById(string $id): Feature
    {
        return Feature::findOrFail($id);
    }

    public function create(FeactureDto $dto): Feature
    {
        return DB::transaction(function () use ($dto) {
            return Feature::create($dto->toArray());
        });
    }

    public function update(string $id, FeactureDto $dto): Feature
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
