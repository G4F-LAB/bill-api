<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class FileNaming extends Model
{
    use HasFactory;
    use LogsActivity;
    protected $table = 'file_naming';

    protected $fillable = [
        'file_name',
        'standard_file_naming',
    ];

    public function rules()
    {
        return [
            'id' => 'required|string',
            'file_name' => 'required|string',
            'standard_file_naming' => 'required|numeric'
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('FileNaming')->logOnly([
            'file_name',
            'standard_file_naming'
        ]);
    }

    public function feedback()
    {
        return [
            'id.required' => 'O ID é obrigatório.',
            'file_name.required' => 'O campo Nome do arquivo é de preenchimento obrigatório.',
            'standard_file_naming.required' => 'O campo Nomenclatura é de preenchimento obrigatório.'
        ];

    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

}
