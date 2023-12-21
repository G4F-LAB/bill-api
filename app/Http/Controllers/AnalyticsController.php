<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Contract;
use App\Models\Checklist;
use App\Models\Executive;
use App\Models\Operation;

class AnalyticsController extends Controller {
    
    public function __construct(Collaborator $collaborator,Contract $contract,Checklist $checklist,Executive $executive,Operation $operation){
        $this->auth_user = $collaborator->where('objectguid', Auth::user()->getConvertedGuid())->first();
        $this->contract = $contract;
        $this->checklist = $checklist;
        $this->executive = $executive;
        $this->operation = $operation;
    }

    public function getMyAnalytics(Request $request) {

        $month = now()->format('m');
        $year = now()->format('Y');

        if($this->auth_user->permission_id == 2) {

            $this->executive = $this->executive->with('manager')->where('manager_id',$this->auth_user->id)->first();
            $operations = $this->operation->with(['contract.checklist' => function($query) use($month,$year) {
                                $query->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?",[$month,$year]);
                            }])->where('executive_id',$this->executive->id)->get()->toArray();

            //print_r($operations);

            //$total_contracts = count($user_contracts);
            $total_complete_checklists = 0;

            foreach($operations as $index => $operation) {
                $contracts = $operation['contract'];

                foreach($contracts as $index2 => $contract) {

                    $checklists = $contract['checklist'];

                    foreach($checklists as $index3 => $checklist) {

                        if(count($contract['checklist']) == 1 && $checklist['completion'] == 100) {
                            if(!isset($operations[$index]['complete_checklists'])){
                                $operations[$index]['complete_checklists'] = 0;
                            } else {
                                $operations[$index]['complete_checklists'] = $operations[$index]['complete_checklists'] + 1;
                            }
    
                        }elseif(count($contract['checklist']) > 1){
                            if($contracts[$index2]['checklist'][0]['completion'] == 100 && $contracts[$index2]['checklist'][1]['completion'] == 100) {
                                if(!isset($operations[$index]['complete_checklists'])){
                                    
                                    $operations[$index]['complete_checklists'] = 0;
                                } else {
                                    $operations[$index]['complete_checklists'] = $operations[$index]['complete_checklists'] + 1;
                                }
                            }
                        }
                    }
                    //print_r($contract);
                }
            }
            print_r($operations);die;

            return response()->json(['success' => $this->executive], 200);

        }else {
            $user_contracts = $this->contract->with(['operation.executive','checklist' => function($query) use($month,$year) {
                                    $query->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?",[$month,$year]);
                                }])->when($this->auth_user->permission_id !== 2, function($query) {
                                    $query->whereHas('operation',function($query2) {
                                        $query2->where('manager_id',$this->auth_user->id);
                                    });
                                })->get();
    
            if($user_contracts->isEmpty()) return response()->json(['error' => 'Não foram encontrados contratos']);
    
            foreach($user_contracts as $contract) {
                if(!$contract->checklist->isEmpty()){
                    $checklist_array = $contract->checklist->toArray();
    
                    foreach($checklist_array as $checklist) {
                        $checklist_sync = Checklist::find($checklist['id']);
                        $checklist_sync->sync_itens();
                    }
                }
            }
    
            $user_contracts = $this->contract->with(['operation','checklist' => function($query) {
                                    $query->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?",[now()->format('m'),now()->format('Y')]);
                                }])->whereHas('operation',function($query) {
                                    $query->where('manager_id',$this->auth_user->id);
                                })->get();
    
            $total_contracts = count($user_contracts);
            $total_complete_checklists = 0;
    
            $contracts = $user_contracts->toArray();
    
            // calculo do total de checklists concluídos
            foreach($contracts as $index => $contract) {
                if(!empty($contract['checklist'])) {
                    //print_r($contract['checklist']);
                    foreach($contract['checklist'] as $checklist) {
    
                        if(count($contract['checklist']) == 1 && $checklist['completion'] == 100) {
                            $total_complete_checklists = $total_complete_checklists + 1;
    
                        }elseif(count($contract['checklist']) > 1){
                            if($contracts[$index]['checklist'][0]['completion'] == 100 && $contracts[$index]['checklist'][1]['completion'] == 100) {
                                $total_complete_checklists = $total_complete_checklists + 1;
                            }
                        }
                    }
                }
                
            }
    
            $response = [
                'contracts' => [
                    'list' => $user_contracts,
                    'total_contracts' => $total_contracts,
                    'completed_checklists' => $total_complete_checklists
                ]
            ];
            return response()->json(['success' => $response], 200);
        }
    }
}