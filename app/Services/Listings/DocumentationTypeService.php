<?php

namespace App\Services\Listings;

use App\Dtos\Listings\DocumentationTypeDto;
use App\Models\DocumentationType;
use Illuminate\Database\Eloquent\Collection;

class DocumentationTypeService
{
    const ID_PUBLIC_DOCUMENTATION = 1;
    const ID_PRIVATE_DOCUMENTATION = 32;

    /**
     * @return Collection Retorna todos los tipos de documentación
     */
    public function getAll(): Collection
    {
        return DocumentationType::all();
    }

    public function getAllPrivate(): Collection
    {
        return DocumentationType::where('parent_id', self::ID_PRIVATE_DOCUMENTATION)->get();
    }

    public function getAllPublic(): Collection
    {
        return DocumentationType::where('parent_id', self::ID_PUBLIC_DOCUMENTATION)->get();
    }

    /**
     * @return Collection Retorna todos los tipos de documentación con sus hijos
     */
    public function getAllWithChildren(): Collection
    {
        return DocumentationType::with('documentacions_types_hijos')->whereNull('parent_id')->get();
    }

    /**
     * @param int $id
     * @return DocumentationType Retorna un tipo de documentación por su id
     */
    public function findById(int $id): DocumentationType
    {
        return DocumentationType::findOrFail($id);
    }

    /**
     * @param DocumentationTypeDto $dto
     * @return DocumentationType Retorna el tipo de documentación creado
     */
    public function create(DocumentationTypeDto $dto): DocumentationType
    {
        $documentationType = DocumentationType::create($dto->toArray());
        return $documentationType;
    }

    /**
     * @param int $id
     * @param DocumentationTpeDto $dto
     * @return DocumentationType Retorna el tipo de documentación actualizado
     */
    public function update(int $id, DocumentationTypeDto $dto): DocumentationType
    {
        $documentationType = $this->findById($id);
        $documentationType->fill($dto->toArray());
        $documentationType->save();
        return $documentationType;
    }
}
