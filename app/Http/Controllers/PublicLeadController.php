<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicLeadController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $response = $next($request);
            
            // Add CORS headers to all responses
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Accept, X-CSRF-TOKEN');
            
            return $response;
        });
    }

    public function form($token = null)
    {
        $categories = \App\Models\LeadCategory::where('is_active', true)->orderBy('name')->get();
        return view('public.leads.form', compact('categories', 'token'));
    }

    public function store(Request $request)
    {
        // Handle preflight request
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json([], 200);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:new,contacted,qualified,lost'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the lead
            $lead = Lead::create($validator->validated());

            // Return success response
            return response()->json([
                'message' => 'Lead created successfully',
                'lead' => $lead
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Lead creation error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create lead',
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }

    public function script()
    {
        $filePath = public_path('leads/embed.js');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Script file not found'], 404);
        }
        
        return response()->file($filePath, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}
