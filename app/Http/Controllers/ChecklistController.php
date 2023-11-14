<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;


class ChecklistController extends Controller
{
    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
    }


    public function getAll(){
        try{
        $checklist = Checklist::all();
        return response()->json($checklist,200);
        
        }catch(\Exception $e){
            return response()->json(['error'=>'Não foi possivel acessar a checklist'],500);
        }
    }

    public function getbyId($id){
        try {
            $checklist = Checklist::find($id);
            if($checklist) {
                return response()->json($checklist, 200);
            } else {
                return response()->json(['error' => 'Checklist não encontrado'], 404);
            }
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        
        // $request->validate($this->checklist->rules(), $this->checklist->feedback());
        try{
            
            $this->checklist->id_contrato = $request->id_contrato;
            $this->checklist->data_checklist  = $request->data_checklist;
            $this->checklist->objeto_contrato = $request-> objeto_contrato;
            $this->checklist->forma_envio = $request->forma_envio;
            $this->checklist->obs = $request->obs;
            $this->checklist->aceite = $request->aceite;
            $this->checklist->setor = $request->setor;
            $this->checklist->assinado_por = $request->assinado_por;
            $this->checklist->created_at = NULL;
            $this->checklist->updated_at = NULL;

            $this->checklist->save();
            
            

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
        
        
    }
    


}
