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
        $current_date = now();
        $last_months = [];

        for ($i = 0; $i < 12; $i++) {
            $sub_date = $current_date->copy()->subMonths($i);
            $last_months[] = $sub_date->format('Y-m');
        }

        $user_contracts = $this->contract->with('operation','checklist')
                                    ->whereHas('operation',function($query) {
                                        $query->where('manager_id',$this->auth_user->id);
                                    })->whereHas('checklist',function($query) {
                                        $query->whereNotNull('id')->orderBy('id', 'desc')->first();
                                    })->get();

        /*$contract_checklists = $this->checklist->with('contract')->with('item')
                                    ->whereHas('operation',function($query) {
                                        $query->where('manager_id',$this->auth_user->id);
                                    })->whereHas('checklist',function($query) {
                                        $query->where('manager_id',$this->contract->id)->orderBy('id DESC')->first();
                                    })->get();*/

        if($user_contracts->isEmpty()) return response()->json(['error' => 'Não foram encontrados contratos']);
        $total = count($user_contracts);

        $response = [
            'contracts' => [
                'list' => $user_contracts,
                'total_contracts' => $total
            ]
        ];
        return response()->json(['succes' => $response], 200);
    }
}