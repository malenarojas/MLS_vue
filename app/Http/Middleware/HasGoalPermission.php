<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasGoalPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('login_as')) {
            $user = User::with('agent')->with(['agent.office'])->find(session('login_as'));
        } else {
            if (auth() && auth()->user()) {
                $user = User::find(auth()->user()->id);
            } else {
                $user = null;
            }
        }

        if($user && $user->hasPermissionTo('create goals')) {
            return $next($request);
        }
        
        return response('Not found.', Response::HTTP_NOT_FOUND);
    }
}
