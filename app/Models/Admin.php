<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'capabilities'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capabilities' => 'array',
        'email_verified_at' => 'datetime',
    ];

    public function hasCapability($capability)
    {
        if ($this->role === 'admin') {
            return true;
        }

        return in_array($capability, $this->capabilities ?? []);
    }

    public function hasAnyCapability(array $capabilities)
    {
        if ($this->role === 'admin') {
            return true;
        }

        return !empty(array_intersect($capabilities, $this->capabilities ?? []));
    }

    public function hasAllCapabilities(array $capabilities)
    {
        if ($this->role === 'admin') {
            return true;
        }

        return empty(array_diff($capabilities, $this->capabilities ?? []));
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isViewer()
    {
        return $this->role === 'viewer';
    }
}
