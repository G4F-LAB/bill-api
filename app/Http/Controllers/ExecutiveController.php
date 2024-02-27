<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Executive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExecutiveController extends Controller
{
    public function getAll()
    {
        try {
            $executive = Executive::with('operations.collaborator')->get();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function getById()
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();

            $executive = Executive::with(['operations' => function ($query) {
                $query->orderBy('id');
            }, 'operations.collaborator'])->where('manager_id','=', $colaborador->id)->get();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Não foi possivel encontrar os dados.'], 500);
        }
    }

    public function getAllManager()
    {
        try {
            $executive = Collaborator::with('operations.executives')->get();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function getAllExecutives(Request $request)
    {
        try {
            $executive = Executive::with('manager')->where([
                [function ($query) use ($request) {
                    if (($s = $request->q)) {
                        $query->orWhere('name', 'LIKE', '%' . $s . '%')
                            ->get();
                    }
                }],

            ])->orderBy('id','ASC')->get();
            return response()->json($executive, 200);

        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }
    public function create(Request $request)
    {
        try {
            $manager = Collaborator::find($request->manager_id);

            if (empty($manager)) return response()->json(['erro' => 'Colaborador não encontrado'], 200);

            $executive_old = Executive::where('name', 'like', '%' . $request->name . '%')->withTrashed()->first();
            if ($executive_old) {
                $executive_old->deleted_at = null;
                $executive_old->save();
                return response()->json($executive_old, 200);
            }
            $executive = new Executive();
            $executive->name = $request->name;
            $executive->manager_id = $request->manager_id;
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
            $executive->delete();
            return response()->json(['message' => 'Gerência excluída com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => 'Não foi possivel apagar esse registro.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $executive = Executive::findOrFail($id);
            if ($request->name) $executive->name = $request->name;
            if ($request->has('manager_id')) $executive->manager_id = $request->manager_id;
            $executive->save();
            return response()->json($executive, 200);
        } catch (\Exception $e) {
            var_dump($e);
            return response()->json(['erro' => 'Não foi possivel atualizar esse registro.'], 500);
        }
    }
}
