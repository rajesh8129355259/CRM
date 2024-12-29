@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Lead Details</h1>
            <div class="flex space-x-4">
                <a href="{{ route('admin.leads.edit', $lead) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                    Edit
                </a>
                <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this lead?')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Lead
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Lead Information</h3>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Full name</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->first_name }} {{ $lead->last_name }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->email }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->phone ?? 'N/A' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Company</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->company ?? 'N/A' }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $lead->status === 'new' ? 'bg-green-100 text-green-800' : ($lead->status === 'contacted' ? 'bg-blue-100 text-blue-800' : ($lead->status === 'qualified' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($lead->status) }}
                        </span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $lead->notes ?? 'No notes' }}</dd>
                </div>

                @if($lead->customValues->isNotEmpty())
                    <div class="bg-gray-50 px-4 py-5 sm:px-6">
                        <h4 class="text-lg font-medium text-gray-900">Custom Fields</h4>
                    </div>
                    @foreach($lead->customValues as $customValue)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">{{ $customValue->customField->label }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $customValue->value }}</dd>
                        </div>
                    @endforeach
                @endif
            </dl>
        </div>
    </div>

    <div class="mt-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-2xl font-semibold text-gray-900">Lead Activity History</h3>
                <p class="mt-1 text-sm text-gray-500">Track all changes and updates to this lead</p>
            </div>
            <div class="border-t border-gray-200">
                <div class="bg-white">
                    @forelse($lead->activities->sortByDesc('created_at') as $activity)
                        <div class="border-b border-gray-200 px-4 py-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100">
                                            <span class="text-sm font-medium leading-none text-blue-700">
                                                {{ strtoupper(substr($activity->admin_name, 0, 1)) }}
                                            </span>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $activity->admin_name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $activity->created_at->format('M d, Y') }} at {{ $activity->created_at->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $activity->activity_type === 'created' ? 'bg-green-100 text-green-800' : 
                                        ($activity->activity_type === 'updated' ? 'bg-blue-100 text-blue-800' : 
                                        ($activity->activity_type === 'deleted' ? 'bg-red-100 text-red-800' : 
                                        'bg-gray-100 text-gray-800')) 
                                    }}">
                                        {{ ucfirst($activity->activity_type) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-11">
                                <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                @if($activity->changes)
                                    <div class="mt-2">
                                        <details class="text-sm">
                                            <summary class="text-blue-600 hover:text-blue-700 cursor-pointer font-medium">
                                                View details
                                            </summary>
                                            <div class="mt-2 pl-4 border-l-2 border-gray-200">
                                                @if(isset($activity->changes['old']))
                                                    <div class="space-y-2">
                                                        @foreach($activity->changes['old'] as $field => $value)
                                                            <div class="flex flex-col">
                                                                <span class="font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $field)) }}</span>
                                                                <div class="flex items-center space-x-2 mt-1">
                                                                    <span class="line-through text-red-600">{{ is_array($value) ? json_encode($value) : ($value ?: 'empty') }}</span>
                                                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                                    </svg>
                                                                    <span class="text-green-600">{{ is_array($activity->changes['new'][$field]) ? json_encode($activity->changes['new'][$field]) : ($activity->changes['new'][$field] ?: 'empty') }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif(is_array($activity->changes))
                                                    <div class="space-y-2">
                                                        @foreach($activity->changes as $key => $value)
                                                            <div class="flex flex-col">
                                                                <span class="font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                                                <span class="text-gray-900 mt-1">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <pre class="text-sm text-gray-600 whitespace-pre-wrap">{{ json_encode($activity->changes, JSON_PRETTY_PRINT) }}</pre>
                                                @endif
                                            </div>
                                        </details>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-5 text-center text-sm text-gray-500">
                            No activities recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 