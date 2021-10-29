<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Hash;

class CheckPasswordRefresh
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if(Hash::check($user->dni, $user->getAuthPassword())) {
            return redirect('/change_password')->withErrors('Es obligatorio que cambie su contraseña por una diferente a su DNI');
        }
        return $next($request);
    }
}
