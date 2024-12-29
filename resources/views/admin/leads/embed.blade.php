@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Embed Code') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Copy and paste this code into your website where you want the lead form to appear.') }}
                        </p>
                        <div class="bg-gray-100 p-4 rounded-md">
                            <pre class="text-sm overflow-x-auto"><code>&lt;div id="lead-form"&gt;&lt;/div&gt;
&lt;script src="{{ route('public.leads.script') }}"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <button onclick="copyToClipboard()" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Copy to Clipboard') }}
                        </button>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Preview') }}</h3>
                        <div class="border rounded-md p-4">
                            <div id="lead-form"></div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Instructions') }}</h3>
                        <div class="prose">
                            <ol class="list-decimal list-inside space-y-2">
                                <li>{{ __('Copy the embed code above') }}</li>
                                <li>{{ __('Paste it into your website\'s HTML where you want the form to appear') }}</li>
                                <li>{{ __('The form will automatically load and match your website\'s styling') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToClipboard() {
            const embedCode = `<div id="lead-form"></div>\n<script src="{{ route('public.leads.script') }}"><\/script>`;
            navigator.clipboard.writeText(embedCode).then(() => {
                alert('{{ __("Embed code copied to clipboard!") }}');
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
    <script src="{{ route('public.leads.script') }}"></script>
    @endpush
@endsection 