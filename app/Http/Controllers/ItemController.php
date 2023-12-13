<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Item;
use App\Models\Checklist;
use Illuminate\Http\Request;
use App\Services\ItemService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class ItemController extends Controller
{
    // public function __construct(
    //     protected ItemService $service,
    // ) {
    // }


    public function show()
    {
        try{
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo', 'Analista', 'Rh', 'Fin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);
            $itens = Item::all();
            return response()->json($itens, 200);

        }catch(\Exception $e){
            return response()->json(['error'=>'Não foi possivel acessar os Itens'],500);
        }
    }


    public function getbyID(string $id)
    {
        try{
            $colaborador = Collaborator::where('objectguid', Auth::user()->getConvertedGuid())->first();
            if (!$colaborador->hasPermission(['Admin', 'Operacao', 'Executivo', 'Analista', 'Rh', 'Fin'])) return response()->json(['error' => 'Acesso não permitido.'], 403);
            $itens = Item::find($id);
            return response()->json($itens, 200);

        }catch(\Exception $e){
            return response()->json(['error'=>'Não foi possivel acessar os Itens'],500);
        }

    }

    public function store(Request $request)
    {
        try{
            $item = $this->new($request);
            return response()->json(['message'=>'Item criado com sucesso'],200);

        }catch(\Exception $e){
            return response()->json(['erro'=> $e->getMessage()],500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{

            $item = Item::find($id);

            if(!$item){
                return response()->json([
                    'error' => 'Item não encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            $item = $this->updateItem($request, $item);
            return response()->json(['message'=>'Item atualizado com sucesso'],200);

        }catch(\Exception $e){
            return response()->json(['erro'=> $e->getMessage()],500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $item = Item::find($id);
            //dd($item);
            if (!$item) {
                return response()->json([
                    'error' => 'Not Found'
                ], Response::HTTP_NOT_FOUND);
            }

            $item->id = $id;
            $item->deleted_at = date('Y/m/d H:i');
            $item->save();

            return response()->json(['message'=>'Item deletado com sucesso'],200);
        }catch(\Exception $e){
            return response()->json(['erro'=> $e->getMessage()],500);
        }

    }

    public function updateItem(Request $request, Item $item)
    {
        if ($request->has('id'))$item->id = $request->id;
        if ($request->has('status'))$item->status = $request->status;
        if ($request->has('file_naming_id'))$item->file_naming_id = $request->file_naming_id;
        if ($request->has('file_type_id'))$item->file_type_id = $request->file_type_id;
        if ($request->has('checklist_id'))$item->checklist_id = $request->checklist_id;
        $item->save();
        // return response()->json(['message'=>'Item atualizado com sucesso'],200);
    }

    public function new(Request $request)
    {
        $item = new Item();
        if ($request->has('status'))$item->status = $request->status;
        if ($request->has('file_naming_id'))$item->file_naming_id = $request->file_naming_id;
        if ($request->has('file_type_id'))$item->file_type_id = $request->file_type_id;
        if ($request->has('checklist_id'))$item->checklist_id = $request->checklist_id;
        if ($request->has('file_competence_id'))$item->file_competence_id = $request->file_competence_id;
        //dd($item);
        $item->save();


    }

}
