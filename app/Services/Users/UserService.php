<?php

namespace App\Services\Users;

use App\Dtos\Agents\UserDto;
use App\Models\User;
use App\Services\Agents\AchievementService;
use App\Services\Agents\AreaService;
use App\Services\Agents\AreaSpecialityService;
use App\Services\Agents\CustomerPreferenceService;
use App\Services\Agents\RemaxTitleService;
use App\Services\Agents\RemaxtitletoshowService;
use App\Services\Agents\SpecialityService;
use App\Services\Agents\TeamStatusService;
use App\Services\Agents\UserTypeService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    // laravel > node

    public function __construct(
        private AchievementService $AchievementService,
        private AreaService $AreaService,
        private AreaSpecialityService $AreaSpecialityService,
        private CustomerPreferenceService $CustomerPreferenceService,
        private RemaxTitleService $RemaxTitleService,
        private RemaxtitletoshowService $RemaxtitletoshowService,
        private SpecialityService $specialityService,
        private TeamStatusService $teamStatusService,
        private UserTypeService $userTypeService,

    ) {}

    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return User::paginate($perPage);
    }

    public function getAll(): Collection
    {

        $user=User::with(
            'user_type',
            'remax_title',
            'remax_title_to_show',
            'team_status',
            'customer_preference',
            'area_specialities',
            'achievements',
            'area_services',
            'areaSpecialityUsers'
            )->get();

        return $user;
    }

    public function findById(string $id): User
    {
        return User::with([
            'user_type',
            'remax_title',
            'remax_title_to_show',
            'team_status',
            'customer_preference',
            'area_specialities',
            'achievements',
            'area_services',
        ])->findOrFail($id);
    }
    public function getFormOptions(): array
    {
        return [
            'user_types' => $this->userTypeService->getAll(), // Trae todos los tipos de usuario
            'remax_titles' => $this->RemaxTitleService->getAll(), // Trae todos los títulos de Remax
            'remax_titles_to_show' => $this->RemaxtitletoshowService->getAll(), // Trae los títulos a mostrar
            'team_statuses' => $this->teamStatusService->getAll(), // Trae los estados de equipo
        ];
    }


    public function create(UserDto $dto): User
    {
        return DB::transaction(function () use ($dto) {
            // Crear el usuario principal
            $user = User::create($dto->toArray());

            // Relacionar tablas pivot (si los datos existen en el DTO)
            if (!empty($dto->area_specialities)) {
                $user->area_specialities()->sync($dto->area_specialities);
            }

            if (!empty($dto->achievements)) {
                $user->achievements()->sync($dto->achievements);
            }

            if (!empty($dto->area_services)) {
                $user->area_services()->sync($dto->area_services);
            }

            // Obtener las opciones relacionadas para los selects
            $options = $this->getFormOptions();

            // Retornar el usuario creado junto con las opciones
            return [
                'user' => $user,
                'options' => $options,
            ];
        });
    }

    public function update(string $id, UserDto $dto): User
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }
    public function delete(int $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}
