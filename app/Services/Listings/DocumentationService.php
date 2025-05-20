<?php

namespace App\Services\Listings;

use App\Dtos\Listings\DocumentationDto;
use App\Models\Documentation;
use App\Models\DocumentationType;
use App\Models\Listing;
use App\Services\ImageService;
use Dom\Document;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DocumentationService
{
    public function __construct(
        private ImageService $imageService
    ) {}

    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Documentation::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Documentation::all();
    }

    public function findById(string $id): Documentation
    {
        return Documentation::findOrFail($id);
    }

    /**
     * @param DocumentationDto[] $dto
     */
    public function create(array $dto = [], Listing $listing, bool $isPrivate = true)
    {
        $newIdDocumentations = [];

        foreach ($dto as $document) {
            if (isset($document['is_new'])) {
                $link = $this->imageService->uploadImageFromFile($document['file'], "listings/{$listing->key}/documentations");
                $document['link'] = $link;
                $document['original_name'] = $document['file']->getClientOriginalName();

                // Create new document and attach it
                $newDocument = $listing->documentation()->create((array) $document);
                $newIdDocumentations[] = $newDocument->id;
            } else {
                Documentation::where('id', $document['id'])->update([
                    'description' => $document['description'],
                    'documentation_type_id' => $document['documentation_type_id'],
                    'updated_at' => now(),
                ]);
                $newIdDocumentations[] = $document['id'];
            }
        }

        $documentation_type_id = $isPrivate ? DocumentationType::ID_PRIVATE_DOCUMENTATION : DocumentationType::ID_PUBLIC_DOCUMENTATION;

        $deletedDocuments = $listing->documentation()
            ->whereHas('documentation_type', function ($query) use ($documentation_type_id) {
                $query->where('parent_id', $documentation_type_id);
            })
            ->whereNotIn('documentations.id', $newIdDocumentations)
            ->get(['documentations.id', 'documentations.link']);

        foreach ($deletedDocuments as $deletedDocument) {
            $this->imageService->deleteImage($deletedDocument->link);
        }

        $listing->documentation()
            ->whereHas('documentation_type', function ($query) use ($documentation_type_id) {
                $query->where('parent_id', $documentation_type_id);
            })
            ->whereNotIn('documentations.id', $newIdDocumentations)
            ->delete();
    }

    public function update(string $id, DocumentationDto $dto): Documentation
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
}
