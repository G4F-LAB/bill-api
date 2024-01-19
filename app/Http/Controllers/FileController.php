<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileNaming;
use App\Models\Collaborator;
use App\Models\Item;
use App\Models\File;
use App\Models\FileType;
use App\Models\Checklist;
use App\Models\Contract;
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

    public function uploadDctf(Request $request) {//return response()->json($request->all());
        $rules = [
            'file' => 'required|array'
        ];
        $request->validate($rules);

        $month = now()->format('m');
        $year = now()->format('Y');
        $month = 12;
        $year = 2023;
        $files = $request->file;
        $data = [];

        // busca de todos itens de checklist do mês atual
        $itens = Item::with('fileNaming','checklist')
                    ->whereHas('checklist', function($query) use($month,$year) {
                        $query->whereRaw('extract(month from date_checklist) = ? and extract(year from date_checklist) = ?',[$month,$year]);
                    })
                    ->get()->toArray();

        foreach($files as $file_array) {

            foreach($file_array[0] as $archive) {
                foreach ($itens as $key => $item) {
                    $file_name = $item['file_naming']['standard_file_naming'];

                    $filetype = $archive->getClientOriginalExtension();
                    $filename = substr($archive->getClientOriginalName(), 0, -strlen($filetype) -1);
    
                    // verificação apenas do itens dos checklists do mês se possuem algum item DCTFWEB 
                    if(
                        strpos($filename,'DCTF') !== FALSE 
                        && strpos($item['file_naming']['standard_file_naming'],'DCTF') !== FALSE
                        && $file_array[1] == $item['file_competence_id']
                    ) {
                        if($item['status'] == FALSE){

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
                                    $msg = ['status' => 'Error', 'message'=> 'Error ao salvar arquivo no banco','name' => $filename];
                                }
        
                            } else{
                                $msg = ['status' => 'Error', 'message'=> 'Error ao subir arquivo','name' => $filename];
                            }
                        }else {
                            $msg = ['status' => 'Error', 'message'=> 'Arquivo já enviado','name' => $filename];
                        }
                        //echo "$path - {$item['id']} - {$item['file_naming']['standard_file_naming']} \n\n";
                    }else {
                        $msg = ['status' => 'Error', 'message'=> 'Arquivo inválido para upload','name' => $filename];
                    }
                }
                if($msg['status'] == 'Error') $data['errors'][] = $msg;
            }
        }
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
                        $occurrences[$filename]['occurrences'][] = '';
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
}
