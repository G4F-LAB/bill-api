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
            $operation_manager = new OperationManager();
            if ($request->has('operation_id'))$operation_manager->operation_id = $request->operation_id;
            if ($request->has('manager_id'))$operation_manager->manager_id = $request->manager_id;
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
            ->where('status','!=','Inativo')
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
            ->where('status','!=','Inativo')
            ->orderBy('id','ASC')->get();
            return response()->json($executivo, 200);

        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $operation = OperationManager::where('operation_id',$id)->first();
            if ($request->has('manager_id')) $operation->manager_id = $request->manager_id;
            if ($request->has('executive_id')) $operation->executive_id = $request->executive_id;
            $operation->save();
            return response()->json($operation, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage(), 'Não foi possivel atualizar esse registro.'], 500);
        }
    }
}
