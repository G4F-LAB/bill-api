<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FileNaming;
use App\Models\Item;
use App\Models\FileType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileNamingController extends Controller
{

    public function __construct(User $user) {
        $this->user = $user->getAuthUser();
        $this->allow_types = ['Admin', 'Operação', 'Executivo', 'Analista', 'RH', 'Financeiro'];

    }

    public function getAll()
    {
        // Validate permissions
        if (!in_array($this->user->type, $this->allow_types)){
            return response()->json(['error' => 'Acesso não permitido.'], 403);
        }



            $file_naming = FileNaming::with('type');

            if (request('filter')) {
                $file_naming->whereRaw('LOWER(file_name) LIKE ?', ['%' . strtolower(request('filter')) . '%']);
            }

            $file_naming = $file_naming->get();


        return response()->json($file_naming, 200);
    }

    public function getByID(Request $request)
    {
        $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
        if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo', 'Analista', 'Rh', 'Fin']))
            return response()->json(['error' => 'Acesso não permitido.'], 403);

        $file_naming = FileNaming::find($request->id);

        return response()->json($file_naming, 200);
    }

    public function getFileCatogary(Request $request)
    {
        $file_type = FileType::All();

        return response()->json($file_type, 200);
    }

    public function store(Request $request)
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin,Executivo,Operacao,Analista,Rh,Fin']))
                return response()->json(['error' => 'Acesso não permitido.'], 403);

            $file_naming = new FileNaming();
            $file_naming->file_name = trim($request->file_name);
            $file_naming->standard_file_naming = trim($request->standard_file_naming);
            $file_naming->group = trim($request->group);
            $file_naming->file_type_id = trim($request->file_type_id);
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
            if (!$colaborador->hasPermission(['Admin']))
                return response()->json(['error' => 'Acesso não permitido.'], 403);



            }


            $file_naming = FileNaming::find($request->id);
            if (!$file_naming) {
                return response()->json(['error' => 'Nomenclatura não encontrada'], 404);

            } else{

                $file_naming->file_name = trim($request->file_name);
                $file_naming->group = trim($request->group);
                $file_naming->standard_file_naming = trim($request->standard_file_naming);
                $file_naming->file_type_id = trim($request->file_type_id);
                $file_naming->save();

            }

            return response()->json(['message' => 'Nomeclatura atualizada com sucesso'], 200);

        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível atualizar, tente novamente mais tarde.'], 500);
            // return response()->json(['error'=> $exception->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {

            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin']))
                return response()->json(['error' => 'Acesso não permitido.'], 403);

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

    public function getAllRelCheklist(Request $request)
    {
        $id_checklist = $request->id;
        $filenaming_registered = Item::where('checklist_id', $id_checklist)->pluck('file_naming_id')->toArray();
        $file_naming = FileNaming::whereNotIn('id', $filenaming_registered)->get();

        $dataGrouped = [];

        foreach ($file_naming as $item) {
            $group = $item['group'];
            if (!array_key_exists($group, $dataGrouped)) {
                $dataGrouped[$group] = [];
            }
            $dataGrouped[$group][] = $item;
        }

        $dataGroupedFormatted = [];
        foreach ($dataGrouped as $group => $items) {
            $grupoFormatado = [
                'group' => $group,
                'items' => $items,
            ];
            $dataGroupedFormatted[] = $grupoFormatado;
        }

        return response()->json($dataGroupedFormatted, 200);
    }

}
