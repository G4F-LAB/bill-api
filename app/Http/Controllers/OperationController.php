<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Operation;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    //
    public function getAll()
    {
        $operations = Operation::all();
        return response()->json($operations, 200);
    }

    public function create(Request $request)
    {
        try {
            //code...
            $manager = Collaborator::find($request->manager_id);

            if (empty($manager)) return response()->json(['erro' => 'Colaborador não encontrado'], 200);

            $operation_old = Operation::where('name', 'like', '%' . $request->name . '%')->withTrashed()->first();

            if ($operation_old) {
                $operation_old->deleted_at = null;
                $operation_old->manager_id = $manager->id;
                $operation_old->save();
                return response()->json($operation_old, 200);
            }

            $operation = new Operation();
            $operation->name = $request->name;
            $operation->manager_id = $manager->id;
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
            if ($request->has('name')) $operation->name = $request->name;
            if ($request->has('manager_id')) $operation->manager_id = Collaborator::find($request->manager_id)->id;
            $operation->save();
            return response()->json($operation, 200);
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
