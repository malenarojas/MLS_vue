<?php

namespace App\Services\Agents;

use App\Dtos\Agents\CustomerPreferenceDto;
use App\Models\CustomerPreference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CustomerPreferenceService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return CustomerPreference::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return CustomerPreference::all();
    }

    public function findById(string $id): CustomerPreference
    {
        return CustomerPreference::findOrFail($id);
    }

    public function create(CustomerPreferenceDto $dto): CustomerPreference
    {
        return DB::transaction(function () use ($dto) {
            return CustomerPreference::create($dto->toArray());
        });
    }

    public function update(string $id, CustomerPreferenceDto $dto): CustomerPreference
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }

      /**
     * Eliminar una preferencia por ID.
     */
    public function delete(int $id): bool
    {
        $preference = CustomerPreference::findOrFail($id);
        return $preference->delete();
    }
}
