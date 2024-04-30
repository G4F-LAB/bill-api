<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Checklist;
use App\Models\File;
use App\Models\FileNaming;
use App\Models\FilesItens;
use App\Models\Notification;
use App\Models\User;
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
            $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            if (!$user->hasPermission(['Admin', 'Operação', 'Executivo', 'Analista', 'Rh', 'Financeiro']))
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
            $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            if (!$user->hasPermission(['Admin', 'Operação', 'Executivo', 'Analista', 'Rh', 'Financeiro']))
                return response()->json(['error' => 'Acesso não permitido.'], 403);
            $itens = Item::find($id);
            return response()->json($itens, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Não foi possivel acessar os Itens'], 500);
        }
    }

    public function store(Request $request)
    {

        // return $request->file_naming_id;

        $data = [
            "file_naming_id" => $request->file_naming_id,
            "checklist_id" => $request->checklist_id,
            //"status" => $request->status,
            "file_competence_id" => $request->file_competence_id,
            "mandatory" => true
        ];
        $addItems = $this->addItems($data);
        // return $addItems;
        if (isset($addItems['errors'])) {
            return response()->json($addItems, 200);
        }
        return response()->json(['message' => 'Item(s) adicionado(s) com sucesso'], 200);
    }


    public function addItems($data)
    {

        // return $data;

        try {
            $errors = [];
            $errors['status'] = 'Error';
            $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            $notification = new NotificationController($user);
            $data_notification = new Notification();

            foreach ($data['file_naming_id'] as $file_naming_id) {
                $item = Item::where('checklist_id', $data['checklist_id'])->where('file_naming_id', $file_naming_id)->first();

                if (!empty($item)) {
                    $errors['errors'][] = [
                        'message' => 'O item com o nome escolhido já existe para esse checklist!',
                        'Item' => $item
                    ];
                    continue;
                }


                $this->item = new Item();
                $this->item->status = false;
                $this->item->file_naming_id = $file_naming_id;
                $this->item->file_competence_id = $data['file_competence_id'];
                $this->item->checklist_id = $data['checklist_id'];
                $this->item->save();

                $checklist = Checklist::where('id', $this->item->checklist_id)->first();
                $data_notification->desc_id = 1;
                $data_notification->notification_cat_id = 3;
                $data_notification->contract_id = $checklist['contract_uuid'];
                $data_notification->notification_type_id = 1;
                $notification->registerNotification($data_notification);

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
                    //Acredito que aqui é so colocar um $this->item->mandatory = true; ai no caso ele so vai contar também os status de true.
                    $this->item->save();
                    $checklist->sync_itens();
                }
            }

            if ($errors['errors']) {
                return $errors;
            } else {
                return 'Atualizdo com sucesso';
            }
            

        } catch (\Exception $e) {
            return ['error' => 'Houve um erro interno na aplicação'];
        }
    }


    public function updateCompetence(Request $request, string $id)
    {
        try {
            $this->item = Item::find($id);
            if ($request->has('file_competence_id')) $this->item->file_competence_id = $request->file_competence_id;
            $this->item->save();

            return response()->json(['message' => 'Competência atualizada com sucesso'], 200);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            $notification = new NotificationController($user);
            $data_notification = new Notification();
            $this->item = Item::find($id);

            if (!$this->item) {
                return response()->json([
                    'error' => 'Item não encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
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
            if ($request->has('mandatory')) {
                $this->item->mandatory = $request->mandatory;
            }
            $this->item->save();

            $checklist = Checklist::find($this->item->checklist_id);
            //Notification
            $data_notification->desc_id = 2;
            $data_notification->notification_cat_id = 3;
            $data_notification->contract_id = $checklist->contract_uuid;
            $data_notification->notification_type_id = 1;
            $notification->registerNotification($data_notification);

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
            $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            $notification = new NotificationController($user);
            $data_notification = new Notification();
            $item = Item::find($id);
            $checklist = Checklist::where('id', $item->checklist_id)->first();
            if (!$item) {
                return response()->json([
                    'error' => 'Not Found'
                ], Response::HTTP_NOT_FOUND);
            }
            $item->id = $id;
            $item->deleted_at = date('Y/m/d H:i');
            $item->save();
            $checklist->sync_itens();
            $data_notification->desc_id = 3;
            $data_notification->notification_cat_id = 3;
            $data_notification->contract_id = $checklist['contract_uuid'];
            $data_notification->notification_type_id = 1;
            $notification->registerNotification($data_notification);
            return response()->json(['message' => 'Item deletado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function exportFiles(Request $request)
    {
        if (!empty($request->ids)) {
            $id_itens = explode(',', $request->ids);
            $files = [];

            foreach ($id_itens as $id_item) {
                $file_itens = FilesItens::where('item_id', $id_item)->get()->toArray();
                if (!empty($file_itens)) {

                    foreach ($file_itens as $file_item) {
                        $item = FileNaming::with('items')
                            ->whereHas('items', function ($query) use ($file_item) {
                                $query->where('id', $file_item['item_id']);
                            })
                            ->pluck('group')->toArray();
                        $files[] = [
                            'path' => File::where('id', $file_item['file_id'])->first()->toArray()['path'],
                            'group' => $item[0]
                        ];
                    }
                }
            }

            $zip = new \ZipArchive();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition', 'attachment; filename=itens.zip');

            if ($zip->open(storage_path('app/itens.zip'), \ZipArchive::CREATE) === TRUE) {
                foreach ($files as $file) {
                    if (Storage::disk('s3')->exists($file['path'])) {
                        $tempImage = tempnam(sys_get_temp_dir(), basename($file['path']));
                        copy(env('AWS_URL') . $file['path'], $tempImage);
                        $zip->addFile($tempImage, $file['group'] . '/' . basename($file['path']));
                    }
                }
                $zip->close();
                return response()->download(storage_path('app/itens.zip'))->deleteFileAfterSend(true);
            }
        }
    }
}
