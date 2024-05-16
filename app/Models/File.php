<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Storage;
class File extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $connection =  'book';
    protected $hidden = ['pivot'];
    protected $appends = ['full_path', 'basename'];

    protected $fillable = [
        'item_id',
        'complementary_name',
        'created_at',
        'updated_at',
        'path'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('File')->logOnly([
            'item_id',
            'complementary_name',
            'created_at',
            'updated_at',
            'path',
            'itens'
        ])->dontLogIfAttributesChangedOnly(['updated_at'])->logOnlyDirty();
    }

    public function getFullPathAttribute()
    {
       return 'https://g4fcombr.sharepoint.com/sites/NuvemBill/Documentos%20Compartilhados/'. $this->path;
    }
    
    

    public function getBasenameAttribute()
    {

        return basename($this->path);
    }

    public function itens()
    {
        return $this->belongsToMany(Item::class,'files_itens','file_id','item_id')->withTimestamps();
    }

}
