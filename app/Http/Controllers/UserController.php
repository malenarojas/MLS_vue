<?php

namespace App\Http\Controllers;


use App\Dtos\Agents\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\UserController\UpdateUserRequest;
use App\Http\Requests\Api\v1\UserController\StoreUserRequest;
use App\Models\User;
use App\Services\Users\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService= $userService;
    }
    /**
     * Lista todos los usuarios con soporte para búsqueda y paginación.
     *
     * @group Users
     * @authenticated
     * @queryParam search string Filtrar por nombre, correo o username. Example: John
     * @queryParam per_page int Número de usuarios por página. Example: 10
     * @queryParam page int Página actual. Example: 1
     * @response {
     *  "data": [
     *      {
     *          "id": 1,
     *          "first_name": "John",
     *          "email": "john.doe@example.com"
     *      }
     *  ],
     *  "meta": {
     *      "current_page": 1,
     *      "total": 20
     *  }
     * }
     */
    public function index():JsonResponse
    {
        $users = $this->userService->getAll();
        return response()->json($users);
    }

     /**
     * Crea un nuevo usuario.
     *
     * @group Users
     * @authenticated
     * @bodyParam first_name string Nombre del usuario. Example: John
     * @bodyParam middle_name string Segundo nombre del usuario. Example: A.
     * @bodyParam last_name string Apellido del usuario. Example: Doe
     * @bodyParam name_to_show string Nombre público del usuario. Example: J. Doe
     * @bodyParam ci string Documento de identidad. Example: 12345678
     * @bodyParam gender string Género. Example: Male
     * @bodyParam phone_number string Número de teléfono. Example: 123456789
     * @bodyParam email string required Correo electrónico. Example: john.doe@example.com
     * @bodyParam url string URL personal del usuario. Example: https://example.com
     * @bodyParam remax_start_date date Fecha de inicio en RE/MAX. Example: 2023-01-01
     * @bodyParam password string Contraseña del usuario. Example: password123
     * @bodyParam username string required Nombre de usuario único. Example: johndoe
     * @response 201 {
     *  "message": "Usuario creado correctamente",
     *  "data": {
     *      "id": 1,
     *      "first_name": "John",
     *      "email": "john.doe@example.com"
     *  }
     * }
     */
    public function store(StoreUserRequest $request):JsonResponse
    {


        $dto = UserDto::from($request->validated());
        $user= $this->userService->create($dto);

        return response()->json($user, 201);
    }

     /**
     * Muestra un usuario específico.
     *
     * @group Users
     * @authenticated
     * @urlParam id int required El ID del usuario. Example: 1
     * @response {
     *  "id": 1,
     *  "first_name": "John",
     *  "email": "john.doe@example.com"
     * }
     */
    public function show(string $id):JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json($user, 200);
    }

    /**
     * Actualiza un usuario existente.
     *
     * @group Users
     * @authenticated
     * @urlParam id int required El ID del usuario. Example: 1
     * @bodyParam first_name string Nombre del usuario. Example: John
     * @bodyParam email string required Correo electrónico. Example: john.doe@example.com
     * @response 200 {
     *  "message": "Usuario actualizado correctamente",
     *  "data": {
     *      "id": 1,
     *      "first_name": "John",
     *      "email": "john.doe@example.com"
     *  }
     * }
     */
    public function update(UpdateUserRequest $request, string $id):JsonResponse
    {

        // Crear el DTO a partir de los datos validados
        $dto = UserDto::from($request->validated());

        if (isset($dto->password)) {
            $dto->password = bcrypt($dto->password);
        }

        // Pasar el DTO al servicio para actualizar el registro
        $user = $this->userService->update($id, $dto);
            // Si se envía una nueva contraseña, encriptarla antes de guardarla


        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $user
        ], 200);
    }

    /**
     * Elimina un usuario específico.
     *
     * @group Users
     * @authenticated
     * @urlParam id int required El ID del usuario. Example: 1
     * @response 204 {}
     */
    public function destroy(string $id):JsonResponse
    {
        $this->userService->delete($id);
        return response()->json(null, 204);
    }
}
