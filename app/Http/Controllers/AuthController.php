<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\User;
use App\Models\ADUser;
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

                if ($user['employeeid'] === null) {
                    return response()->json(['message' => 'Necessário atualizar as informações cadastrais'], 401);
                }
                $db_user = User::where('taxvat', $user['employeeid'])->where('status', 'Ativo')->first();
     
                if (!$db_user) {
                    return response()->json(['message' => 'Falha ao realizar login'], 422);
                }
                
                $checkDatabaseUser = $this->checkDatabaseUser();

                if ($checkDatabaseUser->firstLogin) {
                    return response()->json(['token' => $token, 'first_access' => true, 'userData' => $checkDatabaseUser->user], $httpCode);
                }

              

                return response()->json(['token' => $token, 'userData' => $checkDatabaseUser->user], $httpCode);
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

    private function checkDatabaseUser()
    {
        $firstLogin = false;
        $user_auth = Auth::user();

        // Ensure $user_auth['employeeid'] is set and not empty
        if (isset($user_auth['employeeid']) && !empty($user_auth['employeeid'])) {
            // Check if $user_auth['employeeid'] is an array
            if (is_array($user_auth['employeeid'])) {
                $taxvat = $user_auth['employeeid'][0];
            } else {
                $taxvat = $user_auth['employeeid'];
            }

            $user = User::firstOrNew(['taxvat' => $taxvat, 'status' => 'Ativo']);

            if (!$user->exists && $user->status != 'Inativo') {
                // Ensure that name is set as a string
                $name = is_array($user_auth->name) ? $user_auth->name[0] : strtoupper($user_auth->name);
                $user->name = strtoupper($name);
                $user->status = 'Ativo';
                $user->type = 'Geral';
            }

            $user->username = isset($user_auth['samaccountname']) ? $user_auth['samaccountname'][0] : null;
            $user->email_corporate = isset($user_auth['mail']) ? $user_auth['mail'][0] : null;
            $user->phone = isset($user_auth['telephonenumber']) ? $user_auth['telephonenumber'][0] : null;
            $user->save();

            $firstLogin = !$user->exists;

            return (object)['firstLogin' => $firstLogin, 'user' => $user];
        } else {
            return (object)['error' => 'Employee ID not found'];
        }
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

    public function ldap(Request $request)
    {
        try {
          
            $user = 'teste';

            $users = ADUser::where('userprincipalname', $request->email)->first();

            if (!$users) {
                $users = ADUser::where('samaccountname', $request->username)->first();
            }
            

            // Add info

            // if ($users) {
            //     try {
            //         //code...

            //         $users->employeeid = ['73773484100'];
            //         $users->save();
            //     } catch (\Throwable $th) {
            //        return $th;
            //     }
               
            // }


            // $users = ADUser::get(100);



            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    private function checkPermission($user)
    {
        $group = $user['memberof'];
        $arr_permission = [
            "trainee_w"             => 'Admin',
            "gerencia_executiva1_w" => 'Executivo',
            "gerencia_executiva2_w" => 'Executivo',
            "Gerentes_Operacoes"    => 'Operação',
            "rh_checklist_w"        => 'RH',
            "rh_book_w"             => 'RH',
            "financeiro_book_w"     => 'Financeiro',
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
