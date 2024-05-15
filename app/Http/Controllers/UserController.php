<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LdapRecord\Container;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

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
        $per_page = $request->input('per_page') ? : 100;
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
        ->paginate($per_page);
    
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
        $usersGrouped = User::select('type', DB::raw('COUNT(*) as total'))
                            ->where('status', 'Ativo')
                            ->groupBy('type')
                            ->orderBy('type')
                            ->get();
    
        $data = [];
    
        foreach ($usersGrouped as $group) {
            $users = User::where('type', $group->type)
                         ->where('status', 'Ativo')
                         ->get(['name']); // Fetch only the 'name' column
    
            $data[] = [
                'name' => $group->type,
                'total' => $group->total,
                'users' => $users,
            ];
        }
    
        return $data;
    }
    


    public function birthdays(Request $request) {
        // Get day and month from request, or use current day and month if not provided
        $day = $request->input('day', date('d'));
        $month = $request->input('month', date('m'));
    
        // Make API request using Laravel HTTP client
        $response = Http::withHeaders([
            'x-uuid' => '07CE8192-BB8E-425D-9ECF-0A12636CCC36',
            'x-api-key' => 'xirZhoimtBkUa8Xm1b0TxH3iNE7D7PekJDX49KeTyf',
            'Content-Type' => 'application/json'
        ])->post('https://senior.g4fcorporate.com/employee/birthdays', [
            'day' => $day,
            'month' => $month
        ]);
    
        // Check if request was successful
        if ($response->failed()) {
            return response()->json(['error' => 'Failed to retrieve birthdays'], 500);
        }
    
        // Decode the response
        $data = $response->json();
    
        // Check if decoding was successful
        if (!$data) {
            return response()->json(['error' => 'Failed to decode response'], 500);
        }
    
        // Modify the data keys
        foreach ($data['data'] as &$item) {
            $item['name'] = $item['nomfun'];
            unset($item['nomfun']);
    
            $item['taxvat'] = $item['numcpf'];
            unset($item['numcpf']);
    
            $item['contract'] = $item['nomccu'];
            unset($item['nomccu']);
        }
    
        // Return the response data as JSON
        return response()->json(['message' => 'Processado com sucesso', 'data' => $data['data']], 200);
    }
    
 

}
