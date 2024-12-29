<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-transparent">
    <div class="max-w-md mx-auto">
        <form id="leadForm" class="space-y-4 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="first_name">
                    First Name *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="first_name" name="first_name" type="text" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="last_name">
                    Last Name *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="last_name" name="last_name" type="text" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="email" name="email" type="email" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                    Phone
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="phone" name="phone" type="tel">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="company">
                    Company
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="company" name="company" type="text">
            </div>

            @if($categories->count() > 0)
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="category_id">
                    Category
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="category_id" name="category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                    Notes
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="notes" name="notes" rows="3"></textarea>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Submit
                </button>
            </div>

            <div id="formMessage" class="mt-4 hidden">
                <p class="text-center text-sm font-medium"></p>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('leadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const messageDiv = document.getElementById('formMessage');
            const messagePara = messageDiv.querySelector('p');

            // Disable submit button
            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';

            fetch('{{ route("public.leads.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.classList.remove('hidden');
                if (data.success) {
                    messagePara.textContent = data.message;
                    messagePara.className = 'text-center text-sm font-medium text-green-600';
                    this.reset();
                } else {
                    throw new Error(data.message || 'Something went wrong');
                }
            })
            .catch(error => {
                messageDiv.classList.remove('hidden');
                messagePara.textContent = error.message;
                messagePara.className = 'text-center text-sm font-medium text-red-600';
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Submit';
            });
        });
    </script>
</body>
</html> 