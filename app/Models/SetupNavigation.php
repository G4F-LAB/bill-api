<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SetupNavigation extends Model
{
    use HasFactory;
    // use LogsActivity;

    protected $table = 'setup_navigations';
    protected $primaryKey = 'id';
    protected $appends = ['title'];

    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'path',
        'slug',
        'sort',
        'permission_ids'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('SetupNavigation')->logOnly([
            'parent_id',
            'name',
            'icon',
            'path',
            'slug',
            'sort',
            'permission_ids'
        ]);
    }

    protected function title(): Attribute
    {

        return new Attribute(
            get: fn () => $this->name,
        );
    }
}
