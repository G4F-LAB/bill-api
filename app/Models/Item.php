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

    protected $table = 'itens';
    protected $primaryKey = 'id';
    protected $fillable = [
        'file_type_id',
        'status',
        'file_naming_id',
        'checklist_id'
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('item')->logOnly(['file_type_id',
        'status',
        'file_naming_id',
        'checklist_id']);


    }

    public function rules()
    {
        return [
            'checklist_id' => 'required',
            'file_naming_id' => 'required',
            'file_type_id' => 'required',
            'status' => 'required',
            'competence' => 'required'
        ];
    }

    public function feedback() {
        return[
            'checklist_id.required' => 'O campo do checklist_id é de preenchimento obrigatório.',
            'file_naming_id.required' => 'O campo do id da nomeclatura de arquivo é de preenchimento obrigatório.',
            'file_type_id.required' => 'O campo do id de tipo de arquivo é de preenchimento obrigatório.',
            'status.required' => 'O campo de status é de preenchimento obrigatório.',
            'competence.required' => 'O campo do competencia é de preenchimento obrigatório.'

        ];

    }

    public function checklist() {
        return $this->belongsTo(Checklist::class);
    }

    public function file_nomenclatures() {
        return $this->belongsTo(Item::class,'file_nomenclatures', 'file_naming_id', 'id');
    }

    public function file_types() {
        return $this->belongsTo(Item::class,'file_types', 'file_type_id', 'id');
    }

    public function fileNaming()
    {
        return $this->belongsTo(FileNaming::class);
    }

    public function files() {
        return $this->hasMany(File::class);
    }
}
