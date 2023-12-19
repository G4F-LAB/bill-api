<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileNaming;
use App\Models\Collaborator;
use App\Models\Item;
use App\Models\File;
use App\Models\FileType;
use App\Models\Checklist;
use App\Models\Collaborator;
use Illuminate\Support\Facades\Storage;



class FileController extends Controller
{
    public function __construct(Collaborator $collaborator) {
        $this->env = env('APP_ENV') ? env('APP_ENV') : 'developer';
        $this->auth_user = $collaborator->getAuthUser();
    }

    public function uploadChecklistFiles(Request $request) {

        $checklist_id = (int)$request->id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $items = $this->getChecklistItems($checklist_id);
            $result = array();
            if(is_array($file))
            {
                foreach($file as $archive) {
                    array_push($result, self::saveChecklistFiles($checklist_id, $items, $archive));
                }
            }
            else
            {
                array_push($result, self::saveChecklistFiles($checklist_id, $items, $file));
            }


            $response = [
                'checklist_id' => $checklist_id,
                'files' => $result
            ];

            return response()->json($response, 200);

        }else{
            return response()->json(['error'=>'Nenhum arquivo enviado.'], 422);
        }

    }


    function getChecklistItems($checklist_id) {
        $items = Item::where('checklist_id',  $checklist_id )->pluck('file_naming_id', 'id');
        return $items;
    }

    function getChecklistFilesName($ids) {
        $names = FileNaming::whereIn('id', $ids)->select('id','standard_file_naming','file_type_id')->get()->toArray();
        return $names;
    }


    function saveChecklistFiles($checklist_id, $items, $file){

        $filetype = $file->getClientOriginalExtension();
        $filename = substr($file->getClientOriginalName(), 0, -strlen($filetype) -1);

        $fileNames = self::getChecklistFilesName($items);
        $data = ['status' => 'Error', 'message'=> 'Não é um nome de arquivo válido para este checklist','name' => $filename];

        $permission = $this->auth_user->getAuthUserPermission();
        foreach ($fileNames as $fileAttrs) {

            $name = $fileAttrs['standard_file_naming'];
            $id = $fileAttrs['id'];
            $file_type_id = $fileAttrs['file_type_id'];

            // se o nome do arquivo estiver na lista de nomes (presentes no banco)
            if (strpos($filename, $name) !== FALSE) {
                
                $file_type = FileType::find($file_type_id);
                // recupera as regras de upload baseado no tipo do arquivo (file_type)
                $rules = FileType::uploadRules()[$file_type->files_category];
                
                // verifica se o usuário logado tem permissão para inserir o arquivo
                if(in_array($permission->name,$rules)) {

                    // retira o nome adicional enviado
                    $filenameplus = str_replace($name, '', $filename);
                    $file_naming_id = $id; //array_search($name, $fileNames);

                    // busca o id do item associado ao nome do arquivo
                    $item_id = array_search($file_naming_id , $items->toArray());

                    $path = "/$this->env/book/checklists/$checklist_id/". $file->getClientOriginalName(); 

                    if($upload = Storage::disk('s3')->put($path, file_get_contents($file), 'public')){
                        try {
                            // salva o arquivo com o id do item, o camnho e o nome complementar/adicional
                            $saveFile = File::updateOrCreate(
                                ['item_id' => $item_id, 'path' => $path, 'complementary_name' => $filenameplus],
                            );
                            Item::where('id', $item_id)->update(['status' => true]);

                            $checklist = Checklist::find($checklist_id);
                            $checklist->sync_itens();

                            $data = ['status' => 'Ok', 'item_id' => $item_id, 'file_id'=> $saveFile->id, 'file_url'=> env('AWS_URL').$path, 'name' => $filename];
                        } catch (\Throwable $th) {
                            //throw $th;
                            $data = ['status' => 'Error', 'message'=> 'Error ao salvar arquivo no banco','name' => $name];
                        }
                        
                    } else{
                        $data = ['status' => 'Error', 'message'=> 'Error ao subir arquivo','name' => $name];
                    }
                } else {
                    $data = ['status' => 'Error', 'message'=> 'Você não tem permissão para subir esse arquivo','name' => $name];
                }

                break;
            }else{
                $data = ['status' => 'Error', 'message'=> 'Não é um nome de arquivo válido','name' => $filename];

            }

        }
        return $data;

    }

}
