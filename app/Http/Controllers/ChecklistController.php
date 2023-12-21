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
            ->where('sector_id',$request->sector_id)->first();

            if ($verificacaoArea) {
                return response()->json(['error'=> 'Não foi possivel criar checklist,ja existe esse checklist.'],200);
            }

            $this->checklist->contract_id  = $request->contract_id;
            $this->checklist->date_checklist  = $request->date_checklist;
            $this->checklist->object_contract = $request->object_contract;
            $this->checklist->shipping_method = $request->shipping_method;
            $this->checklist->sector_id = $request->sector_id;
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

        function checklistItensCreate(Request $request)
        {
            try{
                //define as datas
                $dataAtual = Carbon::now();
                $months = [];
                for ($i = 2; $i >= 0; $i--) {
                    $months[] = $dataAtual->copy()->subMonths($i)->format('Y-m');
                }
                $months[] = $dataAtual->copy()->addMonth()->format('Y-m');
                //define as datas

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
                $data['current_dates'] = $months;

                return response()->json($data, 200);
                return response()->json($data, 200);

            }catch(\Exception $e){
                return response()->json(['error'=>$e->getMessage()],500);
            }
        }


    }




