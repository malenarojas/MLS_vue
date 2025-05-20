<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\MenuItemService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $menuItemService = new MenuItemService();
        //dd(Session::all());
        if (session()->has('login_as')) {
            $user = User::with('agent')->with(['agent.office'])->find(session('login_as'));
        } else {
            if (auth() && auth()->user()) {
                $user = User::with('agent')->with(['agent.office'])->find(auth()->user()->id);
            } else {
                $user = null;
                session()->forget('login_as');
                session()->forget('original_user');
                session()->save();
            }
        }

        // dd($user);

        $role = $user ? $user->roles()->first()?->name : '';
        // Permisos directos
        // $permissions = $user ? $user->getDirectPermissions()->pluck('name') : [];
        $permissions = $user ? $user->getAllPermissions()->pluck('name') : [];

        // dd($permissions, $role);

        return array_merge(parent::share($request), [
            'menus' => $user ? $menuItemService->getMenuItems() : [],
            'user' => $user ?? null,
            'role' => $role ?? null,
            'permissions' => $permissions ?? [],
            'is_logued_as' => session()->has('login_as'),
        ]);
    }
}
