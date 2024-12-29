<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id',
        'admin_id',
        'activity_type',
        'description',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $with = ['admin'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class)->withDefault([
            'name' => 'System'
        ]);
    }

    public function getAdminNameAttribute(): string
    {
        return $this->admin->name ?? 'System';
    }
}
