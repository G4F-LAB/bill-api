<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Collaborator extends Model
{
    use HasFactory;
    use LogsActivity;
    protected $primaryKey = 'id';
    protected $table = 'collaborators';
    protected $appends = ['name_initials'];
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


    public function contracts()
    {
        return $this->belongsToMany(Contract::class, 'collaborator_contracts', 'collaborator_id', 'contract_id')->withTimestamps();
    }

    public function manager()
    {
        return $this->hasMany(Contract::class, 'id', 'manager_id');
    }

    public function operation() {
        return $this->hasMany(Operation::class,'manager_id','id');
    }

    public function executive() {
        return $this->hasOne(Executive::class, 'manager_id', 'id');
    }

    protected function nameInitials(): Attribute
    {
        preg_match('/(?:\w+\. )?(\w+).*?(\w+)(?: \w+\.)?$/', $this->name, $result);
        $initials =  strtoupper($result[1][0] . $result[2][0]);

        return new Attribute(
            get: fn () => $initials,
        );
    }

    public function getAuthUser() {
        return $this->where('objectguid', Auth::user()->getConvertedGuid())->first();
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
}
