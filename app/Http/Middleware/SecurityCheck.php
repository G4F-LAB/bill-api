<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityCheck
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
        foreach($request->all() as $index => $requestItem) {

            //checagem de scripts maliciosos para os inputs
            if(is_string($requestItem)) {
                if(!$this->purifierPreCheck($requestItem)) return response()->json(['error'=>'Falha ao enviar dados'],401); //XSS detected
            }
        }
        return $next($request);
    }

    /**
     * This method makes sure no dangerous markup can be smuggled in
     * attributes when HTML mode is switched on.
     *
     * If the precheck considers the string too dangerous for
     * purification false is being returned.
     *
     * @param string $key
     * @param string $value
     * @since  0.6
     *
     * @return boolean
     */
    private function purifierPreCheck($key = '', $value = '')
    {
        /*
         * Remove control chars before pre-check
         */
        $tmpValue = preg_replace('/\p{C}/', null, $value);
        $tmpKey = preg_replace('/\p{C}/', null, $key);

        //echo $tmpKey;

        $preCheck = '/<(\/?(?:script|iframe|applet|object|button|img|form|input)|.*?(?:on\w+\s*=|script:))[^>]*>|<\?php.*?\?>/i';
        return !(preg_match($preCheck, $tmpKey) || preg_match($preCheck, $tmpValue));
    }
}
