<?php

namespace App\Services\Agents;

use App\Dtos\Agents\SocialNetworkDto;
use App\Models\SocialNetwork;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SocialNetworkService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return SocialNetwork::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return SocialNetwork::all();
    }

    public function findById(string $id): SocialNetwork
    {
        return SocialNetwork::findOrFail($id);
    }

    public function create(SocialNetworkDto $dto): SocialNetwork
    {
        return DB::transaction(function () use ($dto) {
            return SocialNetwork::create($dto->toArray());
        });
    }

    public function update(string $id, SocialNetworkDto $dto): SocialNetwork
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
    public function getAllByAgentId(int $agentId)
    {
        return SocialNetwork::where('agent_id', $agentId)->get();
    }
}
