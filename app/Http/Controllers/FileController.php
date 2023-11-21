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

            if(is_array($file))
            {
                foreach($file as $archive) {
                    $result = self::saveChecklistFiles($checklist_id, $items, $archive);
                }
            }
            else 
            {
                $result = self::saveChecklistFiles($checklist_id, $items, $file);
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
        $items = array(
            (object) [
              'id' => '1',
              'file_naming_id' => 1
            ],
            (object) [
                'id' => '2',
                'file_naming_id' => 2
             ],
             (object) [
                'id' => '3',
                'file_naming_id' => 3
             ]
          );


        return $items;
    }

    function getChecklistFilesName($ids) {
        $names = FileNaming::pluck('standard_file_naming', 'id')->all();


        return $names;
    }


    function saveChecklistFiles($checklist_id, $item, $file){
        
        $filetype = $file->getClientOriginalExtension();
        $filename = substr($file->getClientOriginalName(), 0, -strlen($filetype) -1);
        
        
        $fileNames = self::getChecklistFilesName([]);

        $success = array();
        $erros = array();

        foreach ($fileNames as $name) {

            if (strpos($filename, $name) !== FALSE) {
                $filenameplus = str_replace($name, '', $filename);
                // echo '<pre>';
                // echo $filename;
                // echo '<pre>';
                // echo $filenameplus;
                // echo '<pre>';
                // echo $key = array_search ($name, $fileNames);
                
                $item_id = 7;
 
                $path = "/$this->env/book/checklists/$checklist_id/". $file->getClientOriginalName(); 


                if($upload = Storage::disk('s3')->put($path, file_get_contents($file))){

                    array_push($success, ['file_id'=> NULL, 'item_id' => $item_id, 'name' => $filename]);
                    try {
                        $saveFile = File::updateOrCreate(
                            ['complementary_name' =>  $filenameplus],
                            ['path' => $path],
                            ['item_id' => $item_id]
                        );
                    } catch (\Throwable $th) {
                        //throw $th;
                        array_push($erros, ['message' => 'Erro ao salvar no banco de dados', 'name' => $name]);
                    }
                    
                } else{
                    array_push($erros, ['message' => 'Error ao subir arquivo', 'name' => $name]);
                } 
    

            }else{
                // array_push($erros, ['name' => $name]);
            }
        }

        return [
                'success' => $success,
                'erros' => $erros
          ];
      
    }
 
}
