<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\CustomField;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;

class LeadsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    protected $customFields;
    protected $importedCount = 0;

    public function __construct()
    {
        $this->customFields = CustomField::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('label');
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Create the lead
            $lead = Lead::create([
                'first_name' => $row['first_name'],
                'last_name'  => $row['last_name'],
                'email'      => $row['email'],
                'phone'      => $row['phone'] ?? null,
                'company'    => $row['company'] ?? null,
                'status'     => $row['status'] ?? 'new',
                'notes'      => $row['notes'] ?? null,
            ]);

            // Handle custom fields
            $customFieldValues = [];
            foreach ($this->customFields as $label => $field) {
                $key = strtolower($label);
                if (isset($row[$key])) {
                    $lead->customValues()->create([
                        'custom_field_id' => $field->id,
                        'value' => $row[$key]
                    ]);
                    $customFieldValues[$label] = $row[$key];
                }
            }

            // Record import activity
            $importData = array_filter([
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone'] ?? null,
                'company' => $row['company'] ?? null,
                'status' => $row['status'] ?? 'new',
                'notes' => $row['notes'] ?? null,
            ]);

            if (!empty($customFieldValues)) {
                $importData['custom_fields'] = $customFieldValues;
            }

            $lead->recordActivity('imported', 'Lead was imported from Excel file', [
                'import_type' => 'Excel',
                'imported_data' => $importData
            ]);

            $this->importedCount++;
        }
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:leads,email',
            'phone'      => 'nullable|string|max:20',
            'company'    => 'nullable|string|max:255',
            'status'     => 'nullable|in:new,contacted,qualified,lost',
            'notes'      => 'nullable|string',
        ];

        // Add validation rules for custom fields
        foreach ($this->customFields as $field) {
            $rule = $field->required ? 'required' : 'nullable';
            
            switch ($field->type) {
                case 'number':
                    $rule .= '|numeric';
                    break;
                case 'date':
                    $rule .= '|date';
                    break;
                case 'select':
                    $rule .= '|in:' . implode(',', $field->options);
                    break;
                default:
                    $rule .= '|string';
            }

            $rules[strtolower($field->label)] = $rule;
        }

        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'email.unique' => 'A lead with this email already exists.',
            'status.in' => 'Status must be one of: new, contacted, qualified, lost',
        ];
    }
} 