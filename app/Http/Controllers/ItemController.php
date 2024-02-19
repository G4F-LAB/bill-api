<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Item;
use App\Models\Checklist;
use App\Models\File;
use App\Models\FileNaming;
use App\Models\FilesItens;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public $item = '';

    public function __construct(Item $item = null)
    {
        $this->item = $item;
    }


    public function show()
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo', 'Analista', 'Rh', 'Fin']))
                return response()->json(['error' => 'Acesso não permitido.'], 403);
            $itens = Item::all();
            return response()->json($itens, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Não foi possivel acessar os Itens'], 500);
        }
    }


    public function getbyID(string $id)
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo', 'Analista', 'Rh', 'Fin']))
                return response()->json(['error' => 'Acesso não permitido.'], 403);
            $itens = Item::find($id);
            return response()->json($itens, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Não foi possivel acessar os Itens'], 500);
        }

    }

    public function store(Request $request)
    {
        $data = [
            "file_naming_id" => $request->file_naming_id,
            "checklist_id" => $request->checklist_id,
            "status" => $request->status,
            "file_competence_id" => $request->file_competence_id
        ];

        $addItems = $this->addItems($data);

        if (isset($addItems['error'])) {
            return response()->json($addItems, 200);
        }

        return response()->json(['message' => 'Item(s) atualizado(s) com sucesso'], 200);
    }


    public function addItems($data)
    {
        $errors = [];
        $errors['status'] = 'Error';

        foreach ($data['file_naming_id'] as $file_naming_id) {

            $item = Item::where('checklist_id', $data['checklist_id'])->where('file_naming_id', $file_naming_id)->first();
            if (!empty($item)) {
                $errors['errors'][] = [
                    'message' => 'O item com o nome escolhido já existe para esse checklist!',
                    'Item' => $item
                ];
            }

            if (!empty($errors['errors']))
                return response()->json($errors, 422);

            try {
                $this->item = new Item();

                $this->item->status = false;
                $this->item->status = false;
                $this->item->file_naming_id = $file_naming_id;
                $this->item->file_competence_id = $data['file_competence_id'];
                $this->item->checklist_id = $data['checklist_id'];
                $this->item->save();

                $checklist = Checklist::where('id', $this->item->checklist_id)->first();
                $sub_months = NULL;

                if ($this->item->file_competence_id == 1) {
                    $sub_months = 2;
                } elseif ($this->item->file_competence_id == 2) {
                    $sub_months = 1;
                }

                $date = Carbon::createFromFormat('Y-m-d', $checklist->date_checklist)->startOfMonth();
                $date = $date->subMonths($sub_months)->format('Y-m');

                $files = File::where('path', 'ilike', "%$date%")->get()->toArray();
                $item_name = FileNaming::where('id', $this->item->file_naming_id)->first();

                $file_found = null;

                foreach ($files as $file) {
                    $file_name = substr($file['path'], strrpos($file['path'], '/') + 1);
                    if (strpos($file_name, $item_name->standard_file_naming) !== FALSE) {
                        $file_found = File::find($file['id']);
                        break;
                    }
                }

                if (!empty($file_found)) {
                    $file_found->itens()->attach($this->item->id);
                    $this->item->status = true;
                    $this->item->save();
                    $checklist->syncItens();
                }

            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }

        }

        return 'Item(s) adicionado(s) com sucesso';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $this->item = Item::find($id);

            // print_r($this->item);exit;

            if (!$this->item) {
                return response()->json([
                    'error' => 'Item não encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            // if($this->item->isEmpty()){
            //     return response()->json([
            //         'error' => 'Item não encontrado'
            //     ], Response::HTTP_NOT_FOUND);
            // }

            if ($request->has('id'))
                $this->item->id = $request->id;
            if ($request->has('status'))
                $this->item->status = $request->status;
            if ($request->has('file_naming_id'))
                $this->item->file_naming_id = $request->file_naming_id;
            if ($request->has('file_type_id'))
                $this->item->file_type_id = $request->file_type_id;
            if ($request->has('file_competence_id'))
                $this->item->file_competence_id = $request->file_competence_id;
            if ($request->has('checklist_id'))
                $this->item->checklist_id = $request->checklist_id;
            $this->item->save();

            // $checklist = Checklist::find($this->item->checklist_id);
            // $checklist->sync_itens();

            return response()->json(['message' => 'Item atualizado com sucesso'], 200);

        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = Item::find($id);
            //dd($item);
            if (!$item) {
                return response()->json([
                    'error' => 'Not Found'
                ], Response::HTTP_NOT_FOUND);
            }

            $item->id = $id;
            $item->deleted_at = date('Y/m/d H:i');
            $item->save();

            return response()->json(['message' => 'Item deletado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }

    }

    public function exportFiles(Request $request) {
        if(!empty($request->ids)){
            $id_itens = explode(',',$request->ids);
            $files = [];

            foreach($id_itens as $id_item) {
                $file_itens = FilesItens::where('item_id',$id_item)->get()->toArray();
                if(!empty($file_itens)) {
                    
                    foreach($file_itens as $file_item) {
                        $files[] = File::where('id', $file_item['file_id'])->first()->toArray()['path'];
                    }
                }
            }

            $zip = new \ZipArchive();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition', 'attachment; filename=itens.zip');

            if($zip->open(storage_path('app/itens.zip'), \ZipArchive::CREATE) === TRUE) {
                foreach($files as $path) {
                    if(Storage::disk('s3')->exists($path)) {
                        $tempImage = tempnam(sys_get_temp_dir(), basename($path));
                        copy(env('AWS_URL').$path, $tempImage);
                        $zip->addFile($tempImage,basename($path));
                    }
                }

                $zip->close();
                return response()->download(storage_path('app/itens.zip'));
            }

        }
    }
}
