<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
use App\Models\Collaborator;

class SystemAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            //Autorização de token JWT
            return app(\Tymon\JWTAuth\Http\Middleware\Authenticate::class)->handle($request, function ($request) use ($next) {

                // $colaborador = Collaborator::where('objectguid',Auth::user()->getConvertedGuid())->first();
                // $log = new Log();
                // dd($request);
                // $log->id_collaborator = $colaborador->id_collaborator;
                // $log->origin_ip = $request->ip();
                // $log->action = $request->method();

                return $next($request);
            });
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()],401);
        }
    }
}
