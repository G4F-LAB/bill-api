<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Contract;

class AnalyticsController extends Controller {
    
    public function __construct(Collaborator $collaborator,Contract $contract){
        $this->auth_user = $collaborator->where('objectguid', Auth::user()->getConvertedGuid())->first();
        $this->contract = $contract;
    }

    public function getMyAnalytics() {
        //print_r($this->auth_user);
        $user_contracts = $this->contract->with('collaborator', function($query){
            return $query->where('manager_id',$this->auth_user->id);
        })->get();

        /*if(empty($user_contracts)) return response()->json(['error' => 'Não foram encontrados contratos']);
        $total = count($user_contracts);

        $response = [
            'contracts' => [
                'list' => $user_contracts,
                'total_contracts' => $total
            ]
        ];
        return response()->json(['succes' => $response], 200);*/
    }
}