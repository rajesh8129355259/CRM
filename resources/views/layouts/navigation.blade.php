<x-nav-link :href="route('admin.lead-categories.index')" :active="request()->routeIs('admin.lead-categories.*')">
    {{ __('Lead Categories') }}
</x-nav-link>

<x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
    {{ __('User Management') }}
</x-nav-link>

<div class="hidden sm:flex sm:items-center sm:ml-6">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                <div>{{ __('Tools') }}</div>
                <div class="ml-1">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('leads.export')">
                {{ __('Export Leads') }}
            </x-dropdown-link>
            
            <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="file" name="file" id="importFile" class="hidden" accept=".csv,.xlsx" onchange="this.form.submit()">
            </form>
            <x-dropdown-link href="#" onclick="document.getElementById('importFile').click(); return false;">
                {{ __('Import Leads') }}
            </x-dropdown-link>

            <x-dropdown-link :href="route('public.leads.form')" target="_blank">
                {{ __('Public Lead Form') }}
            </x-dropdown-link>
        </x-slot>
    </x-dropdown>
</div> 