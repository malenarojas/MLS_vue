<?php

namespace App\Services\Listings;

use App\Dtos\Listings\ContractTypeDto;
use App\Models\ContractType;
use Illuminate\Database\Eloquent\Collection;

class ContractTypeService
{
    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return ContractType::all();
    }

    /**
     * @param string $id ContractType id
     * @return ContractType
     */
    public function findById(string $id): ContractType
    {
        $contractType = ContractType::findOrFail($id);
        return $contractType;
    }

    /**
     * @param ContractTypeDto $contractTypeDto
     * @return ContractType
     */
    public function create(ContractTypeDto $contractTypeDto): ContractType
    {
        $contractType = ContractType::create($contractTypeDto->toArray());
        return $contractType;
    }

    /**
     * @param string $id
     * @param ContractTypeDto $contractTypeDto
     * @return ContractType
     */
    public function update(string $id, ContractTypeDto $contractTypeDto): ContractType
    {
        $contractType = $this->findById($id);
        $contractType->fill($contractTypeDto->toArray());
        return $contractType;
    }
}
