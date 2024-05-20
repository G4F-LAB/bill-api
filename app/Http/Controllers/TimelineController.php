<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogData;
use App\Models\Log;
use Spatie\Activitylog\Models\Activity;
use App\Models\Contract;
use App\Models\User;
use App\Models\Checklist;
use App\Models\Operation;
use Carbon\Carbon;

class TimelineController extends Controller
{
    public function contract(Request $request, string $id)
    {
        try {
            // Fetch timeline data
            $contract = Contract::with(['operationContractUsers', 'checklists'])->find($id);

            if (!$contract) {
                return response()->json(['error' => 'Contract not found'], 404);
            }

            $logs_ids = collect([$id]);
            $checklists = collect();

            // Extract IDs from related entities
            foreach ($contract->operationContractUsers as $operationContractUser) {
                $logs_ids->push($operationContractUser->id);
                // $logs_ids->push($operationContractUser->contractUser->id);
            }
            foreach ($contract->checklists as $checklist) {
                $checklists->push($this->checklist($request, $checklist->id));
            }

            
            $timeline = LogData::inLog('contract', 'operationContractUser' )->whereIn('subject_id', $logs_ids)
                ->orderByDesc('created_at') 
                ->get();
    
                $timelineArray = $timeline->toArray();
                $checklistsArray = !empty($checklists) ? $checklists[0]->original->toArray() : [];

            $response = array_merge($timelineArray, $checklistsArray);

            // Sort merged array by created_at
            usort($response, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

    
            // Return response with merged data
            return response()->json($response, 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' =>$e], 500);
        }
    }
    

    public function checklist(Request $request, $id)
    {
        try {
            $checklist = Checklist::with('itens.file_itens')->find($id);
            
            $fileItensIds = [$id];
    
            foreach ($checklist->itens as $item) {
                $fileItensIds = array_merge($fileItensIds, $item->file_itens->pluck('id')->toArray());
            }

    
            $response = Activity::inLog('checklist', 'file_item')
                ->whereIn('subject_id', $fileItensIds)
                ->orderByDesc('created_at') 
                ->get()
                ->reject(function ($activity) {
                    return empty($activity->properties['attributes']) && empty($activity->properties['old']);
                })
                ->map(function ($activity) {
                    // Get user's name if causer_id is not null
                    $activity->name = $activity->log_name === 'checklist' ? 'Checklist' : 'Arquivo';
                    $causer = User::find($activity->causer_id);

                if ($causer) {
                    $activity->causer = $causer->name;
                    $activity->causer_position = $causer->position;
                } else {
                    $activity->causer = null;
                    $activity->causer_position = null;
                }
                    $activity->created = Carbon::parse($activity->created_at)->locale('pt_BR')->isoFormat('LL');
                    $activity->created_time = Carbon::parse($activity->created_at)->locale('pt_BR')->isoFormat('HH:mm');
    
                    // Unset "causer_id"
                    unset($activity->causer_id);
    
                    return $activity;
                });
    
            return response()->json($response->values(), 200);
        
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    

    
    private function fetchRelatedModel($userDetails)
    {
        $relatedModel = [];
    
        foreach ($userDetails as $field => $value) {
            switch ($field) {
                case 'operation_id':
                    $operation = Operation::find($value);
                    $relatedModel['operation'] = $operation ? $operation->name : null;
                    break;
                case 'contract_id':
                    $contract = Contract::find($value);
                    $relatedModel['contract'] = $contract ? $contract->name : null;
                    break;
                case 'user_id':
                    $user = User::find($value);
                    $relatedModel['user'] = $user ? $user->name : null;
                    break;
                default:
                    $relatedModel[$field] = $value;
                    break;
            }
        }
    
        return $relatedModel;
    }
    
    
    
}
