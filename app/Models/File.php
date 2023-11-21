<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';
    protected $primaryKey = 'id';

    protected $fillable = [
        'item_id',
        'complementary_name',
        'path'
    ];

    public function item()
    {
        return $this->hasOne('Item');
    }
}
