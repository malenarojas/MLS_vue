<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class RoleController extends Controller
{

    public function index()
    {
        //$this->authorize('list roles');
        $roles = Role::all()->except(1);
        return Inertia::render('Role/Index', [
            'roles' => $roles
        ]);
    }

    public function edit($id)
    {
        if ($id == 1) {
            return redirect('roles');
        }

        $permissions = Permission::all();
        $roles = Role::find($id);

        /*  $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id')
            ->all();*/
        $rolePermissions = DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $id)
            ->pluck('permissions.name');
        //dd($permissions);
        $lista_permisos = [];
        foreach ($permissions as $key => $permiso) {
            array_push($lista_permisos, [
                "id" => $permiso->name,
                "name" => $permiso->description,
            ]);
        }
        return Inertia::render('Role/Edit', [
            'role' => $roles,
            'rolePermissions' => $rolePermissions,
            'permissions' => $lista_permisos,
        ]);
    }

    public function update(Request $request, $id)
    {
        $rol = Role::find($id);
        $rol->syncPermissions($request->input('permisos'));
    }
}
