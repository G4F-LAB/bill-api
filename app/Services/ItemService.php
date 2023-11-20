<?php

namespace App\Services;


use App\Models\Item;
use Illuminate\Http\Request;

class ItemService 
{ 
    public function getAll(string $filter = null)
    {
        $itens = Item::all();        
        return response()->json($itens, 200);
    }

    public function findOne(string $id)
    {
        $itens = Item::findOne($id);        
        return response()->json($itens, 200);
    }

    public function new(Request $request)
    {
        $item = new Item();
        if ($request->has('id_arquivo'))$item->id_arquivo  = $request->id_arquivo;
        if ($request->has('status'))$item->status = $request->status;
        if ($request->has('competencia'))$item->competencia = $request->competencia;
        if ($request->has('id_nomeclatura_arquivo'))$item->id_nomeclatura_arquivo = $request->id_nomeclatura_arquivo;
        if ($request->has('setor'))$item->setor = $request->setor;
        if ($request->has('assinado_por'))$item->assinado_por = $request->assinado_por;
        $item->save();

        return response()->json(['message'=>'Item criado com sucesso'],200);
    }

    public function update(Request $request, string $id)
    {
        $item = Item::findOne($id);        
        if ($request->has('id_item'))$item->id_item = $request->id_item;
        if ($request->has('id_arquivo'))$item->id_arquivo  = $request->id_arquivo;
        if ($request->has('status'))$item->status = $request->status;
        if ($request->has('competencia'))$item->competencia = $request->competencia;
        if ($request->has('id_nomeclatura_arquivo'))$item->id_nomeclatura_arquivo = $request->id_nomeclatura_arquivo;
        if ($request->has('setor'))$item->setor = $request->setor;
        if ($request->has('assinado_por'))$item->assinado_por = $request->assinado_por;
        $item->save();
        return response()->json(['message'=>'Item atualizado com sucesso'],200);
    }

    public function delete(string $id)
    {
        $item = Item::findOne($id);   
        $item->id_item = $id;
        $item->delete_at  = false;
        $item->save();

        return response()->json(['message'=>'Item deletado com sucesso'],200);
    }

    // public function updateStatus(string $id, SupportStatus $status): void
    // {
    //     $this->repository->updateStatus($id, $status);
    // }





}