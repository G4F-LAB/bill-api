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



    public function getById($id)
    {
        try {
            $checklist = Checklist::find($id);

            if ($checklist) {
                $contractId = $checklist->contract_id;

                $buscarChecklist = DB::table('checklists')
                    ->join('itens', 'itens.checklist_id', '=', 'checklists.id')
                    ->join('file_competences', 'file_competences.id', '=', 'itens.file_competence_id')
                    ->join('file_types', 'file_types.id', '=', 'itens.file_type_id')
                    ->join('contracts', 'contracts.id', '=', 'checklists.contract_id')
                    ->where('contract_id', $contractId)
                    //>orWhere("id",$id)
                    ->orderBy('date_checklist', 'DESC')->limit(3)->get();
                return response()->json($buscarChecklist, 200);
            } else {
                return response()->json(['error' => 'Checklist não encontrado'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {
        // return response()->json([$request->all()],200);

        try {
            $this->checklist->contract_id = $request->contract_id;
            $this->checklist->date_checklist  = $request->date_checklist;
            $this->checklist->object_contract = $request->object_contract;
            $this->checklist->shipping_method = $request->shipping_method;
            $this->checklist->obs = $request->obs;
            $this->checklist->accept = $request->accept;
            $this->checklist->signed_by = $request->signed_by;
            $this->checklist->save();
            return response()->json(['message' => 'Checklista criado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $checklist = Checklist::find($id);
            if (!$checklist) {
                return response()->json(['message' => 'Checklist não existe'], 404);
            }

            if ($request->method() == 'PATCH') {
                $dinamicRules = array();

                //aplica regras dinâmicas para os campos que foram enviados
                foreach ($checklist->rules() as $input => $rule) {
                    if (array_key_exists($input, $request->all())) {
                        $dinamicRules[$input] = $rule;
                    }
                }
                $request->validate($dinamicRules, $checklist->feedback());
            } else {
                $request->validate($checklist->rules(), $checklist->feedback());
            }

            if ($request->has('date_checklist')) $checklist->date_checklist  = $request->date_checklist;
            if ($request->has('object_contract')) $checklist->object_contract = $request->object_contract;
            if ($request->has('shipping_method')) $checklist->shipping_method = $request->shipping_method;
            if ($request->has('obs')) $checklist->obs = $request->obs;
            if ($request->has('accept')) $checklist->accept = $request->accept;
            if ($request->has('signed_by')) $checklist->signed_by = $request->signed_by;
            $checklist->save();
            return response()->json(['message' => 'Checklist atualizado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

        function checklistItens(Request $request)
        {
            try{
            $items = Item::with('fileNaming')->where('checklist_id', $request->id)->get();
            $contract_id = Checklist::where('id', $request->id)->pluck('contract_id');
            $contract = Contract::where('id', $contract_id)->first();

            $data['contract'] = $contract;
            $data['itens'] = $items;

            return response()->json($data, 200);

            }catch(\Exception $e){
                return response()->json(['error'=>'Não foi possivel acessar a checklist'],500);
            }
        }


    }




