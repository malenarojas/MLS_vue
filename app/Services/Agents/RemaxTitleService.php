<?php

namespace App\Services\Agents;

use App\Dtos\Agents\RemaxTitleDto;
use App\Models\RemaxTitle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RemaxTitleService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return RemaxTitle::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return RemaxTitle::all();
    }

    public function findById(string $id): RemaxTitle
    {
        return RemaxTitle::findOrFail($id);
    }

    public function create(RemaxTitleDto $dto): RemaxTitle
    {
        return DB::transaction(function () use ($dto) {
            return RemaxTitle::create($dto->toArray());
        });
    }

    public function update(string $id, RemaxTitleDto $dto): RemaxTitle
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
      /**
     * Eliminar un título por ID.
     */
    public function delete(int $id): bool
    {
        $remaxTitle = RemaxTitle::findOrFail($id);
        return $remaxTitle->delete();
    }
}
