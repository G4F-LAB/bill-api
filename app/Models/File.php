<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class File extends Model
{
    use HasFactory;

    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $connection =  'book';
    protected $hidden = ['pivot'];
    protected $appends = ['full_path'];

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
            'path'
        ]);
    }

    public function getFullPathAttribute()
    {
        // Check if the path starts with "storage"
        if (strpos($this->path, 'storage') === 0) {
            return asset($this->path); // If it's already a storage path, return it
        }

        // Otherwise, prepend the storage path
        return storage_path('app/public/' . ltrim($this->path, '/'));
    }

    public function itens()
    {
        return $this->belongsToMany(Item::class,'files_itens','file_id','item_id')->withTimestamps();
    }

}
