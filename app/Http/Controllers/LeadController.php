<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\CustomField;
use App\Exports\LeadsExport;
use App\Imports\LeadsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;
use App\Models\LeadCategory;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $leads = $query->paginate(10)->withQueryString();
        $categories = LeadCategory::where('is_active', true)->orderBy('name')->get();

        return view('leads.index', compact('leads', 'categories'));
    }

    public function create()
    {
        $categories = LeadCategory::where('is_active', true)->orderBy('name')->get();
        return view('leads.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'status' => 'required|string|in:new,contacted,qualified,lost',
            'category_id' => 'nullable|exists:lead_categories,id',
            'notes' => 'nullable|string',
        ]);

        $lead = Lead::create($validated);

        // Handle custom fields
        if ($request->has('custom_fields')) {
            foreach ($request->custom_fields as $fieldId => $value) {
                if ($value !== null && $value !== '') {
                    $lead->customValues()->create([
                        'custom_field_id' => $fieldId,
                        'value' => $value
                    ]);
                }
            }
        }

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['customValues.customField', 'activities.admin']);
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $categories = LeadCategory::where('is_active', true)->orderBy('name')->get();
        return view('leads.edit', compact('lead', 'categories'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'status' => 'required|string|in:new,contacted,qualified,lost',
            'category_id' => 'nullable|exists:lead_categories,id',
            'notes' => 'nullable|string',
        ]);

        $lead->update($validated);

        // Handle custom fields
        if ($request->has('custom_fields')) {
            // Delete existing custom values
            $lead->customValues()->delete();

            // Create new custom values
            foreach ($request->custom_fields as $fieldId => $value) {
                if ($value !== null && $value !== '') {
                    $lead->customValues()->create([
                        'custom_field_id' => $fieldId,
                        'value' => $value
                    ]);
                }
            }
        }

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function export(Request $request)
    {
        if (!Gate::allows('export_leads')) {
            return redirect()->route('leads.index')
                ->with('error', 'You do not have permission to export leads.');
        }

        try {
            return Excel::download(new LeadsExport($request), 'leads.xlsx');
        } catch (\Exception $e) {
            return redirect()->route('leads.index')
                ->with('error', 'Failed to export leads. ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        if (!Gate::allows('import_leads')) {
            return redirect()->route('leads.index')
                ->with('error', 'You do not have permission to import leads.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $import = new LeadsImport;
            Excel::import($import, $request->file('file'));

            return redirect()->route('leads.index')
                ->with('success', $import->getImportedCount() . ' leads imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(function ($failure) {
                return "Row {$failure->row()}: {$failure->errors()[0]}";
            })->join('<br>');

            return redirect()->route('leads.index')
                ->with('error', 'Import failed. ' . $errors);
        } catch (\Exception $e) {
            return redirect()->route('leads.index')
                ->with('error', 'Failed to import leads. ' . $e->getMessage());
        }
    }
}
