<?php

namespace App\Services\Listings;

use App\Dtos\Listings\ListingInformationDto;
use App\Models\ListingInformation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListingInformationService
{
    public function __construct(
        private RoomService $roomService
    ) {}

    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return ListingInformation::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return ListingInformation::all();
    }

    public function findById(string $id): ListingInformation
    {
        return ListingInformation::findOrFail($id);
    }

    public function findByListingId(?string $listingId): ?ListingInformation
    {
        return ListingInformation::where('listing_id', $listingId)->first();
    }

    public function createOrUpdate(array $data): bool
    {
        $id = $data['id'] ?? $this->findByListingId($data['listing_id'])?->id ?? null;
        if ($id) {
            return $this->update($id, $data);
        }

        return $this->create($data);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $model = new ListingInformation();
            $model->fill($data);
            return $model->save();
        });
    }

    public function update(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            Log::info('Updating listing information', ['id' => $id]);
            $model = $this->findById($id);
            $model->fill($data);

            $rooms = $data['rooms'] ?? [];
            if (!empty($rooms)) {
                $this->roomService->saveAll($rooms, $model->id);
            }

            return $model->save();
        });
    }
}
