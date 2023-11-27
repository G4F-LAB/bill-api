<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LdapRecord\Container;
use App\Models\Collaborator;
use App\Models\Permission;

class CollaboratorController extends Controller
{
    public function getAllDb()
    {
        try {
            $colaboradores = Collaborator::with('permissao')->orderBy('id')->get();

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
                            array_push($grupo_secundario, str_replace("OU=", '', str_replace("CN=",'',$explode[1])));
                        }
                    }
                }

                $not_defined = 'Não definido';
                $colaborador->nome = array_key_exists('cn', $adUser) ? $adUser['cn'][0] : $not_defined;
                $colaborador->cargo = array_key_exists('physicaldeliveryofficename', $adUser) ? $adUser['physicaldeliveryofficename'][0] : $not_defined;
                $colaborador->email =  array_key_exists('mail', $adUser) ? $adUser['mail'][0] : $not_defined;
                $colaborador->grupo_primario = $grupo_primario;
                $colaborador->grupo_secundario = $grupo_secundario;
            }

            return response()->json($colaboradores, 200);
        } catch (\Exception) {
            return response()->json(['error' => 'Falha ao buscar colaboradores'], 500);
        }
    }



    public function update(Request $request)
    {
        $rules = [
            'id_collaborator' => 'required|numeric|exists:collaborators,id_collaborator',
            'id_permission' => 'required|numeric|exists:permissions,id_permission',
        ];

        $feedback = [
            'id_collaborator.required' => 'O campo de id do colaborador vazio',
            'id_permission.required' => 'O campo de id da permissão vazio',
            'id_collaborator.numeric' => 'O campo de id do colaborador deve ser numérico',
            'id_permission.numeric' => 'O campo de id da permissão deve ser numérico',
            'id_collaborator.exists' => 'Colaborador não encontrado',
            'id_permission.exists' => 'Permissão inválida!',
        ];

        $request->validate($rules, $feedback);

        try {
            $colaborador = Collaborator::find($request->id_collaborator);
            $colaborador->id_permission = $request->id_permission;

            $colaborador->save();
            return response()->json(['message' => 'Permissão alterada com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao salvar os dados'], 500);
            // return response()->json(['error' => $e->getMessage()], 500);
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


    public function collaboratorsByPermission(){
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
