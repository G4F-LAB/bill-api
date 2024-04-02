<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Contract;
use App\Models\Checklist;
use App\Models\Executive;
use App\Models\Operation;
use App\Models\CollaboratorsOperations;

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

    public function getMyAnalytics(Request $request)
    {
        $id = $request->input('id');
        $month = now()->format('m');
        $year = now()->format('Y');


        // return response()->json($request);
        if ($this->auth_user->is_executive()) {

            $this->executive = $this->executive->with('manager')->where('manager_id', $this->auth_user->id)->first();
            $operationsQuery = $this->operation->with(['contract.checklist' => function ($query) use ($month, $year) {
                $query->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?", [$month, $year]);
            }])->where('executive_id', $this->executive->id);

            if ($id) {
                $operationsQuery = $operationsQuery->where('id', $id);
            }
            $operations = $operationsQuery->get()->toArray();

            foreach ($operations as $index => $operation) {
                $contracts = $operation['contract'];

                if (!isset($operation['complete_checklists'])) {
                    $operations[$index]['complete_checklists'] = 0;
                }
                if (!isset($operation['total_contracts'])) {
                    $operations[$index]['total_contracts'] = 0;
                }
                if (!isset($operation['total_checklists'])) {
                    $operations[$index]['total_checklists'] = 0;
                }

                foreach ($contracts as $index2 => $contract) {
                    $checklists = $contract['checklist'];
                    $operations[$index]['total_contracts'] = $operations[$index]['total_contracts'] + 1;

                    foreach ($checklists as $index3 => $checklist) {
                        $operations[$index]['total_checklists'] = $operations[$index]['total_checklists'] + 1;

                        if (count($contract['checklist']) == 1 && $checklist['completion'] == 100) {
                            $operations[$index]['complete_checklists'] = $operations[$index]['complete_checklists'] + 1;
                        } elseif (count($contract['checklist']) > 1) {
                            if ($contracts[$index2]['checklist'][0]['completion'] == 100 && $contracts[$index2]['checklist'][1]['completion'] == 100) {
                                $operations[$index]['complete_checklists'] = $operations[$index]['complete_checklists'] + 1;
                            }
                        }
                    }
                }


                if ($operations[$index]['total_checklists'] > 0) {
                    $operations[$index]['percentage_complete'] = round(($operations[$index]['complete_checklists'] / $operations[$index]['total_checklists']) * 100);
                } else {
                    $operations[$index]['percentage_complete'] = 0;
                }
            }

            $response = [
                'gerencia' => $this->executive,
                'operacoes' => $operations
            ];

            return response()->json(['success' => $response], 200);
        } else {
            $user_contracts = $this->contract->with(['operation.executive', 'checklist' => function ($query) use ($month, $year) {
                $query->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?", [$month, $year]);
            }])->when($this->auth_user->permission_id !== 2, function ($query) {
                $query->whereHas('operation', function ($query2) {
                    $query2->where('manager_id', $this->auth_user->id);
                });
            })->get();

            if ($user_contracts->isEmpty()) return response()->json(['error' => 'Não foram encontrados contratos']);

            foreach ($user_contracts as $contract) {
                if (!$contract->checklist->isEmpty()) {
                    $checklist_array = $contract->checklist->toArray();

                    foreach ($checklist_array as $checklist) {
                        $checklist_sync = Checklist::find($checklist['id']);
                        $checklist_sync->sync_itens();
                    }
                }
            }

            $user_contracts = $this->contract->with(['operation', 'checklist' => function ($query) {
                $query->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?", [now()->format('m'), now()->format('Y')]);
            }])->whereHas('operation', function ($query) {
                $query->where('manager_id', $this->auth_user->id);
            })->get();

            $total_contracts = count($user_contracts);
            $total_complete_checklists = 0;

            $contracts = $user_contracts->toArray();

            // calculo do total de checklists concluídos
            foreach ($contracts as $index => $contract) {
                if (!empty($contract['checklist'])) {
                    //print_r($contract['checklist']);
                    foreach ($contract['checklist'] as $checklist) {

                        if (count($contract['checklist']) == 1 && $checklist['completion'] == 100) {
                            $total_complete_checklists = $total_complete_checklists + 1;
                        } elseif (count($contract['checklist']) > 1) {
                            if ($contracts[$index]['checklist'][0]['completion'] == 100 && $contracts[$index]['checklist'][1]['completion'] == 100) {
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
                    ->get();

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

            $id_operation = $request->id;

            $operations = Contract::select('id', 'name')
                ->where('status_id', 1)
                ->where('operation_id', $id_operation)
                ->get();

            return response()->json(['success' => $operations], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Houve um erro interno na aplicação'], 500);
        }
    }

    public function check_complete(Request $request)
    {
        
        
        try {
            
            $operations = $this->getOperationsByUser()->getData();
            $ids_operations = [];

            foreach ($operations->success as $item) {
                $ids_operations[] = $item->id;
            }

            $ids_contracts = Contract::select('id')->whereIn('operation_id', $ids_operations)->pluck('id');

            $date = date('Y-m', strtotime('-1 month'));

            $complete = Checklist::where('date_checklist', 'LIKE', $date . '%')->whereIn('contract_id', $ids_contracts)->where('completion', 100)->count();
            $incomplete = Checklist::where('date_checklist', 'LIKE', $date . '%')->whereIn('contract_id', $ids_contracts)->where('completion', '!=', 100)->count();

            $data = ['complete' => $complete, 'incomplete' => $incomplete];

            return response()->json(['success' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
