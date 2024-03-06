<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Checklist;
use App\Models\Contract;
use App\Models\Collaborator;
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
            if($request->contract_id){
                if($request->log_name == "Checklist"){
                    $checklist = Checklist::where('contract_id',$request->contract_id)->pluck('id');
                    foreach($checklist as $key => $value){
                        $logs = Log::where('subject_id', $value )->where('log_name',$request->log_name)->get();
                        foreach($logs as $index => $log){
                            $collaborator = Collaborator::where('id',$log->causer_id)->pluck('name')->toArray();
                            $logs[$index]->name = $collaborator[0];
                        }
                    }
                }else{
                    $logs = Log::where('subject_id', $request->contract_id )->where('log_name',$request->log_name)->get();
                    foreach($logs as $index => $log){
                        $collaborator = Collaborator::where('id',$log->causer_id)->pluck('name')->toArray();
                        $logs[$index]->name = $collaborator[0];
                    }
                }
            }else{
                $logs = Log::where('log_name',$request->log_name)->get();
                foreach($logs as $index => $log){
                    $collaborator = Collaborator::where('id',$log->causer_id)->pluck('name')->toArray();
                    $logs[$index]->name = $collaborator[0];
                }
            }
            return response()->json($logs, 200);
    
        }catch(\Exception $e){
            return response()->json(['error'=>'Não foi possivel acessar os Logs'],500);
        }  
     
    }


    public function getLogCollaborator(Request $request)
    {
        try{
            if($request->contract_id){
                if($request->log_name == "Checklist"){
                    $checklist = Checklist::where('contract_id',$request->contract_id)->pluck('id');
                    foreach($checklist as $key => $value){
                        $logs = Log::where('subject_id', $value )->where('log_name',$request->log_name)->get();
                        foreach($logs as $index => $log){
                            $collaborator = Collaborator::where('id',$log->causer_id)->pluck('name')->toArray();
                            $logs[$index]->name = $collaborator[0];
                        }
                    }
                }else{
                    $logs = Log::where('subject_id', $request->contract_id )->where('log_name',$request->log_name)->get();
                    foreach($logs as $index => $log){
                        $collaborator = Collaborator::where('id',$log->causer_id)->pluck('name')->toArray();
                        $logs[$index]->name = $collaborator[0];
                    }
                }
            }else{
                $logs = Log::where('log_name',$request->log_name)->get();
                foreach($logs as $index => $log){
                    $collaborator = Collaborator::where('id',$log->causer_id)->pluck('name')->toArray();
                    $logs[$index]->name = $collaborator[0];
                }
            }
            return response()->json($logs, 200);
    
        }catch(\Exception $e){
            return response()->json(['error'=>'Não foi possivel acessar os Logs'],500);
        }  
     
    }


}

    