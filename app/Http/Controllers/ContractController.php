<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ContractController extends Controller
{

    //Obter todos os contratos
    public function getAllContracts(Request $request)
    {
        $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
        if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

        //Puxar todos os contratos, incluindo os encerrados
        if ($request->has('encerrados')) {
            $contracts = Contract::all();
            return response()->json($contracts, 200);
        }

        $contracts = Contract::where('situacao_contratual', true)->get();
        return response()->json($contracts, 200);
    }

    //Vincular um colaborador a um contrato
    public function collaborator(Request $request)
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();

            if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $contrato = Contract::find($request->contrato);
            $contrato->collaborator()->attach($request->id_collaborator);
            return response()->json([$request], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao vincular o colaborador ao contrato.'], 500);
        }
    }

    //Atualizar os dados de um contrato
    public function update(Request $request)
    {
        // return response()->json([$request->contrato], 200);
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();

            if (!$colaborador->hasPermission(['Admin', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $contrato = Contract::find($request->contrato);

            if ($request->has('situacao_contratual')) {
                $contrato->situacao_contratual = $request->situacao_contratual;
            }

            if ($request->has('id_gerente')) {
                $contrato->id_gerente = $request->id_gerente;
            }

            $contrato->save();

            return response()->json([$contrato], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
            // return response()->json(['error' => 'Não foi possível atualizar o contrato.'], 500);
        }
    }

    //Atualizar a lista de contratos
    public function updateContracts()
    {
        try {
            $response = Http::withHeaders([
                'Content-type' => 'application/json',
                'x-uuid' => 'B7DA9848-514D-42AC-82FD-1391E124D20C',
                'x-api-key' => 'rO4km1L2j5SFYU071iSLY8I6O1lOK8uxC78TquVscM'
            ])
                ->withBody(json_encode([
                    'table_name' => 'R018CCU',
                    'nav' => [
                        'page_items' => 500
                    ],
                ]), 'application/json')
                ->get('https://senior.g4fcorporate.com/table/list');
            $jsonData = $response->json();


            foreach ($jsonData['data']['list'] as $contract) {
                $contract_find = Contract::find($contract['codccu']);
                echo $contract_find;
            
                // if (empty($contract_find)) {
                //     $new_contract = new Contract();
                //     $new_contract->contrato = $contract['codccu'];
                //     $new_contract->name = $contract['nomccu'];
                //     $new_contract->situacao_contratual = true;
                //     $new_contract->save();
                // }
            }
            return response()->json(['message' => 'Contratos atualizados com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
