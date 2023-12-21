<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LdapRecord\Container;
use App\Models\Collaborator;
use App\Models\Permission;

class CollaboratorController extends Controller
{

    public function create(Request $request)
    {

        $username = $request->name;
        $permission = $request->permission_id;


        try {
            $existinUser = Collaborator::where('name', $username)->first();

            if ($existinUser) {
                return response()->json(['erro' => 'O colaborador já existe']);
            } else {

                $user = Container::getConnection('default')->query()->where('samaccountname', $username)->first();

                if ($user) {
                    $collaborator = new Collaborator();
                    $collaborator->name = $user['displayname'][0];
                    $collaborator->objectguid = $this->guid_to_str($user['objectguid'][0]);
                    $collaborator->permission_id = $permission;
                    $collaborator->save();

                    return response()->json([$collaborator, 'message' => 'Colaborador adicionado com sucesso!'], 200);
                } else {
                    return response()->json(['error' => 'Usuário não encontrado']);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }



    public function getAllDb(Request $request)
    {


        try {


            $colaboradores = Collaborator::with('permission')->where([
                ['name', '!=', Null],
                [function ($query) use ($request) {
                    if (($s = $request->q)) {
                        $query->orWhere('name', 'LIKE', '%' . $s . '%')
                            // ->orWhere('email', 'LIKE', '%' . $s . '%')
                            ->get();
                    } else if (($id = $request->permission)) {
                        $query->orWhere('permission_id', (int)$id)
                            ->get();
                    }
                }]
            ])->paginate(100);




            foreach ($colaboradores as $index => $colaborador) {
                $encodedGuid = $this->str_to_guid($colaborador->objectguid);

                $adUser = Container::getConnection('default')->query()->where('objectguid', $encodedGuid)->first();

                $grupo_primario = [];
                $grupo_secundario = [];

                if (array_key_exists('memberof', $adUser)) {
                    foreach ($adUser['memberof'] as $key => $value) {
                        if ($key !== 'count') {
                            $explode = explode(',', $value);
                            array_push($grupo_primario, str_replace("CN=", '', $explode[0]));
                            array_push($grupo_secundario, str_replace("OU=", '', str_replace("CN=", '', $explode[1])));
                        }
                    }
                }

                $not_defined = 'Não definido';
                $colaborador->role = array_key_exists('physicaldeliveryofficename', $adUser) ? $adUser['physicaldeliveryofficename'][0] : $not_defined;
                $colaborador->email =  array_key_exists('mail', $adUser) ? $adUser['mail'][0] : $not_defined;
                $colaborador->primary_group = $grupo_primario;
                $colaborador->secondary_group = $grupo_secundario;
            }

            return response()->json($colaboradores, 200);
        } catch (\Exception) {
            return response()->json(['error' => 'Falha ao buscar colaboradores'], 500);
        }
    }



    public function update(Request $request)
    {
        $rules = [
            'collaborator_id' => 'required|numeric|exists:collaborators,collaborator_id',
            'permission_id' => 'required|numeric|exists:permissions,permission_id',
        ];

        $feedback = [
            'collaborator_id.required' => 'O campo de id do colaborador vazio',
            'permission_id.required' => 'O campo de id da permissão vazio',
            'collaborator_id.numeric' => 'O campo de id do colaborador deve ser numérico',
            'permission_id.numeric' => 'O campo de id da permissão deve ser numérico',
            'collaborator_id.exists' => 'Colaborador não encontrado',
            'permission_id.exists' => 'Permissão inválida!',
        ];


        try {
            $colaborador = Collaborator::find($request->collaborator_id);
            $colaborador->permission_id = $request->permission_id;

            $colaborador->save();
            return response()->json(['message' => 'Permissão alterada com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao salvar os dados'], 500);
        }
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

    private function guid_to_str($binary_guid)
    {
        $unpacked = unpack('Va/v2b/n2c/Nd', $binary_guid);
        $uuid = sprintf('%08X-%04X-%04X-%04X-%04X%08X', $unpacked['a'], $unpacked['b1'], $unpacked['b2'], $unpacked['c1'], $unpacked['c2'], $unpacked['d']);
        return mb_strtolower($uuid);
    }


    public function collaboratorsByPermission()
    {
        $permissions = Permission::get();
        $data = array();
        foreach ($permissions as $key => $permission) {
            $collaborator = Collaborator::where('permission_id', $permission->id)->orderBy('id')->get();

            $item = array(
                'id' => $permission->id,
                'name' => $permission->name,
                'total' => $collaborator->count(),
                'collaborators' => $collaborator,
            );
            array_push($data, $item);
        }
        return $data;
    }
}
