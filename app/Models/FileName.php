<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\IntegrationTask;
class FileName extends Model
{
    use HasFactory;
    // use LogsActivity;

    protected $connection = 'book';

    protected $fillable = [
        'name',
        'standard_file_naming',
        'file_group',
        'automate',
        'automate_path',
        'integration_task_id',
        'per_user',
        'is_general'
    ];

    public function rules()
    {
        return [
            'name' => 'required|string',
            'standard_file_naming' => 'required|string',
            'file_group' => 'required|string',
            'automate' => 'boolean',
            'automate_path' => 'string|nullable',
            'integration_task_id' => 'integer|nullable',
            'per_user' => 'boolean',
            'is_general' => 'boolean'
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('FileNaming')->logOnly([
            'name',
            'standard_file_naming',
            'file_group',
            'file_type_id',
            'automate',
            'automate_path',
            'automate_fullpath'
        ]);
    }

    public function feedback()
    {
        return [
            'name.required' => 'O campo Nome do arquivo é obrigatório.',
            'standard_file_naming.required' => 'O campo Nomenclatura é obrigatório.',
            'file_group.required' => 'O campo Grupo é obrigatório.',
            'automate.boolean' => 'O campo Automação deve ser um valor booleano.',
            'automate_path.string' => 'O campo Caminho de automação deve ser uma string.',
            'integration_task_id.integer' => 'O campo ID da tarefa de integração deve ser um número inteiro.',
            'per_user.boolean' => 'O campo Por usuário deve ser um valor booleano.',
            'is_general.boolean' => 'O campo É geral deve ser um valor booleano.'
        ];
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function type()
    {
        return $this->belongsTo(FileType::class, 'file_type_id', 'id');
    }

    public function task()
    {
        return $this->belongsTo(IntegrationTask::class, 'integration_task_id', 'id');
    }
}
