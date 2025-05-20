<?php

namespace App\Services\Listings;

use App\Dtos\Listings\ListingTransactionTypeDto;
use App\Models\ListingTransactionType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ListingTransactionTypeService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return ListingTransactionType::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return ListingTransactionType::select('id', 'name', 'status')->where('status', 1)->get();
    }

    public function findById(string $id): ListingTransactionType
    {
        return ListingTransactionType::findOrFail($id);
    }

    public function create(ListingTransactionTypeDto $dto): ListingTransactionType
    {
        return DB::transaction(function () use ($dto) {
            return ListingTransactionType::create($dto->toArray());
        });
    }

    public function update(string $id, ListingTransactionTypeDto $dto): ListingTransactionType
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
