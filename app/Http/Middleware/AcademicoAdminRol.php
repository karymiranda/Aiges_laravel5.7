<?php

namespace App\Http\Middleware;

use Closure;
use App\RolesSesion;//modelo que contiene los roles que tiene asignados el usuario logeado

class AcademicoAdminRol
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
        $rols=RolesSesion::sesionRoles();
        if($rols[3]==false && $rols[0]==false)//si es admin academico y superusuario
        {
            return redirect()->route('restringido');
            // return redirect()->route('menu');
        }
        return $next($request);
    }
}
