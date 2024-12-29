<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadCategory extends Model
{
    protected $fillable = [
        'name',
        'color',
        'description',
        'is_active',
        'default_admin_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'category_id');
    }

    public function defaultAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'default_admin_id');
    }
} 