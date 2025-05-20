<?php

namespace App\Services\Listings;

use App\Dtos\Listings\CommissionOptionDto;
use App\Models\CommissionOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CommissionOptionService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return CommissionOption::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return CommissionOption::all();
    }

    public function findById(string|CommissionOption  $modelOrId): CommissionOption
    {
        if ($modelOrId instanceof CommissionOption) {
            return $modelOrId;
        }

        return CommissionOption::findOrFail($modelOrId);
    }

    public function findByListingId(?string $listingId): ?CommissionOption
    {
        return CommissionOption::where('listing_id', $listingId)->first();
    }

    public function createOrUpdate(array $data): bool
    {
        $listingId = $data['listing_id'];
        $id = $this->findByListingId($listingId)?->id;

        if ($id) {
            return $this->update($id, $data);
        }

        return $this->create($data);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $model = new CommissionOption();
            $model->fill($data);
            return $model->save();
        });
    }

    public function update(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $model = $this->findById($id);
            $model->fill($data);
            return $model->save();
        });
    }
}