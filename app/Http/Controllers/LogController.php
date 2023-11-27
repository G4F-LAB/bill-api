<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
class LogController extends Controller
{

    public function show()
    {
        try{
            $logs = Log::all();
            return response()->json($logs, 200);
    
        }catch(\Exception $e){
            return response()->json(['error'=>'Não foi possivel acessar os Logs'],500);
        }  
    }

    public function getLogName(Request $request)
    {
        try{
            $log = Log::where('log_name', $request->log_name )->get();;  
            return response()->json($log, 200);
    
        }catch(\Exception $e){
            return response()->json(['error'=>'Não foi possivel acessar os Logs'],500);
        }  
     
    }


}

    