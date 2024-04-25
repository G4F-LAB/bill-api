<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileNaming;
use App\Models\FilesItens;
use App\Models\Collaborator;
use App\Models\Item;
use App\Models\File;
use App\Models\FileType;
use App\Models\AutomacaoErrors;
use App\Models\Checklist;
use App\Models\Contract;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Services\SharePointService;

class FileController extends Controller
{
    // public function __construct(Collaborator $collaborator) {
    //     $this->env = env('APP_ENV') ? env('APP_ENV') : 'developer';
    //     $this->auth_user = $collaborator->getAuthUser();
    // }


    public function __construct(SharePointService $sharePointService)
    {
        $this->sharePointService = $sharePointService;
    }

    public function importRH(Request $request){
     

        try {
            // Storage::disk('sharepoint')->put('test.txt', 'testContentN7');
            $files = Storage::disk('sharepoint')->allFiles('/SGG/GDP.%E2%80%8B/CDP/03-Fopag/01-SEFIP_Conectividade Social/2024/03-2024/INSS');


            
            
            // // Output directories
            // foreach ($files as $file) {
            //     echo $file . "\n";
            // }
            return $files;
            
            
        } catch (\Exception $exception) {
            dd($exception);
        }
        return 'error';

        $clientId = config('services.sharepoint.client_id');
        $clientSecret = config('services.sharepoint.client_secret');
        $tenantId = config('services.sharepoint.tenant_id');
        $siteUrl = config('services.sharepoint.site_url');
        $siteId = 'g4fcombr.sharepoint.com,b0e923d7-1544-4e3b-8011-1f959cbbba2e,62824dcd-426d-4ece-8494-8909f6ce2de2';
        $driveId = 'b!1yPpsEQVO06AER-VnLu6Ls1NgmJtQs5OhJSJCfbOLeL30pMUsZWITZ7q9sYfinO1';
        $folderPath = 'your-folder-path';

        $accessToken = $this->sharePointService->generateAccessToken($clientId, $clientSecret, $tenantId);

        // Verificando se o access token foi gerado com sucesso
        if (!$accessToken) {
            return response()->json(['message' => 'Falha ao gerar o token de acesso'], 500);
        }

        // Obtendo os IDs do site e do drive
        // $siteAndDriveIds = $this->sharePointService->getSiteAndDriveIds($accessToken, $siteUrl);
        $siteAndDriveIds = $this->sharePointService->searchSites($accessToken);

// dd($siteAndDriveIds);
        // Verificando se os IDs do site e do drive foram obtidos com sucesso
        if (!$siteAndDriveIds) {
            return response()->json(['message' => 'Falha ao obter os IDs do site e do drive'], 500);
        }

        // // $siteId = $siteAndDriveIds['siteId'];
        // // $driveId = $siteAndDriveIds['driveId'];
        // $folderPath = '/'; // Substitua pelo caminho da pasta desejada

        // // Listando os arquivos na pasta especificada
        // $files = $this->sharePointService->listFiles($accessToken, $siteId, $driveId, $folderPath);

        // // Verificando se os arquivos foram listados com sucesso
        // if (!$files) {
        //     return response()->json(['message' => 'Falha ao listar os arquivos'], 500);
        // }

        return response()->json($siteAndDriveIds);
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
                                ['path' => $path, 'complementary_name' => $filenameplus],
                            );
                            //Item::where('id', $item_id)->update(['status' => true]);
                            $file_item = FilesItens::where('item_id', $item_id)->where('file_id',$saveFile->id)->first();

                            if(empty($file_item)) {
                                $saveFile->itens()->attach($item_id);
                                Item::where('id', $item_id)->update(['status' => true]);
                                $checklist = Checklist::find($checklist_id);
                                $checklist->sync_itens();
                            }

                            $data = ['status' => 'Ok', 'item_id' => $item_id, 'file_id'=> $saveFile->id, 'file_url'=> env('AWS_URL').$path, 'name' => $filename];
                        } catch (\Throwable $th) {
                            //throw $th;
                            $data = ['status' => 'Error', 'message'=> $th->getMessage(),'name' => $name];
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

    public function uploadDefaultFiles(Request $request) {
        $rules = [
            'file' => 'required|array'
        ];
        $request->validate($rules);

        $month = now()->format('m');
        $year = now()->format('Y');
        $files = $request->file;
        $data = [];
        $request_errors = [];
        $request_errors['status'] = 'Error';

        foreach($files as $index => $file_array) {
            if(!isset($file_array[0]) || empty($file_array[0])){
                $request_errors['errors'][] = [
                    'message' => "Índice de arquivos não existente ou vazio no array $index"
                ];
            }

            if(!isset($file_array[1]) || empty($file_array[1])){
                $request_errors['errors'][] = [
                    'message' => "Índice de competência não existente ou vazio no array $index"
                ];
            }
        }

        if(!empty($request_errors['errors'])) return response()->json($request_errors,200);

        /*
            array() = [
                [0] => [
                    '0' => ['arquivo1','arquivo2','arquivo3'],
                    '1' => 1
                ],
                [1] => [
                    '0' => ['arquivo1','arquivo2','arquivo3'],
                    '1' => 2
                ]
            ]
        */

        // busca de todos itens de checklist do mês atual
        $itens = Item::with('fileNaming','checklist','files')
                    ->whereHas('checklist', function($query) use($month,$year) {
                        $query->whereRaw('extract(month from date_checklist) = ? and extract(year from date_checklist) = ?',[$month,$year]);
                    })
                    ->whereHas('fileNaming', function($query) {
                        $query->where('default',true);
                    })
                    ->get()->toArray();

        $file_names = FileNaming::where('default',true)->pluck('standard_file_naming')->all();

        // [0] => [
        //     '0' => ['arquivo1','arquivo2','arquivo3'],
        //     '1' => 1
        // ],
        foreach($files as $file_array) {

            //'0' => ['arquivo1','arquivo2','arquivo3']
            foreach($file_array[0] as $archive) {

                $filetype = $archive->getClientOriginalExtension();
                $filename = substr($archive->getClientOriginalName(), 0, -strlen($filetype) -1);

                // referencia
                // 1 => Mês anterior (Mês atual - 2)
                // 2 => Mês de prestação de serviço (Mês atual - 1)
                $sub_months = '';
                if($file_array[1] == 1) {
                    $sub_months = 2;
                }elseif($file_array[1] == 2) {
                    $sub_months = 1;
                }

                // vigencia do arquivo
                //$ref_date = Carbon::createFromFormat('Y-m-d', '2023-12-01')->startOfMonth();
                $ref_date = now()->subMonths($sub_months)->format('Y-m');
                $path = '';
                if(stripos($filename,'CAGED') !== FALSE) {
                    $path = "/$this->env/book/default/". $archive->getClientOriginalName();
                }else {
                    $path = "/$this->env/book/default/$ref_date/". $archive->getClientOriginalName();
                }

                $date_archive = substr($path,strrpos($path, '/')-7,7);

                // se arquivo estiver na lista de arquivos default e não estiver inserido no repositório -> lógica de inserção
                // && !Storage::disk('s3')->exists($path)
                if(in_array($filename,$file_names)){
                    //echo "1 - $filename\n\n";
                    if($upload = Storage::disk('s3')->put($path, file_get_contents($archive), 'public')){
                        $saveFile = File::updateOrCreate(['path' => $path]);
                        $synchro_itens = [];

                        foreach($itens as $item) {

                            $sub_months = NULL;
                            if($item['file_competence_id'] == 1) {
                                $sub_months = 2;
                            }elseif($item['file_competence_id'] == 2) {
                                $sub_months = 1;
                            }

                            // pega a data de vigência do item a partir da data de checklist
                            $date_item = Carbon::createFromFormat('Y-m-d', $item['checklist']['date_checklist'])->startOfMonth();
                            $date_item = $date_item->subMonths($sub_months)->format('Y-m');

                            if(
                                $item['file_naming']['standard_file_naming'] == $filename // o nome do arquivo deve ser igual ao nome do item
                                && $date_item == $date_archive // a data de vigencia do checklist do item deve ser igual a data no caminho do arquivo
                            ){
                                $file_item = FilesItens::where('item_id', $item['id'])->where('file_id',$saveFile->id)->first();
                                if(empty($file_item)) {
                                    $saveFile->itens()->attach($item['id']);
                                    $item_copy = $item;
                                    unset($item_copy['file_naming']);
                                    unset($item_copy['checklist']);
                                    unset($item_copy['files']);
                                    $synchro_itens[] = $item_copy;
                                    Item::where('id', $item['id'])->update(['status' => true]);
                                    $checklist = Checklist::where('id',$item['checklist_id'])->first();
                                    $checklist->sync_itens();
                                }
                            }
                        }

                        $msg = [
                            'status' => 'Ok',
                            'file_id'=> $saveFile->id,
                            'file_url'=> env('AWS_URL').$path,
                            'file_name' => $filename,
                            'synchronized_itens' => $synchro_itens

                        ];
                        $data['uploaded_files'][] = $msg;
                    }
                // }elseif(in_array($filename,$file_names) && Storage::disk('s3')->exists($path)) {
                //     //echo "2 - $filename\n\n";
                //     $msg = ['status' => 'Error', 'message'=> 'Arquivo já adicionado','name' => $filename];
                }elseif(!in_array($filename,$file_names)) {
                    //echo "3 - $filename\n\n";
                    $msg = ['status' => 'Error', 'message'=> 'O arquivo não é padrão','name' => $filename];
                }

                /*foreach ($itens as $key => $item) {
                    $file_name = $item['file_naming']['standard_file_naming'];

                    // verificação apenas do itens dos checklists do mês se possuem algum item DCTFWEB
                    if(
                        (
                            (strpos($filename,'DCTF') !== FALSE && strpos($item['file_naming']['standard_file_naming'],'DCTF') !== FALSE) ||
                            (strpos($item['file_naming']['standard_file_naming'],$filename) !== FALSE)
                        )
                        strpos($item['file_naming']['standard_file_naming'],$filename) !== FALSE
                        && $file_array[1] == $item['file_competence_id']
                    ) {
                        //print_r($item);
                        if(!empty($item['files'])) {

                            if(true) {

                            }
                        }
                        //if($item['status'] == FALSE){

                            // inserir arquivos
                            $path = "/$this->env/book/checklists/{$item['checklist_id']}/". $archive->getClientOriginalName();
                            if($upload = Storage::disk('s3')->put($path, file_get_contents($archive), 'public')){
                                try {
                                    // salva o arquivo com o id do item, o camnho e o nome complementar/adicional
                                    $filenameplus = str_replace($item['file_naming']['standard_file_naming'], '', $filename);
                                    $saveFile = File::updateOrCreate(
                                        ['item_id' => $item['id'], 'path' => $path, 'complementary_name' => $filenameplus],
                                    );
                                    Item::where('id', $item['id'])->update(['status' => true]);

                                    $checklist = Checklist::find($item['checklist_id']);
                                    //$checklist->sync_itens();
                                    $msg = [
                                        'status' => 'Ok',
                                        'item_id' => $item['id'],
                                        'file_id'=> $saveFile->id,
                                        'file_url'=> env('AWS_URL').$path,
                                        'item_name' => $item['file_naming']['standard_file_naming'],
                                        'file_name' => $filename
                                    ];
                                    $data['updated_itens'][] = $msg;
                                    //break;
                                } catch (\Throwable $th) {
                                    //echo $th->getMessage()."\n\n";
                                    $msg = ['status' => 'Error', 'message'=> $th->getMessage(),'name' => $filename];
                                }

                            // } else{
                            //     $msg = ['status' => 'Error', 'message'=> 'Error ao subir arquivo','name' => $filename];
                            // }
                        }else {
                            $msg = ['status' => 'Error', 'message'=> 'Arquivo já enviado','name' => $filename];
                        }
                        //echo "$path - {$item['id']} - {$item['file_naming']['standard_file_naming']} \n\n";
                    }else {
                        $msg = ['status' => 'Error', 'message'=> 'Arquivo inválido para upload','name' => $filename];
                    }
                }*/
                if($msg['status'] == 'Error') $data['errors'][] = $msg;
            }
        }
        return response()->json($data,200);
    }

    public function searchOcurrence(Request $request) {
        //return response()->json(Item::with('fileNaming')->get());
        $rules = [
            'files' => 'required|array'
        ];
        //$request->validate($rules);

        $month = '';
        $year = '';
        $request->has('month') ? $month = $request->month : $month = now()->format('m');
        $request->has('year') ? $year = $request->year : $year = now()->format('Y');

        $occurrences = [];

        foreach($request->file('files') as $index => $file) {
            $filetype = $file->getClientOriginalExtension();
            $filename = substr($file->getClientOriginalName(), 0, -strlen($filetype) -1);

            $result_file = $this->getFileAliasNames($file);
            $exact_result = Contract::with([
                                // 'checklist.itens' => function($query) {
                                //     $query->where('status',false);
                                // },
                                // 'checklist' => function($query) use($month,$year) {
                                //     $query->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?",[$month,$year]);
                                // }
                                ])
                                ->where('alias', 'ilike','%'.$result_file['file_alias'].'%')
                                ->first();

            if(empty($exact_result)) {
                $related_contracts_by_name = Contract::where('name', 'ilike','%'.$result_file['first_name'].'%')->get()->toArray();

                if(empty($related_contracts_by_name)) {
                    $related_contracts_by_cto = Contract::where('name', 'ilike','%'.$result_file['first_name'].'%')->get()->toArray();

                    if(empty($related_contracts_by_cto)) {
                        $occurrences['not_found_contracts'][] = $file->getClientOriginalName();
                    } else {
                        $occurrences[$filename]['occurrences'][] = $related_contracts_by_cto;
                    }
                }else {
                    $occurrences['file_occurrences'][] = [
                        'filename' => $filename,
                        'occurrences' => $related_contracts_by_name
                    ];
                }
            }else {
                $exact_result = $exact_result->toArray();
                $doc_name = substr($filename, strlen($exact_result['alias']) + 1);

                // busca dos itens de checklist pertencentes ao contrato que possuem a nomenclatura do arquivo enviado
                $itens = Item::with('fileNaming','checklist')
                            ->whereHas('fileNaming', function($query) use($doc_name) {
                                $query->where('standard_file_naming','ilike',$doc_name);
                            })
                            ->whereHas('checklist', function($query) use($exact_result,$month,$year) {
                                $query->where('contract_id',$exact_result['id'])->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?",[$month,$year]);
                            })
                            ->get()->toArray();

                if(!empty($itens)) {

                    // agrupamento dos checklists do contrato exato ($exact_result)
                    foreach($itens as $item) {
                        if(!isset($exact_result['checklists'])) {
                            $exact_result['checklists'][] = $item['checklist'];
                        }elseif(!in_array($item['checklist']['id'],array_column($exact_result['checklists'],'id'))) {
                            $exact_result['checklists'][] = $item['checklist'];
                        }
                    }

                    // atribuição dos itens ao checklist
                    /*foreach($exact_result['checklists'] as $checklist_index => $checklist) {

                        // verifica se o checklist do item é igual ao checklist do resultado exato e se o item já está incluso dentro do resultado exato
                        foreach($itens as $item) {

                            // verifica se existe o indice 'itens' do checklist para então cria-lo (Primeiro a ser adicionado)
                            if(!isset($checklist['itens']) && $item['checklist_id'] == $checklist['id']) {
                                unset($item['checklist']);
                                $exact_result['checklists'][$checklist_index]['itens'][] = $item;
                                //echo "XXX - id checklist: {$checklist['id']} - id item: {$item['id']} - chekclist index: $checklist_index\n\n";
                            }elseif($item['checklist_id'] == $checklist['id']){
                                unset($item['checklist']);
                                $exact_result['checklists'][$checklist_index]['itens'][] = $item;
                                //echo "YYY - id checklist: {$checklist['id']} - id item: {$item['id']}\n\n";
                            }
                        }
                    }*/
                }else {
                    $occurrences['not_found_checklist_itens'][] = [
                        'file' => $file->getClientOriginalName()
                    ];
                }
                // se o indice 'contracts' já existir é feito a tratativa de agrupamento de dados para os contratos
                if(isset($occurrences['contracts'])) {
                    // se já existir um valor $exact_result dentro das ocorrências será agrupado novos dados de checklist e itens a esse valor
                    if(!empty($indexes = array_keys(array_column($occurrences['contracts'], 'id'),$exact_result['id']))) {

                        if(!empty($itens)) {

                            if(isset($occurrences['contracts'][$indexes[0]]['checklists'])){
                                foreach($occurrences['contracts'][$indexes[0]]['checklists'] as $occ_checklist_index => $occ_checklist) {
                                    foreach($exact_result['checklists'] as $exact_checklist) {

                                        // se o checklist do resultado exato já estiver no checklist do contrato das $occurrences
                                        if($exact_checklist['id'] == $occ_checklist['id']) {
                                            // se o id do item de checklist do contrato exato não estiver incluso no array de ocorrências
                                            /*if(!in_array($exact_checklist['itens'][0]['id'],array_column($occ_checklist['itens'],'id'))) {
                                                // é adicionado o item ao checklist das ocorrências
                                                $occurrences['contracts'][$indexes[0]]['checklists'][$occ_checklist_index]['itens'][] = $exact_checklist['itens'][0];
                                            }*/
                                        // se não existir é adicionado o checklist novo ao contrato das ocorrências
                                        }else {
                                            if(!in_array($exact_checklist['id'],array_column($occurrences['contracts'][$indexes[0]]['checklists'],'id'))){
                                                $occurrences['contracts'][$indexes[0]]['checklists'][] = $exact_checklist;// é adicionado o item ao checklist das ocorrências
                                            }
                                        }
                                    }
                                }
                            }else {
                                if(isset($exact_result['checklists'])) {
                                    $occurrences['contracts'][$indexes[0]]['checklists'] = $exact_result['checklists'];
                                }
                            }
                        }

                    // caso esse índice ainda não exista ele é adicionado
                    }else {
                        if(isset($exact_result['checklists'])) {
                            $occurrences['contracts'][] = $exact_result;
                        }
                    }
                // caso contrário é adicionado o primeiro registro ao índice
                }else {
                    if(isset($exact_result['checklists'])) {
                        $occurrences['contracts'][] = $exact_result;
                    }
                }
            }
        }
        return response()->json($occurrences, 200);
    }

    private function contractAlias(Request $request) {

        $regex_cto = '/CTO\s*([^\s_]+)/';
        //$regex_first_name = '/^([^\s_]+)/';
        //$regex_second_name = '/\b(?!CTO\b)([^\s_]+)\b(?:\s+([^\s_]+)\b)?/';
        $regex_names = '/\b(?!CTO\b)([^\s_]+)\b(?:\s+([^\s_]+)\b)?(?:\s+(?!CTO\b)([^\s_]+)\b)?/';

        $contracts = Contract::where('name','ilike','%CTO%')->orderBy('name')->get()->toArray();

        foreach($contracts as $contract) {
            $cto = '';
            $first_name = '';
            $second_name = '';
            $third_name = '';
            $string = '';

            //echo "$name - ";
            if (preg_match($regex_cto,$contract['name'], $matches)){
                $cto = $matches[1];
                if (strpos($cto, '/') !== false) {
                    $cto = str_replace('/', '', $cto);
                }
            }

            if (preg_match($regex_names,$contract['name'], $matches)){
                // Busca do primeiro nome
                $first_name = $matches[1];
                // Busca do segundo nome
                $second_name = isset($matches[2]) ? $matches[2] : null;
                // Busca do terceiro nome
                $third_name = isset($matches[3]) ? $matches[3] : null;
            }

            //$string .= '======>>>> '.$first_name;
            $string .= $first_name;

            // Concatenação do segundo nome caso não esteja nulo
            /*if(!empty($second_name)) {
                $string .= $second_name;
            }*/

            // Concatenação do terceiro nome caso não esteja nulo
            /*if(!empty($third_name)) {
                $string .= $third_name;
            }*/

            $string .= "_CTO{$cto}";

            /*$update_contract = Contract::find($contract['id']);
            $update_contract->alias = $string;
            $update_contract->save();*/
            //echo $string;
        }
    }

    private function getFileAliasNames($file,$alias_size = 1) {
        $regex_cto = '/CTO\s*([^\s_]+)/';
        $regex_first_name = '/^([^\s_]+)/';

        $filetype = $file->getClientOriginalExtension();
        $filename = substr($file->getClientOriginalName(), 0, -strlen($filetype) -1);

        $cto = '';
        $first_name = '';
        $string = '';
        $result = [];

        if (preg_match($regex_cto,$filename, $matches)){
            $cto = $matches[1];
            if (strpos($cto, '/') !== false) {
                $cto = str_replace('/', '', $cto);
            }
        }

        if (preg_match($regex_first_name,$filename, $matches)){
            // Busca do primeiro nome
            $first_name = $matches[1];
        }

        $string .= $first_name;

        $result['first_name'] = $first_name;
        $result['contract'] = "CTO{$cto}";
        $result['file_alias'] = $string .= "_CTO{$cto}";

        return $result;
    }

    // Nomenclatura errada ou contrato não encontrado
    // Checklist ou item não encontrado
    public function automacao(Request $request) {

        // $not_found = false;
        // $occurrences = $this->getOccurrence($request);

        $rules = [
            'errors' => 'required|array'
        ];
        $request->validate($rules);

        // $errors = [
        //     'wrong_name' => [],
        //     'not_found_item' => [],
        //     'not_found_contract' => []
        // ];

        foreach($request->errors as $error) {
            $automacaoErrors = new AutomacaoErrors();
            $automacaoErrors->path = $error->path;
            $automacaoErrors->filename = $error->filename;
            $automacaoErrors->error = $error->error;
            $automacaoErrors->save();
        }

        //print_r($occurrences['not_found_contracts']);

        // if(isset($occurrences['file_occurrences'])) {
        //     foreach($occurrences['file_occurrences'] as $occurrence) {
        //         $errors['wrong_name']['files'][] = $occurrence['filename'];
        //         $automacaoErrors->path = '';
        //     }
        //     $errors['wrong_name']['message'] = 'Nomenclatura fora do padrão ou não encontrada';
        // }

        // if(isset($occurrences['not_found_checklist_itens'])) {
        //     foreach($occurrences['not_found_checklist_itens'] as $occurrence) {
        //         $errors['not_found_item']['files'][] = $occurrence['file'];
        //     }
        //     $errors['not_found_item']['message'] = 'Itens ou checklist(s) não encontrado(s)';
        // }

        // if(isset($occurrences['not_found_contracts'])) {
        //     foreach($occurrences['not_found_contracts'] as $occurrence) {
        //         $errors['not_found_contract']['files'][] = $occurrence;
        //     }
        //     $errors['not_found_contract']['message'] = 'Contrato não encontrado';
        // }

        return response()->json(['message' => 'Erros salvos com sucesso'], 201);
    }

    public function deleteFile(Request $request, $file_id, $item_id) {
        try {
            $items_file = FilesItens::where('item_id', $item_id)->where('file_id', $file_id)->first();
            
            if (!$items_file) {
                return response()->json(['message' => ' Arquivo não encontrado'], 404);
            }
            $items_file->delete();

            // $file = File::find($file_id);
            // $file->delete();

            $item_files = FilesItens::where('item_id', $item_id)->first();
            if($item_files === null){
                Item::where('id', $item_id)->update(['status' => false]);
            }else{
                Item::where('id', $item_id)->update(['status' => true]);
            }

            $checklist_id =  Item::where('id', $item_id)->first()->checklist_id;
            $checklist = Checklist::find($checklist_id);
            $checklist->sync_itens();

            return response()->json(['message' => ' Arquivo excluído com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }




}
