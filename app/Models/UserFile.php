<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class UserFile extends Model
{

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $connection =  'data_G4F';

    protected $fillable = [
        'user_id',
        'filename',
        'file_path',
        'file_type',
    ];


    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::uuid();
            }
        });
    }
}
