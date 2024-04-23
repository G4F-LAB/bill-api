<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contract extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $primaryKey = 'id';
    protected $hidden = ['pivot'];
    protected $appends = ['checklist_current'];

    protected $fillable = [
        'client_id',
        'name',
        'contractual_situation',
        'manager_id',
        'status_id',
        'alias'
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Contract')->logOnly([
            'client_id',
            'name',
            'contractual_situation',
            'manager_id',
            'status_id',
            'alias'
        ]);
    }

    public function collaborators() {
        return $this->belongsToMany(Collaborator::class,'operations','manager_id', 'manager_id')->withTimestamps();
    }

    public function manager() {
        return $this->belongsTo(Operation::class,'manager_id', 'id');
    }

    public function checklist(){
        return $this->hasMany(Checklist::class);
    }


    public function operation() {
        return $this->belongsTo(Operation::class);
    }
    public function status() {
        return $this->belongsTo(StatusContract::class);
    }

    //current checklist
    protected function checklistCurrent(): Attribute
    {
        $initials =  [];

        $current_checklist = Checklist::where('contract_id', $this->id)->with('itens.fileNaming')->latest()->first();

        return new Attribute(
            get: fn () => $current_checklist,
        );
    }

}
