<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LdapRecord\Container;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function __construct(User $user) {
        $this->user = $user->getAuthUser();
    }

    public function me()
    {
        try {
            return response()->json($this->user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao buscar seus dados'], 500);
        }
    }
    public function index(Request $request)
    {
        $q = $request->input('q');
        $status = $request->input('status');
        $type = $request->input('type');
        $contractId = $request->input('contract');
    
        $usersQuery = User::query();
    
        $usersQuery->when($contractId, function ($query) use ($contractId) {
            $query->whereHas('operationContractUsers', function ($subQuery) use ($contractId) {
                $subQuery->where('contract_id', $contractId)->with('contractUser');
            });
        })->with(['operationContractUsers' => function ($query) use ($contractId) {
            if ($contractId) {
                $query->where('contract_id', $contractId)->with('contractUser');
            } else {
                $query->with('contractUser');
            }
        }]);
    
        $users = $usersQuery->where(function ($query) use ($q) {
            $query->where('name', 'ILIKE', '%' . $q . '%')
                ->orWhere('register', 'ILIKE', '%' . $q . '%')
                ->orWhere('taxvat', 'ILIKE', '%' . $q . '%');
        })
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->when($type, function ($query) use ($type) {
            $query->where('type', $type);
        })
        ->orderByRaw("CASE WHEN status = 'Ativo' THEN 0 ELSE 1 END")
        ->paginate(100);
    
        return response()->json($users, 200);
    }
    
    
    

    // public function create(Request $request)
    // {

    //     $username = $request->name;
    //     $permission = $request->permission_id;


    //     try {
    //         $existinUser = Collaborator::where('name', $username)->first();

    //         if ($existinUser) {
    //             return response()->json(['erro' => 'O colaborador já existe']);
    //         } else {

    //             $user = Container::getConnection('default')->query()->where('samaccountname', $username)->first();

    //             if ($user) {
    //                 $collaborator = new Collaborator();
    //                 $collaborator->name = $user['displayname'][0];
    //                 $collaborator->objectguid = $this->guid_to_str($user['objectguid'][0]);
    //                 $collaborator->permission_id = $permission;
    //                 $collaborator->save();

    //                 return response()->json([$collaborator, 'message' => 'Colaborador adicionado com sucesso!'], 200);
    //             } else {
    //                 return response()->json(['error' => 'Usuário não encontrado']);
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()]);
    //     }
    // }



    public function update(Request $request) {
        $rules = [
            'user_id' => 'required',
            'type' => 'nullable|in:Admin,Diretoria,Superintendência,Executivo,Gerente,Operação,RH,Financeiro,Processos,TI,Colaborador,Geral',
            'email' => 'nullable|email|unique:users,email',
        ];

        // Aplicar as regras de validação
        $validator = Validator::make($request->all(), $rules);

        // Verificar se há erros de validação
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            // Encontrar o usuário pelo ID
            $user = User::findOrFail($request->user_id);

            // Atualizar a permissão do usuário, se fornecida
            if ($request->has('type')) {
                $user->type = $request->type;
            }

            // Atualizar o email do usuário, se fornecido
            if ($request->has('email')) {
                $user->email = $request->email;
            }

            // Salvar as alterações
            $user->save();

            // Retornar uma resposta de sucesso
            return response()->json(['message' => 'Dados do usuário atualizados com sucesso!', 'user' => $user], 200);
        } catch (\Exception $e) {
            // Em caso de falha, retornar uma resposta de erro
            return response()->json(['error' => 'Falha ao atualizar os dados do usuário'], 500);
        }
    }


    
    public function getUsersGroupedByType() {
        $usersGrouped = User::orderBy('type')->get()->groupBy('type');

        $data = [];

        foreach ($usersGrouped as $type => $users) {
            $data[] = [
                'name' => $type,
                'total' => $users->count(),
                'users' => $users,
            ];
        }

        return $data;
    }


 

}
