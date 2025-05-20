<?php

namespace App\Services\Listings;

use App\Dtos\Listings\StatusListingDto;
use App\Models\StatusListing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StatusListingService
{
    const DRAFT_STATUS_ID = 1;

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return StatusListing::select('id', 'name')->get();
    }

    public function getFinalStatus(): Collection
    {
        return StatusListing::where('is_final', true)->get();
    }

    /**
     * Retonar todos los estados con sus transiciones a las que puede llegar
     */
    public function getAllWithTransitions(): Collection
    {
        return StatusListing::with('transitions_from')->get();
    }

    /**
     * @param string $id StatusListing id
     * @return StatusListing
     */
    public function findById(string $id)
    {
        try {
            $statusListing = StatusListing::select('id', 'name', 'is_final')
                ->with(['transitions_from:id,name,is_final'])
                ->findOrFail($id);

            return $statusListing;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("StatusListing con ID {$id} no fue encontrado.");
        } catch (\Exception $e) {
            throw new \Exception("Error inesperado al buscar el StatusListing con ID {$id}: " . $e->getMessage());
        }
    }

    /**
     * @param StatusListingDto $statusListingDto
     * @return StatusListing
     */
    public function create(StatusListingDto $statusListingDto)
    {
        $statusListing = StatusListing::create($statusListingDto->toArray());
        return $statusListing;
    }

    /**
     * @param string $id StatusListing id
     * @param StatusListingDto $statusListingDto
     * @return StatusListing
     */
    public function update(string $id, StatusListingDto $statusListingDto)
    {
        $statusListing = $this->findById($id);
        $statusListing->fill($statusListingDto->toArray());
        $statusListing->save();
        return $statusListing;
    }

    public function getDraftStatu(): StatusListing
    {
        return StatusListing::findOrfail(self::DRAFT_STATUS_ID);
    }

    public function getDraftStatuId(): int
    {
        return self::DRAFT_STATUS_ID;
    }
}
