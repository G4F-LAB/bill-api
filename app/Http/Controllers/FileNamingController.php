<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\FileNaming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileNamingController extends Controller
{
    public function getAll()
    {
        $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
        if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo', 'Analista', 'Rh', 'Fin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

        $nomenclatures = FileNaming::all();

        return response()->json($nomenclatures, 200);
    }

    public function create(Request $request)
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $nomenclature = new FileNaming();
            $nomenclature->file_name = $request->file_name;
            $nomenclature->standard_file_naming = $request->standard_file_naming;

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

           $nomenclatura = FileNaming::find($request->id_file_naming);
           
           if (!$nomenclatura) return response()->json(['error'=> 'Nomenclatura não encontrada'], 404);
           
           $nomenclatura->file_name = $request->file_name;
           $nomenclatura->standard_file_naming = $request->standard_file_naming;
           $nomenclatura->save();   

           $nomenclatura = FileNaming::find($request->id_file_naming);

            return response()->json($nomenclatura, 200);

        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível atualizar, tente novamente mais tarde.'], 500);
            // return response()->json(['error'=> $exception->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {

            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

           $nomenclatura = FileNaming::find($request->id_nomenclatura);
           
           if (!$nomenclatura) return response()->json(['error'=> 'Nomenclatura não encontrada'], 404);
          
           $nomenclatura->delete();   

           return response()->json('Nomenclatura excluída.', 200);

        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível atualizar, tente novamente mais tarde.'], 500);
            // return response()->json(['error'=> $exception->getMessage()], 500);
        }
    }


}
