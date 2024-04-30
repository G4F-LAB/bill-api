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
use App\Models\OperationManager;
use App\Models\ContractUser;

class AnalyticsController extends Controller
{

   public function operation_data (Request $request){

        try {
            $user_id = 'd2f99cb5-77f4-4855-ad7c-cb2742d6a537';
            $data = [];

            $id_contracts = $this->getAllContractsByOperations($user_id)->pluck('id');
            $id_operations = $this->getIdAllUsersByOperations($user_id)->pluck('operation_id');

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
    public function contracts($id) {
        $data = [];

        $operation = Operation::with([
                'contracts' => function($query) {
                    $query->where('status','Ativo');
                },
                'contracts.checklists' => function($query) {
                    $query->orderBy('id','desc')->limit(2);
                }
            ])
            ->where('id', $id)
            ->first();

        if (!$operation) {
            return ['error' => 'Operação não encontrada'];
        }

        $contracts = $operation->contracts;

        if ($contracts->isEmpty()) {
            return ['error' => 'Nenhum contrato ativo encontrado para esta operação'];
        }

        $data['contracts'] = $contracts;

        $manager = OperationManager::with('manager')
            ->where('operation_id', $operation->id)
            ->whereNotNull('manager_id')
            ->first();

        if ($manager) {
            $data['manager'] = ['id' => $manager->manager->id, 'name' => $manager->manager->name];
        } else {
            $data['manager'] = null;
        }

        $executive = OperationManager::with('executive')
            ->where('operation_id', $operation->id)
            ->whereNotNull('executive_id')
            ->first();

        if ($executive) {
            $data['executive'] = ['id' => $executive->executive->id, 'name' => $executive->executive->name];
        } else {
            $data['executive'] = null;
        }

        return $data;
    }



    public function collaborators($id) {
        // Recupera os detalhes das operações relacionadas ao contrato
        $operations = Contract::leftJoin('operation_contract_users', 'operation_contract_users.contract_id', 'contracts.id')
            ->leftJoin('users', 'users.id', 'operation_contract_users.user_id')
            ->leftJoin('operations', 'operations.id', 'operation_contract_users.operation_id')
            ->select('operations.id as id', 'operations.name as name')
            ->where('contracts.id', $id)
            ->where('users.status', 'Ativo')
            ->groupBy('operations.id') // Agrupar pelos IDs das operações
            ->get();

        // Verifica se há operações
        if ($operations->isEmpty()) {
            return [
                'error' => 'Contrato não encontrado ou encerrado'
            ];
        }

        // Monta a estrutura de retorno para a primeira operação encontrada
        $firstOperation = $operations->first();

        // Recupera os detalhes do gerente associado à primeira operação encontrada
        $manager = OperationManager::with('executive')
            ->where('operation_id', $firstOperation->id)
            ->whereNotNull('executive_id')
            ->first();

        // Recupera os detalhes do executivo associado à primeira operação encontrada
        $executive = OperationManager::with('executive')
            ->where('operation_id', $firstOperation->id)
            ->whereNotNull('executive_id')
            ->first();

        // Preenche os detalhes dos colaboradores para o contrato
        $collaborators = User::join('operation_contract_users', 'operation_contract_users.user_id', '=', 'users.id')
            ->where('operation_contract_users.contract_id', $id)
            ->where('users.status', 'Ativo')
            ->select('users.id', 'users.name', 'users.type') // Adiciona 'type' à seleção
            ->get();

        $collaboratorsData = [
            'id' => $firstOperation->id,
            'name' => $firstOperation->name,
            'manager' => $manager ? ['id' => $manager->executive->id, 'name' => $manager->executive->name] : null,
            'executive' => $executive ? ['id' => $executive->executive->id, 'name' => $executive->executive->name] : null,
            'collaborators' => $collaborators->count(), // Retorna apenas o número total de colaboradores
            'collaborator_details' => $collaborators->map(function ($collaborator) {
                return ['id' => $collaborator->id, 'name' => $collaborator->name, 'type' => $collaborator->type];
            }),
        ];

        // Retorna os dados sem a contagem de colaboradores no JSON
        return response()->json($collaboratorsData);
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



private function getAllContractsByOperations ($user_id) {

    $user = User::with('operationContractUsers')->where('id',$user_id)->first();
    $id_operations = $user->operationContractUsers->pluck('operation_id');
    $contracts = Contract::whereIn('operation_id',$id_operations)->where('status','Ativo')->get();

    return $contracts;
}

private function getIdAllUsersByOperations ($user_id) {

    $user = User::with('operationContractUsers')->where('id',$user_id)->first();
    $operations = $user->operationContractUsers;
    return $operations;

}

}
