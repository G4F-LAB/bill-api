<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FileNaming;
use App\Models\FileName;
use App\Models\Item;
use App\Models\FileType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileNamingController extends Controller
{

    public function __construct(User $user)
    {
        $this->user = $user->getAuthUser();
        $this->allow_types = ['Admin', 'Operação', 'Executivo', 'Analista', 'RH', 'Financeiro'];
    }

    public function getAll()
    {
        // Validate permissions
        if (!in_array($this->user->type, $this->allow_types)) {
            return response()->json(['error' => 'Acesso não permitido.'], 403);
        }



        $file_naming = FileName::with('type');

        if (request('filter')) {
            $file_naming->whereRaw('LOWER(file_name) LIKE ?', ['%' . strtolower(request('filter')) . '%']);
        }

        $file_naming = $file_naming->get();


        return response()->json($file_naming, 200);
    }

    public function getByID(Request $request)
    {
        // $user = User::where('taxvat', Auth::user()['employeeid'])->first();
        // if (!$user->hasPermission(['Admin', 'Operação', 'Executivo', 'Analista', 'RH', 'Financeiro']))
        //     return response()->json(['error' => 'Acesso não permitido.'], 403);

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
            // $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            // if (!$user->hasPermission(['Admin', 'Operação', 'Executivo', 'Analista', 'RH', 'Financeiro']))
            //     return response()->json(['error' => 'Acesso não permitido.'], 403);

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

            // $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            // if (!$user->hasPermission(['Admin', 'Operação', 'Executivo', 'Analista', 'RH', 'Financeiro']))
            //     return response()->json(['error' => 'Acesso não permitido.'], 403);

            $file_naming = FileNaming::find($request->id);
            if (!$file_naming) {
                return response()->json(['error' => 'Nomenclatura não encontrada'], 404);
            } else {
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

            // $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            // if (!$user->hasPermission(['Admin', 'Operação', 'Executivo', 'Analista', 'RH', 'Financeiro']))
            //     return response()->json(['error' => 'Acesso não permitido.'], 403);

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

        // return 'ok';
        $id_checklist = $request->id;
        $filenaming_registered = Item::where('checklist_id', $id_checklist)->pluck('file_name_id')->toArray();
        $file_naming = FileName::whereNotIn('id', $filenaming_registered)->get();
        // return $file_naming;

        $dataGrouped = [];

        foreach ($file_naming as $item) {
            $group = $item['file_group'];
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
