<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Contract;
use App\Models\Checklist;
use App\Models\Executive;
use App\Models\Operation;
use Illuminate\Support\Facades\DB;
use App\Models\StatusChecklist;
use App\Models\ContractChange;

class AnalyticsController extends Controller
{

    public function __construct(Collaborator $collaborator, Contract $contract, Checklist $checklist, Executive $executive, Operation $operation)
    {
        $this->auth_user = $collaborator->getAuthUser();
        $this->contract = $contract;
        $this->checklist = $checklist;
        $this->executive = $executive;
        $this->operation = $operation;
    }

    public function operations(Request $request)
    {
        $data = [];

        $data['total_contracts'] = 0;
        $data['total_checklists_done'] = 1;
        $data['total_checklists_undone'] = 1;
        $data['total_collaborators'] = 100;
        $data['checklists_status_progress'] = [['name' => 'Iniciado', 'total' => 0], ['name' => 'Em progresso', 'total' => 0]];

        return response()->json(['status' => 'ok', 'data' => $data], 200);
    }

    public function operationsById(Request $request)
    {
        $operationsById = [];

        $operationsById['id'] = 7;
        $operationsById['name'] = 'operacao 7';
        $operationsById['manager'] = ['id' => 12, 'name' => 'Fulano de tal'];
        $operationsById['executive'] = ['id' => 23, 'name' => 'Fulano de tal'];
        $operationsById['contracts'] = ['id' => 84, 'name' => 'ABDI - CTO 04/20242'];

        return response()->json(['status' => 'ok', 'data' => $operationsById], 200);
    }

    public function contractsByCollaborators(Request $request)
    {
        $contractsByCollaborators = [];

        $contractsByCollaborators['id'] = 7;
        $contractsByCollaborators['name'] = 'operacao 7';
        $contractsByCollaborators['manager'] = ['id' => 12, 'name' => 'Fulano de tal'];
        $contractsByCollaborators['executive'] = ['id' => 23, 'name' => 'Fulano de tal'];
        $contractsByCollaborators['collaborators'] = [['id' => 2, 'name' => 'Vitor Campos'], ['id' => 7, 'name' => 'Rafael Barroso'], ['id' => 5, 'name' => 'Matheus'],['id' => 6, 'name' => 'Wesley Carlos Severiano']];



        return response()->json(['status' => 'ok', 'data' => $contractsByCollaborators], 200);
    }



    public function getOperationsByUser()
    {
        try {

            $id_user = $this->auth_user->id;

            $executive = Executive::select('id')
                ->where('manager_id', $id_user)
                ->pluck('id');


            if (!$executive->isEmpty()) {
                $operations = Operation::select('operations.id', 'operations.name')
                    ->whereIn('executive_id', $executive)
                    ->get();

                if ($operations->isEmpty()) {
                    return response()->json(['error' => 'Nenhum dado vinculado'], 500);
                }

                return response()->json(['success' => $operations], 200);
            } else {
                $operations = Operation::join('collaborator_operations', 'operations.id', '=', 'collaborator_operations.operation_id')
                ->select('operations.id', 'operations.name')
                ->where('collaborator_id', $id_user)
                ->whereNull('collaborator_operations.deleted_at')
                ->get();

              // print_r($id_user);exit;
           // return $operations;

                if ($operations->isEmpty()) {
                    return response()->json(['error' => 'Nenhum dado vinculado'], 500);
                }

                return response()->json(['success' => $operations], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getContractsByOperation(Request $request)
    {
        try {
            // $id_operation = $request->id;
            // $date = date('Y-m', strtotime('-1 month'));

            // Buscar contratos ativos relacionados à operação específica
            $contracts = Contract::where('status_id', 1)
                            //    where('name')
                                //  ->where('operation_id', $id_operation)
                                 ->get();



            $totalContracts = $contracts->count();

            // Inicializar contadores de checklists concluídos e não concluídos
            $totalChecklistsDone = 0;
            $totalChecklistsUndone = 0;

            // Iterar sobre cada contrato para buscar os checklists associados
            foreach ($contracts as $contract) {
                // Buscar os checklists relacionados a este contrato dentro do período desejado
                $checklists = Checklist::where('contract_id', $contract->id)
                                        // ->where('date_checklist', 'LIKE', $date . '%')
                                        ->get();

                // Contabilizar checklists concluídos e não concluídos deste contrato
                foreach ($checklists as $checklist) {
                    if ($checklist->completion == 100) {
                        $totalChecklistsDone++;
                    } else {
                        $totalChecklistsUndone++;
                    }
                }
            }

            // Retornar os resultados
            $data = [
                'total_contracts' => $totalContracts,
                'total_checklists_done' => $totalChecklistsDone,
                'total_checklists_undone' => $totalChecklistsUndone,
                'contracts' => $contracts  // Lista de contratos (opcional)
            ];

            return response()->json(['success' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Houve um erro interno na aplicação'], 500);
        }
    }


   
    // public function check_complete(Request $request)
    // {

    //     try {

    //         $operations = $this->getOperationsByUser()->getData();
    //         $ids_operations = [];

    //         foreach ($operations->success as $item) {
    //             $ids_operations[] = $item->id;
    //         }

    //         $ids_contracts = Contract::select('id')->whereIn('operation_id', $ids_operations)->pluck('id');
    //          return count($ids_contracts);

    //         $date = date('Y-m', strtotime('-1 month'));

    //         $complete = Checklist::where('date_checklist', 'LIKE', $date . '%')->whereIn('contract_id', $ids_contracts)->where('completion', 100)->count();
    //         $incomplete = Checklist::where('date_checklist', 'LIKE', $date . '%')->whereIn('contract_id', $ids_contracts)->where('completion', '!=', 100)->count();

    //         $data = ['complete' => $complete,
    //                 'incomplete' => $incomplete,
    //                 'contracts' => count($ids_contracts)
    //             ];

    //         return response()->json(['success' => $data], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function qtdStatusChecklists(Request $request)
    {

        try {
            $status_checklist = [];
            $query_status_checklist = StatusChecklist::all();
            foreach ($query_status_checklist as $value) {
                $status_checklist[] = ['name' => $value->name, 'total' => 0];
            }
             return $status_checklist;

            $date = date('Y-m', strtotime('-1 month'));

            $operations = $this->getOperationsByUser()->getData();
            $ids_operations = [];

            foreach ($operations->success as $item) {
                $ids_operations[] = $item->id;
            }

            $ids_contracts = Contract::select('id')->whereIn('operation_id', $ids_operations)->pluck('id');
             return $ids_contracts;

            $checklistCounts = Checklist::select('status_checklist.name', DB::raw('count(status_checklist.name) as total'))
                ->leftJoin('status_checklist', 'status_checklist.id', '=', 'checklists.status_id')
                ->whereIn('contract_id', $ids_contracts)
                ->where('date_checklist', 'LIKE', $date . '%')
                ->groupBy('status_checklist.name')
                ->get();
                 return $checklistCounts;

            foreach ($status_checklist as $key1 => $status1) {
                foreach ($checklistCounts as $key2 => $status2) {
                    if ($status1['name'] == $status2['name']) {
                        $status_checklist[$key1]['total'] = $status2['total'];
                    }
                }
            }


            return $status_checklist;


            return response()->json(['success' => $checklistCounts], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

//  public function getAllCollaborators()
//     {
//      try {
//           $collaborators = Collaborator::select('id', 'name', 'permission_id', 'phone', 'office', 'email')
//                ->get();

//            return response()->json(['status'=>'ok','data' => $collaborators], 200);
//        } catch (\Exception $e) {
//            return response()->json(['status'=>'error', 'message' => 'Houve um erro interno na aplicação'], 500);
//        }
//    }

//    public function getChecklist()
// {
//     try {
//         $checklists = Checklist::select('id', 'contract_id', 'completion', 'date_checklist')
//             ->get();

//         return response()->json(['status' => 'ok', 'data' => $checklists], 200);
//     } catch (\Exception $e) {
//         return response()->json(['status' => 'error', 'message' => 'Houve um erro interno na aplicação'], 500);
//     }

// }
//     public function getCollaboratorById($id)
// {
//     try {
//         $collaborator = Collaborator::select('id', 'name', 'permission_id', 'phone', 'office', 'email')
//             ->find($id);

//         if (!$collaborator) {
//             return response()->json(['error' => 'Colaborador não encontrado'], 404);
//         }

//         return response()->json(['success' => $collaborator], 200);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }

//  Dentro do método que atualiza o contrato
// public function updateContract(Request $request, $id)
// {
//     try {
//         $contract = Contract::findOrFail($id);

//          Dados originais do contrato antes da atualização
//         $originalData = $contract->toArray();

//          Atualizar o contrato com os dados recebidos na requisição
//         $contract->update($request->all());

//          Comparar dados originais com dados atualizados para identificar alterações
//         $changes = array_diff_assoc($request->all(), $originalData);

//          Verificar se houve realmente alterações
//         if (!empty($changes)) {
//              Formatar detalhes da alteração para registro no histórico
//             $changeDetails = "Contrato atualizado - Alterações: " . json_encode($changes);

//              Criar um registro no histórico de alterações do contrato
//             ContractChange::create([
//                 'contract_id' => $contract->id,
//                 'user_id' => auth()->id(), // ID do usuário autenticado
//                 'change_details' => $changeDetails
//             ]);
//         }

//         return response()->json(['success' => 'Contrato atualizado com sucesso'], 200);
//     } catch (\Exception $e) {
//         return response()->json(['error' => 'Contrato não atualizado. Erro no processo!'], 500);
//     }
// }

    public function contractsAll(Request $request)
    {

        try {
            $date = date('Y-m', strtotime('-1 month'));

            $operations = $this->getOperationsByUser()->getData();
            $ids_operations = [];

            foreach ($operations->success as $item) {
                $ids_operations[] = $item->id;
            }

            $date = date('Y-m', strtotime('-1 month'));

            $operations = Contract::leftJoin('checklists', 'contracts.id', '=', 'checklists.contract_id')
                ->select('contracts.id', 'contracts.name', 'checklists.completion')
                ->where('contracts.status_id', 1)
                ->whereIn('contracts.operation_id', $ids_operations)
                ->where('date_checklist', 'LIKE', $date . '%')
                ->get();

            return response()->json(['success' => $operations], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Houve um erro interno na aplicação'], 500);
        }
    }



}
