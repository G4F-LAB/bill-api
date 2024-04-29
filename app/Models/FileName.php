<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class FileName extends Model
{
    use HasFactory;
    use LogsActivity;
    protected $connection =  'book';

    protected $fillable = [
        'file_name',
        'standard_file_naming',
        'file_group',
        'automate',
        'automate_path',
        'automate_fullpath',
        'file_type_id'
    ];

    // public function rules()
    // {
    //     return [
    //         'id' => 'required|string',
    //         'file_name' => 'required|string',
    //         'standard_file_naming' => 'required|numeric'
    //     ];
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('FileNaming')->logOnly([
            'file_name',
            'standard_file_naming',
            'file_group',
            'file_type_id',
            'automate',
            'automate_path',
            'automate_fullpath'
        ]);
    }

    // public function feedback()
    // {
    //     return [
    //         'id.required' => 'O ID é obrigatório.',
    //         'file_name.required' => 'O campo Nome do arquivo é de preenchimento obrigatório.',
    //         'standard_file_naming.required' => 'O campo Nomenclatura é de preenchimento obrigatório.'
    //     ];

    // }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function type() {
        return $this->belongsTo(FileType::class,'file_type_id','id');
    }
}
