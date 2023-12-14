<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Contract;
use App\Models\Checklist;

class AnalyticsController extends Controller {
    
    public function __construct(Collaborator $collaborator,Contract $contract,Checklist $checklist){
        $this->auth_user = $collaborator->where('objectguid', Auth::user()->getConvertedGuid())->first();
        $this->contract = $contract;
        $this->checklist = $checklist;
    }

    public function getMyAnalytics(Request $request) {
        $user_contracts = $this->contract->with(['operation','checklist' => function($query) {
                                $query->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?",[now()->format('m'),now()->format('Y')]);
                            }])->whereHas('operation',function($query) {
                                $query->where('manager_id',$this->auth_user->id);
                            })->get();

        if($user_contracts->isEmpty()) return response()->json(['error' => 'Não foram encontrados contratos']);
        $total = count($user_contracts);

        foreach($user_contracts as $contract) {
            if(!$contract->checklist->isEmpty()){
                $checklist_array = $contract->checklist->toArray();

                foreach($checklist_array as $checklist) {
                    $checklist_sync = Checklist::find($checklist['id']);
                    $checklist_sync->sync_itens();
                }
            }
        }

        $response = [
            'contracts' => [
                'list' => $user_contracts,
                'total_contracts' => $total
            ]
        ];
        return response()->json(['success' => $response], 200);
    }
}