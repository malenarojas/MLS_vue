<?php

namespace App\Services\Listings;

use App\Dtos\Listings\MarketStatusDto;
use App\Models\MarketStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MarketStatusService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return MarketStatus::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return MarketStatus::all();
    }

    public function findById(string $id): MarketStatus
    {
        return MarketStatus::findOrFail($id);
    }

    public function create(MarketStatusDto $dto): MarketStatus
    {
        return DB::transaction(function () use ($dto) {
            return MarketStatus::create($dto->toArray());
        });
    }

    public function update(string $id, MarketStatusDto $dto): MarketStatus
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
