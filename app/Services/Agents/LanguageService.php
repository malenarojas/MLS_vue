<?php

namespace App\Services\Agents;

use App\Dtos\Agents\LanguageDto;
use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LanguageService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Language::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Language::all();
    }

    public function findById(string $id): Language
    {
        return Language::findOrFail($id);
    }

    public function create(LanguageDto $dto): Language
    {
        return DB::transaction(function () use ($dto) {
            return Language::create($dto->toArray());
        });
    }

    public function update(string $id, LanguageDto $dto): Language
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
        /**
     * Elimina un idioma por su ID.
     */
    public function delete(int $id): bool
    {
        $language = $this->findById($id);
        return $language->delete();
    }

}
