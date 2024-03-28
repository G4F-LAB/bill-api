<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\Collaborator;
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
                if ($this->checkDatabaseUser()['firstLogin']) return response()->json(['token' => $token, 'first_access' => true, 'userData'=> $this->checkDatabaseUser()['user']], $httpCode);
                return response()->json(['token' => $token, 'userData'=> $this->checkDatabaseUser()['user']], $httpCode);
            } else {
                if (empty($message)) {
                    $message = 'Usuário ou senha inválidos!';
                    $httpCode = 401;
                }
                return response()->json(['error' => $message], $httpCode);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()],401);
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
        $user = Auth::user();

        $permissionResult = $this->checkPermission($user);
        $permission = ($permissionResult === null) ? $this->permissionID('Geral') : $permissionResult;

    
        
        $colaborador = Collaborator::with('permission')->where('objectguid', $user->getConvertedGuid())->first();
        if ($colaborador == NULL) {

            $colaborador = new Collaborator();
            $colaborador->name = isset($user['displayname']) ? $user['displayname'][0] : NULL;
            $colaborador->username = isset($user['samaccountname']) ? $user['samaccountname'][0] : NULL;
            $colaborador->email = isset($user['mail']) ? $user['mail'][0] : NULL;
            $colaborador->phone = isset($user['telephonenumber']) ? $user['telephonenumber'][0] : NULL;
            $colaborador->taxvat = isset($user['employeeid']) ? $user['employeeid'][0] : NULL;
            $colaborador->office = isset($user['physicaldeliveryofficename']) ? $user['physicaldeliveryofficename'][0] : NULL;
            $colaborador->role = isset($user['title']) ? $user['title'][0] : NULL;
            $colaborador->objectguid = $user->getConvertedGuid();
            $colaborador->permission_id = $permission;
            $colaborador->save();

            $firstLogin = true;
        } 
   

        return ['firstLogin'=>$firstLogin, 'user'=>$colaborador];
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
            $colaborador = Collaborator::with('permission')->where('objectguid', Auth::user()->getConvertedGuid())->first();


            return response()->json($colaborador);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao buscar seus dados'], 500);
        }
    }

    private function checkPermission($user)
    {
        $group = $user['memberof'];
        $arr_permission = [
            "trainee_w"             => $this->permissionID('Admin'),
            "gerencia_executiva1_w" => $this->permissionID('Executivo'),
            "gerencia_executiva2_w" => $this->permissionID('Executivo'),
            "Gerentes_Operacoes"    => $this->permissionID('Operacao'),
            "rh_checklist_w"        => $this->permissionID('Rh'),
            "rh_book_w"             => $this->permissionID('Rh'),
            "financeiro_book_w"     => $this->permissionID('Fin'),
            "g_TI"                  => $this->permissionID('TI'),
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
