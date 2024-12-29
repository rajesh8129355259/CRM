<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadCategory;
use App\Models\Admin;
use Illuminate\Http\Request;

class LeadCategoryController extends Controller
{
    public function index()
    {
        $categories = LeadCategory::with('defaultAdmin')->orderBy('name')->get();
        return view('admin.lead-categories.index', compact('categories'));
    }

    public function create()
    {
        $admins = Admin::where('is_active', true)->orderBy('name')->get();
        return view('admin.lead-categories.create', compact('admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:lead_categories',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'default_admin_id' => 'nullable|exists:admins,id'
        ]);

        LeadCategory::create($validated);

        return redirect()->route('admin.lead-categories.index')
            ->with('success', 'Lead category created successfully.');
    }

    public function edit(LeadCategory $leadCategory)
    {
        $admins = Admin::where('is_active', true)->orderBy('name')->get();
        return view('admin.lead-categories.edit', compact('leadCategory', 'admins'));
    }

    public function update(Request $request, LeadCategory $leadCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:lead_categories,name,' . $leadCategory->id,
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'default_admin_id' => 'nullable|exists:admins,id'
        ]);

        $leadCategory->update($validated);

        return redirect()->route('admin.lead-categories.index')
            ->with('success', 'Lead category updated successfully.');
    }

    public function destroy(LeadCategory $leadCategory)
    {
        $leadCategory->delete();

        return redirect()->route('admin.lead-categories.index')
            ->with('success', 'Lead category deleted successfully.');
    }
} 