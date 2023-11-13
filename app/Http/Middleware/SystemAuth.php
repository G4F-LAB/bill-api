<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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

                

                return $next($request);
            });
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()],401);
        }
    }
}
