<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-transparent">
    <div id="lead-form-container" class="max-w-md mx-auto p-6">
        <form id="lead-form" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" id="first_name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <span class="text-red-600 text-sm error-message" data-field="first_name"></span>
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" id="last_name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <span class="text-red-600 text-sm error-message" data-field="last_name"></span>
                </div>

                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <span class="text-red-600 text-sm error-message" data-field="email"></span>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" name="phone" id="phone"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <span class="text-red-600 text-sm error-message" data-field="phone"></span>
                </div>

                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700">Company</label>
                    <input type="text" name="company" id="company"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <span class="text-red-600 text-sm error-message" data-field="company"></span>
                </div>

                @foreach($customFields as $field)
                <div class="{{ $field->type === 'textarea' ? 'sm:col-span-2' : '' }}">
                    <label for="custom_{{ $field->name }}" class="block text-sm font-medium text-gray-700">
                        {{ $field->label }}
                        @if($field->required)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>

                    @switch($field->type)
                        @case('textarea')
                            <textarea name="custom_fields[{{ $field->name }}]" id="custom_{{ $field->name }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                {{ $field->required ? 'required' : '' }}></textarea>
                            @break

                        @case('select')
                            <select name="custom_fields[{{ $field->name }}]" id="custom_{{ $field->name }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                {{ $field->required ? 'required' : '' }}>
                                <option value="">Select {{ $field->label }}</option>
                                @foreach($field->options as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('date')
                            <input type="date" name="custom_fields[{{ $field->name }}]" id="custom_{{ $field->name }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                {{ $field->required ? 'required' : '' }}>
                            @break

                        @default
                            <input type="{{ $field->type }}" name="custom_fields[{{ $field->name }}]" id="custom_{{ $field->name }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                {{ $field->required ? 'required' : '' }}>
                    @endswitch
                    <span class="text-red-600 text-sm error-message" data-field="custom_fields.{{ $field->name }}"></span>
                </div>
                @endforeach
            </div>

            <div class="mt-4">
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Submit
                </button>
            </div>

            <div id="success-message" class="hidden mt-4 p-4 text-sm text-green-700 bg-green-100 rounded-md">
                Thank you! Your information has been submitted successfully.
            </div>
        </form>
    </div>

    <script>
        document.getElementById('lead-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.getElementById('success-message').classList.add('hidden');

            try {
                const response = await fetch('{{ route("public.leads.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(Object.fromEntries(new FormData(this)))
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, messages]) => {
                            const errorEl = document.querySelector(`[data-field="${field}"]`);
                            if (errorEl) {
                                errorEl.textContent = messages[0];
                            }
                        });
                    }
                    return;
                }

                // Show success message
                this.reset();
                document.getElementById('success-message').classList.remove('hidden');

                // Notify parent window if in iframe
                if (window.parent !== window) {
                    window.parent.postMessage({ type: 'leadSubmitted', success: true }, '*');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html> 