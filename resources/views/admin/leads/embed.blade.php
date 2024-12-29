@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Embed Lead Form</h2>
                    <p class="mt-2 text-gray-600">Add this code to your website where you want the lead form to appear.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <label class="block text-sm font-medium text-gray-700">HTML Code</label>
                        <button onclick="copyCode('html-code')" class="text-sm text-blue-600 hover:text-blue-800">Copy</button>
                    </div>
                    <pre id="html-code" class="bg-gray-800 text-white p-4 rounded-md overflow-x-auto"><code>&lt;div id="lead-form-container"&gt;&lt;/div&gt;
&lt;script src="{{ route('public.leads.script') }}"&gt;&lt;/script&gt;</code></pre>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>
                    <div class="border rounded-lg p-4">
                        <div id="lead-form-container"></div>
                        <script src="{{ route('public.leads.script') }}"></script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyCode(elementId) {
    const el = document.getElementById(elementId);
    const text = el.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        // Show feedback
        const button = el.parentElement.querySelector('button');
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        setTimeout(() => {
            button.textContent = originalText;
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy text:', err);
    });
}
</script>
@endsection 