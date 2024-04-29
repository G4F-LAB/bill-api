<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\OperationManager;
use App\Models\User;
use Illuminate\Http\Request;

class OperationManagerController extends Controller
{
    public function create(Request $request)
    {
        try {
            //$manager = User::find($request->manager_id);
            //if (empty($manager)) return response()->json(['erro' => 'Colaborador não encontrado'], 200);
            // dd($request);exit;
            $operation_manager = new OperationManager();
            $operation_manager->operation_id = $request->operation_id;
            $operation_manager->manager_id = $request->manager_id;
            if($request->has('executive_id')){
                $user = User::where('id',$request->executive_id)->get()->toArray();
                if($user[0]['type'] == "Executivo"){
                    $operation_manager->executive_id = $request->executive_id;
                }
            }
            $operation_manager->save();
            return response()->json([$operation_manager, 'message' => 'Gerente adicionado com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }


    public function getAllManager()
    {
        try {
            $operation_manager = User::where('type','Gerente')
            ->orderBy('name','ASC')->get();
            return response()->json($operation_manager, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function getAllExecutives()
    {
        try {
            $executivo = User::where('type','Executivo')
            ->orderBy('id','ASC')->get();
            return response()->json($executivo, 200);

        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    // public function getAllExecutives(Request $request)
    // {
    //     try {
    //         $executive = OperationManager::whereNotNull('executive_id')
    //         ->orderBy('id','ASC')->get();
    //         return response()->json($executive, 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['erro' => $e->getMessage()], 500);
    //     }
    // }

    public function delete($id)
    {
        try {
            $executive = OperationManager::findOrFail($id);
            $executive->delete();
            return response()->json(['message' => 'Gerência excluída com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Não foi possivel apagar esse registro.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $executive = OperationManager::findOrFail($id);
            if ($request->has('operation_id')) $executive->operation_id = $request->operation_id;
            if ($request->has('manager_id')) $executive->manager_id = $request->manager_id;
            if ($request->has('executive_id')) $executive->executive_id = $request->executive_id;
            $executive->save();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Não foi possivel atualizar esse registro.'], 500);
        }
    }
}
