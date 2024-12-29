<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }
        return !!$this->permissions->intersect($permission)->count();
    }
}
