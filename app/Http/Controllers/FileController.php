<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileNaming;
use App\Models\Item;
use App\Models\File;
use Illuminate\Support\Facades\Storage;



class FileController extends Controller
{
    public function __construct() {
        $this->env = env('APP_ENV') ? env('APP_ENV') : 'developer';
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
        $names = FileNaming::whereIn('id', $ids)->pluck('standard_file_naming', 'id')->all();
        return $names;
    }


    function saveChecklistFiles($checklist_id, $items, $file){
        
        $filetype = $file->getClientOriginalExtension();
        $filename = substr($file->getClientOriginalName(), 0, -strlen($filetype) -1);
        
        $fileNames = self::getChecklistFilesName($items);
        
        $data = ['status' => 'Error', 'message'=> 'Não é um nome de arquivo válido para este checklist','name' => $filename];

        foreach ($fileNames as $name) {

            if (strpos($filename, $name) !== FALSE) {
                $filenameplus = str_replace($name, '', $filename);

                $file_naming_id = array_search($name, $fileNames);
                $item_id = array_search($file_naming_id , $items->toArray());

                $path = "/$this->env/book/checklists/$checklist_id/". $file->getClientOriginalName(); 

                if($upload = Storage::disk('s3')->put($path, file_get_contents($file), 'public')){
                    try {
                        $saveFile = File::updateOrCreate(
                            ['item_id' => $item_id, 'path' => $path, 'complementary_name' => $filenameplus],
                        );
                        $data = ['status' => 'Ok', 'item_id' => $item_id, 'file_id'=> $saveFile->id, 'file_url'=> env('AWS_URL').$path, 'name' => $filename];
                    } catch (\Throwable $th) {
                        //throw $th;
                        $data = ['status' => 'Error', 'message'=> 'Error ao salvar arquivo no banco','name' => $filename];
                    }
                    
                } else{
                    $data = ['status' => 'Error', 'message'=> 'Error ao subir arquivo','name' => $name];
                } 

            }else{
                $data = ['status' => 'Error', 'message'=> 'Não é um nome de arquivo válido','name' => $filename];

            }

        }
        return $data;
      
    }
 
}
