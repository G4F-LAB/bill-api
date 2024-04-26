<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Contract;
use App\Models\Checklist;
use Illuminate\Support\Facades\DB;
use App\Models\StatusChecklist;
use App\Models\OperationContractUser;
use App\Models\Operation;


class AnalyticsController extends Controller
{

   public function operation_contract (Request $request){

        try {
            $user_id = 'd2f99cb5-77f4-4855-ad7c-cb2742d6a537';
            $data = [];

            $user = User::with('operationContractUsers')->where('id',$user_id)->first();
            $id_operations = $user->operationContractUsers->pluck('operation_id');
            $id_contracts = Contract::whereIn('operation_id',$id_operations)->where('status','Ativo')->pluck('id');

            $checklists = Checklist::whereIn('contract_uuid',$id_contracts)->get();

            $checklists_status = Checklist::leftJoin('status_checklist', 'status_checklist.id', '=', 'checklists.status_id')
            ->select('status_checklist.name', DB::raw('COUNT(status_checklist.name) as total'))
            ->whereIn('contract_uuid', $id_contracts)
            ->groupBy('status_checklist.name')
            ->get();

            $data['total_contracts'] = $id_contracts->count();
            $data['total_checklists_done'] = $checklists->where('completion', 100)->count();
            $data['total_checklists_undone'] = $checklists->where('completion','!=', 100)->count();
            $data['total_collaborators'] = $this->collaborators_operation($id_operations)->count();
            $data['checklists_status_progress'] = $checklists_status;

            return response()->json(['status' => 'ok','message' => 'Dados carregado com sucesso', 'data' =>  $data], 500);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Houve um erro interno na aplicação'], 500);
        }
    }
    public function operationId_teste (Request $request) {

        return 'teste';

        // $user_id = 'd2f99cb5-77f4-4855-ad7c-cb2742d6a537';
        //     $data = [];

        //     $user = User::with('operationContractUsers')->where('id',$user_id)->first();
        //     $id_operations = $user->operationContractUsers->pluck('operation_id');
        //     $id_contracts = Contract::whereIn('operation_id',$id_operations)->where('status','Ativo');

        //     $checklists = Checklist::whereIn('contract_uuid',$id_contracts)->get();

        //     return response()->json($checklists);

   }

   public function teste_teste (Request $request){
    return 'teste 123456798';
   }



















   private function collaborators_operation ($id_operations) {

    $users = User::with('operationContractUsers')
        ->whereHas('operationContractUsers', function ($query) use ($id_operations) {
            $query->whereIn('operation_id', $id_operations)->where('status','Ativo');
        })
        ->get()
        ->makeHidden('operationContractUsers');

    return $users;

}

}
