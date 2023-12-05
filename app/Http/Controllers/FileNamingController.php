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

        $file_naming = FileNaming::all();

        return response()->json($file_naming, 200);
    }

    public function getByID(Request $request)
    {
        $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
        if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo', 'Analista', 'Rh', 'Fin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

        $file_naming = FileNaming::find($request->id);

        return response()->json($file_naming, 200);
    }

    public function store(Request $request)
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $file_naming = new FileNaming();
            $file_naming->file_name = trim($request->file_name);
            $file_naming->standard_file_naming = trim($request->standard_file_naming);
            $file_naming->save();

            return response()->json($file_naming, 200);

        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível atualizar, tente novamente mais tarde.'], 500);
        }
    }

    public function update(Request $request)
    {
        try {

            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $file_naming = FileNaming::find($request->id_file_naming);

            if (!$file_naming)
                return response()->json(['error' => 'Nomenclatura não encontrada'], 404);

            $file_naming->file_name = trim($request->file_name);
            $file_naming->standard_file_naming = trim($request->standard_file_naming);
            $file_naming->save();

            $file_naming = FileNaming::find($request->id_file_naming);

            return response()->json($file_naming, 200);

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

            $file_naming = FileNaming::find($request->id_file_naming);

            if (!$file_naming)
                return response()->json(['error' => 'Nomenclatura não encontrada'], 404);

            $file_naming->delete();

            return response()->json('Nomenclatura excluída.', 200);

        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível atualizar, tente novamente mais tarde.'], 500);
            // return response()->json(['error'=> $exception->getMessage()], 500);
        }
    }


}
