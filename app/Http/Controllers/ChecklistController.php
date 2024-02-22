<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\FileNaming;
use App\Models\Contract;
use App\Models\Item;
use App\Models\Collaborator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ItemController;
use App\Notifications\ChecklistNotification;
use Notification;

const PERMISSIONS_RH_FIN = [5,6];

class ChecklistController extends Controller
{   
 
    public $checklist;
    public function __construct(Checklist $checklist, Collaborator $collaborator)
    {
        $this->user = $collaborator->getAuthUser();
        $this->checklist = $checklist;
    }


    public function getAll()
    {
        try {

            // $checklist = DB::table('checklists')
            //         ->join('itens', 'itens.checklist_id', '=', 'checklists.id')
            //         ->join('file_competences', 'file_competences.id', '=', 'itens.file_competence_id')
            //         ->select('*')
            //         ->get();

    // verificar query para casos de não ter contrato com gerente e consistencia de dados.
            $checklist = Checklist::join('contracts', 'checklists.contract_id', '=', 'contracts.id')
                ->join('operations', 'contracts.operation_id', '=', 'operations.id')
                ->join('collaborators', 'collaborators.id', '=', 'operations.manager_id')
                ->select(['checklists.id', 'contracts.name as contrato', 'checklists.date_checklist', 'operations.manager_id', 'collaborators.name'])
                ->get();
            return response()->json($checklist, 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Não foi possivel acessar a checklist'], 500);
        }
    }



    // public function getById($id)
    // {
    //     try {

    //         /*$checklist = Checklist::find($id);

    //         if ($checklist) {
    //             $contractId = $checklist->contract_id;

    //             $buscarChecklist = DB::table('checklists')
    //                 ->join('itens', 'itens.checklist_id', '=', 'checklists.id')
    //                 ->join('file_competences', 'file_competences.id', '=', 'itens.file_competence_id')
    //                 ->join('file_types', 'file_types.id', '=', 'checklists.sector_id')
    //                 ->join('contracts', 'contracts.id', '=', 'checklists.contract_id')
    //                 ->where('contract_id', $contractId)
    //                 ->orderBy('date_checklist', 'DESC')->limit(3)->get();
    //             return response()->json($buscarChecklist, 200);
    //         } else {
    //             return response()->json(['error' => 'Checklist não encontrado'], 404);
    //         }*/
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }


    public function store(Request $request)
    {

        try {
            $checklistExists = Checklist::where('contract_id', $request->contract_id)
                ->whereYear('date_checklist', '=', date('Y', strtotime($request->date_checklist)))
                ->whereMonth('date_checklist', '=', date('m', strtotime($request->date_checklist)))
                ->where('sector_id', $request->sector_id)
                ->exists();

            if ($checklistExists) {
                return response()->json(['error'=> 'Não foi possivel criar checklist, já existe esse checklist.'],404);
            };

            $this->checklist->contract_id  = $request->contract_id;
            $this->checklist->date_checklist  = $request->date_checklist;
            $this->checklist->object_contract = $request->object_contract;
            $this->checklist->shipping_method = $request->shipping_method;
            $this->checklist->sector_id = $request->sector_id;
            $this->checklist->obs = $request->obs;
            $this->checklist->accept = $request->accept;
            $this->checklist->signed_by = $request->signed_by;
            $this->checklist->save();
            
            if ($request->duplicate != null) {
                $duplicated = $this->duplicateItems($request->duplicate, $this->checklist->id, $request->contract_id);
                if (isset($duplicated['error'])) {
                    return response()->json(['message' => $duplicated], 200);
                }
            }

            // Send Notifications
            $to_collaborators = Collaborator::whereIn('permission_id', PERMISSIONS_RH_FIN)->get()->pluck('email');
            Notification::sendNow( [], new ChecklistNotification($this->checklist, $to_collaborators));

            return response()->json(['message' => 'Checklist criado com sucesso'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function duplicateItems($duplicate, $checklist, $contract_id){

        $data = [
            "file_naming_id" => [],
            "checklist_id" => $checklist,
            "status" => false,
            "file_competence_id" => 1
        ];

        $checklist_id_copy = Checklist::where([
            'contract_id' => $contract_id,
            'date_checklist' => $duplicate.'-01'
        ])->value('id');

        $data['file_naming_id'] = Item::where('checklist_id',$checklist_id_copy)->pluck('file_naming_id');

        $itemController = new ItemController(null);

        return $itemController->addItems($data);
    }


    public function update(Request $request, $id)
    {
        try {

            $this->checklist = $this->checklist->find($id);
            if (empty($this->checklist)) {
                return response()->json(['message' => 'Checklist não existe'], 404);
            }

            if ($request->method() == 'PATCH') {
                $dinamicRules = array();

                //aplica regras dinâmicas para os campos que foram enviados
                foreach ($this->checklist->rules() as $input => $rule) {
                    if (array_key_exists($input, $request->all())) {
                        $dinamicRules[$input] = $rule;
                    }
                }
                $request->validate($dinamicRules, $this->checklist->feedback());
            } else {
                $request->validate($this->checklist->rules(), $this->checklist->feedback());
            }

            if ($request->has('date_checklist')) $this->checklist->date_checklist  = $request->date_checklist;
            if ($request->has('object_contract')) $this->checklist->object_contract = $request->object_contract;
            if ($request->has('shipping_method')) $this->checklist->shipping_method = $request->shipping_method;
            if ($request->has('obs')) $this->checklist->obs = $request->obs;
            if ($request->has('accept')) $this->checklist->accept = $request->accept;
            if ($request->has('signed_by')) $this->checklist->signed_by = $request->signed_by;

            // validação pendente
            if(!empty($this->checklist->signed_by) && !$this->checklist->accept && $this->checklist->completion == 100) $this->checklist->status_id = 4;

            // finalizado
            if(!empty($this->checklist->signed_by) && $this->checklist->accept && $this->checklist->completion == 100) $this->checklist->status_id = 5;

            $this->checklist->save();
            return response()->json(['message' => 'Checklist atualizado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

        function checklistItensCreate(Request $request)
        {
            // return response()->json('aaaaaaaaaaa bbbbbbbbbbb cccccccccc',200);
            // exit;
            try{
                //define as datas
                $dataAtual = Carbon::now();
                //$dataAtual = Carbon::createFromFormat('Y-m-d', '2024-01-31')->startOfMonth();
                $months = [];
                $id_contract = $request->id;
                $current_dates = [];

                for ($i = 2; $i >= 0; $i--) {
                    $months[] = $dataAtual->copy()->subMonths($i)->format('Y-m');
                }
                $months[] = $dataAtual->copy()->addMonth(1)->format('Y-m');

                foreach ($months as $month) {
                    $count_items = Item::where('checklist_id', function ($query) use ($id_contract, $month) {
                        $query->select('id')
                            ->from('checklists')
                            ->where('contract_id', $id_contract)
                            ->where('date_checklist', $month.'-01');
                    })->count();
                    // $current_dates[] = array('date' => $month, 'items' => $count_items);
                    $current_dates[] = array('date' => $month, 'items' => $count_items > 0 ? true : false);
                }
                //define as datas

                // return $current_dates;

                $id = $request->id;
                $reference = ($request->reference) ? $request->reference : $months[2] ;

                $itens = Item::with('checklist', 'fileNaming')->whereHas('checklist',function($query) use($id,$reference) {
                    $query->whereRaw("TO_CHAR( checklists.date_checklist, 'YYYY-MM' ) LIKE '".$reference."%' and checklists.contract_id = ".$id);
                })->get();

                $id_checklist = Checklist::where('contract_id', $id)
                    ->whereRaw("TO_CHAR( checklists.date_checklist, 'YYYY-MM' ) LIKE '".$reference."%'")
                    ->value('id');


                $contract = Contract::where('id', $id)->first();

                $data['contract'] = $contract;
                $data['checklist_id'] = $id_checklist;
                $data['itens'] = $itens;
                $data['current_dates'] = $current_dates;

                return response()->json($data, 200);

            }catch(\Exception $e){
                return response()->json(['error'=>'Não foi possivel acessar a checklist'],500);
            }
        }

        function checklistItens(Request $request)
        {
            try{
                $type = NULL;
                if($this->user->is_hr()) {
                    $type = 1;
                }
                if($this->user->is_fin()) {
                    $type = 2;
                }
                $items = Item::with('fileNaming')->with('files')
                    ->when(!empty($type), function($query) use($type){
                        $query->whereHas('fileNaming', function($query2) use($type) {
                            $query2->where('file_type_id',$type);
                        });
                    })
                    ->where('checklist_id', $request->id)->get();
                $this->checklist = Checklist::find($request->id);
                $contract = Contract::find($this->checklist->contract_id);

                $data['checklist'] = $this->checklist;
                $data['contract'] = $contract;
                $data['items_name'] = FileNaming::whereIn('id',$items->pluck('file_naming_id'))->pluck('standard_file_naming');

                $items = $items->toArray();
                foreach($items as $index => $item) {
                    $files = $item['files'];
                    foreach($files as $index2 => $file) {
                        if(!empty($file)) {
                            $items[$index]['files'][$index2]['full_path'] = env('AWS_URL').$file['path'];
                        }
                    }
                }

                $data['itens'] = $items;

                return response()->json($data, 200);

            }catch(\Exception $e){
                return response()->json(['error'=>$e->getMessage()],500);
            }
        }

        function getDataChecklist(Request $request)
        {
            try{

                $data = Checklist::where('contract_id', $request->id)
                ->select('object_contract', 'obs', 'shipping_method')
                ->where('date_checklist', $request->reference.'-01')
                ->first();

                return response()->json($data, 200);

            }catch(\Exception $e){
                return response()->json(['error'=>$e->getMessage()],500);
            }
        }



        function duplicateall(Request $request)
        {

            try {
                $date = Carbon::now();
                
                $ids_contracts = Contract::where('contractual_situation',true)->pluck('id');
                // $date_atual = '2024-02';
                // $date_reference = '2024-01';
                $date_atual = $date->format('Y-m');
                $date_reference = $date->subMonth()->format('Y-m');
    
    
                foreach ($ids_contracts as $id_contract) {
    
                    $checklist = new Checklist();
                    $id_checklist = null;
    
                    $id_checklist_reference = Checklist::where('contract_id', $id_contract)
                                            ->where('date_checklist', 'LIKE', $date_reference.'%')
                                            ->first();
    
                    if (!empty($id_checklist_reference)) {
    
                        $ids_items_duplicate = Item::where('checklist_id', $id_checklist_reference->id)
                                                ->pluck('file_naming_id');
            
            
                        $checklist_exists_atual = Checklist::where('contract_id', $id_contract)
                            ->whereYear('date_checklist', '=', date('Y', strtotime($date_atual.'-01')))
                            ->whereMonth('date_checklist', '=', date('m', strtotime($date_atual.'-01')))
                            ->first();
                            
                        if (empty($checklist_exists_atual)) {
                            $checklist->contract_id  = $id_checklist_reference->contract_id;
                            $checklist->date_checklist  = $date_atual.'-01';
                            $checklist->object_contract = $id_checklist_reference->object_contract;
                            $checklist->shipping_method = $id_checklist_reference->shipping_method;
                            $checklist->sector_id = $id_checklist_reference->sector_id;
                            $checklist->obs = $id_checklist_reference->obs;
                            $checklist->accept = $id_checklist_reference->accept;
                            $checklist->signed_by = $id_checklist_reference->signed_by;
                            $checklist->save();
                            $id_checklist = $checklist->id;
                        } else {
                            $id_checklist = $checklist_exists_atual->id;
                        }
                        
                        if (!empty($ids_items_duplicate)) {
                            $data = [
                                "file_naming_id" => $ids_items_duplicate,
                                "checklist_id" => $id_checklist,
                                "status" => false,
                                "file_competence_id" => 1
                            ];
                        
                            $itemController = new ItemController(null);
                    
                            $itemController->addItems($data);
                        }
                    }
                }   
    
                return response()->json(['status'=>'ok'],500);
                
            }catch(\Exception $e){
                return response()->json(['status'=>'error','message'=>$e->getMessage()],500);
            }
        }

}