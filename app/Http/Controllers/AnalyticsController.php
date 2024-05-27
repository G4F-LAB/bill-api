<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Contract;
use App\Models\Checklist;
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
            // Inicializa variáveis para os totais
            $totalOperations = Operation::count();
            $totalContracts = Contract::where('status', 'Ativo')->count();
            $totalChecklistsDone = Checklist::where('completion', 100)->count();
            $totalChecklistsUndone = Checklist::where('completion', '!=', 100)->count();
            $totalCollaborators = User::where('status', 'Ativo')->count();

            // Inicializa o status de progresso das checklists
            $checklistsStatusProgress = [
                ['name' => 'Iniciado', 'total' => 0],
                ['name' => 'Em progresso', 'total' => 0],
                ['name' => 'Assinatura pendente', 'total' => 0],
                ['name' => 'Validação pendente', 'total' => 0],
                ['name' => 'Finalizado', 'total' => 0]
            ];

            // Atualiza o progresso das checklists
            foreach (Checklist::all() as $checklist) {
                $status = $checklist->status->name;
                foreach ($checklistsStatusProgress as &$statusProgress) {
                    if ($statusProgress['name'] == $status) {
                        $statusProgress['total']++;
                        break;
                    }
                }
            }

            // Monta os dados finais de retorno
            $responseData = [
                'total_operations' => $totalOperations,
                'total_contracts' => $totalContracts,
                'total_checklists_done' => $totalChecklistsDone,
                'total_checklists_undone' => $totalChecklistsUndone,
                'total_collaborators' => $totalCollaborators,
                'checklists_status_progress' => $checklistsStatusProgress,
            ];

            return response()->json(['status' => 'ok', 'message' => 'Dados carregados com sucesso', 'data' => $responseData], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function contracts($id, Request $request) {
        $data = [];

        // Recupera o parâmetro de consulta 'name' da solicitação
        $contractName = $request->query('name');

        // Recupera o parâmetro de consulta 'date_checklist' da solicitação (esperado no formato MM-YYYY)
        $dateChecklist = $request->query('date_checklist');

        // Recupera os detalhes do gerente associado à operação
        $manager = OperationManager::with('manager')
            ->where('operation_id', $id)
            ->whereNotNull('manager_id')
            ->first();

        // Recupera os detalhes do executivo associado à operação
        $executive = OperationManager::with('executive')
            ->where('operation_id', $id)
            ->whereNotNull('executive_id')
            ->first();

        // Adiciona os detalhes do gerente e do executivo aos dados
        $operation = Operation::find($id);
        if (!$operation) {
            return response()->json(['error' => 'Operação não encontrada'], 404);
        }

        $data['id'] = $operation->id;
        $data['name'] = $operation->name;
        $data['manager'] = $manager ? ['id' => $manager->manager->id, 'name' => $manager->manager->name] : null;
        $data['executive'] = $executive ? ['id' => $executive->executive->id, 'name' => $executive->executive->name] : null;

        // Recupera as informações dos contratos associados à operação
        $contractsQuery = Contract::where('operation_id', $id)
            ->where('status', 'Ativo');

        // Se 'name' estiver presente, aplica filtro pelo nome do contrato
        if ($contractName) {
            $contractsQuery->where('name', 'ILIKE', '%' . $contractName . '%');
        }

        $contracts = $contractsQuery->get();

        // Verifica se há contratos associados à operação
        if ($contracts->isEmpty()) {
            return response()->json(['error' => 'Nenhum contrato ativo encontrado para esta operação'], 404);
        }

        // Adiciona a soma de contratos ativos associados à operação
        $data['total_contracts'] = $contracts->count();

        // Adiciona os contratos com seus detalhes
        $data['contracts'] = $contracts->map(function ($contract) use ($dateChecklist) {
            $contractData = [
                'id' => $contract->id,
                'name' => $contract->name,
                'checklists' => []
            ];

            // Filtra os checklists pelo mês e ano fornecidos, se disponível
            $filteredChecklistsQuery = $contract->checklists();
            if ($dateChecklist) {
                $dateParts = explode('-', $dateChecklist);
                if (count($dateParts) == 2) {
                    $month = $dateParts[0];
                    $year = $dateParts[1];
                    $filteredChecklistsQuery->whereYear('created_at', $year)
                                            ->whereMonth('created_at', $month);
                }
            }
            $filteredChecklists = $filteredChecklistsQuery->orderBy('id', 'desc')->get();

            // Adiciona os detalhes dos checklists
            foreach ($filteredChecklists as $checklist) {
                $checklistData = [
                    'id' => $checklist->id,
                    'name' => $checklist->name,
                    'completion' => $checklist->completion,
                    'created_at' => $checklist->created_at->toDateString(),
                    'items' => []
                ];

                // Adiciona os detalhes dos itens do checklist
                // foreach ($checklist->items as $item) {
                //     $itemData = [
                //         'id' => $item->id,
                //         'status' => $item->status,
                //         'files' => $item->files->map(function ($file) {
                //             return [
                //                 'id' => $file->id,
                //                 'name' => $file->name,
                //                 'url' => $file->url,
                //             ];
                    //     }),
                    // ];

                //     $checklistData['items'][] = $itemData;
                // }

                $contractData['checklists'][] = $checklistData;
            }

            return $contractData;
        });

        return response()->json($data);
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
