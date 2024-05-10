<?php

namespace App\Services;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use App\Models\FilesItens;
use App\Models\User;

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


    public function uploadByUserRegister($checklist, $item_id, $uploadedFiles)
    {

        
        // Initialize arrays to store results
        $successFiles = [];
        $failedFiles = [];
    


        foreach ($uploadedFiles as $file) {
            $valid = false;
            $matchedItem = '';
    
            // Loop through checklist items to validate the file name

            $filenameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            if (!is_numeric($filenameWithoutExtension)) {
                $failedFiles[] = $file->getClientOriginalName();
            } 
            else {
            
                $check_user =  User::where('register', $filenameWithoutExtension)->where('status', 'Ativo');

          
                if(!$check_user->exists()){

                    $failedFiles[] = $file->getClientOriginalName();
                } else{

                    $user = $check_user->first();

            
                    $checklistItems = $checklist->itens;
                    foreach ($checklistItems as $item) {
                
                        if (intval($item_id) === $item->id) {
                            $valid = true;
                            $item_name_id = $item->file_name->id; 
                            $item_name = $item->file_name->name; 
                            $standard_file_name = $item->file_name->standard_file_naming; 
                            break;
                        }
                    }
            
                
                    // Split the name into parts
                    $parts = explode(" ",  $user->name);
                    $firstName = ucfirst(strtolower($parts[0])); // Capitalize first name
                    $lastName = ucfirst(strtolower(end($parts))); // Capitalize last name
                    $name = $firstName . $lastName;
        
                    $newFileName =  $standard_file_name . '_' .  $name;


                    // Process the file 

                    $storagePath = $checklist->contract->alias . '/' . date('Y/m-Y');

                    // Store the file in SharePoint
                    $filePath = $file->storeAs($storagePath, $newFileName, 'sharepoint');
                    $fileContent = file_get_contents($file->path()); // Get file contents
                    $saveFile = Storage::disk('sharepoint')->put($storagePath . '/' . $newFileName . '.' . $file->extension(), $fileContent);

                    // Update the database
                    $newFile = File::create([
                        'path' => $storagePath . '/' . $newFileName. '.' . $file->extension()
                    ]);

                    // Associate the file with the checklist item
                    $filesItem = FilesItens::create([
                        'item_id' => $item->id,
                        'file_id' => $newFile->id
                    ]);

                        $successFiles[] = [
                            'file_name' => $newFileName. '.' . $file->extension(),
                            'item_name' => $item_name,
                            'item_name_id' => $item_name_id,
                            // 'file_path' => Storage::disk('sharepoint')->read($filePath)
                        ];

                }
                
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
