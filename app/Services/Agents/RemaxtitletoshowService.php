<?php

namespace App\Services\Agents;

use App\Dtos\Agents\RemaxtitletoshowDto;
use App\Models\RemaxTitleToShow;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RemaxtitletoshowService
{

    public function getAll(): Collection
    {
        return RemaxTitleToShow::all();
    }

    public function findById(string $id): RemaxTitleToShow
    {
        return RemaxTitleToShow::findOrFail($id);
    }

    public function create(RemaxtitletoshowDto $dto): RemaxTitleToShow
    {
        return DB::transaction(function () use ($dto) {
            return RemaxTitleToShow::create($dto->toArray());
        });
    }

    public function update(string $id, RemaxtitletoshowDto $dto): RemaxTitleToShow
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
        $remaxTitleToShow = RemaxTitleToShow::findOrFail($id);
        return $remaxTitleToShow->delete();
    }
}
