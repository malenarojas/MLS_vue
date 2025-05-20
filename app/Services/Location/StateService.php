<?php

namespace App\Services\Location;

use App\Dtos\Location\StateDto;
use App\Models\State;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StateService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return State::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return State::all();
    }

    public function findById(string $id): State
    {
        return State::findOrFail($id);
    }

    public function create(StateDto $dto): State
    {
        return DB::transaction(function () use ($dto) {
            return State::create($dto->toArray());
        });
    }

    public function update(string $id, StateDto $dto): State
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
