<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'group',
        'description'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public static function groups()
    {
        return self::select('group')
            ->distinct()
            ->pluck('group');
    }
}
