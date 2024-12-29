@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Leads</h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('leads.create') }}" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Lead
                        </a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Import/Export
                            </button>
                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                <div class="py-1">
                                    <a href="{{ route('leads.export', request()->query()) }}" 
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Export Leads
                                    </a>
                                    <button @click="$refs.importForm.click()" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Import Leads
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Import Form (Hidden) -->
                <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="file" x-ref="importForm" 
                        class="hidden" accept=".csv" 
                        onchange="this.form.submit()">
                </form>

                <!-- Filters -->
                <form action="{{ route('leads.index') }}" method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Search leads...">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Status</option>
                            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="qualified" {{ request('status') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                            <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-1">
                        <label for="category" class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
                        <select id="category" name="category" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4 flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Apply Filters
                        </button>
                        <a href="{{ route('leads.index') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Clear Filters
                        </a>
                    </div>
                </form>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('leads.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_order' => request('sort_order', 'asc') === 'asc' ? 'desc' : 'asc'])) }}" 
                                        class="flex items-center">
                                        Name
                                        @if(request('sort_by') === 'name')
                                            <span class="ml-1">
                                                @if(request('sort_order', 'asc') === 'asc') ↑ @else ↓ @endif
                                            </span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('leads.index', array_merge(request()->query(), ['sort_by' => 'email', 'sort_order' => request('sort_order', 'asc') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="flex items-center">
                                        Email
                                        @if(request('sort_by') === 'email')
                                            <span class="ml-1">
                                                @if(request('sort_order', 'asc') === 'asc') ↑ @else ↓ @endif
                                            </span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('leads.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_order' => request('sort_order', 'asc') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="flex items-center">
                                        Status
                                        @if(request('sort_by') === 'status')
                                            <span class="ml-1">
                                                @if(request('sort_order', 'asc') === 'asc') ↑ @else ↓ @endif
                                            </span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('leads.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => request('sort_order', 'asc') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="flex items-center">
                                        Created At
                                        @if(request('sort_by') === 'created_at')
                                            <span class="ml-1">
                                                @if(request('sort_order', 'asc') === 'asc') ↑ @else ↓ @endif
                                            </span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('leads.index', array_merge(request()->query(), ['sort_by' => 'category', 'sort_order' => request('sort_order', 'asc') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="flex items-center">
                                        {{ __('Category') }}
                                        @if(request('sort_by') === 'category')
                                            <span class="ml-1">
                                                @if(request('sort_order', 'asc') === 'asc') ↑ @else ↓ @endif
                                            </span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($leads as $lead)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $lead->first_name }} {{ $lead->last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $lead->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $lead->company ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($lead->status === 'new') bg-blue-100 text-blue-800
                                        @elseif($lead->status === 'contacted') bg-yellow-100 text-yellow-800
                                        @elseif($lead->status === 'qualified') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $lead->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($lead->category)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $lead->category->color }}20; color: {{ $lead->category->color }}">
                                            {{ $lead->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">{{ __('Uncategorized') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('leads.show', $lead) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('leads.edit', $lead) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $leads->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
@endpush
@endsection 