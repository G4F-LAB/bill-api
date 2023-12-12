<?php

namespace App\Http\Controllers;

use App\Models\Executive;
use Illuminate\Http\Request;

class ExecutiveController extends Controller
{
    public function getAll()
    {
        try {
            $executive = Executive::with('operations')->get();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Não foi possivel encontrar os dados.'], 500);
        }
    }

    public function getById($id)
    {
        try {
            $executive = Executive::findOrFail($id);
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Não foi possivel encontrar os dados.'], 500);
        }
    }

    public function create(Request $request)
    {
        // return response()->json($request, 200);
        try {
            $executive_old = Executive::where('name', 'like', '%' . $request->name . '%')->withTrashed()->first();
            if ($executive_old) {
                $executive_old->deleted_at = null;
                $executive_old->save();
                return response()->json($executive_old, 200);
            }
            $executive = new Executive();
            $executive->name = $request->name;
            $executive->save();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
            // return response()->json(['erro' => 'Não foi possivel criar esse registro.'], 500);
        }
    }

    public function delete($id)
    {
        try {
            $executive = Executive::findOrFail($id);
            $executive->deleted_at = now();
            $executive->save();
            return response()->json(['message' => 'Gerência excluída com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Não foi possivel apagar esse registro.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $executive = Executive::findOrFail($id);
            if ($request->has('name')) $executive->name = $request->name;
            $executive->save();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Não foi possivel atualizar esse registro.'], 500);
        }
    }
}
