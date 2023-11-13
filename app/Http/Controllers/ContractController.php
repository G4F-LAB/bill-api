<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ContractController extends Controller
{

    public function getAllContracts()
    {
        $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
        if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);


        $contracts = Contract::all();
        return response()->json($contracts, 200);
    }

    public function collaborator(Request $request)
    {
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();

            if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $contrato = Contract::find($request->id_contrato);
            $contrato->collaborator()->attach($request->id_colaborador);
            return response()->json([$request], 201);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
                // return response()->json([$request->id_contrato], 200);
        try {
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();

            if (!$colaborador->hasPermission(['Admin', 'Executivo'])) return response()->json(['error' => 'Acesso não permitido.'], 403);

            $contrato = Contract::find($request->id_contrato);
            
            if ($request->has('contractual_situation')) {
                $contrato->contractual_situation = $request->contractual_situation;
            }

            if ($request->has('id_gerente')) {
                $contrato->id_gerente = $request->id_gerente;
            }else {
                return response()->json([$contrato], 200);
            }
            $contrato->save();
            
            return response()->json([$contrato], 200);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 500);
        }
    }

    public function updateContracts(){
        try {
            $response = Http::withHeaders([
                'Content-type' => 'application/json',
                'x-uuid' => 'B7DA9848-514D-42AC-82FD-1391E124D20C',
                'x-api-key' => 'rO4km1L2j5SFYU071iSLY8I6O1lOK8uxC78TquVscM'
            ])
                ->withBody(json_encode([
                    'table_name' => 'R018CCU'
                ]), 'application/json')
                ->get('https://senior.g4fcorporate.com/table/list');
            $jsonData = $response->json();


            foreach ($jsonData['data']['list'] as $contract) {
                Contract::firstOrCreate([
                    'id_contrato' => $contract['codccu'],
                    'name' => $contract['nomccu'],
                    'situacao_contratual' => true
                ]);
            }
            return response()->json(['message'=>'Contratos atualizados com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao atualizar.'], 500);
        }
    }
}
