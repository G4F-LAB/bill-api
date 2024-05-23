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
use App\Models\Collaborator;

class AnalyticsController extends Controller
{
    public function operation_data(Request $request)
    {
        try {
            // Busca as operações com contratos e checklists ativos
            $operations = Operation::with(['contracts' => function ($query) {
                $query->where('status', 'Ativo');
            }, 'contracts.checklists'])->get();

            // Inicializa variáveis para os totais
            $totalOperations = $operations->count();
            $totalContracts = 0;
             $totalChecklistsDone = 0;
             $totalChecklistsUndone = 0;
             $totalCollaborators = 0;

            // Inicializa o status de progresso das checklists
            $checklistsStatusProgress = [
                ['name' => 'Iniciado', 'total' => 0],
                ['name' => 'Em progresso', 'total' => 0],
            ];

            // Monta os dados de retorno para cada operação
            $operationsData = [];
            foreach ($operations as $operation) {
                $operationData = [
                    'operation_id' => $operation->id,
                    'operation_name' => $operation->name,
                    'contracts' => [],
                    'total_collaborators_operation' => 0,
                ];

                // Conta os colaboradores ativos da operação
                $totalCollaboratorsOperation = OperationContractUser::where('operation_id', $operation->id)
                    ->whereHas('user', function ($query) {
                        $query->where('status', 'Ativo');
                    })
                    ->count();
                $operationData['total_collaborators_operation'] = $totalCollaboratorsOperation;
                $totalCollaborators += $totalCollaboratorsOperation;

                // Processa os contratos da operação
                foreach ($operation->contracts as $contract) {
                    $contractData = [
                        'contract_id' => $contract->id,
                        'contract_name' => $contract->name,
                        'checklists_done' => 0,
                        'checklists_undone' => 0,
                        'total_collaborators_contract' => 0,
                    ];

                    // Conta os checklists concluídos e em andamento do contrato
                    $checklistsDoneContract = $contract->checklists()->where('completion', 100)->count();
                    $checklistsUndoneContract = $contract->checklists()->where('completion', '!=', 100)->count();
                    $contractData['checklists_done'] = $checklistsDoneContract;
                    $contractData['checklists_undone'] = $checklistsUndoneContract;
                    $totalChecklistsDone += $checklistsDoneContract;
                    $totalChecklistsUndone += $checklistsUndoneContract;

                    // Conta os colaboradores ativos do contrato
                    $totalCollaboratorsContract = OperationContractUser::where('contract_id', $contract->id)
                        ->whereHas('user', function ($query) {
                            $query->where('status', 'Ativo');
                        })
                        ->count();
                    $contractData['total_collaborators_contract'] = $totalCollaboratorsContract;

                    // Atualiza o progresso das checklists
                    foreach ($contract->checklists as $checklist) {
                        if ($checklist->completion == 0) {
                            $checklistsStatusProgress[0]['total']++;
                        } elseif ($checklist->completion < 100) {
                            $checklistsStatusProgress[1]['total']++;
                        }
                    }

                    $operationData['contracts'][] = $contractData;
                    $totalContracts++;
                }

                $operationsData[] = $operationData;
            }

            // Monta os dados finais de retorno
            $responseData = [
                'total_operations' => $totalOperations,
                'total_contracts' => $totalContracts,
                'total_checklists_done' => $totalChecklistsDone,
                'total_checklists_undone' => $totalChecklistsUndone,
                'total_collaborators' => $totalCollaborators,
                'checklists_status_progress' => $checklistsStatusProgress,
                'operations' => $operationsData,
            ];

            return response()->json(['status' => 'ok', 'message' => 'Dados carregados com sucesso', 'data' => $responseData], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }





public function contracts($id) {
    $data = [];

    // Recupera a operação com os contratos ativos e os últimos checklists
    $operation = Operation::with([
            'contracts' => function($query) {
                $query->where('status','Ativo')->with(['checklists' => function($query) {
                    $query->orderBy('id','desc')->limit(2);
                }]);
            },
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

    // Monta a estrutura de retorno para a operação
    $data = [
        'id' => $operation->id,
        'name' => $operation->name,
        'manager' => null,
        'executive' => null,
        'contracts' => [],
    ];

    // Recupera os detalhes do gerente associado à operação
    $manager = OperationManager::with('manager')
        ->where('operation_id', $operation->id)
        ->whereNotNull('manager_id')
        ->first();

    if ($manager) {
        $data['manager'] = ['id' => $manager->manager->id, 'name' => $manager->manager->name];
    }

    // Recupera os detalhes do executivo associado à operação
    $executive = OperationManager::with('executive')
        ->where('operation_id', $operation->id)
        ->whereNotNull('executive_id')
        ->first();

    if ($executive) {
        $data['executive'] = ['id' => $executive->executive->id, 'name' => $executive->executive->name];
    }

    // Monta a estrutura de retorno para cada contrato
    foreach ($contracts as $contract) {
        $contractData = [
            'id' => $contract->id,
            'name' => $contract->name,
            'checklists' => [],
        ];

        // Monta a estrutura de retorno para os checklists do contrato
        foreach ($contract->checklists as $checklist) {
            $checklistData = [
                'id' => $checklist->id,
                'name' => $checklist->name,
                'status' => $checklist->status, // Suponho que o modelo tenha um atributo "status"
                // Adicione aqui mais informações que deseja retornar sobre os checklists
            ];

            // Adicione mais informações sobre os itens e arquivos, se necessário

            $contractData['checklists'][] = $checklistData;
        }

        $data['contracts'][] = $contractData;
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
            // ->where('users.status', 'Ativo')
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

        // Monta a estrutura de retorno
        $collaboratorsData = [
            'id' => $firstOperation->id,
            'name' => $firstOperation->name,
            'manager' => $manager ? ['id' => $manager->executive->id, 'name' => $manager->executive->name] : null,
            'executive' => $executive ? ['id' => $executive->executive->id, 'name' => $executive->executive->name] : null,
            'collaborators' => [] // Inicializa a lista de colaboradores
        ];

        // Preenche os detalhes dos colaboradores para o contrato
        $collaborators = User::join('operation_contract_users', 'operation_contract_users.user_id', '=', 'users.id')
            ->where('operation_contract_users.contract_id', $id)
            ->where('users.status', 'Ativo')
            ->select('users.id', 'users.name')
            ->get();

        $collaboratorsData['collaborators'] = [
            'count' => $collaborators->count(),
            'list' => $collaborators->map(function ($collaborator) {
                return ['id' => $collaborator->id, 'name' => $collaborator->name];
            })->toArray()
        ];

        // Retorna os dados
        return $collaboratorsData;
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



    private function getAllContractsByOperations($user_id)
    {
        $user = User::with('operationContractUsers')->where('id', $user_id)->first();
        $id_operations = $user->operationContractUsers->pluck('operation_id');
        $contracts = Contract::whereIn('operation_id', $id_operations)->where('status', 'Ativo')->get();

        return $contracts;
    }

    private function getIdAllUsersByOperations($user_id)
    {
        $user = User::with('operationContractUsers')->where('id', $user_id)->first();
        $operations = $user->operationContractUsers;
        return $operations;
    }
}
