<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $connection =  'book';
    protected $table = 'itens';
    protected $primaryKey = 'id';
    protected $hidden = ['pivot'];
    protected $fillable = [
        'file_type_id',
        'status',
        'file_name_id',
        'checklist_id',
        'mandatory',
        'file_competence_id'
    ];

    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([ 
            'file_type_id',
            'status',
            'file_name_id',
            'checklist_id',
            'mandatory',
            'file_competence_id',
            'checklist',
            'user_id',
            'itens',
            'status',
            'contract',
            'file_name',
            'files',
            'file_competence',
            'file_itens'
        ])->useLogName('Item')->dontLogIfAttributesChangedOnly(['updated_at'])->logOnlyDirty();

    }

    public function rules()
    {
        return [
            'checklist_id' => 'required',
            'file_naming_id' => 'required',
            'file_type_id' => 'required',
            'status' => 'required',
            'competence' => 'required',
            'mandatory' => true
        ];
    }

    public function feedback()
    {
        return [
            'checklist_id.required' => 'O campo do checklist_id é de preenchimento obrigatório.',
            'file_naming_id.required' => 'O campo do id da nomeclatura de arquivo é de preenchimento obrigatório.',
            'file_type_id.required' => 'O campo do id de tipo de arquivo é de preenchimento obrigatório.',
            'status.required' => 'O campo de status é de preenchimento obrigatório.',
            'competence.required' => 'O campo do competencia é de preenchimento obrigatório.'

        ];
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function file_nomenclatures()
    {
        return $this->belongsTo(Item::class, 'file_nomenclatures', 'file_naming_id', 'id');
    }

    public function file_types()
    {
        return $this->belongsTo(Item::class, 'file_types', 'file_type_id', 'id');
    }

    public function fileNaming()
    {
        return $this->belongsTo(FileNaming::class);
    }

    public function file_name()
    {
        return $this->belongsTo(FileName::class);
    }

    public function file_itens()
    {
        return $this->hasMany(FilesItens::class);
    }

    public function file_competence()
    {
        return $this->belongsTo(FileCompetence::class);
    }


    public function files()
    {
        return $this->belongsToMany(File::class, 'files_itens', 'item_id', 'file_id');
    }
}
