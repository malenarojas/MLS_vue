<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Region;
class RegionController extends Controller
{
    /**
     * Listar todas las regiones.
     *
     * @group Regions
     * @authenticated
     *
     * @response 200 [
     *     {
     *         "id": 1,
     *         "name": "Region 1",
     *         "created_at": "2023-11-20T12:00:00.000000Z",
     *         "updated_at": "2023-11-20T12:00:00.000000Z"
     *     },
     *     {
     *         "id": 2,
     *         "name": "Region 2",
     *         "created_at": "2023-11-20T12:00:00.000000Z",
     *         "updated_at": "2023-11-20T12:00:00.000000Z"
     *     }
     * ]
     */
    public function index()
    {
        return Region::all();
    }
     /**
     * Crear una nueva región.
     *
     * @group Regions
     * @authenticated
     *
     * @bodyParam name string required Nombre de la región. Example: Region 1
     *
     * @response 201 {
     *     "id": 1,
     *     "name": "Region 1",
     *     "created_at": "2023-11-20T12:00:00.000000Z",
     *     "updated_at": "2023-11-20T12:00:00.000000Z"
     * }
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        $region = Region::create(['name' => $request->name]);
        return response()->json($region, 200);
    }
    /**
     * Mostrar una región específica.
     *
     * @group Regions
     * @authenticated
     *
     * @urlParam id integer required El ID de la región. Example: 1
     *
     * @response 200 {
     *     "id": 1,
     *     "name": "Region 1",
     *     "created_at": "2023-11-20T12:00:00.000000Z",
     *     "updated_at": "2023-11-20T12:00:00.000000Z"
     * }
     */

    public function show(Region $region)
    {
        return response()->json($region);
    }


    /**
     * Actualizar una región específica.
     *
     * @group Regions
     * @authenticated
     *
     * @urlParam id integer required El ID de la región. Example: 1
     * @bodyParam name string required Nombre actualizado de la región. Example: Updated Region
     *
     * @response 200 {
     *     "id": 1,
     *     "name": "Updated Region",
     *     "created_at": "2023-11-20T12:00:00.000000Z",
     *     "updated_at": "2023-11-20T12:00:00.000000Z"
     * }
     */
    public function update(Request $request, Region $region)
    {
        $request->validate(['name' => 'required']);
        $region->update(['name' => $request->name]);
        return response()->json([
            'message' => 'Región actualizada correctamente',
            'data' => $region,
        ], 200);
    }

    /**
     * Eliminar una región específica.
     *
     * @group Regions
     * @authenticated
     *
     * @urlParam id integer required El ID de la región. Example: 1
     *
     * @response 204 {}
     */

    public function destroy(Region $region)
    {


        $region->delete();
          // Devolver respuesta de éxito
        return response()->json([
        'message' => 'Región eliminada correctamente'
        ], 200);
    }
}

