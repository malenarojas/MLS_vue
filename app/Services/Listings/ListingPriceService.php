<?php

namespace App\Services\Listings;

use App\Dtos\Listings\ListingPriceDto;
use App\Models\ListingPrice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ListingPriceService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return ListingPrice::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return ListingPrice::with(['listing', 'currency'])
            ->get()
            ->map(function ($listingPrice) {
                return ListingPriceDto::fromModel($listingPrice);
            });
    }

    public function findById(string $id): ListingPrice
    {
        return ListingPrice::findOrFail($id);
    }

    public function create(ListingPriceDto $dto): ListingPrice
    {
        return DB::transaction(function () use ($dto) {
            return ListingPrice::create($dto->toArray());
        });
    }

    public function update(string $id, ListingPriceDto $dto): ListingPrice
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
