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
            $timeline = LogData::where('subject_id', $id)->get();
    
            $response = [];
    
            foreach ($timeline as $log) {
                // Decode properties to array
                $properties = json_decode($log->properties, true);
    
                $changesData = [];
    
                // Check for attributes
                if (isset($properties['attributes'])) {
                    // Keep original fields intact
                    $changesData['attributes'] = $properties['attributes'];
    
                    // Check inside 'attributes' for specific fields
                    foreach (['operation.operationContractUsers'] as $field) {
                        if (isset($properties['attributes'][$field])) {
                            $relatedModels = [];
                            foreach ($properties['attributes'][$field] as $userDetails) {
                                $relatedModel = $this->fetchRelatedModel($userDetails);
                                if ($relatedModel) {
                                    $relatedModels[] = $relatedModel;
                                }
                            }
                            $changesData['attributes'][$field] = $relatedModels;
                        }
                    }
                }
    
                // Check for old data
                if (isset($properties['old'])) {
                    // Keep original fields intact
                    $changesData['old'] = $this->fetchRelatedModel($properties['old']);
    
                    // Check inside 'old' for specific fields
                    foreach (['operation.operationContractUsers'] as $field) {
                        if (isset($properties['old'][$field])) {
                            $relatedModels = [];
                            foreach ($properties['old'][$field] as $userDetails) {
                                $relatedModel = $this->fetchRelatedModel($userDetails);
                                if ($relatedModel) {
                                    $relatedModels[] = $relatedModel;
                                }
                            }
                            $changesData['old'][$field] = $relatedModels;
                        }
                    }
                }
    
                $response[] = [
                    'type' => $log->event,
                    'name' => $log->log_name ,
                    'causer' =>  $log->causer_id ? $this->fetchRelatedModel($log->causer_id) : null,
                    'causer_type' => $log->causer_type,
                    'created' =>  Carbon::parse($log->created_at)->locale('pt_BR')->isoFormat('LL'),
                    'created_time' =>  Carbon::parse($log->created_at)->locale('pt_BR')->isoFormat('HH:mm'),
                    'changes' => $changesData
                ];
            }
    
            // Return response with merged data
            return response()->json($response, 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Não foi possível acessar os Logs'], 500);
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
