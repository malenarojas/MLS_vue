<?php

namespace App\Services\Agents;

use App\Dtos\Agents\AchievementUserDto;
use App\Models\AchievementUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AchievementUserService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return AchievementUser::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return AchievementUser::all();
    }

    public function findById(string $id): AchievementUser
    {
        return AchievementUser::findOrFail($id);
    }

    public function create(AchievementUserDto $dto): AchievementUser
    {
        return DB::transaction(function () use ($dto) {
            return AchievementUser::create($dto->toArray());
        });
    }

    public function update(string $id, AchievementUserDto $dto): AchievementUser
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
	public function getAllByAgentId(int $agentId): Collection
    {
        return AchievementUser::where('user_id', function ($query) use ($agentId) {
            $query->select('user_id')
                  ->from('agents')
                  ->where('id', $agentId)
                  ->limit(1);
        })->get();
    }
}

