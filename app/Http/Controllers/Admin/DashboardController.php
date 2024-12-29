<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $totalLeads = Lead::count();
        $qualifiedLeads = Lead::where('status', 'qualified')->count();
        $newLeads = Lead::where('status', 'new')->count();
        $recentLeads = Lead::latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalLeads',
            'qualifiedLeads',
            'newLeads',
            'recentLeads'
        ));
    }
}
