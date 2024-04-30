<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\Collaborator;
use App\Models\User;
use App\Models\Permission;
use LdapRecord\Container;
use LdapRecord\Auth\Events\Failed;

class AuthController extends Controller
{
    //Endpoint para autenticação
    public function login(Request $request)
    {
        // return response()->json(['error' => $request->all()], 200);

        //regras de validação
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string',
        ];

        //mensagens de feedback
        $feedback = [
            'password.required' => 'Preencha o campo de senha!',
            'username.required' => 'Preencha o campo de login!'
        ];
        //validação dos campos da requisição
        $request->validate($rules, $feedback);

        try {
            $dispatcher = Container::getEventDispatcher();

            $message = '';
            $httpCode = 200;

            //Evento listener que espera alguma falha de acesso
            $dispatcher->listen(Failed::class, function (Failed $event) use (&$message, &$httpCode) {
                $ldap = $event->getConnection();

                //Recupera o erro retornado
                $error = $ldap->getDiagnosticMessage();

                if (strpos($error, '532') !== false) {
                    $message = 'Falha ao se conectar!';
                    $httpCode = 401;
                } elseif (strpos($error, '533') !== false) {
                    $message = 'Falha ao se conectar!';
                    $httpCode = 401;
                } elseif (strpos($error, '701') !== false) {
                    $message = 'Falha ao se conectar!';
                    $httpCode = 401;
                } elseif (strpos($error, '775') !== false) {
                    $message = 'Limite de tentativas de acesso atingidas. Tente novamente mais tarde.';
                    $httpCode = 401;
                } else {
                    $message = $error;
                    $httpCode = 401;
                }
            });

            $credentials = [
                'samaccountname' => $request->username,
                'password' => $request->password
            ];

            //autenticação
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = JWTAuth::fromUser($user);
                if ($this->checkDatabaseUser()['firstLogin']) return response()->json(['token' => $token, 'first_access' => true, 'userData' => $this->checkDatabaseUser()['user']], $httpCode);
                return response()->json(['token' => $token, 'userData' => $this->checkDatabaseUser()['user']], $httpCode);
            } else {
                if (empty($message)) {
                    $message = 'Usuário ou senha inválidos!';
                    $httpCode = 401;
                }
                return response()->json(['error' => $message], $httpCode);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }



    public function logout()
    {
        try {
            auth('api')->logout();
            return response()->json(['message' => 'Logout realizado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao realizar logout'], 500);
        }
    }

    public function update_info()
    {
        $collaborators = Collaborator::all();

        foreach ($collaborators as $collaborator) {

            $user = Container::getConnection('default')->query()->where('objectguid', $this->str_to_guid($collaborator->objectguid))->first();

            $collaborator->username = isset($user['samaccountname']) ? $user['samaccountname'][0] : NULL;
            $collaborator->email = isset($user['mail']) ? $user['mail'][0] : NULL;
            $collaborator->phone = isset($user['telephonenumber']) ? $user['telephonenumber'][0] : NULL;
            $collaborator->taxvat = isset($user['employeeid']) ? $user['employeeid'][0] : NULL;
            $collaborator->office = isset($user['physicaldeliveryofficename']) ? $user['physicaldeliveryofficename'][0] : NULL;
            $collaborator->role = isset($user['title']) ? $user['title'][0] : NULL;
            $collaborator->save();
        }

        return response()->json(['message' => 'Realizado com sucesso!']);
    }

    private function checkDatabaseUser()
    {
        $firstLogin = false;
        $user_auth = Auth::user();
        
        // $permission = $this->checkPermission($user_auth) ?? $this->permissionID('Geral');

        $user = User::with('operationContractUsers')->firstOrNew(['taxvat' => $user_auth['employeeid']]);
        $user->username = isset($user_auth['samaccountname']) ? $user_auth['samaccountname'][0] : NULL;
        $user->email_corporate = isset($user_auth['mail']) ? $user_auth['mail'][0] : NULL;
        $user->phone = isset($user_auth['telephonenumber']) ? $user_auth['telephonenumber'][0] : NULL;
        $user->taxvat = isset($user_auth['employeeid']) ? $user_auth['employeeid'][0] : NULL;
        $user->save();
        
        $firstLogin = !$user->exists;

        return ['firstLogin' => $firstLogin, 'user' => $user];
        return response()->json(['message' => 'Realizado com sucesso!']);
    }

    public function refresh()
    {
        try {
            $token = auth('api')->refresh();
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao buscar novo token'], 500);
        }
    }

    public function me()
    {
        try {
            $colaborador = Collaborator::where('taxvat', Auth::user()['employeeid'])->first();

            // $colaborador = [
            //     "id" => 6,
            //     "name" => "Wesley Carlos Severiano",
            //     "objectguid" => "0facb771-7861-44f7-8ea4-72a6da3202d8",
            //     "permission_id" => 5,
            //     "created_at" => "2024-02-27T20:19:14.000000Z",
            //     "updated_at" => "2024-03-28T15:32:37.000000Z",
            //     "email" => "wesley.severiano@g4f.com.br",
            //     "phone" => "(61)984837763",
            //     "taxvat" => "03880023107",
            //     "office" => "SEDE - TI DESENVOLVIMENTO",
            //     "role" => "Analista De Desenvolvimento Junior",
            //     "username" => "wesley.severiano",
            //     "name_initials" => "WS",
            //     "permission" => [
            //         "id" => 5,
            //         "name" => "Rh"
            //     ]
            // ];
            

            return response()->json($colaborador);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao buscar seus dados'], 500);
        }
    }

    private function checkPermission($user)
    {
        $group = $user['memberof'];
        $arr_permission = [
            "trainee_w"             => 'Admin',
            "gerencia_executiva1_w" => 'Executivo',
            "gerencia_executiva2_w" => 'Executivo',
            "Gerentes_Operacoes"    => 'Operacao',
            "rh_checklist_w"        => 'Rh',
            "rh_book_w"             => 'Rh',
            "financeiro_book_w"     => 'Fin',
            "g_TI"                  => 'TI'
        ];

        if (!$group)
            return null;

        foreach ($group as $value) {
            $value_filtered = str_replace('CN=', '', explode(',', $value)[0]);

            if (array_key_exists($value_filtered, $arr_permission))
                return $arr_permission[$value_filtered];
        }
    }

    private function permissionID($permission)
    {
        $id = Permission::where('name', $permission)->first();
        return $id->id;
    }
    private function str_to_guid(string $uuidString): string
    {
        $uuidString = str_replace('-', '', $uuidString);
        $pieces = [
            ltrim(substr($uuidString, 0, 8), '0'),
            ltrim(substr($uuidString, 8, 4), '0'),
            ltrim(substr($uuidString, 12, 4), '0'),
            ltrim(substr($uuidString, 16, 4), '0'),
            ltrim(substr($uuidString, 20, 4), '0'),
            ltrim(substr($uuidString, 24, 4), '0'),
            ltrim(substr($uuidString, 28, 4), '0'),
        ];
        $pieces = array_map('hexdec', $pieces);
        return pack('Vv2n4', ...$pieces);
    }
}
