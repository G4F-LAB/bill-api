<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Item;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class ItemController extends Controller
{
    public function __construct(Item $item) {
        $this->item = $item;
    }


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
            if ($request->has('status'))$this->item->status = $request->status;
            if ($request->has('file_naming_id'))$this->item->file_naming_id = $request->file_naming_id;
            if ($request->has('file_type_id'))$this->item->file_type_id = $request->file_type_id;
            if ($request->has('file_competence_id'))$this->item->file_competence_id = $request->file_competence_id;
            if ($request->has('checklist_id'))$this->item->checklist_id = $request->checklist_id;
            //dd($item);
            $this->item->save();

            $checklist = Checklist::find($this->item->checklist_id);
            $checklist->sync_itens();

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

            $this->item = Item::find($id);

            if(!$this->item->isEmpty()){
                return response()->json([
                    'error' => 'Item não encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($request->has('id'))$this->item->id = $request->id;
            if ($request->has('status'))$this->item->status = $request->status;
            if ($request->has('file_naming_id'))$this->item->file_naming_id = $request->file_naming_id;
            if ($request->has('file_type_id'))$this->item->file_type_id = $request->file_type_id;
            if ($request->has('file_competence_id'))$this->item->file_competence_id = $request->file_competence_id;
            if ($request->has('checklist_id'))$this->item->checklist_id = $request->checklist_id;
            $this->item->save();

            $checklist = Checklist::find($this->item->checklist_id);
            $checklist->sync_itens();

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

}
