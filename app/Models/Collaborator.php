<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use LdapRecord\Container;

class Collaborator extends Model
{
    use HasFactory;
    use LogsActivity;
    protected $primaryKey = 'id';
    protected $table = 'collaborators';
    protected $appends = ['name_initials', 'email'];
    protected $hidden = ['pivot'];
    protected $fillable = [
        'id',
        'collaborators',
        'name_initials',
        'pivot',
    ];

    public function getActivitylogOptions(): LogOptions
    {        
        return LogOptions::defaults()->useLogName('Contract')->logOnly([
            'id',
            'collaborators',
            'name_initials',
            'pivot',
        ]);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function hasPermission($validPermissions)
    {
        $flag = false;

        foreach ($validPermissions as $index => $validPermission) {

            $permissao = Permission::where('name', $validPermission)->first();
            if ($permissao == NULL) return response()->json(['error' => 'Permissão inválida: ' . $validPermission], 404);

            if ($this->permission_id == $permissao->id) {
                $flag = true;
                break;
            }
        }

        return $flag;
    }


    // public function contracts()
    // {
    //     return $this->belongsToMany(Contract::class, 'collaborator_contracts', 'collaborator_id', 'contract_id')->withTimestamps();
    // }

    public function manager()
    {
        return $this->hasMany(Contract::class, 'id', 'manager_id');
    }

    public function operations()
    {
        return $this->belongsToMany(Operation::class, 'collaborator_operations', 'collaborator_id', 'operation_id')->withTimestamps();
    }

    public function executive() {
        return $this->hasOne(Executive::class, 'manager_id', 'id');
    }

    public function executives() {
        return $this->belongsTo(Executive::class,'manager_id','id');
    }

    protected function nameInitials(): Attribute
    {
        preg_match('/(?:\w+\. )?(\w+).*?(\w+)(?: \w+\.)?$/', $this->name, $result);
        $initials =  strtoupper($result[1][0] . $result[2][0]);

        return new Attribute(
            get: fn () => $initials,
        );
    }


    protected function email(): Attribute
    {
        $adUser = Container::getConnection('default')->query()->where('objectguid', $this->str_to_guid($this->objectguid))->first();

        $email =  array_key_exists('mail', $adUser) ? $adUser['mail'][0] : null;

        return new Attribute(
            get: fn () => $email,
        );
    }


    public function getAuthUser() {
        if(Auth::user()){
            return $this->where('objectguid', Auth::user()->getConvertedGuid())->first();
        } 
       
    }

    public function getAuthUserPermission() {
        $user = $this->getAuthUser();
        return Permission::where('id',$user->permission_id)->first();
    }

    public function is_analyst() {
        $analyst = false;
        $permission = Permission::where('name','ilike','%Analista%')->first();
        if($permission->id == $this->permission_id) {
            $analyst = true;
        }
        return $analyst;
    }

    public function is_executive() {
        $executive = false;
        $permission = Permission::where('name','ilike','%Executivo%')->first();
        if($permission->id == $this->permission_id) {
            $executive = true;
        }
        return $executive;
    }

    public function is_manager() {
        $manager = false;
        $permission = Permission::where('name','ilike','%Operacao%')->first();
        if($permission->id == $this->permission_id) {
            $manager = true;
        }
        return $manager;
    }

    public function is_hr() {
        $hr = false;
        $permission = Permission::where('name','ilike','%Rh%')->first();
        if($permission->id == $this->permission_id) {
            $hr = true;
        }
        return $hr;
    }

    public function is_fin() {
        $fin = false;
        $permission = Permission::where('name','ilike','%Fin%')->first();
        if($permission->id == $this->permission_id) {
            $fin = true;
        }
        return $fin;
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

    public function routeNotificationFor()
    {
        return 'm@ninaut.com'; //You e-mail property here
    }
}
