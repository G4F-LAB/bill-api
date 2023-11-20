<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Collaborator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // return response()->json([$request->all()],200);
        
        try{
                $this->checklist->id_contrato = $request->id_contrato;
                $this->checklist->data_checklist  = $request->data_checklist;
                $this->checklist->objeto_contrato = $request-> objeto_contrato;
                $this->checklist->forma_envio = $request->forma_envio;
                $this->checklist->obs = $request->obs;
                $this->checklist->aceite = $request->aceite;
                $this->checklist->setor = $request->setor;
                $this->checklist->assinado_por = $request->assinado_por;
                $this->checklist->save();
                return response()->json(['message'=>'Checklista criado com sucesso'],200);
            
                

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
        
        public function update(Request $request , $id){
           try{ 

            $checklist = Checklist::find($id);
            if(!$checklist){
                return response()->json(['message'=>'Checklist não existe'],404);
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

            if ($request->has('id_contrato'))$checklist->id_contrato = $request->id_contrato;
            if ($request->has('data_checklist'))$checklist->data_checklist  = $request->data_checklist;
            if ($request->has('objeto_contrato'))$checklist->objeto_contrato = $request-> objeto_contrato;
            if ($request->has('forma_envio'))$checklist->forma_envio = $request->forma_envio;
            if ($request->has('obs'))$checklist->obs = $request->obs;
            if ($request->has('aceite'))$checklist->aceite = $request->aceite;
            if ($request->has('setor'))$checklist->setor = $request->setor;
            if ($request->has('assinado_por'))$checklist->assinado_por = $request->assinado_por;
            $checklist->save();
            return response()->json(['message'=>'Checklist atualizado com sucesso'],200);

           }catch(\Exception $e){
            return response()->json(['erro'=> $e->getMessage()],500);
           }
        }
        
        
    }
    



