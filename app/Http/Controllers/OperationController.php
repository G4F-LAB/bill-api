<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationController extends Controller
{
    //
    public function getAll(Request $request)
    {
        $collaborator = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
        if ($request->has('q')) {
            $operations = Operation::with('contract','collaborators')
                ->where('manager_id', $collaborator->id)
                ->get();
            return response()->json($operations, 200);
        }
    }

    public function getAllOperations(Request $request)
    {
        $operations = Operation::with('executives')->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->orWhere('name', 'LIKE', '%' . $s . '%')                        
                        ->get();
                }
            }],
          
        ])->orderBy('id','ASC')->get();
        return response()->json($operations, 200);
    }
    public function getAllManagerofOperation(Request $request)
    {            
        try {
            $executive = Operation::with('collaborator','executives')
            ->where([ 
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->orWhere('name', 'LIKE', '%' . $s . '%')                        
                        ->get();
                }
            }],
          
            ])->orderBy('id','ASC')
            ->get();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            //code...
            if ($request->has('manager_id')) {
                $manager = Collaborator::find($request->manager_id);
                if (empty($manager)) return response()->json(['erro' => 'Gerente de Operação não encontrado'], 200);
            }

            if ($request->has('executive_id')) {
                $executive = Collaborator::find($request->executive_id);
                if (empty($executive)) return response()->json(['erro' => 'Gerente Executivo não encontrado'], 200);
            }

            $operation_old = Operation::where('name', 'like', '%' . $request->name . '%')->withTrashed()->first();

            if ($operation_old) {
                $operation_old->deleted_at = null;
                if ($request->has('manager_id')) $operation_old->manager_id = $manager->id;
                if ($request->has('executive_id')) $operation_old->executive_id = $executive->id;
                if ($request->has('reference')) $operation_old->reference = $request->reference;

                $operation_old->save();
                return response()->json($operation_old, 200);
            }

            $operation = new Operation();
            $operation->name = $request->name;
            if ($request->has('manager_id')) $operation->manager_id = $manager->id;
            if ($request->has('executive_id')) $operation->executive_id = $executive->id;
            if ($request->has('reference')) $operation->reference = $request->reference;
            $operation->save();
            return response()->json($operation, 200);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 500);
            //throw $th;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            //code...
            $operation = Operation::find($id);
            if ($request->name) $operation->name = $request->name;
            if ($request->has('manager_id')) $operation->manager_id = Collaborator::find($request->manager_id)->id;
            if ($request->has('executive_id')) $operation->executive_id = Executive::find($request->executive_id)->id;
            if ($request->reference) $operation->reference = $request->reference;
            $operation->save();
            return response()->json([$operation, 'message' => 'Operação atualizada com sucesso!'], 200);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 500);
            //throw $th;
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $operation = Operation::findOrFail($id);
            $operation->deleted_at = now();
            $operation->save();
            return response()->json(['message' => 'Registro com id = ' . $operation->id . ' foi excluído com sucesso!'], 200);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 500);
        }
    }
}
