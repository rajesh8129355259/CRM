<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $customFields = CustomField::orderBy('sort_order')->get();
        return view('admin.custom_fields.index', compact('customFields'));
    }

    public function create()
    {
        return view('admin.custom_fields.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:custom_fields',
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,date,textarea',
            'options' => 'nullable|array',
            'required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if ($request->type === 'select' && empty($validated['options'])) {
            return back()->withErrors(['options' => 'Options are required for select type fields.']);
        }

        CustomField::create($validated);
        return redirect()->route('admin.custom-fields.index')->with('success', 'Custom field created successfully.');
    }

    public function edit(CustomField $customField)
    {
        return view('admin.custom_fields.edit', compact('customField'));
    }

    public function update(Request $request, CustomField $customField)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:custom_fields,name,' . $customField->id,
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,date,textarea',
            'options' => 'nullable|array',
            'required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if ($request->type === 'select' && empty($validated['options'])) {
            return back()->withErrors(['options' => 'Options are required for select type fields.']);
        }

        $customField->update($validated);
        return redirect()->route('admin.custom-fields.index')->with('success', 'Custom field updated successfully.');
    }

    public function destroy(CustomField $customField)
    {
        $customField->delete();
        return redirect()->route('admin.custom-fields.index')->with('success', 'Custom field deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:custom_fields,id',
            'fields.*.sort_order' => 'required|integer'
        ]);

        foreach ($request->fields as $field) {
            CustomField::where('id', $field['id'])->update(['sort_order' => $field['sort_order']]);
        }

        return response()->json(['message' => 'Fields reordered successfully']);
    }
}
