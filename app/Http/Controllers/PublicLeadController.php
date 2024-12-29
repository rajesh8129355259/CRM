<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Admin;
use App\Models\LeadCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicLeadController extends Controller
{
    public function form($token = null)
    {
        $categories = LeadCategory::where('is_active', true)->get();
        return view('public.leads.form', compact('categories', 'token'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:lead_categories,id',
            'notes' => 'nullable|string',
        ]);

        // Set default status for new leads
        $validated['status'] = 'new';

        // Auto-assign to admin based on category if available
        if (!empty($validated['category_id'])) {
            $category = LeadCategory::find($validated['category_id']);
            if ($category && $category->default_admin_id) {
                $validated['assigned_to'] = $category->default_admin_id;
            }
        }

        // Create the lead
        $lead = Lead::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lead submitted successfully!'
        ]);
    }

    public function script()
    {
        $script = view('public.leads.script')->render();
        return response($script)->header('Content-Type', 'application/javascript');
    }
}
