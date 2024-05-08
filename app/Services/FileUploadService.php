<?php

namespace App\Services;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use App\Models\FilesItens;

class FileUploadService
{
    // protected $client;

    public function __construct()
    {
        // $this->client = new Client();
    }

    public function uploadByName($checklist, $uploadedFiles)
    {

        
        // Initialize arrays to store results
        $successFiles = [];
        $failedFiles = [];
    


        foreach ($uploadedFiles as $file) {
            $valid = false;
            $matchedItem = '';
    
            // Loop through checklist items to validate the file name
            $checklistItems = $checklist->itens;
            foreach ($checklistItems as $item) {
                if (stripos($file->getClientOriginalName(), $item->file_name->standard_file_naming) !== false) {
                    $valid = true;
                    $item_name_id = $item->file_name->id; 
                    $item_name = $item->file_name->name; 
                    break;
                }
            }
    
            // If file does not match any checklist item, add to failed files array
            if (!$valid) {
                $failedFiles[] = $file->getClientOriginalName();
            } else {
               
              


                // Process the file 

                $storagePath = $checklist->contract->alias . '/' . date('Y/m-Y');

                // Store the file in SharePoint
                $filePath = $file->store($storagePath, 'sharepoint');
                $fileContent = file_get_contents($file->path()); // Get file contents
                $saveFile = Storage::disk('sharepoint')->put($storagePath . '/' . $file->getClientOriginalName(), $fileContent);

                // Update the database
                $newFile = File::create([
                    'path' => $storagePath . '/' . $file->getClientOriginalName()
                ]);

                // Associate the file with the checklist item
                $filesItem = FilesItens::create([
                    'item_id' => $item->id,
                    'file_id' => $newFile->id
                ]);

                
                $successFiles[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'item_name' => $item_name,
                    'item_name_id' => $item_name_id,
                    // 'file_path' => Storage::disk('sharepoint')->read($filePath)
                ];

                
            }
        }
    
        // Prepare response with success and failed files
        $message = count($successFiles) > 0 ? 'Arquivos enviados com sucesso (' . count($successFiles) . ' de ' . count($uploadedFiles) . ')' : 'Nenhum arquivo enviado com sucesso';
         $response = [
            'message' => $message,
            'successFiles' => $successFiles,
            'failedFiles' => $failedFiles
        ];

        return $response;
    }


}
