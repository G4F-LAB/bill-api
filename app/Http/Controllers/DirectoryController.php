<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Contract;
use App\Models\Checklist;
use App\Models\Executive;
use App\Models\Operation;

class DirectoryController extends Controller
{
    public function __construct(Checklist $checklist, Contract $contract, Operation $operation)
    {
        $this->checklist = $checklist;
        $this->contract = $contract;
        $this->operation = $operation;
    }

    public function getAnalyticsDirectory(Request $request)

    
    {
        return $request; die;
        $month = $request->input('month');
        $year = $request->input('year');

        if(!checkdate($month, 1 , $year)){
            return response()->json(['error' => 'Mês ou ano invalido.']);
        }
        // $month = now()->format('m');
        // $year = now()->format('Y');

        $checklist = $this->checklist->whereRaw("extract(month from date_checklist)= ? and extract(year from date_checklist)= ?", [$month, $year])->get();
        $totalChecklist = count($checklist);

        $completedChecklist = $checklist->filter(function ($checklist) {
            return $checklist->completion == 100;
        });
        $totalCompletedChecklist = count($completedChecklist);

        $checklist = $this->checklist->whereRaw("extract(month from date_checklist)= ? and extract(year from date_checklist)= ?", [$month, $year])
            ->where('completion', '<', 100)->get();

        // print_r($checklist);

        $contractIds = $checklist->pluck('contract_id')->toArray();
        $contracts = $this->contract->whereIn('id', $contractIds)->get();
        $operationIds = $contracts->pluck('operation_id')->toArray();
        $operations = $this->operation->whereIn('id', $operationIds)->get();

        $operationChecklistCounts = [];
        foreach ($operations as $operation) {
            $operationContracts = $contracts->where('operation_id', $operation->id);
            $operationChecklistCount = $checklist->whereIn('contract_id', $operationContracts->pluck('id'))->count();

            $operationChecklistCounts[] = [
                'operation_name' => $operation->name,
                'incomplete_checklist_count' => $operationChecklistCount
            ];
        }

        $allContracts = $this->contract->all();
        $allOperations = $this->operation->all();

        $operationContractsCount = [];
        foreach ($allOperations as $operation) {
            $operationContracts = $allContracts->where('operation_id', $operation->id);
            $operationContractsCount[] = [
                'operation_name' => $operation->name,
                'contracts_count' => $operationContracts->count()
            ];
        }

        $contractIds = $this->contract->pluck('id')->toArray();
        $operations = $this->operation->all();

        $checklistCompletion = [];
        foreach ($operations as $operation) {
            $operationContracts = $this->contract->whereIn('id', $contractIds)->where('operation_id', $operation->id)->get();
            $operationContractIds = $operationContracts->pluck('id')->toArray();

            $checklist = $this->checklist->whereIn('contract_id', $operationContractIds)
                ->whereRaw("extract(month from date_checklist)= ? and extract(year from date_checklist)= ?", [$month, $year])
                ->where('completion', '=', 100)->get();

            $checklistCompletion[] = [
                'operation_name' => $operation->name,
                'complete_checklist_count' => count($checklist)
            ];
        }

        $operationContractDetails = [];

        foreach ($operations as $operation) {
            $operationContracts = $this->contract->whereIn('id', $contractIds)->where('operation_id', $operation->id)->get();

            foreach ($operationContracts as $contract) {
                $contractChecklist = $this->checklist->where('contract_id', $contract->id)
                ->whereRaw("extract(month from date_checklist)= ? and extract(year from date_checklist)= ?", [$month, $year])->get();
                
                $checklistDetails = $contractChecklist->map(function ($checklist){
                    return [
                        'id_checklist' => $checklist ->id,
                        'date_checklist' => $checklist ->date_checklist,
                        'completion' => $checklist->completion
                        
                    ];
                });

                $operationContractDetails[] = [
                    'id' => $contract->id,
                    'operation_name' => $operation->name,
                    'contract_name' => $contract->name,
                    'checklist_details' => $checklistDetails
                ];

                }
        }







        return response()->json([
            'checklist_quantity' => $totalChecklist,
            'total_completed_checklists' => $totalCompletedChecklist,
            'Incomplete_checklist' => $operationChecklistCounts,
            'Operation_contracts' => $operationContractsCount,
            'Operation_complete' => $checklistCompletion,
            'Operation' => $operationContractDetails

        ], 200);
    }
}
