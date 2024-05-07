<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Permission;
use Spatie\Activitylog\Facades\CauserResolver;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $premissas = ['ALL', 'EXCLUDE'];
        // $validPermissions = $permissions;
        // $permissions = array_unique(array_merge($permissions, $premissas));
        $permissions = array_map('trim', $permissions);

        // if (in_array($premissas[0], $permissions)) {
        //     //Recupera todas as permissoes em array
        //     $validPermissions = Permission::pluck('name')->toArray();
        // } else if (in_array($premissas[1], $permissions)) {
        //     //Retira do array a premissa EXCLUDE para recuperar as permissões que não sejam as definidas nesse array
        //     unset($permissions[array_search($premissas[1], $permissions)]);
        //     $validPermissions = Permission::whereNotIn('name', $permissions)->pluck('name')->toArray();
        // }

        $user = User::where('taxvat', Auth::user()['employeeid'])->first();

        CauserResolver::setCauser($user);

        if (in_array($user->type, $permissions) || count(array_intersect($permissions, $premissas)) > 0) {
            return $next($request);
        }

        return response()->json(['status' => 'error', 'message' => 'Acesso não permitido'], 403);
    }
}
