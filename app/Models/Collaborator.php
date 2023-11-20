<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use HasFactory;

    protected $table = 'collaborators';


    public function permissao() {
        //belongsTo: (Nome da classe de modelo, foreign_key, 'owner_key')
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function hasPermission($validPermissions) {
        $flag = false;

        foreach($validPermissions as $index => $validPermission) {
            
            $permissao = Permission::where('name',$validPermission)->first();
            if($permissao == NULL) return response()->json(['error' => 'Permissão inválida: '.$validPermission],404);

            if($this->permission_id == $permissao->id) {
                $flag = true;
                break;
            }
        }
        
        return $flag;
    }

    public function permission(){
        return $this->hasMany(Permission::class, 'permission_id', 'id');
    }

    public function contract() {
        return $this->belongsToMany(Contract::class,'collaborator_contracts', 'id', 'contract_id')->withTimestamps();
    }
}
