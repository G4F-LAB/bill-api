<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Contract;

class AnalyticsController extends Controller {
    
    public function __construct(Collaborator $collaborator,Contract $contract){
        $this->auth_user = $collaborator->where('objectguid', Auth::user()->getConvertedGuid())->first();;
        $this->contract = $contract;
    }

    public function getMyAnalytics() {
        //print_r($this->auth_user);
        $user_contracts = $this->contract->with('collaborators')->where('manager_id',$this->auth_user->id)->get();
        //count($user_contracts->contracts);
        return response()->json(['contracts' => $user_contracts], 200);
    }
}