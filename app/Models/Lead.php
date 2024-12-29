<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'status',
        'category_id',
        'notes'
    ];

    protected $dates = ['deleted_at'];

    protected static function booted()
    {
        static::created(function ($lead) {
            $lead->recordActivity('created', 'Lead was created');
        });

        static::updated(function ($lead) {
            $changes = $lead->getChanges();
            unset($changes['updated_at']);
            
            if (!empty($changes)) {
                $changedFields = array_keys($changes);
                $description = 'Updated ' . implode(', ', array_map(function($field) {
                    return strtolower(str_replace('_', ' ', $field));
                }, $changedFields));

                $oldValues = array_intersect_key($lead->getOriginal(), $changes);
                $newValues = $changes;

                // Format status changes
                if (isset($oldValues['status'])) {
                    $oldValues['status'] = ucfirst($oldValues['status']);
                    $newValues['status'] = ucfirst($newValues['status']);
                }

                $lead->recordActivity('updated', $description, [
                    'old' => $oldValues,
                    'new' => $newValues
                ]);
            }
        });

        static::deleted(function ($lead) {
            $lead->recordActivity('deleted', 'Lead was deleted');
        });
    }

    public function recordActivity($type, $description, $changes = null)
    {
        $this->activities()->create([
            'admin_id' => Auth::guard('admin')->id(),
            'activity_type' => $type,
            'description' => $description,
            'changes' => $changes
        ]);
    }

    public function customValues()
    {
        return $this->hasMany(LeadCustomValue::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function getCustomFieldValue($fieldName)
    {
        $customField = CustomField::where('name', $fieldName)->first();
        if (!$customField) return null;

        $value = $this->customValues()
            ->where('custom_field_id', $customField->id)
            ->first();

        return $value ? $value->value : null;
    }

    public function setCustomFieldValue($fieldName, $value)
    {
        $customField = CustomField::where('name', $fieldName)->first();
        if (!$customField) return;

        $this->customValues()->updateOrCreate(
            ['custom_field_id' => $customField->id],
            ['value' => $value]
        );
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LeadCategory::class);
    }
}
