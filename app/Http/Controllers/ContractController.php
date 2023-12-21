<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Contract;
use App\Models\Checklist;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ContractController extends Controller
{

    //Obter todos os contratos
    public function getAllContracts(Request $request)
    {
        $contracts = Contract::with('operation.collaborator')
            ->where([
                ['contractual_situation', '=', true],
                [function ($query) use ($request) {
                    if (($s = $request->q)) {
                        $query->orWhere('name', 'LIKE', '%' . $s . '%')                        
                            ->get();
                    }
                }]
            ])->paginate(500);
        
        //Puxar todos os contratos, incluindo os encerrados
        // if ($request->has('encerrados')) {
        //     $contracts = Contract::with('manager')->get();
        //     return response()->json($contracts, 200);
        // }

        return response()->json($contracts, 200);
    }

    //Vincular um colaborador a um contrato
    public function collaboratorContract(Request $request)
    {
            
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            
            if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $contrato = Contract::find($request->contract_id);
            
            $contrato->collaborator()->attach($request->collaborator_id);
            return response()->json(['message' => 'Colaborador vinculado com sucesso!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //Atualizar os dados de um contrato
    public function update(Request $request)
    {
        // return response()->json([$request->contrato], 200);
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();

            if (!$colaborador->hasPermission(['Admin', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $contrato = Contract::where('client_id',$request->id_contrato)->first();

            if ($request->has('contractual_situation')) {
                $contrato->contractual_situation = $request->contractual_situation;
            }

            if ($request->has('id_gerente')) {
                $contrato->manager_id = $request->id_gerente;
            }

            $contrato->save();

            return response()->json([$contrato], 200);
        } catch (\Exception $e) {
            // return response()->json(['error' => $e->getMessage()], 500);
            return response()->json(['error' => 'Não foi possível atualizar o contrato.'], 500);
        }
    }

    //Atualizar a lista de contratos
    public function updateContracts()
    {
        try {
            $references = Operation::pluck('reference','id')->toArray();
            $pcc_classific_c = array();
            $resultAPIViewContracts = self::requestAPIViewContracts();
            $resultAPIViewCentrodeCusto = self::requestAPIViewCentroCusto();
            $array = [];

            foreach($resultAPIViewCentrodeCusto as $key => $result){
                array_push($pcc_classific_c ,$result['Pcc_classific_c']);
            }
            
            foreach($resultAPIViewContracts as $index => $result){
                foreach($resultAPIViewCentrodeCusto as $key => $result2){
                    if($result['CONTA_GEREN'] == $result2['Pcc_classific_c']) {
                        $array[] = [
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

            foreach ($array as $contract) {
                foreach($references as $key => $reference){
                    if((string)$reference == $contract['CD_OBJETO']){
                        $contract_find_active = Contract::where('client_id',$contract['Cd_pcc_reduzid'])->first();
                        $contract_closed = Contract::where('client_id',$contract['Cd_pcc_reduzid'])->where('contractual_situation',false)->first();
                        if (empty($contract_find_active)) {             
                            if($contract['SITUACAO'] == "ATIVO"){
                                $new_contract = new Contract();
                                $new_contract->client_id = $contract['Cd_pcc_reduzid'];
                                $new_contract->name = $contract['DESC_GEREN'];
                                $new_contract->contractual_situation = true;
                                $new_contract->operation_id = $key;
                                $new_contract->save();                           
                            }
                        }elseif($contract_closed && $contract['SITUACAO'] == "ATIVO"){    
                            $new_contract = Contract::find($contract_closed['id']);                                 
                            $new_contract->contractual_situation = true;
                            $new_contract->operation_id = $key;
                            $new_contract->save();
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
            for($i = 1;$valueTrue == true ; $i++  ){
                $response = Http::withHeaders([
                    'Authorization' => 'CG46H-JQR3C-2JRHY-XYRKY-GSPVM'
                ])
                    ->withBody(json_encode([                 
                        "filtros" => [
                            "pagina" => $i
                        ]            
                    ]), 'application/json')
                    ->post('http://g4f.begcloud.com:85/rules/WSCIGAMCRM.asmx/VIEW_CONTRATOS');
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
            for($i = 1;$valueTrue == true ; $i++  ){
                $response = Http::withHeaders([
                    'Authorization' => 'CG46H-JQR3C-2JRHY-XYRKY-GSPVM'
                ])
                    ->withBody(json_encode([                 
                        "filtros" => [
                            "pagina" => $i
                        ]            
                    ]), 'application/json')
                    ->post('http://g4f.begcloud.com:85/rules/WSCIGAMCRM.asmx/VIEW_CENTRO_CUSTO');
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
            $checklist = Contract::with('checklist.status')->where('id',$id)
            ->first();
            return response()->json($checklist);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
       
    }



}
