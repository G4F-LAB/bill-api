<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\Collaborator;
use LdapRecord\Container;
use LdapRecord\Auth\Events\Failed;

class AuthController extends Controller
{
    //Endpoint para autenticação
    public function login(Request $request)
    {

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
                    $message = 'Usuário ou senha inválidos!';
                    $httpCode = 401;
                }
            });

            $credentials = [
                'samaccountname' => $request->username,
                'password' => $request->password
            ];
dd($ldap);
dd($error);
            //autenticação
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = JWTAuth::fromUser($user);
                if ($this->checkDatabaseUser()) return response()->json(['token' => $token, 'first_access' => true], $httpCode);
                return response()->json(['token' => $token], $httpCode);
            } else {
                if (empty($message)) {
                    $message = 'Usuário ou senha inválidos!';
                    $httpCode = 401;
                }
                return response()->json(['error' => $message], $httpCode);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
        $user = Auth::user();

        $permissionResult = $this->checkPermission($user);
        $permission = ($permissionResult === null) ? 7 : $permissionResult;

        $colaborador = Collaborator::where('objectguid', $user->getConvertedGuid())->first();
        if ($colaborador == NULL) {

            $colaborador = new Collaborator();
            $colaborador->name = $user['displayname'][0];
            $colaborador->objectguid = $user->getConvertedGuid();
            $colaborador->id_permissao = $permission;
            $colaborador->save();

            $firstLogin = true;
        }

        return $firstLogin;
    }

    private function checkPermission($user)
    {
        $group = $user['memberof'];
        $arr_permission = [
            "trainee_w"             => 1,
            "gerencia_executiva1_w" => 2,
            "gerencia_executiva2_w" => 2,
            "Gerentes_Operacoes"    => 3,
            "rh_checklist_w"        => 5,
            "rh_book_w"             => 5,
            "financeiro_book_w"     => 5,
            "g_TI"                  => 6,
        ];

        if (!$group)
            return null;

        foreach ($group as $value) {
            $value_filtered = str_replace('CN=', '', explode(',', $value)[0]);

            if (array_key_exists($value_filtered, $arr_permission))
                return $arr_permission[$value_filtered];
        }
    }
}
