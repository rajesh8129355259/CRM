<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class LeadsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;
    protected $customFields;
    protected $query;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->customFields = CustomField::where('is_active', true)->orderBy('sort_order')->get();
        $this->query = $this->buildQuery();
        
        // Record export activity for each lead
        $this->query->chunk(100, function ($leads) {
            foreach ($leads as $lead) {
                $filters = array_filter([
                    'status' => $this->request->status,
                    'date_from' => $this->request->date_from,
                    'date_to' => $this->request->date_to,
                    'search' => $this->request->search,
                ]);

                $description = 'Lead was exported to Excel';
                if (!empty($filters)) {
                    $description .= ' with filters: ' . implode(', ', array_map(function($key, $value) {
                        return "$key: $value";
                    }, array_keys($filters), $filters));
                }

                $lead->recordActivity('exported', $description, [
                    'export_type' => 'Excel',
                    'filters' => $filters
                ]);
            }
        });
    }

    protected function buildQuery()
    {
        $query = Lead::query()->with(['customValues.customField']);

        // Apply filters
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $this->request->date_to);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        $headers = [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Company',
            'Status',
            'Notes',
            'Created At',
            'Updated At'
        ];

        // Add custom field headers
        foreach ($this->customFields as $field) {
            $headers[] = $field->label;
        }

        return $headers;
    }

    public function map($lead): array
    {
        $row = [
            $lead->id,
            $lead->first_name,
            $lead->last_name,
            $lead->email,
            $lead->phone,
            $lead->company,
            $lead->status,
            $lead->notes,
            $lead->created_at->format('Y-m-d H:i:s'),
            $lead->updated_at->format('Y-m-d H:i:s')
        ];

        // Add custom field values
        foreach ($this->customFields as $field) {
            $value = $lead->customValues
                ->where('custom_field_id', $field->id)
                ->first();
            $row[] = $value ? $value->value : '';
        }

        return $row;
    }
} 