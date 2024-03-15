<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class File extends Model
{
    use HasFactory;
    use LogsActivity;
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $hidden = ['pivot'];

    protected $fillable = [
        'item_id',
        'complementary_name',
        'path'
    ];

    public function getActivitylogOptions(): LogOptions
    {        
        return LogOptions::defaults()->useLogName('File')->logOnly([
            'item_id',
            'complementary_name',
            'path'
        ]);
    }

    public function itens()
    {
        return $this->belongsToMany(Item::class,'files_itens','file_id','item_id')->withTimestamps();
    }
}
