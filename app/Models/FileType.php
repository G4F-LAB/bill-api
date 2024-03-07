<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class FileType extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'file_types';
    
    protected $fillable = [
        "files_category",
    ];

    public function getActivitylogOptions(): LogOptions
    {        
        return LogOptions::defaults()->useLogName('FileType')->logOnly([
        'files_category'        
        ]);
    }
    public $timestamps = false;

    public static function uploadRules() {
        $rules = [
            'Recursos Humanos' => ['Rh','Operacao','Admin'],
            'Financeiro' => ['Fin','Operacao','Admin']
        ];
        return $rules;
    }
}
