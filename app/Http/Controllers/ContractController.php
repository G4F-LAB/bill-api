<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Collaborator;
use App\Models\Permission;
use App\Models\Contract;
use App\Models\Executive;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function __construct(Collaborator $collaborator) {
        // $this->user = $collaborator->getAuthUser();
        // $this->current_month =  now()->format('m');

        // if(now()->format('d') <= 17){
        //     $this->current_month = now()->format('m') - 1;
        // }
    }

    //Obter todos os contratos

    public function index(Request $request)
    {
        // Parameters
        $status = $request->input('status', 'Ativo');
        $searchTerm = $request->input('q');
    
        $contracts = Contract::with(['operation'])
            // ->whereNotNull('operation_id')
            ->whereHas('operation', function ($query) {
                $query->whereNotNull('reference'); 
            })
            ->where(function ($query) use ($searchTerm) {
                $searchTermLower = mb_strtolower($searchTerm); 
                $query->whereHas('operation', function ($query) use ($searchTermLower) {
                    $query->whereRaw('LOWER(name) LIKE ?', ["%$searchTermLower%"]); 
                })
                ->orWhereRaw('LOWER(name) LIKE ?', ["%$searchTermLower%"]); 
            })
            ->where('status', $status)
            // ->whereHas('operation', function ($query) use ($user) {
            //     // Filter contracts based on the user's operations
            //     $query->whereIn('id', $user->operations()->pluck('id'));
            // })
            ->get();
    
        return response()->json($contracts, 200);
    }


    public function getAllContracts(Request $request)
    {

        $permission = Permission::where('name','ilike','')->first();

            $contracts = Contract::with([
                'checklist' => function($query){
                    // $query->with('itens.fileNaming')->whereRaw("extract(month from date_checklist) = ? and extract(year from date_checklist) = ?",[$this->current_month ,now()->format('Y')]);
                }
                ,'operation'
                ,'operation.executive'
                ,'operation.collaborators'              
                  ])
            // ->when($this->user->is_analyst(), function($query) {
            //     $query->whereHas('collaborators', function($query2) {
            //         $query2->where('collaborator_id',$this->user->id);
            //     });
            // })
            ->when($this->user->is_analyst(), function($query) {

                $query->whereHas('operation.collaborators', function($query2) {
                    $query2->where('collaborator_id',$this->user->id);
                });

            })
            ->when($this->user->is_executive(), function($query) {
                $query->whereHas('operation.executive', function($query2) {
                    $query2->where('manager_id',$this->user->id);
                });
            })
            ->when($this->user->is_manager(), function($query) {
                $query->whereHas('operation', function($query2) {
                    $query2->where('manager_id',$this->user->id);
                });
            })
            ->where([
                // ['contractual_situation', '=', true],
                [function ($query) use ($request) {
                    if (($s = $request->q)) {
                        $query->orWhere('name', 'LIKE', '%' . $s . '%')
                            ->get();
                    }
                }],
                [function ($query) use ($request) {
                    if ($request->has('ids')) {
                        $query->whereNotIn('id',explode(',',$request->ids));
                    }
                }]
            ])->orderBy('name')->get();
            // ->orderBy('id','ASC')
            // ->paginate(500);
        // }
        //Puxar todos os contratos, incluindo os encerrados
        // if ($request->has('encerrados')) {
        //     $contracts = Contract::with('manager')->get();
        //     return response()->json($contracts, 200);
        // }
        // var_dump(response()->json($contracts, 200));

              if ($request->checklist == 'true') {
                // dd($contracts);
                foreach ($contracts as $key => $value) {
                    // Check if the 'field' is empty for the current element

                    if (count($value['checklist']) === 0) {

                        // If 'field' is empty, remove this element from the array
                        unset($contracts[$key]);
                    }
                }
              }

              $data = array_values($contracts->toArray());
              if($data === []){
                return response()->json($data, 204);
              }

        return response()->json($data, 200);
    }

    //Vincular um colaborador a um contrato
    // public function collaboratorContract(Request $request)
    // {

    //     try {
    //         $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();

    //         if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

    //         $contrato = Contract::find($request->contract_id);

    //         $contrato->collaborators()->attach($request->collaborator_id);
    //         return response()->json(['message' => 'Colaborador vinculado com sucesso!'], 201);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    //Atualizar os dados de um contrato
    public function update(Request $request)
    {
        try {

            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();

            if (!$colaborador->hasPermission(['Admin', 'Executivo', 'Operacao'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $contrato = Contract::where('id',$request->id)->first();
            if ($request->has('contractual_situation')) {
                $contrato->contractual_situation = $request->contractual_situation;
            }

            if ($request->has('alias')) {
                $contrato->alias = $request->alias;
            }

            if ($request->has('operation_id')) {
                $contrato->operation_id = $request->operation_id;
            }

            if ($request->has('name')) {
                $contrato->name = $request->name;
            }

            if ($request->has('client_id')) {
                $contrato->client_id = $request->client_id;
            }
            $contrato->save();

            return response()->json([$contrato], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage() + 'Não foi possível atualizar o contrato.'], 500);
        }
    }

    //Atualizar a lista de contratos
    public function updateListContracts()
    {
        try {
            $references = Operation::pluck('reference','id')->toArray();
            $resultAPIViewContracts = self::requestAPIViewContracts();
            $resultAPIViewCentrodeCusto = self::requestAPIViewCentroCusto();

            $contracts_array = [];
            foreach($resultAPIViewContracts as $index => $result){
                foreach($resultAPIViewCentrodeCusto as $key => $result2){
                    if($result['CONTA_GEREN'] == $result2['Pcc_classific_c']) {
                        $contracts_array[] = [
                            'Cd_pcc_reduzid' => $result2['Cd_pcc_reduzid'],
                            'Pcc_classific_c' => $result2['Pcc_classific_c'],
                            'CD_OBJETO' => $result['CD_OBJETO'],
                            'DESC_GEREN' => $result['DESC_GEREN'],
                            'CONTA_GEREN' => $result['CONTA_GEREN'],
                            'SITUACAO' => $result['SITUACAO']
                        ];
                    }
                }
            }

            foreach ($contracts_array as $contract) {
                foreach($references as $key => $reference){
                    if((string)$reference == $contract['CD_OBJETO']){
                        switch ($contract['SITUACAO']) {
                            case $contract['SITUACAO'] == "ATIVO":
                                $status_id = 1;
                              break;
                            case $contract['SITUACAO'] == "ENCERRADO":
                                $status_id = 2;
                              break;
                            case $contract['SITUACAO'] == "PENDENTE":
                                $status_id = 3;
                              break;
                          }
                        $old_contract = Contract::where('client_id',$contract['Cd_pcc_reduzid'])->first();
                        $contract_closed = Contract::where('client_id',$contract['Cd_pcc_reduzid'])->where('contractual_situation',false)->first();

                        //atualiza status do contrato
                        if($old_contract['status_id'] != $status_id){
                            $new_contract = Contract::find($old_contract['id']);
                            $new_contract->status_id = $status_id;
                            $new_contract->update();
                        }
                        if ((empty($old_contract) || $old_contract['client_id'] == null) && $contract['DESC_GEREN'] != null ) {
                            if($contract['SITUACAO'] == "ATIVO"){
                                $new_contract = new Contract();
                                $new_contract->client_id = $contract['Cd_pcc_reduzid'];
                                $new_contract->name = $contract['DESC_GEREN'];
                                $new_contract->contractual_situation = true;
                                $new_contract->operation_id = $key;
                                $new_contract->save();
                            }
                        }
                        if(!empty($old_contract)){
                            if((is_null($old_contract['operation_id']) && $contract['SITUACAO'] == "ATIVO") && $contract['DESC_GEREN'] != null){
                                $new_contract = Contract::find($old_contract['id']);
                                $new_contract->operation_id = $key;
                                $new_contract->save();
                            }

                        }

                    }
                }
            }
            return response()->json(['message' => 'Contratos atualizados com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function requestAPIViewContracts()
    {
        try {
            $valueTrue = true;
            $result = array();
            for($i = 1;$valueTrue == true ; $i++){
                $response = Http::withHeaders([
                    'Authorization' => 'CG46H-JQR3C-2JRHY-XYRKY-GSPVM'
                ])
                    ->withBody(json_encode([
                        "filtros" => [
                            "pagina" => $i
                        ]
                    ]), 'application/json')
                    ->post('http://g4f.begcloud.com:86/rules/WSCIGAMCRM.asmx/VIEW_CONTRATOS');
                $jsonData = $response->json();

                foreach ($jsonData['d'] as $key => $contract) {
                    array_push($result, $contract);
                    if($contract['mensagem'] == "Nenhum registro encontrado." ){
                        $valueTrue = false;
                    }
                }
            }
            return $result;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function requestAPIViewCentroCusto()
    {
        try {
            $valueTrue = true;
            $result = array();
            for($i = 1;$valueTrue == true ; $i++){
                $response = Http::withHeaders([
                    'Authorization' => 'CG46H-JQR3C-2JRHY-XYRKY-GSPVM'
                ])
                    ->withBody(json_encode([
                        "filtros" => [
                            "pagina" => $i
                        ]
                    ]), 'application/json')
                    ->post('http://g4f.begcloud.com:86/rules/WSCIGAMCRM.asmx/VIEW_CENTRO_CUSTO');
                $jsonData = $response->json();

                foreach ($jsonData['d'] as $key => $contract) {
                    array_push($result, $contract);
                    if($contract['mensagem'] == "Nenhum registro encontrado." ){
                        $valueTrue = false;
                    }
                }
            }
            return $result;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checklistByContractID($id,Request $request) {
        try{
            $checklist = Contract::with('checklist.status')->where('id', $id)->first();

            foreach ($checklist->checklist as $item) {
                $item->date_checklist = Carbon::createFromFormat('Y-m-d', $item->date_checklist)->format('m/Y');
            }

            return response()->json($checklist);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


    public function createContract(Request $request)
    {
        try {
            $contract = Contract::where('client_id', $request->client_id)
            ->first();
            $contract = new Contract();
            $contract->client_id = $request->client_id;
            $contract->name = $request->name;
            $contract->contractual_situation = $request->contractual_situation;
            $contract->operation_id = $request->operation_id;
            $contract->alias = $request->alias;
            $contract->save();
            return response()->json([$contract, 'message' => 'Contrato adicionado com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

}
