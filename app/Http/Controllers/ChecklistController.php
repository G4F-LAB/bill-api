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

class ChecklistController extends Controller
{

    public $checklist;
    public function __construct(Checklist $checklist)
    {
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
        // return response()->json([$request->all()],200);

        try {
            $verificacaoArea = $this->checklist
            ->where('contract_id', $request->contract_id)
            ->whereYear('date_checklist', '=' , date('Y', strtotime($request->date_checklist)))
            ->whereMonth('date_checklist','=', date('m',strtotime($request->date_checklist)))
            ->where('sector_id',$request->sector)->first();

            if ($verificacaoArea) {
                return response()->json(['error'=> 'Não foi possivel criar checklist, já existe esse checklist.'],404);
            }

            $this->checklist->contract_id  = $request->contract_id;
            $this->checklist->date_checklist  = $request->date_checklist;
            $this->checklist->object_contract = $request->object_contract;
            $this->checklist->shipping_method = $request->shipping_method;
            $this->checklist->sector_id = $request->sector_id;
            $this->checklist->obs = $request->obs;
            $this->checklist->accept = $request->accept;
            $this->checklist->signed_by = $request->signed_by;
            $this->checklist->save();
            return response()->json(['message' => 'Checklist criado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

        function checklistItens(Request $request)
        {
            try{
                $items = Item::with('fileNaming')->with('files')->where('checklist_id', $request->id)->get();
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


    }




