<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCustomValue extends Model
{
    protected $fillable = [
        'lead_id',
        'custom_field_id',
        'value'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }
}
