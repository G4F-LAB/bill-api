<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Events\FilesItensSaved;

class FilesItens extends Model
{
    // use LogsActivity;
    protected $primaryKey = 'id';
    protected $table = 'files_itens';

    protected $dispatchesEvents = [
        'saved' => \App\Events\FilesItensSaved::class,
        'deleted' => \App\Events\FilesItensSaved::class,
        // 'deleted' => 'App\Events\FilesItensDeleted',
    ];

    protected $fillable = [
        'item_id',
        'file_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    use HasFactory;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('file_item')->logOnly([
            'item_id',
            'file_id',
            'created_at',
            'updated_at',
            'deleted_at']);
    }

    

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

 
}
