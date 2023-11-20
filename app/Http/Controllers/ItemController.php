<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Services\ItemService;
use Illuminate\Http\Response;
class ItemController extends Controller
{
    public function __construct(
        protected ItemService $service,
    ) {
    }

    
    public function show()
    {
        if (!$itens = $this->service->getAll()) {
            return response()->json([
                'error' => 'Not Found'
            ], Response::HTTP_NOT_FOUND);
        }

        return $itens;
    }


    public function findOne(string $id)
    {
        if (!$itens = $this->service->findOne($id)) {
            return response()->json([
                'error' => 'Not Found'
            ], Response::HTTP_NOT_FOUND);
        }

        return $itens;
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
            
            // if ($request->method() == 'PATCH' || $request->method() == 'PUT') {                
            $item = $this->service->update($request, $item);
            return response()->json([], Response::HTTP_NO_CONTENT);

           }catch(\Exception $e){
            return response()->json(['erro'=> $e->getMessage()],500);
           }
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$this->service->findOne($id)) {
            return response()->json([
                'error' => 'Not Found'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->service->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
