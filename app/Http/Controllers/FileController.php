<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nomenclature;
use Illuminate\Support\Facades\Storage;



class FileController extends Controller
{
    public function upload(Request $request) {
          

        if ($request->hasFile('arquivo')) {
            $file = $request->file('arquivo');
            $filetype = $file->getClientOriginalExtension();
            $filename = substr($file->getClientOriginalName(), 0, -strlen($filetype) -1);
            
            $kinds = Nomenclature::pluck('nomeclatura_padrao_arquivo')->all();
          
            if (in_array($filename, $kinds)) {

                $env = env('APP_ENV') ? env('APP_ENV') : 'developer';
                $path = "/$env/book/items/$request->item_id/". $file->getClientOriginalName(); //Definir padrão
        
                try {
                    $upload = Storage::disk('s3')->put($path, file_get_contents($file));

                    $namenclature_id = Nomenclature::where('nomeclatura_padrao_arquivo', $filename)->first()->id_nomeclatura_arquivo;
                    $kind_file_id = [];
                    dd($namenclature_id);
                    return response()->json($upload, 200);
                } catch (\Throwable $th) {

                    return response()->json(['error'=> $th], 422);
                }

                
            } else {
                return response()->json(['error'=>'Nome do arquivo inválido'], 422);
            }
   
            dd($file->getClientOriginalExtension());
        }else{
            return response()->json(['error'=>'arquivo não encontrado na requisição.'], 422);
        }

    }
 
}
