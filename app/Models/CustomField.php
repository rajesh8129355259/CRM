<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = [
        'name',
        'label',
        'type',
        'options',
        'required',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function leadValues()
    {
        return $this->hasMany(LeadCustomValue::class);
    }
}
