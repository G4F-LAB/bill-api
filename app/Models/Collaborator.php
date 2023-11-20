<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use HasFactory;

    protected $table = 'collaborators';
    protected $primaryKey = 'id_collaborator';


    public function permissao() {
        //belongsTo: (Nome da classe de modelo, foreign_key, 'owner_key')
        return $this->belongsTo(Permission::class, 'id_permission', 'id_permission');
    }

    public function hasPermission($validPermissions) {
        $flag = false;

        foreach($validPermissions as $index => $validPermission) {
            
            $permissao = Permission::where('name',$validPermission)->first();
            if($permissao == NULL) return response()->json(['error' => 'Permissão inválida: '.$validPermission],404);

            if($this->id_permission == $permissao->id_permission) {
                $flag = true;
                break;
            }
        }
        
        return $flag;
    }

    public function permission(){
        return $this->hasMany(Permission::class, 'id_permission', 'id_permission');
    }

    public function contract() {
        return $this->belongsToMany(Contract::class,'collaborator_contracts', 'id_collaborator', 'id_contract')->withTimestamps();
    }
}
