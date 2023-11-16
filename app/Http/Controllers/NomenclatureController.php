<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Nomenclature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NomenclatureController extends Controller
{
    public function getAll()
    {
        $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
        if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo', 'Analista', 'Rh', 'Fin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

        $nomenclatures = Nomenclature::all();

        return response()->json($nomenclatures, 200);
    }

    public function create(Request $request)
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $nomenclature = new Nomenclature();
            $nomenclature->nome_arquivo = $request->nome_arquivo;
            $nomenclature->nomeclatura_padrao_arquivo = $request->nomenclatura_padrao_arquivo;

            $nomenclature->save();
            return response()->json($nomenclature, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível criar, tente novamente mais tarde.'], 500);
            // return response()->json(['error'=> $exception->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {

            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $nomenclatura = Nomenclature::find($request->id_nomenclatura);

            if (!$nomenclatura) return response()->json(['error'=> 'Nomenclatura não encontrada'], 404);


        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível atualizar, tente novamente mais tarde.'], 500);
            // return response()->json(['error'=> $exception->getMessage()], 500);
        }
    }
}
