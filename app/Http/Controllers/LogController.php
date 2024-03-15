<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Checklist;
use App\Models\Contract;
use App\Models\Collaborator;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
                        $endDate = Carbon::now();
  
                        //Filtro de Datas:
                        if ($request->period = 'one_month') {
                            $startDate = Carbon::now()->subMonths(1);
                            $logs = Log::where('subject_id', $value )->where('log_name',$request->log_name)->orderByDesc('id')->whereBetween('created_at', [$startDate, $endDate])->get();

                           
                        } else

                        if ($request->period = 'three_month') {
                            $startDate = Carbon::now()->subMonths(3);
                            $logs = Log::where('subject_id', $value )->where('log_name',$request->log_name)->orderByDesc('id')->whereBetween('created_at', [$startDate, $endDate])->get();

                        } else

                        if ($request->period = 'six_month') {
                            $startDate = Carbon::now()->subMonths(6);
                            $logs = Log::where('subject_id', $value )->where('log_name',$request->log_name)->orderByDesc('id')->whereBetween('created_at', [$startDate, $endDate])->get();

                        } else {

                            $logs = Log::where('subject_id', $value )->where('log_name',$request->log_name)->orderByDesc('id')->get();
                            $logs[0]->properties = json_decode($logs[0]->properties);
                        }
                        
                        
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
            }
            
            
            else if ($request->log_name == "item") {
                $items_ids = Item::where('checklist_id', $request->checklist_id)->pluck('id')->toArray();
                
                $all_logs = [];

                foreach ($items_ids as $key => $value) {
                    $endDate = Carbon::now();

                    if ($request->period = 'one_month') {
                        $startDate = Carbon::now()->subMonths(1);
                        $logs = Log::where('subject_id', $value)->where('log_name', $request->log_name)->whereBetween('created_at', [$startDate, $endDate])->orderByDesc('id')->get();       
                    } 
                    
                    else if ($request->period = 'three_month') {
                        $startDate = Carbon::now()->subMonths(3);
                        $logs = Log::where('subject_id', $value)->where('log_name', $request->log_name)->whereBetween('created_at', [$startDate, $endDate])->orderByDesc('id')->get();

                    } else if ($request->period = 'six_month') {
                        $startDate = Carbon::now()->subMonths(6);
                        $logs = Log::where('subject_id', $value)->where('log_name', $request->log_name)->whereBetween('created_at', [$startDate, $endDate])->orderByDesc('id')->get();

                    } else {
                        $logs = Log::where('subject_id', $value)->where('log_name', $request->log_name)->orderByDesc('id')->get();
                        $logs[0]->properties = json_decode($logs[0]->properties);
                    
                    }
                   
                    foreach($logs as $index => $log){
                        $collaborator = Collaborator::where('id',$log->causer_id)->pluck('name')->first();
                        $log->name = $collaborator;
                
                        //Convertendo o properties para converter a String JSON e conseguir trazer as infos:
                        $properties_format = json_decode($log->properties);
                        $log->properties = $properties_format; 

                        //Passando diretamente os parametros de properties para acesso no frontend:
                        if (isset($properties_format->attributes)) {
                            $log->file_type_id = $properties_format->attributes->file_type_id;
                            $log->status = $properties_format->attributes->status;
                            $log->file_naming_id = $properties_format->attributes->file_naming_id;
                            $log->checklist_id = $properties_format->attributes->checklist_id;
                        } else {
                            $log->file_type_id = $properties_format->old->file_type_id;
                            $log->status = $properties_format->old->status;
                            $log->file_naming_id = $properties_format->old->file_naming_id;
                            $log->checklist_id = $properties_format->old->checklist_id;
                        }
                               
                                       
                        $all_logs[] = $log; 
                    }      
                }

                return response()->json($all_logs, 200);
                
            } 
           

            else{
                $logs = Log::where('log_name',$request->log_name)->get();
                foreach($logs as $index => $log){
                    $collaborator = Collaborator::where('id',$log->causer_id)->pluck('name')->toArray();
                    $logs[$index]->name = $collaborator[0];
                }
            }
            



            return response()->json($logs, 200);
    
        }catch(\Exception $e){
           
            return response()->json(['error'=>$e->getMessage()],500);
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

    